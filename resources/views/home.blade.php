@extends('layouts.app')

@section('title', 'Guía Local — Tu barrio en un solo lugar')
@section('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')

@section('content')

{{-- ============================================================
     SECCIÓN: hero
     ============================================================ --}}
<section id="hero" class="bg-gray-50 relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 sm:pt-16 pb-20 sm:pb-24 text-center">

        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-6 sm:mb-8">
            Descubrí negocios<br class="hidden sm:block"> cerca tuyo
        </h1>

        {{-- Buscador --}}
        <form action="{{ route('negocios.index') }}" method="GET" class="max-w-2xl mx-auto">
            <div class="flex flex-col sm:flex-row bg-white border border-gray-200 rounded-2xl shadow-md overflow-hidden">
                <input
                    type="text"
                    name="q"
                    placeholder="Buscar negocio o categoría..."
                    value="{{ request('q') }}"
                    class="flex-1 px-5 py-4 text-sm text-gray-700 bg-transparent outline-none placeholder-gray-400 min-w-0"
                >
                <div class="hidden sm:block w-px bg-gray-100 my-3"></div>
                <select name="zona"
                        class="px-4 py-3 sm:py-4 text-sm text-gray-600 bg-transparent outline-none sm:w-44 shrink-0 border-t sm:border-t-0 border-gray-100 cursor-pointer">
                    <option value="">Todas las zonas</option>
                    @foreach($zonas as $zona)
                        <option value="{{ $zona->slug }}"
                            {{ request('zona') === $zona->slug || ($zonaPreferida && $zonaPreferida->id === $zona->id && !request('zona')) ? 'selected' : '' }}>
                            {{ $zona->nombre }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="mx-2 mb-2 sm:m-2 px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm whitespace-nowrap transition-colors">
                    Buscar
                </button>
            </div>
        </form>

        {{-- Botón GPS + modal picker fallback --}}
        <div class="mt-6"
             x-data="{
                 pickerOpen: false,
                 gpsLoading: false,
                 search: '',
                 zonas: {{ $zonas->map(fn ($z) => ['slug' => $z->slug, 'nombre' => $z->nombre, 'lat' => $z->lat_centro, 'lng' => $z->lng_centro])->toJson() }},
                 get filtradas() {
                     if (!this.search) return this.zonas;
                     const q = this.search.toLowerCase();
                     return this.zonas.filter(z => z.nombre.toLowerCase().includes(q));
                 },
                 elegir(slug) {
                     document.cookie = 'zona_preferida=' + slug + '; path=/; max-age=' + (60*60*24*30);
                     window.location = '{{ route('negocios.index') }}?zona=' + slug;
                 },
                 usarGPS() {
                     if (!navigator.geolocation) { this.pickerOpen = true; return; }
                     this.gpsLoading = true;
                     navigator.geolocation.getCurrentPosition(
                         (pos) => {
                             const lat = pos.coords.latitude;
                             const lng = pos.coords.longitude;
                             let nearest = null, minDist = Infinity;
                             this.zonas.forEach(z => {
                                 if (z.lat == null || z.lng == null) return;
                                 const d = Math.hypot(z.lat - lat, z.lng - lng);
                                 if (d < minDist) { minDist = d; nearest = z; }
                             });
                             this.gpsLoading = false;
                             if (nearest) { this.elegir(nearest.slug); }
                             else { this.pickerOpen = true; }
                         },
                         () => { this.gpsLoading = false; this.pickerOpen = true; },
                         { timeout: 8000 }
                     );
                 }
             }">

            {{-- Botón principal --}}
            <div class="flex justify-center">
                @if($zonaPreferida)
                {{-- Ya tiene zona: link directo --}}
                <a href="{{ route('negocios.index', ['zona' => $zonaPreferida->slug]) }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                    </svg>
                    Ver negocios en {{ $zonaPreferida->nombre }}
                </a>
                @else
                {{-- Sin zona: GPS → fallback picker --}}
                <button @click="usarGPS()" :disabled="gpsLoading"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 hover:bg-amber-600 disabled:opacity-70 disabled:cursor-wait text-white font-semibold text-sm rounded-xl shadow-sm transition-colors">
                    {{-- Icono GPS / spinner --}}
                    <svg x-show="!gpsLoading" class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                    <svg x-show="gpsLoading" x-cloak class="w-4 h-4 shrink-0 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                    </svg>
                    <span x-text="gpsLoading ? 'Buscando...' : 'Ver negocios cerca tuyo'"></span>
                </button>
                @endif
            </div>

            {{-- Cambiar zona (solo si hay zona activa) --}}
            @if($zonaPreferida)
            <p class="mt-2 text-xs text-gray-400">
                No estás en {{ $zonaPreferida->nombre }} ·
                <button @click="usarGPS()" class="hover:text-amber-600 transition-colors underline underline-offset-2">
                    Actualizar ubicación
                </button>
            </p>
            @endif

            {{-- Modal picker de zonas --}}
            <div x-show="pickerOpen"
                 x-transition:enter="transition ease-out duration-150"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click.self="pickerOpen = false"
                 @keydown.escape.window="pickerOpen = false"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                 x-cloak>
                <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900 text-base">¿En qué zona estás?</h3>
                        <button @click="pickerOpen = false"
                                class="p-1 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                    <input
                        type="text"
                        x-model="search"
                        x-ref="searchInput"
                        x-effect="pickerOpen && $nextTick(() => { if (!window.matchMedia('(hover: none)').matches) $refs.searchInput?.focus() })"
                        placeholder="Buscar zona..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 mb-3"
                    >
                    <ul class="max-h-64 overflow-y-auto -mx-1 space-y-0.5">
                        <template x-for="zona in filtradas" :key="zona.slug">
                            <li>
                                <button
                                    @click="elegir(zona.slug)"
                                    class="w-full text-left px-4 py-2.5 rounded-xl text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors flex items-center gap-2"
                                >
                                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                                    </svg>
                                    <span x-text="zona.nombre"></span>
                                </button>
                            </li>
                        </template>
                        <template x-if="filtradas.length === 0">
                            <li class="px-4 py-3 text-sm text-gray-400 text-center">Sin resultados</li>
                        </template>
                    </ul>
                </div>
            </div>

        </div>{{-- /CTAs --}}

        {{-- Quick actions: centrado, mobile-first --}}
        <div class="absolute left-0 right-0 bottom-0 z-30 flex justify-center translate-y-1/2 px-4">
            <div class="relative flex items-start justify-center gap-3 sm:gap-10">
                {{-- Línea horizontal centrada en los círculos (por detrás) --}}
                <span class="absolute left-0 right-0 top-6 sm:top-8 border-t border-gray-300" aria-hidden="true"></span>

                <a href="{{ route('negocios.index') }}" class="group flex flex-col items-center text-center w-[4.5rem] sm:w-36 relative">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-sm font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Buscar</span>
                        <span class="hidden sm:inline whitespace-nowrap">Buscar negocios</span>
                    </span>
                </a>

                <a href="{{ route('mapa.index') }}" class="group flex flex-col items-center text-center w-[4.5rem] sm:w-36 relative">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-sm font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Mapa</span>
                        <span class="hidden sm:inline whitespace-nowrap">Ver en el mapa</span>
                    </span>
                </a>

                <a href="{{ route('categorias.index') }}" class="group flex flex-col items-center text-center w-[4.5rem] sm:w-36 relative">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-sm font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Categorías</span>
                        <span class="hidden sm:inline whitespace-nowrap">Por categoría</span>
                    </span>
                </a>

                <a href="{{ route('negocios.index', ['abiertos' => 1]) }}" class="group flex flex-col items-center text-center w-[4.5rem] sm:w-36 relative">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full relative">
                        <span class="absolute top-2 right-2 sm:top-2.5 sm:right-2.5 w-2 h-2 rounded-full" style="background:#22c55e">
                            <span class="absolute inset-0 rounded-full animate-ping" style="background:#22c55e;opacity:0.5"></span>
                        </span>
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-sm font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Abiertos</span>
                        <span class="hidden sm:inline whitespace-nowrap">Abierto ahora</span>
                    </span>
                </a>

            </div>
        </div>
    </div>
