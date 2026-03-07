<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fichas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lugar_id')->constrained('lugares')->cascadeOnDelete();
            $table->text('descripcion')->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('sitio_web')->nullable();
            $table->json('horarios')->nullable();
            $table->json('horarios_especiales')->nullable();
            $table->json('redes_sociales')->nullable();
            $table->enum('plan', ['gratuito', 'basico', 'premium'])->default('gratuito');
            $table->boolean('featured')->default(false);
            $table->unsignedSmallInteger('featured_score')->default(0);
            $table->enum('estado', ['pendiente', 'activa', 'rechazada', 'suspendida'])->default('activa')
                ->comment('pendiente=esperando aprobación admin, activa=visible, rechazada=no cumple criterios, suspendida=temporalmente inactiva');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fichas');
    }
};
