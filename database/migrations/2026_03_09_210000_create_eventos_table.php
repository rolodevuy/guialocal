<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->foreignId('lugar_id')->nullable()->constrained('lugares')->nullOnDelete();
            $table->boolean('publicado')->default(false);
            $table->timestamps();

            $table->index(['publicado', 'fecha_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
