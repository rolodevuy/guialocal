<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Ficha;
use App\Models\Lugar;
use App\Models\Zona;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LugarFichaSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Ficha::truncate();
        Lugar::truncate();
        Schema::enableForeignKeyConstraints();

        $categorias = Categoria::pluck('id', 'slug');
        $zonas      = Zona::pluck('id', 'slug');

        // Límites geográficos por zona
        $bounds = [
            'atlantida'        => [-34.780, -34.755, -55.795, -55.745],
            'las-toscas'       => [-34.745, -34.720, -55.685, -55.640],
            'parque-del-plata' => [-34.762, -34.738, -55.840, -55.800],
            'pocitos'          => [-34.920, -34.895, -56.180, -56.145],
            'carrasco'         => [-34.880, -34.857, -56.060, -56.020],
            'lagomar'          => [-34.822, -34.800, -55.960, -55.925],
            'minas'            => [-34.392, -34.365, -55.252, -55.220],
        ];

        $coord = function (string $zona) use ($bounds): array {
            [$latS, $latN, $lngW, $lngE] = $bounds[$zona];
            $r = mt_rand() / mt_getrandmax();

            return [
                'lat' => round($latS + $r * ($latN - $latS), 6),
                'lng' => round($lngW + $r * ($lngE - $lngW), 6),
            ];
        };

        $datos = [

            // ── Restaurantes ────────────────────────────────────────────────────
            [
                'nombre'      => 'La Parrilla de Don Alberto',
                'descripcion' => 'La mejor parrilla del balneario con cortes premium y atención familiar desde 1985.',
                'direccion'   => 'Av. Artigas 1234',
                'telefono'    => '099 123 456',
                'email'       => 'donalberto@parrilla.com.uy',
                'sitio_web'   => 'https://parrillaalbeerto.com.uy',
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Viernes', 'apertura' => '12:00', 'cierre' => '16:00', 'cerrado' => false],
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Viernes', 'apertura' => '20:00', 'cierre' => '00:00', 'cerrado' => false],
                    ['dia_inicio' => 'Sábado', 'dia_fin' => 'Domingo', 'apertura' => '12:00', 'cierre' => '01:00', 'cerrado' => false],
                ],
                'featured'  => true,
                'activo'    => true,
                'plan'      => 'premium',
                'categoria' => 'restaurantes',
                'zona'      => 'atlantida',
            ],
            [
                'nombre'      => 'Sushi Nakamura',
                'descripcion' => 'Rolls artesanales y cocina japonesa fusión con ingredientes frescos del día.',
                'direccion'   => 'Bulevar España 890',
                'telefono'    => '098 456 789',
                'email'       => 'hola@sushinakamura.com.uy',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Martes', 'dia_fin' => 'Domingo', 'apertura' => '19:00', 'cierre' => '23:30', 'cerrado' => false],
                    ['dia_inicio' => 'Lunes',  'dia_fin' => null,      'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => true,
                'activo'    => true,
                'plan'      => 'basico',
                'categoria' => 'restaurantes',
                'zona'      => 'pocitos',
            ],
            [
                'nombre'      => 'Pizzería El Cuartito',
                'descripcion' => 'Pizza a la piedra estilo uruguayo, empanadas y pastas caseras.',
                'direccion'   => 'Av. Bolivia 456',
                'telefono'    => '095 321 654',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes', 'dia_fin' => 'Domingo', 'apertura' => '11:00', 'cierre' => '00:00', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'restaurantes',
                'zona'      => 'carrasco',
            ],
            [
                'nombre'      => 'Bodegón Los Amigos',
                'descripcion' => 'Comida casera, menú del día y las mejores milanesas del departamento.',
                'direccion'   => 'Calle 18 de Julio 345',
                'telefono'    => '094 789 123',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Viernes', 'apertura' => '12:00', 'cierre' => '15:30', 'cerrado' => false],
                    ['dia_inicio' => 'Sábado', 'dia_fin' => 'Domingo', 'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'restaurantes',
                'zona'      => 'minas',
            ],

            // ── Cafés y Bares ────────────────────────────────────────────────────
            [
                'nombre'      => 'Café del Faro',
                'descripcion' => 'Café de especialidad con tortas artesanales y vista al mar desde 1990.',
                'direccion'   => 'Rambla de los Argentinos 829',
                'telefono'    => '099 741 852',
                'email'       => 'info@cafedelfaro.com.uy',
                'sitio_web'   => 'https://cafedelfaro.com.uy',
                'horarios'    => [
                    ['dia_inicio' => 'Lunes', 'dia_fin' => 'Domingo', 'apertura' => '08:00', 'cierre' => '23:00', 'cerrado' => false],
                ],
                'featured'  => true,
                'activo'    => true,
                'plan'      => 'premium',
                'categoria' => 'cafes-y-bares',
                'zona'      => 'atlantida',
            ],
            [
                'nombre'      => 'Bar La Tosca',
                'descripcion' => 'Bar de playa con cerveza artesanal, picadas y música en vivo los fines de semana.',
                'direccion'   => 'Av. Principal 599',
                'telefono'    => '096 963 741',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Martes', 'dia_fin' => 'Domingo', 'apertura' => '10:00', 'cierre' => '01:00', 'cerrado' => false],
                    ['dia_inicio' => 'Lunes',  'dia_fin' => null,      'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'cafes-y-bares',
                'zona'      => 'las-toscas',
            ],
            [
                'nombre'      => 'Lattente Espresso',
                'descripcion' => 'Specialty coffee, brunch y postres artesanales en el corazón de Pocitos.',
                'direccion'   => 'Av. Brasil 5700',
                'telefono'    => '098 852 963',
                'email'       => 'hola@lattente.com.uy',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Viernes', 'apertura' => '08:00', 'cierre' => '20:00', 'cerrado' => false],
                    ['dia_inicio' => 'Sábado', 'dia_fin' => 'Domingo', 'apertura' => '09:00', 'cierre' => '21:00', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'basico',
                'categoria' => 'cafes-y-bares',
                'zona'      => 'pocitos',
            ],

            // ── Panaderías y Pastelerías ─────────────────────────────────────────
            [
                'nombre'      => 'Panadería La Espiga de Oro',
                'descripcion' => 'Pan de masa madre, medialunas y facturas recién horneadas cada mañana.',
                'direccion'   => 'Calle Juana de Ibarbourou 456',
                'telefono'    => '095 147 258',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Sábado', 'apertura' => '07:00', 'cierre' => '20:00', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo','dia_fin' => null,      'apertura' => '08:00', 'cierre' => '13:00', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'panaderias-y-pastelerias',
                'zona'      => 'atlantida',
            ],
            [
                'nombre'      => 'Pastelería Dulce Momento',
                'descripcion' => 'Tortas personalizadas, alfajores y macarons artesanales para cada ocasión.',
                'direccion'   => 'Av. Rivera 1890',
                'telefono'    => '094 369 258',
                'email'       => 'dulcemomento@gmail.com',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Martes', 'dia_fin' => 'Domingo', 'apertura' => '10:00', 'cierre' => '20:00', 'cerrado' => false],
                    ['dia_inicio' => 'Lunes',  'dia_fin' => null,      'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'basico',
                'categoria' => 'panaderias-y-pastelerias',
                'zona'      => 'carrasco',
            ],

            // ── Farmacias ────────────────────────────────────────────────────────
            [
                'nombre'      => 'Farmacia San Jorge',
                'descripcion' => 'Farmacia de turno permanente con atención personalizada y delivery a domicilio.',
                'direccion'   => 'Av. General Rivera 432',
                'telefono'    => '099 258 147',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes', 'dia_fin' => 'Domingo', 'apertura' => '00:00', 'cierre' => '23:59', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'farmacias',
                'zona'      => 'atlantida',
            ],
            [
                'nombre'      => 'Dietética El Granero Natural',
                'descripcion' => 'Productos naturales, suplementos y alimentos orgánicos a granel.',
                'direccion'   => 'Av. Batlle y Ordóñez 789',
                'telefono'    => '096 741 963',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Sábado', 'apertura' => '09:00', 'cierre' => '19:30', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo','dia_fin' => null,      'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'farmacias',
                'zona'      => 'minas',
            ],

            // ── Supermercados ────────────────────────────────────────────────────
            [
                'nombre'      => 'Verdulería Don Ramón',
                'descripcion' => 'Frutas y verduras frescas del día, productos de estación y delivery.',
                'direccion'   => 'Calle Los Aromos 234',
                'telefono'    => '095 963 852',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Sábado', 'apertura' => '08:00', 'cierre' => '20:00', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo','dia_fin' => null,      'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'supermercados',
                'zona'      => 'lagomar',
            ],
            [
                'nombre'      => 'Almacén El Rincón',
                'descripcion' => 'Almacén de barrio con fiambres, quesos y productos de campo.',
                'direccion'   => 'Calle Los Pinos 345',
                'telefono'    => '098 147 369',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Sábado', 'apertura' => '08:30', 'cierre' => '21:00', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo','dia_fin' => null,      'apertura' => '09:00', 'cierre' => '14:00', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'supermercados',
                'zona'      => 'las-toscas',
            ],

            // ── Salud y Bienestar ────────────────────────────────────────────────
            [
                'nombre'      => 'Gym Evolución',
                'descripcion' => 'Gimnasio con pesas, cardio, clases grupales y personal trainers certificados.',
                'direccion'   => 'Rambla República de México 3344',
                'telefono'    => '099 852 741',
                'email'       => 'info@gymevolucion.com.uy',
                'sitio_web'   => 'https://gymevolucion.com.uy',
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',   'dia_fin' => 'Viernes', 'apertura' => '06:00', 'cierre' => '23:00', 'cerrado' => false],
                    ['dia_inicio' => 'Sábado',  'dia_fin' => null,      'apertura' => '08:00', 'cierre' => '20:00', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo', 'dia_fin' => null,      'apertura' => '09:00', 'cierre' => '18:00', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'basico',
                'categoria' => 'salud-y-bienestar',
                'zona'      => 'pocitos',
            ],
            [
                'nombre'      => 'Centro de Yoga Prana',
                'descripcion' => 'Clases de yoga, meditación y mindfulness para todos los niveles.',
                'direccion'   => 'Calle del Mar 2100',
                'telefono'    => '094 258 963',
                'email'       => 'yoga@prana.com.uy',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',   'dia_fin' => 'Sábado', 'apertura' => '07:00', 'cierre' => '21:00', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo', 'dia_fin' => null,     'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'salud-y-bienestar',
                'zona'      => 'atlantida',
            ],

            // ── Servicios Profesionales ──────────────────────────────────────────
            [
                'nombre'      => 'Estudio Contable Pérez & Asociados',
                'descripcion' => 'Contabilidad, BPS, DGI, monotributo y asesoramiento para PyMEs.',
                'direccion'   => 'Av. Artigas 1500, Of. 4',
                'telefono'    => '099 369 741',
                'email'       => 'estudio@perezasociados.com.uy',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Viernes', 'apertura' => '09:00', 'cierre' => '18:00', 'cerrado' => false],
                    ['dia_inicio' => 'Sábado', 'dia_fin' => 'Domingo', 'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'basico',
                'categoria' => 'servicios-profesionales',
                'zona'      => 'atlantida',
            ],
            [
                'nombre'      => 'Estudio Jurídico Morales',
                'descripcion' => 'Derecho laboral, civil y comercial. Primera consulta sin cargo.',
                'direccion'   => 'Calle Ellauri 890, 2do piso',
                'telefono'    => '098 741 258',
                'email'       => 'consultas@moralesabogados.com.uy',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Viernes', 'apertura' => '10:00', 'cierre' => '19:00', 'cerrado' => false],
                    ['dia_inicio' => 'Sábado', 'dia_fin' => 'Domingo', 'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'servicios-profesionales',
                'zona'      => 'pocitos',
            ],

            // ── Indumentaria y Calzado ───────────────────────────────────────────
            [
                'nombre'      => 'Diseños Valentina',
                'descripcion' => 'Ropa de diseño independiente uruguayo, ediciones limitadas y a medida.',
                'direccion'   => 'Av. Rivera km 14, local 3',
                'telefono'    => '096 258 741',
                'email'       => 'valentina@disenos.com.uy',
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Martes', 'dia_fin' => 'Sábado', 'apertura' => '11:00', 'cierre' => '20:00', 'cerrado' => false],
                    ['dia_inicio' => 'Lunes',  'dia_fin' => null,     'apertura' => null,    'cierre' => null,    'cerrado' => true],
                    ['dia_inicio' => 'Domingo','dia_fin' => null,     'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => true,
                'activo'    => true,
                'plan'      => 'basico',
                'categoria' => 'indumentaria-y-calzado',
                'zona'      => 'carrasco',
            ],
            [
                'nombre'      => 'Zapatería El Buen Paso',
                'descripcion' => 'Calzado artesanal de cuero, reparación y personalización.',
                'direccion'   => 'Calle Echevarriarza 1122',
                'telefono'    => '095 369 852',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',  'dia_fin' => 'Sábado', 'apertura' => '10:00', 'cierre' => '19:30', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo','dia_fin' => null,     'apertura' => null,    'cierre' => null,    'cerrado' => true],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'indumentaria-y-calzado',
                'zona'      => 'atlantida',
            ],
            [
                'nombre'      => 'Outlet Urbano',
                'descripcion' => 'Marcas nacionales con descuentos permanentes en ropa y accesorios.',
                'direccion'   => 'Av. Brasil 2890',
                'telefono'    => '094 963 147',
                'email'       => null,
                'sitio_web'   => null,
                'horarios'    => [
                    ['dia_inicio' => 'Lunes',   'dia_fin' => 'Sábado', 'apertura' => '10:00', 'cierre' => '21:00', 'cerrado' => false],
                    ['dia_inicio' => 'Domingo', 'dia_fin' => null,     'apertura' => '14:00', 'cierre' => '20:00', 'cerrado' => false],
                ],
                'featured'  => false,
                'activo'    => true,
                'plan'      => 'gratuito',
                'categoria' => 'indumentaria-y-calzado',
                'zona'      => 'pocitos',
            ],
        ];

        foreach ($datos as $data) {
            $zonaSlug      = $data['zona'];
            $categoriaSlug = $data['categoria'];
            $coordenadas   = $coord($zonaSlug);

            $lugar = Lugar::create([
                'nombre'       => $data['nombre'],
                'direccion'    => $data['direccion'],
                'lat'          => $coordenadas['lat'],
                'lng'          => $coordenadas['lng'],
                'categoria_id' => $categorias[$categoriaSlug],
                'zona_id'      => $zonas[$zonaSlug],
                'activo'       => $data['activo'],
            ]);

            Ficha::create([
                'lugar_id'    => $lugar->id,
                'descripcion' => $data['descripcion'],
                'telefono'    => $data['telefono'],
                'email'       => $data['email'],
                'sitio_web'   => $data['sitio_web'],
                'horarios'    => $data['horarios'],   // cast → array, no json_encode manual
                'plan'        => $data['plan'],
                'featured'    => $data['featured'],
                'estado'      => 'activa',
                'activo'      => $data['activo'],
            ]);
        }
    }
}
