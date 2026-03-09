<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Sector;
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
            'Automotor' => [
                ['nombre' => 'Taller Mecánico',       'icono' => 'wrench'],
                ['nombre' => 'Gomería',               'icono' => 'circle-dot'],
                ['nombre' => 'Lavadero',              'icono' => 'droplets'],
                ['nombre' => 'Repuestos',             'icono' => 'cog'],
                ['nombre' => 'Chapa y Pintura',       'icono' => 'paint-roller'],
            ],
            'Educación' => [
                ['nombre' => 'Academia de Idiomas',   'icono' => 'languages'],
                ['nombre' => 'Informática',           'icono' => 'monitor'],
                ['nombre' => 'Música y Arte',         'icono' => 'music'],
                ['nombre' => 'Apoyo Escolar',         'icono' => 'book-open'],
                ['nombre' => 'Autoescuela',           'icono' => 'id-card'],
            ],
            'Entretenimiento' => [
                ['nombre' => 'Cine y Teatro',         'icono' => 'clapperboard'],
                ['nombre' => 'Salón de Fiestas',      'icono' => 'party-popper'],
                ['nombre' => 'Escape Room',           'icono' => 'key-round'],
                ['nombre' => 'Arcade y Juegos',       'icono' => 'joystick'],
            ],
            'Farmacias' => [
                ['nombre' => 'Farmacia',              'icono' => 'pill'],
                ['nombre' => 'Perfumería',            'icono' => 'spray-can'],
                ['nombre' => 'Óptica',                'icono' => 'glasses'],
                ['nombre' => 'Veterinaria',           'icono' => 'paw-print'],
            ],
            'Hogar y Construcción' => [
                ['nombre' => 'Ferretería',            'icono' => 'hammer'],
                ['nombre' => 'Pinturería',            'icono' => 'paintbrush'],
                ['nombre' => 'Mueblería',             'icono' => 'armchair'],
                ['nombre' => 'Barraca',               'icono' => 'warehouse'],
                ['nombre' => 'Electricidad',          'icono' => 'zap'],
                ['nombre' => 'Vivero y Jardín',       'icono' => 'flower-2'],
            ],
            'Indumentaria y Calzado' => [
                ['nombre' => 'Ropa Mujer',            'icono' => 'shirt'],
                ['nombre' => 'Ropa Hombre',           'icono' => 'shirt'],
                ['nombre' => 'Calzado',               'icono' => 'footprints'],
                ['nombre' => 'Deportiva',             'icono' => 'trophy'],
                ['nombre' => 'Ropa Infantil',         'icono' => 'baby'],
            ],
            'Supermercados' => [
                ['nombre' => 'Supermercado',          'icono' => 'shopping-cart'],
                ['nombre' => 'Almacén',               'icono' => 'store'],
                ['nombre' => 'Verdulería',            'icono' => 'apple'],
                ['nombre' => 'Carnicería',            'icono' => 'beef'],
                ['nombre' => 'Dietética',             'icono' => 'leaf'],
            ],
            'Turismo y Alojamiento' => [
                ['nombre' => 'Hotel',                 'icono' => 'building'],
                ['nombre' => 'Hostel',                'icono' => 'bed-single'],
                ['nombre' => 'Apart Hotel',           'icono' => 'building-2'],
                ['nombre' => 'Cabañas',               'icono' => 'tree-pine'],
                ['nombre' => 'Camping',               'icono' => 'tent'],
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

        // ── Asignar sector a cada familia Nivel 1 ───────────────────────
        $sectorMap = [
            'Comercial' => [
                'Farmacias', 'Supermercados', 'Salud y Bienestar',
                'Servicios Profesionales', 'Indumentaria y Calzado',
                'Hogar y Construcción', 'Automotor', 'Educación',
            ],
            'Gastronomía y Ocio' => [
                'Restaurantes', 'Cafés y Bares', 'Panaderías y Pastelerías',
                'Heladerías', 'Entretenimiento',
            ],
            'Turismo y Alojamiento' => [
                'Turismo y Alojamiento',
            ],
        ];

        foreach ($sectorMap as $sectorNombre => $categoriaNombres) {
            $sector = Sector::where('nombre', $sectorNombre)->first();
            if ($sector) {
                foreach ($categoriaNombres as $catNombre) {
                    if (isset($padres[$catNombre])) {
                        $padres[$catNombre]->update(['sector_id' => $sector->id]);
                    }
                }
            }
        }
    }
}
