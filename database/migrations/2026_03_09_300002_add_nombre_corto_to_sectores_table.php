<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sectores', function (Blueprint $table) {
            $table->string('nombre_corto')->nullable()->after('nombre');
        });

        DB::table('sectores')->where('slug', 'comercial')->update(['nombre_corto' => 'Comercial']);
        DB::table('sectores')->where('slug', 'gastronomia-y-ocio')->update(['nombre_corto' => 'Gastronomía']);
        DB::table('sectores')->where('slug', 'turismo-y-alojamiento')->update(['nombre_corto' => 'Turismo']);
    }

    public function down(): void
    {
        Schema::table('sectores', function (Blueprint $table) {
            $table->dropColumn('nombre_corto');
        });
    }
};
