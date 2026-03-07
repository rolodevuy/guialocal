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
        Schema::table('zonas', function (Blueprint $table) {
            $table->decimal('lat_centro', 10, 7)->nullable()->after('slug');
            $table->decimal('lng_centro', 10, 7)->nullable()->after('lat_centro');
        });
    }

    public function down(): void
    {
        Schema::table('zonas', function (Blueprint $table) {
            $table->dropColumn(['lat_centro', 'lng_centro']);
        });
    }
};
