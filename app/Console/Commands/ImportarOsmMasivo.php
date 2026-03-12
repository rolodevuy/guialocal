<?php

namespace App\Console\Commands;

use App\Filament\Pages\ImportarNegocios;
use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\Zona;
use App\Services\OverpassService;
use Illuminate\Console\Command;

class ImportarOsmMasivo extends Command
{
    protected $signature = 'osm:importar-masivo
        {zonas?* : Slugs de zonas (ej: atlantida parque-del-plata lagomar). Si vacío, importa todas.}
        {--activar : Publicar los negocios directamente (activo=true, estado=activa)}
        {--dry-run : Solo mostrar lo que se importaría, sin insertar}';

    protected $description = 'Importa todos los negocios de OpenStreetMap para las zonas indicadas';

    public function handle(): int
    {
        $slugs = $this->argument('zonas');
        $activar = $this->option('activar');
        $dryRun = $this->option('dry-run');

        $zonas = empty($slugs)
            ? Zona::orderBy('nombre')->get()
            : Zona::whereIn('slug', $slugs)->orderBy('nombre')->get();

        if ($zonas->isEmpty()) {
            $this->error('No se encontraron zonas.');
            return self::FAILURE;
        }

        $service = new OverpassService();
        $mapeo = ImportarNegocios::mapeoOsmCategoria();
        $totalImportados = 0;
        $totalSkipped = 0;

        foreach ($zonas as $zona) {
            $this->info("Buscando negocios en {$zona->nombre}...");

            try {
                $resultados = $service->buscarTodosEnLocalidad($zona->nombre);
            } catch (\Throwable $e) {
                $this->error("  Error: {$e->getMessage()}");
                continue;
            }

            $this->info("  Encontrados: " . count($resultados));

            if (empty($resultados)) {
                continue;
            }

            // Cargar existentes para detectar duplicados
            $existentes = Lugar::select('lugares.id', 'lugares.nombre', 'lugares.lat', 'lugares.lng')
                ->join('fichas', 'fichas.lugar_id', '=', 'lugares.id')
                ->get();

            $importadosZona = 0;
            $skippedZona = 0;

            foreach ($resultados as $r) {
                // Detectar categoría por tags OSM
                $catId = null;
                foreach ($r['tags_relevantes'] ?? [] as $key => $value) {
                    if (isset($mapeo[$value])) {
                        $catId = $mapeo[$value];
                        break;
                    }
                }

                if (!$catId) {
                    $skippedZona++;
                    continue;
                }

                // Detectar duplicado
                $esDuplicado = $existentes->first(function ($l) use ($r) {
                    $mismoNombre = strtolower(trim($l->nombre)) === strtolower(trim($r['nombre']));
                    $cercano = $l->lat && $l->lng
                        && abs($l->lat - $r['lat']) < 0.0003
                        && abs($l->lng - $r['lng']) < 0.0003;

                    if ($mismoNombre && $cercano) return true;
                    if ($l->lat && $l->lng
                        && abs($l->lat - $r['lat']) < 0.00005
                        && abs($l->lng - $r['lng']) < 0.00005) return true;
                    return false;
                });

                if ($esDuplicado) {
                    $skippedZona++;
                    continue;
                }

                if ($dryRun) {
                    $this->line("  [DRY] {$r['nombre']} — cat:{$catId}");
                    $importadosZona++;
                    continue;
                }

                $lugar = Lugar::create([
                    'nombre'       => $r['nombre'],
                    'direccion'    => $r['direccion'],
                    'lat'          => $r['lat'],
                    'lng'          => $r['lng'],
                    'categoria_id' => $catId,
                    'zona_id'      => $zona->id,
                    'activo'       => $activar,
                ]);

                Ficha::create([
                    'lugar_id'  => $lugar->id,
                    'telefono'  => $r['telefono'],
                    'sitio_web' => $r['sitio_web'],
                    'estado'    => $activar ? 'activa' : 'pendiente',
                    'plan'      => 'gratuito',
                    'activo'    => $activar,
                ]);

                // Agregar al set de existentes para no duplicar dentro del mismo batch
                $existentes->push((object) [
                    'id' => $lugar->id,
                    'nombre' => $r['nombre'],
                    'lat' => $r['lat'],
                    'lng' => $r['lng'],
                ]);

                $importadosZona++;
            }

            $this->info("  Importados: {$importadosZona} | Omitidos: {$skippedZona}");
            $totalImportados += $importadosZona;
            $totalSkipped += $skippedZona;
        }

        $this->newLine();
        $prefix = $dryRun ? '[DRY RUN] ' : '';
        $this->info("{$prefix}Total importados: {$totalImportados} | Omitidos: {$totalSkipped}");

        if (!$dryRun && $totalImportados > 0) {
            $this->call('app:recalcular-scores');
        }

        return self::SUCCESS;
    }
}
