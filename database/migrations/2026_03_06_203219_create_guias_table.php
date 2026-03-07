<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guias', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('intro')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->nullOnDelete();
            $table->boolean('publicado')->default(false);
            $table->timestamp('publicado_en')->nullable();
            $table->timestamps();
        });

        Schema::create('guia_negocio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_id')->constrained('guias')->cascadeOnDelete();
            $table->foreignId('negocio_id')->constrained('negocios')->cascadeOnDelete();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->unique(['guia_id', 'negocio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guia_negocio');
        Schema::dropIfExists('guias');
    }
};
