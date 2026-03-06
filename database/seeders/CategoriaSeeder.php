<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    public function run(): void
    {
        Categoria::truncate();

        $categorias = [
            ['nombre' => 'Restaurantes',             'descripcion' => 'Lugares para comer, desde parrillas hasta cocina internacional.', 'icono' => 'utensils'],
            ['nombre' => 'Cafés y Bares',            'descripcion' => 'Cafeterías, bares y lugares para tomar algo rico.',              'icono' => 'coffee'],
            ['nombre' => 'Panaderías y Pastelerías', 'descripcion' => 'Pan artesanal, facturas, tortas y dulces del barrio.',           'icono' => 'cake'],
            ['nombre' => 'Farmacias',                'descripcion' => 'Farmacias y dietéticas con atención personalizada.',             'icono' => 'pill'],
            ['nombre' => 'Supermercados',            'descripcion' => 'Almacenes, verdulerías y mercados del barrio.',                  'icono' => 'shopping-cart'],
            ['nombre' => 'Salud y Bienestar',        'descripcion' => 'Gimnasios, centros de estética, yoga y spas.',                  'icono' => 'heart-pulse'],
            ['nombre' => 'Servicios Profesionales',  'descripcion' => 'Estudios contables, abogados, consultorías y más.',             'icono' => 'briefcase'],
            ['nombre' => 'Indumentaria y Calzado',   'descripcion' => 'Ropa, zapatos y accesorios de diseño local.',                   'icono' => 'shirt'],
        ];

        foreach ($categorias as $data) {
            Categoria::create($data);
        }
    }
}
