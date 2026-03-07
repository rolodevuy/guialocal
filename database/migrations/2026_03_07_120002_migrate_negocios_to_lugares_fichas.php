<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // ── 1. Migrar negocios → lugares + fichas ─────────────────────────────
        $mapa = [];

        foreach (DB::table('negocios')->get() as $negocio) {
            // Idempotente: no reinsertar si ya existe el slug
            $existing = DB::table('lugares')->where('slug', $negocio->slug)->first();
            if ($existing) {
                $lugarId = $existing->id;
            } else {
                $lugarId = DB::table('lugares')->insertGetId([
                    'nombre'       => $negocio->nombre,
                    'slug'         => $negocio->slug,
                    'direccion'    => $negocio->direccion,
                    'lat'          => $negocio->lat,
                    'lng'          => $negocio->lng,
                    'categoria_id' => $negocio->categoria_id,
                    'zona_id'      => $negocio->zona_id,
                    'activo'       => $negocio->activo,
                    'created_at'   => $negocio->created_at,
                    'updated_at'   => $negocio->updated_at,
                ]);
            }

            $existingFicha = DB::table('fichas')->where('lugar_id', $lugarId)->first();
            if ($existingFicha) {
                $fichaId = $existingFicha->id;
            } else {
                $fichaId = DB::table('fichas')->insertGetId([
                    'lugar_id'            => $lugarId,
                    'descripcion'         => $negocio->descripcion,
                    'telefono'            => $negocio->telefono,
                    'email'               => $negocio->email,
                    'sitio_web'           => $negocio->sitio_web,
                    'horarios'            => $negocio->horarios,
                    'horarios_especiales' => $negocio->horarios_especiales,
                    'redes_sociales'      => $negocio->redes_sociales,
                    'plan'                => $negocio->plan,
                    'featured'            => $negocio->featured,
                    'featured_score'      => $negocio->featured_score,
                    'estado'              => 'activa',
                    'activo'              => $negocio->activo,
                    'created_at'          => $negocio->created_at,
                    'updated_at'          => $negocio->updated_at,
                ]);
            }

            $mapa[$negocio->id] = ['lugar_id' => $lugarId, 'ficha_id' => $fichaId];
        }

        // ── 2. slug_redirects: negocio_id → lugar_id ──────────────────────────
        if (!Schema::hasColumn('slug_redirects', 'lugar_id')) {
            Schema::table('slug_redirects', function (Blueprint $table) {
                $table->unsignedBigInteger('lugar_id')->nullable()->after('old_slug');
            });
        }

        foreach (DB::table('slug_redirects')->get() as $row) {
            if ($row->negocio_id && isset($mapa[$row->negocio_id])) {
                DB::table('slug_redirects')
                    ->where('id', $row->id)
                    ->update(['lugar_id' => $mapa[$row->negocio_id]['lugar_id']]);
            }
        }

        if (Schema::hasColumn('slug_redirects', 'negocio_id')) {
            Schema::table('slug_redirects', function (Blueprint $table) {
                $table->dropForeign(['negocio_id']);
                $table->dropColumn('negocio_id');
            });
        }

        Schema::table('slug_redirects', function (Blueprint $table) {
            $table->foreign('lugar_id')->references('id')->on('lugares')->nullOnDelete();
        });

        // ── 3. promociones: negocio_id → ficha_id ─────────────────────────────
        if (!Schema::hasColumn('promociones', 'ficha_id')) {
            Schema::table('promociones', function (Blueprint $table) {
                $table->unsignedBigInteger('ficha_id')->nullable()->after('id');
            });
        }

        foreach (DB::table('promociones')->get() as $row) {
            if ($row->negocio_id && isset($mapa[$row->negocio_id])) {
                DB::table('promociones')
                    ->where('id', $row->id)
                    ->update(['ficha_id' => $mapa[$row->negocio_id]['ficha_id']]);
            }
        }

        if (Schema::hasColumn('promociones', 'negocio_id')) {
            Schema::table('promociones', function (Blueprint $table) {
                $table->dropForeign(['negocio_id']);
                $table->dropColumn('negocio_id');
            });
        }

        Schema::table('promociones', function (Blueprint $table) {
            $table->foreign('ficha_id')->references('id')->on('fichas')->cascadeOnDelete();
        });

        // ── 4. articulos: negocio_id → lugar_id ───────────────────────────────
        if (!Schema::hasColumn('articulos', 'lugar_id')) {
            Schema::table('articulos', function (Blueprint $table) {
                $table->unsignedBigInteger('lugar_id')->nullable()->after('categoria_id');
            });
        }

        foreach (DB::table('articulos')->get() as $row) {
            if ($row->negocio_id && isset($mapa[$row->negocio_id])) {
                DB::table('articulos')
                    ->where('id', $row->id)
                    ->update(['lugar_id' => $mapa[$row->negocio_id]['lugar_id']]);
            }
        }

        if (Schema::hasColumn('articulos', 'negocio_id')) {
            Schema::table('articulos', function (Blueprint $table) {
                $table->dropForeign(['negocio_id']);
                $table->dropColumn('negocio_id');
            });
        }

        Schema::table('articulos', function (Blueprint $table) {
            $table->foreign('lugar_id')->references('id')->on('lugares')->nullOnDelete();
        });

        // ── 5. featured_slots: App\Models\Negocio → App\Models\Ficha ──────────
        foreach (DB::table('featured_slots')->where('slotable_type', 'App\\Models\\Negocio')->get() as $slot) {
            if (isset($mapa[$slot->slotable_id])) {
                DB::table('featured_slots')
                    ->where('id', $slot->id)
                    ->update([
                        'slotable_type' => 'App\\Models\\Ficha',
                        'slotable_id'   => $mapa[$slot->slotable_id]['ficha_id'],
                    ]);
            }
        }

        // ── 6. Drop tabla negocios ────────────────────────────────────────────
        Schema::dropIfExists('negocios');

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Recrear tabla negocios (sin datos — usar seeders para restaurar)
        if (!Schema::hasTable('negocios')) {
            Schema::create('negocios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('slug')->unique();
                $table->text('descripcion')->nullable();
                $table->string('direccion')->nullable();
                $table->string('telefono', 50)->nullable();
                $table->string('email')->nullable();
                $table->string('sitio_web')->nullable();
                $table->decimal('lat', 10, 7)->nullable();
                $table->decimal('lng', 10, 7)->nullable();
                $table->json('horarios')->nullable();
                $table->json('horarios_especiales')->nullable();
                $table->json('redes_sociales')->nullable();
                $table->boolean('featured')->default(false);
                $table->unsignedSmallInteger('featured_score')->default(0);
                $table->boolean('activo')->default(true);
                $table->enum('plan', ['gratuito', 'basico', 'premium'])->default('gratuito');
                $table->foreignId('categoria_id')->constrained('categorias');
                $table->foreignId('zona_id')->constrained('zonas');
                $table->timestamps();
            });
        }

        // Revertir slug_redirects
        if (!Schema::hasColumn('slug_redirects', 'negocio_id')) {
            Schema::table('slug_redirects', function (Blueprint $table) {
                $table->dropForeign(['lugar_id']);
                $table->dropColumn('lugar_id');
                $table->foreignId('negocio_id')->nullable()->constrained('negocios')->nullOnDelete();
            });
        }

        // Revertir promociones
        if (!Schema::hasColumn('promociones', 'negocio_id')) {
            Schema::table('promociones', function (Blueprint $table) {
                $table->dropForeign(['ficha_id']);
                $table->dropColumn('ficha_id');
                $table->foreignId('negocio_id')->constrained('negocios')->cascadeOnDelete();
            });
        }

        // Revertir articulos
        if (!Schema::hasColumn('articulos', 'negocio_id')) {
            Schema::table('articulos', function (Blueprint $table) {
                $table->dropForeign(['lugar_id']);
                $table->dropColumn('lugar_id');
                $table->foreignId('negocio_id')->nullable()->constrained('negocios')->nullOnDelete();
            });
        }

        // Revertir featured_slots
        DB::table('featured_slots')
            ->where('slotable_type', 'App\\Models\\Ficha')
            ->update(['slotable_type' => 'App\\Models\\Negocio']);

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};