</section>

{{-- ============================================================
     SECCIÓN: explorar la guía (3 cards de sectores)
     ============================================================ --}}
<section class="relative z-10 bg-gray-50 pt-20 sm:pt-28 pb-10 sm:pb-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mb-2">Explorar la guía</h2>
        <p class="text-sm text-gray-500 mb-6 sm:mb-8">Tres universos, una sola guía.</p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
            @foreach($sectores as $sector)
            <a href="{{ route('sectores.show', $sector) }}"
               class="group relative overflow-hidden rounded-2xl {{ $sector->color('bg_light', 'bg-gray-50') }} border border-transparent hover:{{ $sector->color('border', 'border-amber-200') }} hover:shadow-lg transition-all duration-200 p-6 sm:p-7 flex flex-col">

                {{-- Icono --}}
                <div class="w-12 h-12 {{ $sector->color('bg', 'bg-amber-100') }} rounded-xl flex items-center justify-center {{ $sector->color('icon', 'text-amber-500') }} mb-4 group-hover:scale-105 transition-transform">
                    <x-cat-icon :name="$sector->icono ?? 'default'" class="w-7 h-7" />
                </div>

                {{-- Texto --}}
                <h3 class="text-lg font-bold text-gray-900 group-hover:{{ $sector->color('text', 'text-amber-600') }} transition-colors mb-1">
                    {{ $sector->nombre }}
                </h3>
                @if($sector->descripcion)
                <p class="text-sm text-gray-500 leading-relaxed mb-4 flex-1">{{ $sector->descripcion }}</p>
                @endif

                {{-- Stats + arrow --}}
                <div class="flex items-center justify-between mt-auto">
                    <span class="text-xs {{ $sector->color('text', 'text-amber-600') }} font-medium">
                        {{ $sector->categorias->sum('negocios_count') }} negocios · {{ $sector->categorias->count() }} categorías
                    </span>
                    <span class="w-8 h-8 rounded-full {{ $sector->color('bg', 'bg-amber-100') }} {{ $sector->color('icon', 'text-amber-500') }} flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                </div>

            </a>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     SECCIÓN: destacados con tabs por sector
     ============================================================ --}}
