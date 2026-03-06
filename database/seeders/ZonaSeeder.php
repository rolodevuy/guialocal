<?php

namespace Database\Seeders;

use App\Models\Zona;
use Illuminate\Database\Seeder;

class ZonaSeeder extends Seeder
{
    public function run(): void
    {
        Zona::truncate();

        $zonas = [
            ['nombre' => 'Centro'],
            ['nombre' => 'Villa del Parque'],
            ['nombre' => 'Palermo'],
            ['nombre' => 'San Telmo'],
            ['nombre' => 'Belgrano'],
        ];

        foreach ($zonas as $data) {
            Zona::create($data);
        }
    }
}
