@extends('layouts.panel')
@section('title', 'Editar datos — ' . $ficha->lugar->nombre)

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('panel.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Editar datos del negocio</h1>
    </div>

    {{-- Flash guardado --}}
    @if(session('guardado'))
    <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
        <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        ¡Cambios guardados correctamente!
    </div>
    @endif

    <form method="POST" action="{{ route('panel.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Descripción --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Descripción del negocio</h2>
            <textarea
                name="descripcion"
                rows="5"
                placeholder="Contá qué hace tu negocio, qué lo hace especial, qué ofrecés..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none
                       {{ $errors->has('descripcion') ? 'border-red-300' : '' }}"
            >{{ old('descripcion', $ficha->descripcion) }}</textarea>
            <p class="text-xs text-gray-400 mt-1.5">Máx. 2000 caracteres. Aparece en tu ficha pública.</p>
            @error('descripcion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Contacto --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Datos de contacto</h2>
            <div class="grid sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono"
                           value="{{ old('telefono', $ficha->telefono) }}"
                           placeholder="+598 99 000 000"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    @error('telefono') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email de contacto</label>
                    <input type="email" name="email"
                           value="{{ old('email', $ficha->email) }}"
                           placeholder="hola@minegocio.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Sitio web</label>
                    <input type="text" name="sitio_web"
                           value="{{ old('sitio_web', $ficha->sitio_web) }}"
                           placeholder="www.minegocio.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('sitio_web') ? 'border-red-300' : '' }}">
                    @error('sitio_web') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Redes sociales --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">Redes sociales</h2>
            <p class="text-xs text-gray-400 mb-4">Pegá la URL completa de tu perfil.</p>

            @php
                $redesActuales = collect($ficha->redes_sociales ?? [])->keyBy('red');
            @endphp

            <div class="space-y-3">

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                          style="background:#E1306C">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </span>
                    <input type="url" name="instagram"
                           value="{{ old('instagram', $redesActuales['instagram']['url'] ?? '') }}"
                           placeholder="https://instagram.com/tunegocio"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('instagram') ? 'border-red-300' : '' }}">
                    @error('instagram') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                          style="background:#1877F2">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </span>
                    <input type="url" name="facebook"
                           value="{{ old('facebook', $redesActuales['facebook']['url'] ?? '') }}"
                           placeholder="https://facebook.com/tunegocio"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('facebook') ? 'border-red-300' : '' }}">
                    @error('facebook') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                          style="background:#25D366">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </span>
                    <input type="url" name="whatsapp"
                           value="{{ old('whatsapp', $redesActuales['whatsapp']['url'] ?? '') }}"
                           placeholder="https://wa.me/59899000000"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('whatsapp') ? 'border-red-300' : '' }}">
                    @error('whatsapp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Horario semanal + Días especiales --}}
        @php
            $diasNombres = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

            // Convertir formato de rangos (Filament) a día‑por‑día para el editor
            $horariosFlat = [];
            foreach ($ficha->horarios ?? [] as $franja) {
                $inicio = array_search($franja['dia_inicio'] ?? '', $diasNombres);
                $fin    = !empty($franja['dia_fin'])
                            ? array_search($franja['dia_fin'], $diasNombres)
                            : $inicio;
                if ($inicio === false) continue;
                if ($fin === false) $fin = $inicio;
                for ($i = $inicio; $i <= $fin; $i++) {
                    $horariosFlat[$diasNombres[$i]] = [
                        'cerrado'  => (bool) ($franja['cerrado'] ?? false),
                        'apertura' => $franja['apertura'] ?? '09:00',
                        'cierre'   => $franja['cierre'] ?? '18:00',
                    ];
                }
            }

            $horariosDias = [];
            foreach ($diasNombres as $dia) {
                $horariosDias[] = [
                    'dia'      => $dia,
                    'cerrado'  => $horariosFlat[$dia]['cerrado'] ?? false,
                    'apertura' => $horariosFlat[$dia]['apertura'] ?? '09:00',
                    'cierre'   => $horariosFlat[$dia]['cierre'] ?? '18:00',
                ];
            }

            $horariosEspeciales = array_values($ficha->horarios_especiales ?? []);
        @endphp

        <div x-data="{
            dias:       {{ \Illuminate\Support\Js::from($horariosDias) }},
            especiales: {{ \Illuminate\Support\Js::from($horariosEspeciales) }},
            agregando:  false,
            nuevoE: { nombre: '', fecha: '', mes: '1', dia: '1', se_repite: false, activo: true, cerrado: true, apertura: '09:00', cierre: '18:00' },
            get horariosJson() {
                return JSON.stringify(this.dias.map(d => ({
                    dia_inicio: d.dia,
                    dia_fin:    null,
                    apertura:   d.cerrado ? null : d.apertura,
                    cierre:     d.cerrado ? null : d.cierre,
                    cerrado:    d.cerrado
                })));
            },
            get especialesJson() { return JSON.stringify(this.especiales); },
            agregar() {
                const fecha = this.nuevoE.se_repite
                    ? '2000-' + String(this.nuevoE.mes).padStart(2,'0') + '-' + String(this.nuevoE.dia).padStart(2,'0')
                    : this.nuevoE.fecha;
                if (!this.nuevoE.nombre || !fecha) return;
                this.especiales.push({ ...this.nuevoE, fecha });
                this.nuevoE = { nombre: '', fecha: '', mes: '1', dia: '1', se_repite: false, activo: true, cerrado: true, apertura: '09:00', cierre: '18:00' };
                this.agregando = false;
            },
            eliminar(i) { this.especiales.splice(i, 1); }
        }" class="space-y-6">

            <input type="hidden" name="horarios"            :value="horariosJson">
            <input type="hidden" name="horarios_especiales" :value="especialesJson">

            {{-- Horario semanal --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-4">Horario semanal</h2>
                <div class="divide-y divide-gray-50">
                    <template x-for="(dia, i) in dias" :key="dia.dia">
                        <div class="flex items-center gap-3 py-2.5">
                            <span class="w-24 text-sm font-medium text-gray-700 shrink-0" x-text="dia.dia"></span>
                            <button type="button"
                                    @click="dias[i].cerrado = !dias[i].cerrado"
                                    class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors shrink-0 w-20 text-center"
                                    :class="dias[i].cerrado
                                        ? 'bg-gray-100 text-gray-500 border-gray-200'
                                        : 'bg-green-50 text-green-700 border-green-200'">
                                <span x-text="dias[i].cerrado ? 'Cerrado' : 'Abierto'"></span>
                            </button>
                            <div x-show="!dias[i].cerrado" class="flex items-center gap-2 flex-1">
                                <input type="time" x-model="dias[i].apertura"
                                       class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 flex-1 min-w-0">
                                <span class="text-gray-400 text-xs shrink-0">a</span>
                                <input type="time" x-model="dias[i].cierre"
                                       class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 flex-1 min-w-0">
                            </div>
                            <span x-show="dias[i].cerrado" class="text-xs text-gray-400">No abre este día</span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Días especiales --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-1">Días especiales</h2>
                <p class="text-xs text-gray-400 mb-4">Feriados, vacaciones u horarios fuera de lo habitual.</p>

                {{-- Form agregar --}}
                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 mb-4 space-y-3">

                    {{-- Fila 1: Nombre + Checks --}}
                    <div class="flex items-start gap-4">
                        <div class="flex-1 min-w-0">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nombre / motivo</label>
                            <input type="text" x-model="nuevoE.nombre" placeholder="Ej: 1ro de Mayo"
                                   class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        </div>
                        <div class="flex flex-col gap-2 pt-5 shrink-0">
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer whitespace-nowrap">
                                <input type="checkbox" x-model="nuevoE.se_repite" class="rounded text-amber-500 focus:ring-amber-400">
                                Se repite cada año
                            </label>
                            <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" x-model="nuevoE.activo" class="rounded text-amber-500 focus:ring-amber-400">
                                Activo
                            </label>
                        </div>
                    </div>

                    {{-- Fila 2: Fecha --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Fecha</label>
                        {{-- Fecha con año (no se repite) --}}
                        <input x-show="!nuevoE.se_repite"
                               type="date" x-model="nuevoE.fecha"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        {{-- Mes + Día sin año (se repite) --}}
                        <div x-show="nuevoE.se_repite" class="flex items-center gap-2">
                            <select x-model="nuevoE.mes"
                                    class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                                <option value="1">Enero</option>
                                <option value="2">Febrero</option>
                                <option value="3">Marzo</option>
                                <option value="4">Abril</option>
                                <option value="5">Mayo</option>
                                <option value="6">Junio</option>
                                <option value="7">Julio</option>
                                <option value="8">Agosto</option>
                                <option value="9">Septiembre</option>
                                <option value="10">Octubre</option>
                                <option value="11">Noviembre</option>
                                <option value="12">Diciembre</option>
                            </select>
                            <select x-model="nuevoE.dia"
                                    class="w-20 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                                <template x-for="d in 31" :key="d">
                                    <option :value="d" x-text="d"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    {{-- Fila 3: Cerrado / Horario --}}
                    <div class="flex items-center gap-3">
                        <button type="button"
                                @click="nuevoE.cerrado = !nuevoE.cerrado"
                                class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-colors w-20 text-center"
                                :class="nuevoE.cerrado
                                    ? 'bg-gray-100 text-gray-500 border-gray-200'
                                    : 'bg-green-50 text-green-700 border-green-200'">
                            <span x-text="nuevoE.cerrado ? 'Cerrado' : 'Abierto'"></span>
                        </button>
                        <div x-show="!nuevoE.cerrado" class="flex items-center gap-2 flex-1">
                            <input type="time" x-model="nuevoE.apertura"
                                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 flex-1 min-w-0">
                            <span class="text-gray-400 text-xs shrink-0">a</span>
                            <input type="time" x-model="nuevoE.cierre"
                                   class="border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 flex-1 min-w-0">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-1">
                        <button type="button" @click="agregando = false"
                                class="text-xs text-gray-500 hover:text-gray-700 px-3 py-1.5 transition-colors">
                            Cancelar
                        </button>
                        <button type="button" @click="agregar()"
                                :disabled="!nuevoE.nombre || (!nuevoE.se_repite && !nuevoE.fecha)"
                                class="text-xs font-semibold px-4 py-1.5 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors disabled:opacity-40 disabled:cursor-not-allowed">
                            Agregar
                        </button>
                    </div>
                </div>

                {{-- Lista de fechas especiales --}}
                <template x-if="especiales.length === 0">
                    <p class="text-xs text-gray-400 text-center py-3">No hay días especiales configurados.</p>
                </template>
                <div class="space-y-2">
                    <template x-for="(e, i) in especiales" :key="i">
                        <div class="flex items-start justify-between gap-3 bg-gray-50 rounded-xl px-4 py-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <span class="text-sm font-medium text-gray-800" x-text="e.nombre"></span>
                                    <span x-show="e.se_repite" class="text-xs bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">Anual</span>
                                    <span x-show="!e.activo"   class="text-xs bg-gray-200 text-gray-500 px-2 py-0.5 rounded-full">Inactivo</span>
                                </div>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span x-text="e.fecha"></span>
                                    <span class="mx-1">·</span>
                                    <span x-text="e.cerrado ? 'Cerrado' : (e.apertura + ' – ' + e.cierre)"></span>
                                </p>
                            </div>
                            <button type="button" @click="eliminar(i)"
                                    class="text-gray-300 hover:text-red-400 transition-colors shrink-0 mt-0.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

        </div>{{-- /x-data horarios --}}

        {{-- Nota sobre lo que maneja el admin --}}
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Para cambiar <strong>dirección, fotos o plan</strong>, contactá al equipo de Guía Local.</p>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('panel.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                ← Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Guardar cambios
            </button>
        </div>

    </form>

@endsection
