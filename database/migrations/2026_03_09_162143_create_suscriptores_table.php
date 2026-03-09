<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suscriptores', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->foreignId('zona_id')->nullable()->constrained('zonas')->nullOnDelete();
            $table->uuid('token_baja')->unique();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->index(['activo', 'zona_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suscriptores');
    }
};
