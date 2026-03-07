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
            ['nombre' => 'Atlántida'],
            ['nombre' => 'Las Toscas'],
            ['nombre' => 'Parque del Plata'],
            ['nombre' => 'Pocitos'],
            ['nombre' => 'Carrasco'],
            ['nombre' => 'Lagomar'],
            ['nombre' => 'Minas'],
        ];

        foreach ($zonas as $data) {
            Zona::create($data);
        }
    }
}
