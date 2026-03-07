<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Categoria::truncate();
        Schema::enableForeignKeyConstraints();

        // ── Nivel 1 — Familias ─────────────────────────────────────────────
        $familias = [
            ['nombre' => 'Restaurantes',             'descripcion' => 'Lugares para comer, desde parrillas hasta cocina internacional.', 'icono' => 'utensils'],
            ['nombre' => 'Cafés y Bares',            'descripcion' => 'Cafeterías, bares y lugares para tomar algo rico.',              'icono' => 'coffee'],
            ['nombre' => 'Panaderías y Pastelerías', 'descripcion' => 'Pan artesanal, facturas, tortas y dulces del barrio.',           'icono' => 'cake'],
            ['nombre' => 'Heladerías',               'descripcion' => 'Helados artesanales, gelato y postres fríos.',                   'icono' => 'ice-cream-cone'],
            ['nombre' => 'Farmacias',                'descripcion' => 'Farmacias y dietéticas con atención personalizada.',             'icono' => 'pill'],
            ['nombre' => 'Supermercados',            'descripcion' => 'Almacenes, verdulerías y mercados del barrio.',                  'icono' => 'shopping-cart'],
            ['nombre' => 'Salud y Bienestar',        'descripcion' => 'Gimnasios, centros de estética, yoga y spas.',                  'icono' => 'heart-pulse'],
            ['nombre' => 'Servicios Profesionales',  'descripcion' => 'Estudios contables, abogados, consultorías y más.',             'icono' => 'briefcase'],
            ['nombre' => 'Indumentaria y Calzado',   'descripcion' => 'Ropa, zapatos y accesorios de diseño local.',                   'icono' => 'shirt'],
            ['nombre' => 'Hogar y Construcción',     'descripcion' => 'Ferreterías, muebles, decoración y materiales de obra.',        'icono' => 'hammer'],
            ['nombre' => 'Automotor',                'descripcion' => 'Talleres, repuestos, lavaderos y servicios para vehículos.',    'icono' => 'car'],
            ['nombre' => 'Entretenimiento',          'descripcion' => 'Cines, juegos, actividades recreativas y cultura.',             'icono' => 'gamepad-2'],
            ['nombre' => 'Turismo y Alojamiento',    'descripcion' => 'Hoteles, cabañas, posadas y servicios turísticos.',             'icono' => 'bed'],
            ['nombre' => 'Educación',                'descripcion' => 'Colegios, academias, cursos y formación.',                      'icono' => 'graduation-cap'],
        ];

        $padres = [];
        foreach ($familias as $data) {
            $padres[$data['nombre']] = Categoria::create(array_merge($data, [
                'nivel' => 1,
                'parent_id' => null,
            ]));
        }

        // ── Nivel 2 — Tipos ────────────────────────────────────────────────
        $tipos = [
            'Restaurantes' => [
                ['nombre' => 'Parrilla',              'icono' => 'flame'],
                ['nombre' => 'Sushi',                 'icono' => 'fish'],
                ['nombre' => 'Pasta y Pizza',         'icono' => 'pizza'],
                ['nombre' => 'Cocina Internacional',  'icono' => 'globe'],
                ['nombre' => 'Comida Rápida',         'icono' => 'sandwich'],
            ],
            'Cafés y Bares' => [
                ['nombre' => 'Cafetería',             'icono' => 'coffee'],
                ['nombre' => 'Bar',                   'icono' => 'beer'],
                ['nombre' => 'Pub',                   'icono' => 'wine'],
            ],
            'Panaderías y Pastelerías' => [
                ['nombre' => 'Panadería',             'icono' => 'wheat'],
                ['nombre' => 'Pastelería',            'icono' => 'cake-slice'],
                ['nombre' => 'Confitería',            'icono' => 'candy'],
            ],
            'Salud y Bienestar' => [
                ['nombre' => 'Gimnasio',              'icono' => 'dumbbell'],
                ['nombre' => 'Spa y Estética',        'icono' => 'sparkles'],
                ['nombre' => 'Yoga y Meditación',     'icono' => 'lotus'],
            ],
            'Servicios Profesionales' => [
                ['nombre' => 'Estudio Contable',      'icono' => 'calculator'],
                ['nombre' => 'Estudio Jurídico',      'icono' => 'scale'],
                ['nombre' => 'Consultoría',           'icono' => 'chart-line'],
            ],
        ];

        foreach ($tipos as $familia => $subCats) {
            $padre = $padres[$familia];
            foreach ($subCats as $data) {
                Categoria::create([
                    'nombre'    => $data['nombre'],
                    'icono'     => $data['icono'],
                    'nivel'     => 2,
                    'parent_id' => $padre->id,
                ]);
            }
        }
    }
}
