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
    public string $modo        = 'localidad'; // 'localidad' | 'radio'
    public ?int   $categoriaId = null;
    public ?int   $zonaId      = null;
    public int    $radio       = 2000;

    /**
     * Mapeo de tag OSM → categoria_id en la guía.
     * Se usa para sugerir categoría automáticamente cuando se buscan "Todos".
     */
    public static function mapeoOsmCategoria(): array
    {
        return [
            // amenity
            'restaurant'     => 1,  // Restaurantes
            'cafe'           => 2,  // Cafés y Bares
            'bar'            => 2,
            'fast_food'      => 1,  // Restaurantes
            'pharmacy'       => 5,  // Farmacias
            'bank'           => 8,  // Servicios Profesionales
            'dentist'        => 7,  // Salud y Bienestar
            'hospital'       => 7,
            'school'         => 14, // Educación
            // shop
            'supermarket'    => 6,  // Supermercados
            'convenience'    => 6,
            'bakery'         => 3,  // Panaderías y Pastelerías
            'butcher'        => 6,
            'clothes'        => 9,  // Indumentaria y Calzado
            'hardware'       => 10, // Hogar y Construcción
            'beauty'         => 7,  // Salud y Bienestar
            'optician'       => 5,  // Farmacias
            'florist'        => 10,
            'books'          => 14, // Educación
            // tourism
            'hotel'          => 13, // Turismo y Alojamiento
            'hostel'         => 13,
            // leisure
            'fitness_centre' => 7,  // Salud y Bienestar
        ];
    }

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
        $rules = [
            'zonaId' => 'required|exists:zonas,id',
        ];

        if ($this->modo === 'radio') {
            $rules['radio'] = 'integer|min:500|max:10000';
        }

        $this->validate($rules, [
            'zonaId.required' => 'Elegí una zona.',
        ]);

        $this->error         = '';
        $this->resultados    = [];
        $this->seleccionados = [];
        $this->importados    = 0;
        $this->buscando      = true;

        $zona    = Zona::find($this->zonaId);
        $service = new OverpassService();
        $todos   = empty($this->tipo); // Si no eligió tipo → traer todos

        try {
            if ($this->modo === 'localidad') {
                $raw = $todos
                    ? $service->buscarTodosEnLocalidad($zona->nombre)
                    : $service->buscarEnLocalidad($this->tipo, $zona->nombre);
            } else {
                if (! $zona->lat_centro || ! $zona->lng_centro) {
                    $this->error    = "La zona \"{$zona->nombre}\" no tiene coordenadas de centro. Configurala en Zonas.";
                    $this->buscando = false;
                    return;
                }
                $raw = $todos
                    ? $service->buscarTodos($zona->lat_centro, $zona->lng_centro, $this->radio)
                    : $service->buscar($this->tipo, $zona->lat_centro, $zona->lng_centro, $this->radio);
            }

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

        // Si no hay categoría global, verificar que todos tengan sugerencia
        if (! $this->categoriaId) {
            $sinCategoria = $aImportar->filter(fn ($r) => empty($r['categoria_id_sugerida']))->count();
            if ($sinCategoria > 0) {
                Notification::make()
                    ->title("Hay {$sinCategoria} negocio(s) sin categoría asignable")
                    ->body('Elegí una categoría en el dropdown o deseleccioná los que no tienen categoría sugerida.')
                    ->warning()
                    ->send();
                return;
            }
        }

        $this->importados = 0;

        foreach ($aImportar as $r) {
            $catId = $this->categoriaId ?? ($r['categoria_id_sugerida'] ?? null);
            if (! $catId) {
                continue; // seguridad extra
            }

            $lugar = Lugar::create([
                'nombre'       => $r['nombre'],
                'direccion'    => $r['direccion'],
                'lat'          => $r['lat'],
                'lng'          => $r['lng'],
                'categoria_id' => $catId,
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
        $existentes = Lugar::select('lugares.id', 'lugares.nombre', 'lugares.lat', 'lugares.lng')
            ->join('fichas', 'fichas.lugar_id', '=', 'lugares.id')
            ->get();
        $zonas      = Zona::select('id', 'nombre')->get();

        return collect($resultados)
            ->map(function ($r) use ($existentes, $zonas) {
                // ── Duplicado ─────────────────────────────────────────────────
                $match = $existentes->first(function ($l) use ($r) {
                    $mismoNombre = strtolower(trim($l->nombre)) === strtolower(trim($r['nombre']));
                    $cercano     = $l->lat && $l->lng
                        && abs($l->lat - $r['lat']) < 0.0003
                        && abs($l->lng - $r['lng']) < 0.0003; // ~33m

                    // Duplicado si: mismo nombre Y cerca, O exactamente mismo punto (<5m)
                    if ($mismoNombre && $cercano) {
                        return true;
                    }
                    // Mismo punto geográfico (~5m) aunque nombre difiera (misma dirección)
                    if ($l->lat && $l->lng
                        && abs($l->lat - $r['lat']) < 0.00005
                        && abs($l->lng - $r['lng']) < 0.00005) {
                        return true;
                    }
                    return false;
                });

                $r['existe']   = (bool) $match;
                $r['lugar_id'] = $match?->id;

                // ── Auto-sugerir categoría por tags OSM ─────────────────────
                $mapeo = self::mapeoOsmCategoria();
                $r['categoria_id_sugerida'] = null;
                foreach ($r['tags_relevantes'] ?? [] as $key => $value) {
                    if (isset($mapeo[$value])) {
                        $r['categoria_id_sugerida'] = $mapeo[$value];
                        break;
                    }
                }

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
