<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OverpassService
{
    const ENDPOINTS = [
        'https://overpass-api.de/api/interpreter',
        'https://overpass.kumi.systems/api/interpreter',
    ];

    /**
     * Tipos de negocio disponibles, mapeados a tags de OpenStreetMap.
     */
    public static function tipos(): array
    {
        return [
            'restaurant'  => ['key' => 'amenity', 'value' => 'restaurant',     'label' => 'Restaurantes'],
            'cafe'        => ['key' => 'amenity', 'value' => 'cafe',            'label' => 'Cafeterías'],
            'bar'         => ['key' => 'amenity', 'value' => 'bar',             'label' => 'Bares'],
            'fast_food'   => ['key' => 'amenity', 'value' => 'fast_food',       'label' => 'Comida rápida'],
            'pharmacy'    => ['key' => 'amenity', 'value' => 'pharmacy',        'label' => 'Farmacias'],
            'supermarket' => ['key' => 'shop',    'value' => 'supermarket',     'label' => 'Supermercados'],
            'convenience' => ['key' => 'shop',    'value' => 'convenience',     'label' => 'Almacenes / Minimercados'],
            'bakery'      => ['key' => 'shop',    'value' => 'bakery',          'label' => 'Panaderías'],
            'butcher'     => ['key' => 'shop',    'value' => 'butcher',         'label' => 'Carnicerías'],
            'bank'        => ['key' => 'amenity', 'value' => 'bank',            'label' => 'Bancos'],
            'hotel'       => ['key' => 'tourism', 'value' => 'hotel',           'label' => 'Hoteles'],
            'hostel'      => ['key' => 'tourism', 'value' => 'hostel',          'label' => 'Hostels'],
            'gym'         => ['key' => 'leisure', 'value' => 'fitness_centre',  'label' => 'Gimnasios'],
            'clothes'     => ['key' => 'shop',    'value' => 'clothes',         'label' => 'Ropa'],
            'hardware'    => ['key' => 'shop',    'value' => 'hardware',        'label' => 'Ferreterías'],
            'beauty'      => ['key' => 'shop',    'value' => 'beauty',          'label' => 'Peluquerías / Estética'],
            'dentist'     => ['key' => 'amenity', 'value' => 'dentist',         'label' => 'Dentistas'],
            'hospital'    => ['key' => 'amenity', 'value' => 'hospital',        'label' => 'Hospitales / Clínicas'],
            'school'      => ['key' => 'amenity', 'value' => 'school',          'label' => 'Escuelas'],
            'optician'    => ['key' => 'shop',    'value' => 'optician',        'label' => 'Ópticas'],
            'florist'     => ['key' => 'shop',    'value' => 'florist',         'label' => 'Floristerías'],
            'books'       => ['key' => 'shop',    'value' => 'books',           'label' => 'Librerías'],
        ];
    }

    /**
     * Busca negocios de un tipo dentro de los límites de una localidad OSM.
     * Usa area["name"="..."] — no requiere radio ni coordenadas.
     *
     * @throws \RuntimeException
     */
    public function buscarEnLocalidad(string $tipoKey, string $nombreLocalidad): array
    {
        $tipos = self::tipos();

        if (! isset($tipos[$tipoKey])) {
            throw new \InvalidArgumentException("Tipo desconocido: {$tipoKey}");
        }

        $t    = $tipos[$tipoKey];
        $name = addslashes($nombreLocalidad);

        // Busca el área de la localidad DENTRO de Uruguay (relation 287072)
        // para evitar traer resultados de localidades homónimas en otros países.
        $query = "[out:json][timeout:30];\n"
            . "area(3600287072)->.uruguay;\n"
            . "area[\"name\"=\"{$name}\"](area.uruguay)->.searchArea;\n"
            . "(\n"
            . "  node[\"{$t['key']}\"=\"{$t['value']}\"](area.searchArea);\n"
            . "  way[\"{$t['key']}\"=\"{$t['value']}\"](area.searchArea);\n"
            . ");\n"
            . "out center;";

        return $this->ejecutar($query);
    }

    /**
     * Busca negocios de un tipo en un radio alrededor de unas coordenadas.
     *
     * @throws \RuntimeException
     */
    public function buscar(string $tipoKey, float $lat, float $lng, int $radio = 2000): array
    {
        $tipos = self::tipos();

        if (! isset($tipos[$tipoKey])) {
            throw new \InvalidArgumentException("Tipo desconocido: {$tipoKey}");
        }

        $t = $tipos[$tipoKey];

        $query = "[out:json][timeout:30];\n"
            . "(\n"
            . "  node[\"{$t['key']}\"=\"{$t['value']}\"](around:{$radio},{$lat},{$lng});\n"
            . "  way[\"{$t['key']}\"=\"{$t['value']}\"](around:{$radio},{$lat},{$lng});\n"
            . ");\n"
            . "out center;";

        return $this->ejecutar($query);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function ejecutar(string $query): array
    {
        $lastError = null;

        foreach (self::ENDPOINTS as $endpoint) {
            try {
                $response = Http::timeout(35)
                    ->asForm()
                    ->post($endpoint, ['data' => $query]);

                if ($response->ok()) {
                    return $this->parsear($response->json('elements', []));
                }

                $lastError = 'HTTP ' . $response->status() . ' desde ' . parse_url($endpoint, PHP_URL_HOST);

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastError = 'Sin respuesta desde ' . parse_url($endpoint, PHP_URL_HOST);
            }
        }

        throw new \RuntimeException(
            'OpenStreetMap no respondió en ningún servidor (' . $lastError . '). Intentá de nuevo en unos minutos.'
        );
    }

    // Bounding box de Uruguay (con margen)
    // lat: -35.8 a -30.0 | lng: -58.5 a -53.0
    private function enUruguay(float $lat, float $lng): bool
    {
        return $lat >= -35.8 && $lat <= -30.0
            && $lng >= -58.5 && $lng <= -53.0;
    }

    private function parsear(array $elements): array
    {
        $resultados = [];

        foreach ($elements as $el) {
            $tags   = $el['tags'] ?? [];
            $nombre = $tags['name'] ?? null;

            if (! $nombre) {
                continue;
            }

            $lat = $el['lat'] ?? ($el['center']['lat'] ?? null);
            $lng = $el['lon'] ?? ($el['center']['lon'] ?? null);

            if (! $lat || ! $lng) {
                continue;
            }

            // Descartar resultados fuera de Uruguay (localidades homónimas)
            if (! $this->enUruguay((float) $lat, (float) $lng)) {
                continue;
            }

            $resultados[] = [
                'osm_id'         => (string) $el['id'],
                'osm_type'       => $el['type'],
                'nombre'         => $nombre,
                'direccion'      => $this->parsearDireccion($tags),
                'localidad'      => $this->parsearLocalidad($tags),
                'tags_relevantes'=> $this->parsearTagsRelevantes($tags),
                'telefono'       => $this->limpiarTelefono(
                    $tags['phone'] ?? $tags['contact:phone'] ?? null
                ),
                'sitio_web'      => $tags['website'] ?? $tags['contact:website'] ?? $tags['url'] ?? null,
                'lat'            => round((float) $lat, 7),
                'lng'            => round((float) $lng, 7),
            ];
        }

        return $resultados;
    }

    private function parsearLocalidad(array $tags): ?string
    {
        // OSM usa distintos campos según el tipo de lugar
        return $tags['addr:city']
            ?? $tags['addr:suburb']
            ?? $tags['addr:hamlet']
            ?? $tags['addr:village']
            ?? $tags['addr:town']
            ?? null;
    }

    private function parsearTagsRelevantes(array $tags): array
    {
        $relevantes = [];
        $claves = ['amenity', 'shop', 'tourism', 'leisure', 'office', 'craft', 'healthcare'];

        foreach ($claves as $clave) {
            if (isset($tags[$clave])) {
                $relevantes[$clave] = $tags[$clave];
            }
        }

        return $relevantes;
    }

    private function parsearDireccion(array $tags): ?string
    {
        $partes = array_filter([
            $tags['addr:street']      ?? null,
            $tags['addr:housenumber'] ?? null,
        ]);

        return $partes
            ? implode(' ', $partes)
            : ($tags['addr:full'] ?? null);
    }

    private function limpiarTelefono(?string $tel): ?string
    {
        if (! $tel) {
            return null;
        }

        // Si hay varios separados por ";" tomamos solo el primero
        return trim(explode(';', $tel)[0]) ?: null;
    }
}
