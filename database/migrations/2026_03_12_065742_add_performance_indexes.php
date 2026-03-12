<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── fichas ──────────────────────────────────────────────────────────
        Schema::table('fichas', function (Blueprint $table) {
            // scopeActivo() + orderByDesc('featured_score')
            $table->index(['activo', 'estado', 'featured_score'], 'fichas_activo_estado_score_idx');
            // user_id para panel de propietario
            $table->index('user_id', 'fichas_user_id_idx');
        });

        // ── lugares ─────────────────────────────────────────────────────────
        Schema::table('lugares', function (Blueprint $table) {
            // Filtros por categoría + activo (HomeController, NegociosIndex)
            $table->index(['categoria_id', 'activo'], 'lugares_categoria_activo_idx');
            // Filtros por zona + activo
            $table->index(['zona_id', 'activo'], 'lugares_zona_activo_idx');
        });

        // ── categorias ──────────────────────────────────────────────────────
        Schema::table('categorias', function (Blueprint $table) {
            // Jerarquía padre + activo
            $table->index(['parent_id', 'activo'], 'categorias_parent_activo_idx');
            // Filtro por sector + activo
            $table->index(['sector_id', 'activo'], 'categorias_sector_activo_idx');
            // Ordenamiento por popularidad
            $table->index('popularidad_score', 'categorias_popularidad_idx');
        });

        // ── articulos ───────────────────────────────────────────────────────
        Schema::table('articulos', function (Blueprint $table) {
            $table->index(['publicado', 'publicado_en'], 'articulos_publicado_fecha_idx');
        });

        // ── guias ───────────────────────────────────────────────────────────
        Schema::table('guias', function (Blueprint $table) {
            $table->index(['publicado', 'publicado_en'], 'guias_publicado_fecha_idx');
        });

        // ── promociones ─────────────────────────────────────────────────────
        Schema::table('promociones', function (Blueprint $table) {
            $table->index(['ficha_id', 'activo'], 'promociones_ficha_activo_idx');
        });
    }

    public function down(): void
    {
        Schema::table('fichas', function (Blueprint $table) {
            $table->dropIndex('fichas_activo_estado_score_idx');
            $table->dropIndex('fichas_user_id_idx');
        });

        Schema::table('lugares', function (Blueprint $table) {
            $table->dropIndex('lugares_categoria_activo_idx');
            $table->dropIndex('lugares_zona_activo_idx');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropIndex('categorias_parent_activo_idx');
            $table->dropIndex('categorias_sector_activo_idx');
            $table->dropIndex('categorias_popularidad_idx');
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->dropIndex('articulos_publicado_fecha_idx');
        });

        Schema::table('guias', function (Blueprint $table) {
            $table->dropIndex('guias_publicado_fecha_idx');
        });

        Schema::table('promociones', function (Blueprint $table) {
            $table->dropIndex('promociones_ficha_activo_idx');
        });
    }
};
