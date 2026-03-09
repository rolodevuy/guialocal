<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ficha_visitas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_id')->constrained('fichas')->cascadeOnDelete();
            $table->date('fecha');
            $table->unsignedInteger('cantidad')->default(0);
            $table->timestamps();

            $table->unique(['ficha_id', 'fecha']);
            $table->index(['ficha_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ficha_visitas');
    }
};