<section id="destacados" class="relative z-10 bg-gray-50 pb-10 sm:pb-16" x-data="{ tab: 'todos' }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-2xl font-bold text-gray-900">Destacados</h2>

            {{-- Tabs --}}
            <div class="flex sm:inline-flex items-center gap-0.5 sm:gap-1 bg-gray-200/80 rounded-xl p-1">
                <button @click="tab = 'todos'"
                        :class="tab === 'todos' ? 'bg-white text-gray-900 shadow' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                        class="flex-1 sm:flex-none px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all whitespace-nowrap cursor-pointer">
                    Todos
                </button>
                @foreach($sectores as $sector)
                <button @click="tab = '{{ $sector->id }}'"
                        :class="tab === '{{ $sector->id }}' ? 'bg-white {{ $sector->color('text', 'text-amber-600') }} shadow' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'"
                        class="flex-1 sm:flex-none px-2 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-semibold transition-all whitespace-nowrap cursor-pointer">
                    {{ $sector->nombre_corto ?? $sector->nombre }}
                </button>
                @endforeach
            </div>
        </div>

        {{-- Panel: Todos --}}
        @if($destacados->isNotEmpty())
        <div x-show="tab === 'todos'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach($destacados->take(6) as $ficha)
                @include('partials._ficha_card', ['ficha' => $ficha, 'accentColor' => 'amber'])
                @endforeach
            </div>
        </div>
        @endif

        {{-- Panels: Por sector --}}
        @foreach($sectores as $sector)
        @php $fichasSector = $destacadosPorSector[$sector->id] ?? collect(); @endphp
        @if($fichasSector->isNotEmpty())
        <div x-show="tab === '{{ $sector->id }}'" x-cloak x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">
                @foreach($fichasSector as $ficha)
                @include('partials._ficha_card', ['ficha' => $ficha, 'sector' => $sector])
                @endforeach
            </div>
            <div class="text-center mt-6">
                <a href="{{ route('sectores.show', $sector) }}"
                   class="text-sm {{ $sector->color('text', 'text-amber-600') }} hover:{{ $sector->color('text_hover', 'text-amber-700') }} font-medium transition-colors">
                    Ver todo en {{ $sector->nombre }} →
                </a>
            </div>
        </div>
        @endif
        @endforeach

    </div>
</section>

{{-- ============================================================
     SECCIÓN: editorial (artículos/guías destacados — solo si hay slots)
     ============================================================ --}}
