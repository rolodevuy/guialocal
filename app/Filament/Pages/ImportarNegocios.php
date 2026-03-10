<?php

namespace App\Filament\Pages;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\Zona;
use App\Services\OverpassService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ImportarNegocios extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationLabel = 'Importar negocios';
    protected static ?string $title           = 'Importar negocios desde OpenStreetMap';
    protected static ?string $navigationGroup = 'Herramientas';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.importar-negocios';

    // ── Formulario ────────────────────────────────────────────────────────────

    public string $tipo        = '';
    public ?int   $categoriaId = null;
    public ?int   $zonaId      = null;
    public int    $radio       = 2000;

    // ── Estado ────────────────────────────────────────────────────────────────

    public array  $resultados    = [];
    public array  $seleccionados = [];
    public bool   $buscando      = false;
    public string $error         = '';
    public int    $importados    = 0;

    // ── Datos para los selects ────────────────────────────────────────────────

    public function getCategorias(): array
    {
        return Categoria::where('activo', true)
            ->orderBy('nivel')
            ->orderBy('nombre')
            ->with('parent')
            ->get()
            ->mapWithKeys(fn ($c) => [$c->id => $c->nombre_completo])
            ->toArray();
    }

    public function getZonas(): array
    {
        return Zona::orderBy('nombre')
            ->pluck('nombre', 'id')
            ->toArray();
    }

    public static function getTipos(): array
    {
        return collect(OverpassService::tipos())
            ->mapWithKeys(fn ($t, $k) => [$k => $t['label']])
            ->toArray();
    }

    // ── Acciones ──────────────────────────────────────────────────────────────

    public function buscar(): void
    {
        $this->validate([
            'tipo'        => 'required',
            'categoriaId' => 'required|exists:categorias,id',
            'zonaId'      => 'required|exists:zonas,id',
            'radio'       => 'integer|min:500|max:10000',
        ], [
            'tipo.required'        => 'Elegí un tipo de negocio.',
            'categoriaId.required' => 'Elegí una categoría.',
            'zonaId.required'      => 'Elegí una zona.',
        ]);

        $this->error        = '';
        $this->resultados   = [];
        $this->seleccionados = [];
        $this->importados   = 0;
        $this->buscando     = true;

        $zona = Zona::find($this->zonaId);

        if (! $zona->lat_centro || ! $zona->lng_centro) {
            $this->error    = "La zona \"{$zona->nombre}\" no tiene coordenadas de centro. Configurala en Zonas.";
            $this->buscando = false;
            return;
        }

        try {
            $service          = new OverpassService();
            $raw              = $service->buscar($this->tipo, $zona->lat_centro, $zona->lng_centro, $this->radio);
            $this->resultados = $this->marcarDuplicados($raw);
        } catch (\Throwable $e) {
            $this->error = 'Error al consultar OpenStreetMap: ' . $e->getMessage();
        } finally {
            $this->buscando = false;
        }
    }

    public function seleccionarTodos(): void
    {
        $this->seleccionados = collect($this->resultados)
            ->where('existe', false)
            ->pluck('osm_id')
            ->values()
            ->toArray();
    }

    public function deseleccionarTodos(): void
    {
        $this->seleccionados = [];
    }

    public function importar(): void
    {
        if (empty($this->seleccionados)) {
            Notification::make()
                ->title('Seleccioná al menos un negocio')
                ->warning()
                ->send();
            return;
        }

        $aImportar = collect($this->resultados)
            ->whereIn('osm_id', $this->seleccionados)
            ->where('existe', false);

        $this->importados = 0;

        foreach ($aImportar as $r) {
            $lugar = Lugar::create([
                'nombre'       => $r['nombre'],
                'direccion'    => $r['direccion'],
                'lat'          => $r['lat'],
                'lng'          => $r['lng'],
                'categoria_id' => $this->categoriaId,
                'zona_id'      => $r['zona_id_sugerida'] ?? $this->zonaId,
                'activo'       => false,
            ]);

            Ficha::create([
                'lugar_id'  => $lugar->id,
                'telefono'  => $r['telefono'],
                'sitio_web' => $r['sitio_web'],
                'estado'    => 'pendiente',
                'plan'      => 'gratuito',
                'activo'    => false,
            ]);

            $this->importados++;
        }

        // Marcamos los recién importados como existentes en la lista
        $importadosIds = $this->seleccionados;
        $this->resultados = collect($this->resultados)
            ->map(function ($r) use ($importadosIds) {
                if (in_array($r['osm_id'], $importadosIds)) {
                    $r['existe'] = true;
                }
                return $r;
            })
            ->toArray();

        $this->seleccionados = [];

        Notification::make()
            ->title("{$this->importados} negocio(s) importado(s)")
            ->body('Quedaron en estado pendiente. Revisalos en Fichas antes de publicar.')
            ->success()
            ->send();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function marcarDuplicados(array $resultados): array
    {
        $existentes = Lugar::select('id', 'nombre', 'lat', 'lng')->get();
        $zonas      = Zona::select('id', 'nombre')->get();

        return collect($resultados)
            ->map(function ($r) use ($existentes, $zonas) {
                // ── Duplicado ─────────────────────────────────────────────────
                $match = $existentes->first(function ($l) use ($r) {
                    if (strtolower(trim($l->nombre)) === strtolower(trim($r['nombre']))) {
                        return true;
                    }
                    if ($l->lat && $l->lng) {
                        return abs($l->lat - $r['lat']) < 0.001
                            && abs($l->lng - $r['lng']) < 0.001;
                    }
                    return false;
                });

                $r['existe']   = (bool) $match;
                $r['lugar_id'] = $match?->id;

                // ── Auto-asignar zona por localidad de OSM ────────────────────
                $zonaMatch = null;
                if (! empty($r['localidad'])) {
                    $zonaMatch = $zonas->first(fn ($z) =>
                        strtolower(trim($z->nombre)) === strtolower(trim($r['localidad']))
                    );
                }
                $r['zona_id_sugerida']     = $zonaMatch?->id ?? $this->zonaId;
                $r['zona_nombre_sugerida'] = $zonaMatch?->nombre
                    ?? ($zonas->find($this->zonaId)?->nombre ?? '');
                $r['zona_auto']            = (bool) $zonaMatch;

                return $r;
            })
            ->toArray();
    }
}
