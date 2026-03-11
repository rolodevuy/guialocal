<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claim_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lugar_id')->constrained('lugares')->cascadeOnDelete();
            $table->string('nombre_completo');
            $table->string('email');
            $table->string('telefono', 50);
            $table->string('rut_numero', 12);
            $table->text('mensaje')->nullable();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('motivo_rechazo')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::table('fichas', function (Blueprint $table) {
            $table->timestamp('verified_at')->nullable()->after('activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claim_requests');

        Schema::table('fichas', function (Blueprint $table) {
            $table->dropColumn('verified_at');
        });
    }
};