@if($slotsEditoriales->isNotEmpty())
<section id="editorial" class="bg-white border-t border-gray-100 py-10 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-2xl font-bold text-gray-900">Del barrio</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($slotsEditoriales as $item)
            @php
                $esGuia     = $item instanceof \App\Models\Guia;
                $ruta       = $esGuia ? route('guias.show', $item) : route('articulos.show', $item);
                $titulo     = $item->titulo;
                $intro      = $esGuia ? $item->intro : ($item->extracto ?? null);
                $portada    = $item->getFirstMediaUrl('portada', 'webp') ?: $item->getFirstMediaUrl('portada');
                $etiqueta   = $esGuia ? 'Guía' : 'Artículo';
                $color      = $esGuia ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700';
                $fecha      = $item->publicado_en?->format('d/m/Y');
            @endphp
            <a href="{{ $ruta }}"
               class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all flex sm:flex-col">

                <div class="w-28 sm:w-full h-auto sm:h-44 bg-amber-50 overflow-hidden shrink-0">
                    @if($portada)
                        <img src="{{ $portada }}" alt="{{ $titulo }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-amber-200">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-3 sm:p-4 flex flex-col justify-center flex-1">
                    <div class="flex items-center gap-2 text-xs mb-1 sm:mb-2">
                        <span class="{{ $color }} px-2 py-0.5 rounded font-medium">{{ $etiqueta }}</span>
                        @if($fecha)<span class="text-gray-400">{{ $fecha }}</span>@endif
                    </div>
                    <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-snug mb-1">
                        {{ $titulo }}
                    </h3>
                    @if($intro)
                        <p class="text-xs text-gray-400 line-clamp-2 hidden sm:block">{{ $intro }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

    </div>
</section>
@endif

{{-- ============================================================
     SECCIÓN: eventos próximos
     ============================================================ --}}
@if($eventosDestacados->isNotEmpty())
<section id="eventos" class="bg-white border-t border-gray-100 py-10 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-2xl font-bold text-gray-900">Eventos próximos</h2>
            <a href="{{ route('eventos.index') }}"
               class="text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors">
                Ver todos →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($eventosDestacados as $evento)
            @php
                $portada = $evento->getFirstMediaUrl('portada', 'webp') ?: $evento->getFirstMediaUrl('portada');
                $esHoy   = $evento->fecha_inicio->isToday();
            @endphp

            <a href="{{ route('eventos.show', $evento) }}"
               class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

                {{-- Imagen --}}
                <div class="relative h-44 bg-amber-50 overflow-hidden">
                    @if($portada)
                        <img src="{{ $portada }}" alt="{{ $evento->titulo }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif
                    @if($esHoy)
                        <span class="absolute top-3 left-3 text-xs font-bold bg-green-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                            Hoy
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4">
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $evento->fecha_inicio->locale('es')->isoFormat('D [de] MMMM') }}
                            @if($evento->fecha_fin && ! $evento->fecha_fin->isSameDay($evento->fecha_inicio))
                                — {{ $evento->fecha_fin->locale('es')->isoFormat('D [de] MMMM') }}
                            @endif
                        </span>
                        @if($evento->hora_inicio)
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}h
                            </span>
                        @endif
                    </div>

                    <h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-amber-600 transition-colors leading-snug mb-1">
                        {{ $evento->titulo }}
                    </h3>

                    @if($evento->descripcion)
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-2">
                            {{ $evento->descripcion }}
                        </p>
                    @endif

                    @if($evento->lugar)
                        <p class="text-xs text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                            {{ $evento->lugar->nombre }}
                        </p>
                    @endif
                </div>

            </a>
            @endforeach
        </div>

    </div>
</section>
@endif


{{-- ============================================================
     SECCIÓN: registro
     ============================================================ --}}
{{-- ============================================================
     SECCIÓN: newsletter
     ============================================================ --}}
<section id="newsletter" class="bg-amber-50 border-t border-amber-100 py-12 sm:py-16">
    <div class="max-w-2xl mx-auto px-4 text-center">

        <div class="inline-flex items-center gap-2 bg-amber-100 text-amber-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
            </svg>
            Newsletter del barrio
        </div>

        <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-2 leading-tight">
            Novedades de tu zona, directo al mail
        </h2>
        <p class="text-gray-500 text-sm mb-6 leading-relaxed">
            Nuevos negocios, promociones vigentes y lo mejor del barrio. Sin spam.
        </p>

        @if(session('newsletter_ok'))
            <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3 text-sm text-green-800 text-left">
                <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('newsletter_ok') }}
            </div>
        @endif

        <form action="{{ route('newsletter.subscribe') }}" method="POST"
              class="flex flex-col sm:flex-row gap-3 max-w-lg mx-auto">
            @csrf

            <input type="email" name="email" required
                   placeholder="tu@email.com"
                   value="{{ old('email') }}"
                   class="flex-1 px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-700 focus:outline-none focus:ring-2 focus:ring-amber-400 shadow-sm
                          {{ $errors->has('email') ? 'border-red-300' : '' }}">

            <select name="zona_id"
                    class="px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-600 focus:outline-none focus:ring-2 focus:ring-amber-400 shadow-sm sm:w-44 shrink-0">
                <option value="">Toda la ciudad</option>
                @foreach($zonas as $zona)
                    <option value="{{ $zona->id }}"
                        {{ (old('zona_id') == $zona->id || ($zonaPreferida && $zonaPreferida->id == $zona->id)) ? 'selected' : '' }}>
                        {{ $zona->nombre }}
                    </option>
                @endforeach
            </select>

            <button type="submit"
                    class="px-5 py-3 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl shadow-sm transition-colors whitespace-nowrap">
                Suscribirme
            </button>
        </form>

        @error('email')
            <p class="mt-2 text-xs text-red-500">{{ $message }}</p>
        @enderror

        <p class="mt-4 text-xs text-gray-400">
            Podés darte de baja en cualquier momento desde el mail que te enviamos.
        </p>
    </div>
</section>

@endsection

