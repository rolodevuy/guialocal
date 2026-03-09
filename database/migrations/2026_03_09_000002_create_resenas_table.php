<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resenas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_id')->constrained('fichas')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->string('email')->nullable();          // privado, nunca se muestra
            $table->unsignedTinyInteger('rating');        // 1–5
            $table->text('cuerpo');
            $table->boolean('aprobada')->default(false);  // requiere moderación
            $table->timestamps();

            $table->index(['ficha_id', 'aprobada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resenas');
    }
};
