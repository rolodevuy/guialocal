<?php

namespace Database\Seeders;

use App\Models\Zona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ZonaSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Zona::truncate();
        Schema::enableForeignKeyConstraints();

        $zonas = [
            ['nombre' => 'Atlántida',        'lat_centro' => -34.7700, 'lng_centro' => -55.7630],
            ['nombre' => 'Las Toscas',       'lat_centro' => -34.7300, 'lng_centro' => -55.6630],
            ['nombre' => 'Parque del Plata', 'lat_centro' => -34.7500, 'lng_centro' => -55.8220],
            ['nombre' => 'Pocitos',          'lat_centro' => -34.9075, 'lng_centro' => -56.1625],
            ['nombre' => 'Carrasco',         'lat_centro' => -34.8685, 'lng_centro' => -56.0400],
            ['nombre' => 'Lagomar',          'lat_centro' => -34.8110, 'lng_centro' => -55.9425],
            ['nombre' => 'Minas',            'lat_centro' => -34.3785, 'lng_centro' => -55.2360],
        ];

        foreach ($zonas as $data) {
            Zona::create($data);
        }
    }
}
