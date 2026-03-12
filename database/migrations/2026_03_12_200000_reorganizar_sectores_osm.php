<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Actualizar sectores existentes ────────────────────────────────

        // ID 1: Comercial → Comercios y Tiendas (shop, craft)
        DB::table('sectores')->where('id', 1)->update([
            'nombre'       => 'Comercios y Tiendas',
            'nombre_corto' => 'Comercios',
            'icono'        => 'shopping-bag',
            'orden'        => 1,
            // color: amber (se mantiene)
        ]);

        // ID 2: Gastronomía y Ocio → Gastronomía (amenity: restaurant, cafe, bar, etc.)
        DB::table('sectores')->where('id', 2)->update([
            'nombre'       => 'Gastronomía',
            'nombre_corto' => 'Gastronomía',
            'icono'        => 'utensils',
            'orden'        => 2,
            // color: rose (se mantiene)
        ]);

        // ID 3: Turismo y Alojamiento → Salud y Servicios (amenity: hospital, pharmacy + office)
        DB::table('sectores')->where('id', 3)->update([
            'nombre'       => 'Salud y Servicios',
            'nombre_corto' => 'Salud',
            'icono'        => 'heart-pulse',
            'color_classes' => json_encode([
                'bg'         => 'bg-emerald-100',
                'bg_light'   => 'bg-emerald-50',
                'text'       => 'text-emerald-600',
                'text_hover' => 'text-emerald-700',
                'border'     => 'border-emerald-200',
                'icon'       => 'text-emerald-500',
            ]),
            'orden'        => 3,
        ]);

        // ── 2. Crear sectores nuevos ─────────────────────────────────────────

        DB::table('sectores')->insert([
            'id'           => 4,
            'nombre'       => 'Turismo y Ocio',
            'nombre_corto' => 'Turismo',
            'slug'         => 'turismo-y-ocio',
            'icono'        => 'map-pin',
            'color_classes' => json_encode([
                'bg'         => 'bg-sky-100',
                'bg_light'   => 'bg-sky-50',
                'text'       => 'text-sky-600',
                'text_hover' => 'text-sky-700',
                'border'     => 'border-sky-200',
                'icon'       => 'text-sky-500',
            ]),
            'orden'        => 4,
            'activo'       => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        DB::table('sectores')->insert([
            'id'           => 5,
            'nombre'       => 'Educación',
            'nombre_corto' => 'Educación',
            'slug'         => 'educacion',
            'icono'        => 'graduation-cap',
            'color_classes' => json_encode([
                'bg'         => 'bg-violet-100',
                'bg_light'   => 'bg-violet-50',
                'text'       => 'text-violet-600',
                'text_hover' => 'text-violet-700',
                'border'     => 'border-violet-200',
                'icon'       => 'text-violet-500',
            ]),
            'orden'        => 5,
            'activo'       => true,
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);

        // ── 3. Reasignar categorías a nuevos sectores ────────────────────────

        // Sector 1 (Comercios): Automotor(11), Hogar y Construcción(10), Indumentaria(9), Supermercados(6)
        DB::table('categorias')->whereIn('id', [11, 10, 9, 6])->update(['sector_id' => 1]);

        // Sector 2 (Gastronomía): Restaurantes(1), Cafés y Bares(2), Heladerías(4), Panaderías(3)
        DB::table('categorias')->whereIn('id', [1, 2, 4, 3])->update(['sector_id' => 2]);

        // Sector 3 (Salud y Servicios): Farmacias(5), Salud y Bienestar(7), Servicios Profesionales(8)
        DB::table('categorias')->whereIn('id', [5, 7, 8])->update(['sector_id' => 3]);

        // Sector 4 (Turismo y Ocio): Turismo y Alojamiento(13), Entretenimiento(12)
        DB::table('categorias')->whereIn('id', [13, 12])->update(['sector_id' => 4]);

        // Sector 5 (Educación): Educación(14)
        DB::table('categorias')->where('id', 14)->update(['sector_id' => 5]);

        // ── 4. Actualizar slug del sector 3 (era turismo-y-alojamiento) ──────
        DB::table('sectores')->where('id', 3)->update(['slug' => 'salud-y-servicios']);
    }

    public function down(): void
    {
        // Restaurar sectores originales
        DB::table('sectores')->where('id', 1)->update([
            'nombre' => 'Comercial', 'nombre_corto' => 'Comercial',
        ]);
        DB::table('sectores')->where('id', 2)->update([
            'nombre' => 'Gastronomía y Ocio', 'nombre_corto' => 'Gastronomía',
        ]);
        DB::table('sectores')->where('id', 3)->update([
            'nombre'       => 'Turismo y Alojamiento',
            'nombre_corto' => 'Turismo',
            'slug'         => 'turismo-y-alojamiento',
            'icono'        => 'map-pin',
            'color_classes' => json_encode([
                'bg' => 'bg-sky-100', 'bg_light' => 'bg-sky-50',
                'text' => 'text-sky-600', 'text_hover' => 'text-sky-700',
                'border' => 'border-sky-200', 'icon' => 'text-sky-500',
            ]),
        ]);

        // Restaurar asignaciones originales
        DB::table('categorias')->whereIn('id', [11, 14, 5, 10, 9, 7, 8, 6])->update(['sector_id' => 1]);
        DB::table('categorias')->whereIn('id', [2, 12, 4, 3, 1])->update(['sector_id' => 2]);
        DB::table('categorias')->where('id', 13)->update(['sector_id' => 3]);

        // Eliminar sectores nuevos
        DB::table('sectores')->whereIn('id', [4, 5])->delete();
    }
};
