<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('negocios', function (Blueprint $table) {
            $table->unsignedSmallInteger('featured_score')->default(0)->after('featured');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->unsignedSmallInteger('popularidad_score')->default(0)->after('activo');
        });
    }

    public function down(): void
    {
        Schema::table('negocios', function (Blueprint $table) {
            $table->dropColumn('featured_score');
        });

        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn('popularidad_score');
        });
    }
};
