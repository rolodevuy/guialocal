<x-filament-panels::page>

    {{-- ══════════════════════════════════════════════════════════════
         FORMULARIO DE BÚSQUEDA
    ══════════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-4">Parámetros de búsqueda</h2>

        {{-- Modo de búsqueda --}}
        <div class="mb-4 flex items-center gap-1 rounded-lg border border-gray-200 bg-gray-50 p-1 w-fit dark:border-gray-700 dark:bg-gray-800">
            <button wire:click="$set('modo', 'localidad')"
                    type="button"
                    class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors
                        {{ $modo === 'localidad'
                            ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white'
                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Por localidad (límites OSM)
                </span>
            </button>
            <button wire:click="$set('modo', 'radio')"
                    type="button"
                    class="rounded-md px-4 py-1.5 text-sm font-medium transition-colors
                        {{ $modo === 'radio'
                            ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-700 dark:text-white'
                            : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="2"/>
                        <circle cx="12" cy="12" r="3" fill="currentColor"/>
                    </svg>
                    Por radio
                </span>
            </button>
        </div>

        @if($modo === 'localidad')
            <p class="mb-3 text-xs text-blue-600 dark:text-blue-400 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Busca dentro de los límites exactos de la localidad en OpenStreetMap, usando el nombre de la zona como referencia.
            </p>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 {{ $modo === 'radio' ? 'lg:grid-cols-3' : 'lg:grid-cols-2' }} gap-4">

            {{-- Tipo de negocio --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Tipo de negocio <span class="text-red-500">*</span>
                </label>
                <select wire:model="tipo"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">— Elegí un tipo —</option>
                    @foreach($this->getTipos() as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
                @error('tipo') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Zona --}}
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Zona <span class="text-red-500">*</span>
                </label>
                <select wire:model="zonaId"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">— Elegí una zona —</option>
                    @foreach($this->getZonas() as $id => $nombre)
                        <option value="{{ $id }}">{{ $nombre }}</option>
                    @endforeach
                </select>
                @error('zonaId') <p class="text-xs text-red-500">{{ $message }}</p> @enderror
            </div>

            {{-- Radio (solo en modo radio) --}}
            @if($modo === 'radio')
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Radio de búsqueda
                </label>
                <select wire:model="radio"
                        class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="1000">1 km</option>
                    <option value="2000">2 km</option>
                    <option value="3000">3 km</option>
                    <option value="5000">5 km</option>
                    <option value="10000">10 km</option>
                </select>
            </div>
            @endif
        </div>

        <div class="mt-5 flex items-center gap-3">
            <button wire:click="buscar"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-amber-600 disabled:opacity-60 transition-colors">
                <svg wire:loading wire:target="buscar" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <svg wire:loading.remove wire:target="buscar" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                </svg>
                <span wire:loading.remove wire:target="buscar">Buscar en OpenStreetMap</span>
                <span wire:loading wire:target="buscar">Buscando...</span>
            </button>

            <p class="text-xs text-gray-400">Los datos provienen de OpenStreetMap (licencia ODbL)</p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         ERROR
    ══════════════════════════════════════════════════════════════ --}}
    @if($error)
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-800 dark:bg-red-950 dark:text-red-300">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                {{ $error }}
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════
         RESULTADOS
    ══════════════════════════════════════════════════════════════ --}}
    @if(count($resultados) > 0)

        {{-- Barra de stats --}}
        @php
            $totalNuevos    = collect($resultados)->where('existe', false)->count();
            $totalExistentes = collect($resultados)->where('existe', true)->count();
        @endphp

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-4 text-sm">
                <span class="font-medium text-gray-700 dark:text-gray-300">
                    {{ count($resultados) }} resultados
                </span>
                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ $totalNuevos }} nuevos
                </span>
                <span class="inline-flex items-center gap-1 text-gray-400">
                    <span class="h-2 w-2 rounded-full bg-gray-300"></span>
                    {{ $totalExistentes }} ya existen
                </span>
                @if(count($seleccionados) > 0)
                    <span class="inline-flex items-center gap-1 font-semibold text-amber-600">
                        {{ count($seleccionados) }} seleccionados
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="seleccionarTodos"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    Seleccionar todos los nuevos
                </button>
                <button wire:click="deseleccionarTodos"
                        class="rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">
                    Deseleccionar todos
                </button>
            </div>
        </div>

        {{-- Tabla de resultados --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="w-10 px-4 py-3"></th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Nombre</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Tipo OSM</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Localidad</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Zona a asignar</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Teléfono</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-500 dark:text-gray-400">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @foreach($resultados as $r)
                        <tr class="{{ $r['existe'] ? 'opacity-40 pointer-events-none' : (in_array($r['osm_id'], $seleccionados) ? 'bg-amber-50 dark:bg-amber-950/30' : 'hover:bg-gray-50 dark:hover:bg-gray-800/30') }} transition-colors">

                            {{-- Checkbox --}}
                            <td class="px-4 py-3 text-center">
                                @if(! $r['existe'])
                                    <input type="checkbox"
                                           wire:model.live="seleccionados"
                                           value="{{ $r['osm_id'] }}"
                                           class="rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                                @endif
                            </td>

                            {{-- Nombre --}}
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $r['nombre'] }}
                                @if($r['direccion'])
                                    <div class="text-xs text-gray-400 font-normal">{{ $r['direccion'] }}</div>
                                @endif
                            </td>

                            {{-- Tipo OSM --}}
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($r['tags_relevantes'] ?? [] as $key => $val)
                                        <span class="inline-block rounded bg-blue-100 px-1.5 py-0.5 text-xs text-blue-700 dark:bg-blue-900/30 dark:text-blue-300"
                                              title="{{ $key }}={{ $val }}">
                                            {{ $val }}
                                        </span>
                                    @endforeach
                                    @if(empty($r['tags_relevantes']))
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Localidad --}}
                            <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                                {{ $r['localidad'] ?? '—' }}
                            </td>

                            {{-- Zona sugerida --}}
                            <td class="px-4 py-3 text-sm">
                                @if($r['zona_auto'] ?? false)
                                    <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 font-medium">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        {{ $r['zona_nombre_sugerida'] }}
                                    </span>
                                    <div class="text-xs text-gray-400">detectada automáticamente</div>
                                @else
                                    <span class="text-gray-500">{{ $r['zona_nombre_sugerida'] }}</span>
                                    <div class="text-xs text-gray-400">zona de búsqueda</div>
                                @endif
                            </td>

                            {{-- Teléfono --}}
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-sm">
                                {{ $r['telefono'] ?? '—' }}
                            </td>

                            {{-- Estado --}}
                            <td class="px-4 py-3">
                                @if($r['existe'])
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-400">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Ya existe
                                        @if($r['lugar_id'])
                                            <a href="{{ route('filament.admin.resources.lugars.edit', $r['lugar_id']) }}"
                                               class="text-amber-600 hover:underline">#{{ $r['lugar_id'] }}</a>
                                        @endif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Nuevo
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Categoría + botón importar --}}
        @if($totalNuevos > 0)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 dark:border-amber-800 dark:bg-amber-950/30">
                <div class="flex flex-wrap items-end gap-4">

                    {{-- Selector de categoría --}}
                    <div class="flex flex-col gap-1 min-w-[260px]">
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Categoría en la guía <span class="text-red-500">*</span>
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">¿En qué categoría querés publicar estos negocios?</p>
                        <select wire:model="categoriaId"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                            <option value="">— Elegí una categoría —</option>
                            @foreach($this->getCategorias() as $id => $nombre)
                                <option value="{{ $id }}">{{ $nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Botón --}}
                    <div class="flex flex-col gap-1">
                        <button wire:click="importar"
                                wire:loading.attr="disabled"
                                wire:confirm="¿Importar {{ count($seleccionados) }} negocio(s)? Se crearán en estado pendiente."
                                @disabled(empty($seleccionados))
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg wire:loading wire:target="importar" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <svg wire:loading.remove wire:target="importar" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Importar {{ count($seleccionados) }} negocio(s) seleccionado(s)
                        </button>
                        <p class="text-xs text-gray-400 mt-1">
                            Se crean <strong>pendientes</strong> e inactivos — revisalos en Fichas antes de publicar.
                        </p>
                    </div>

                </div>{{-- /flex --}}
            </div>{{-- /panel ámbar --}}
        @endif

    @elseif(! $buscando && ! $error && count($resultados) === 0 && $tipo)
        <div class="rounded-xl border border-gray-200 bg-white p-12 text-center text-gray-400 dark:border-gray-700 dark:bg-gray-900">
            <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            @if($modo === 'localidad')
                <p class="text-sm">No se encontraron resultados dentro de los límites de "{{ $this->getZonas()[$zonaId] ?? 'la zona seleccionada' }}" en OSM. Verificá que el nombre de la zona coincida exactamente con el área en OpenStreetMap, o probá el modo Por radio.</p>
            @else
                <p class="text-sm">No se encontraron resultados en esa zona y radio. Probá aumentar el radio.</p>
            @endif
        </div>
    @endif

</x-filament-panels::page>
