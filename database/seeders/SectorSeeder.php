<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SectorSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Sector::truncate();
        Schema::enableForeignKeyConstraints();

        $sectores = [
            [
                'nombre'      => 'Comercial',
                'descripcion' => 'Tiendas, servicios profesionales y todo lo que necesitás día a día.',
                'icono'       => 'shopping-bag',
                'color_classes' => [
                    'bg'         => 'bg-amber-100',
                    'bg_light'   => 'bg-amber-50',
                    'text'       => 'text-amber-600',
                    'text_hover' => 'text-amber-700',
                    'border'     => 'border-amber-200',
                    'icon'       => 'text-amber-500',
                ],
                'orden' => 1,
            ],
            [
                'nombre'      => 'Gastronomía y Ocio',
                'descripcion' => 'Restaurantes, cafés, heladerías y entretenimiento para disfrutar.',
                'icono'       => 'utensils',
                'color_classes' => [
                    'bg'         => 'bg-rose-100',
                    'bg_light'   => 'bg-rose-50',
                    'text'       => 'text-rose-600',
                    'text_hover' => 'text-rose-700',
                    'border'     => 'border-rose-200',
                    'icon'       => 'text-rose-500',
                ],
                'orden' => 2,
            ],
            [
                'nombre'      => 'Turismo y Alojamiento',
                'descripcion' => 'Hoteles, cabañas, posadas y servicios turísticos.',
                'icono'       => 'map-pin',
                'color_classes' => [
                    'bg'         => 'bg-sky-100',
                    'bg_light'   => 'bg-sky-50',
                    'text'       => 'text-sky-600',
                    'text_hover' => 'text-sky-700',
                    'border'     => 'border-sky-200',
                    'icon'       => 'text-sky-500',
                ],
                'orden' => 3,
            ],
        ];

        foreach ($sectores as $data) {
            Sector::create($data);
        }
    }
}
