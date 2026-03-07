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

        {{-- Pill zona preferida --}}
        @if($zonaPreferida)
        <p class="mt-4 text-sm text-gray-500">
            Mostrando resultados para
            <a href="{{ route('zonas.show', $zonaPreferida) }}" class="font-semibold text-amber-600 hover:underline">{{ $zonaPreferida->nombre }}</a>
            ·
            <a href="{{ route('negocios.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors"
               onclick="document.cookie='zona_preferida=; path=/; max-age=0'">Cambiar zona</a>
        </p>
        @endif

        {{-- Quick actions: centrado, mobile-first --}}
        <div class="absolute left-0 right-0 bottom-0 z-30 flex justify-center translate-y-1/2 px-4">
            <div class="flex items-start justify-center gap-3 sm:gap-16">

                <a href="{{ route('negocios.index') }}" class="group flex flex-col items-center text-center w-[5.5rem] sm:w-44">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-base font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Buscar</span>
                        <span class="hidden sm:inline whitespace-nowrap">Buscar negocios</span>
                    </span>
                </a>

                <a href="{{ route('mapa.index') }}" class="group flex flex-col items-center text-center w-[5.5rem] sm:w-44">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-base font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Mapa</span>
                        <span class="hidden sm:inline whitespace-nowrap">Ver en el mapa</span>
                    </span>
                </a>

                <a href="{{ route('categorias.index') }}" class="group flex flex-col items-center text-center w-[5.5rem] sm:w-44">
                    <span class="w-12 h-12 sm:w-16 sm:h-16 flex items-center justify-center bg-white border border-gray-200 shadow-md rounded-full">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                    </span>
                    <span class="mt-2 sm:mt-3 text-xs sm:text-base font-medium text-slate-800 group-hover:text-amber-600 transition-colors leading-tight">
                        <span class="sm:hidden">Categorías</span>
                        <span class="hidden sm:inline whitespace-nowrap">Explorar categorías</span>
                    </span>
                </a>

            </div>
        </div>
    </div>
</section>

@if($destacados->isNotEmpty())
<section id="destacados" class="relative z-10 bg-gray-50 border-t border-gray-200 pt-20 sm:pt-28 pb-10 sm:pb-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-2xl font-bold text-gray-900">Negocios destacados</h2>
            <a href="{{ route('negocios.index') }}"
               class="text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors">
                Ver todos →
            </a>
        </div>

        {{-- ── Carousel ───────────────────────────────────────────────────── --}}
        <div
            x-data="{
                idx: 0,
                n: {{ $destacados->count() }},
                gap: 16,
                cardWidth: 0,
                offset: 0,
                touchStartX: 0,
                get pp()  {
                    if (window.innerWidth >= 1024) return 3;
                    if (window.innerWidth >= 640)  return 2;
                    return 1;
                },
                get max() { return Math.max(0, this.n - this.pp); },
                prev() {
                    this.idx = this.idx <= 0 ? this.max : this.idx - 1;
                    this.update();
                },
                next() {
                    this.idx = this.idx >= this.max ? 0 : this.idx + 1;
                    this.update();
                },
                goto(i) { this.idx = i; this.update(); },
                update() {
                    const pp = this.pp;
                    const W  = this.$refs.wrap.offsetWidth;
                    if (!W) return;
                    this.gap = window.innerWidth >= 640 ? 24 : 16;
                    this.cardWidth = (W - this.gap * (pp - 1)) / pp;
                    this.offset    = -this.idx * (this.cardWidth + this.gap);
                },
                onTouchStart(e) { this.touchStartX = e.touches[0].clientX; },
                onTouchEnd(e) {
                    const dx = this.touchStartX - e.changedTouches[0].clientX;
                    if (Math.abs(dx) > 40) { dx > 0 ? this.next() : this.prev(); }
                },
                init() {
                    this.$nextTick(() => this.update());
                    window.addEventListener('resize', () => {
                        this.idx = Math.min(this.idx, this.max);
                        this.update();
                    });
                }
            }"
        >
            {{-- Fila: [◀] [track] [▶] --}}
            <div class="flex items-center gap-2 sm:gap-3">

                {{-- Botón anterior --}}
                <button
                    @click="prev()"
                    class="shrink-0 w-8 h-8 sm:w-9 sm:h-9 bg-white rounded-full shadow border border-gray-200 flex items-center justify-center text-gray-500 hover:text-amber-600 hover:border-amber-300 transition cursor-pointer"
                    aria-label="Anterior"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                {{-- Track --}}
                <div class="overflow-hidden flex-1" x-ref="wrap"
                     @touchstart="onTouchStart($event)"
                     @touchend="onTouchEnd($event)">
                    <div
                        class="flex"
                        :style="`gap:${gap}px; transform:translateX(${offset}px); transition:transform .45s cubic-bezier(.25,.46,.45,.94)`"
                    >
                        @foreach($destacados as $ficha)
                        <div class="shrink-0" :style="`width:${cardWidth}px`">
                            <a href="{{ route('negocios.show', $ficha->lugar) }}"
                               class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-200 h-full">

                                {{-- Imagen --}}
                                <div class="relative h-36 sm:h-[7.5rem] lg:h-40 bg-amber-50 overflow-hidden shrink-0">
                                    @php $portadaUrl = $ficha->getPortadaUrl(); @endphp
                                    @if($portadaUrl)
                                        <img src="{{ $portadaUrl }}"
                                             alt="{{ $ficha->lugar->nombre }}"
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <svg class="w-14 h-14 text-amber-200" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    @if($ficha->plan === 'premium')
                                        <span class="absolute top-2 right-2 sm:top-3 sm:right-3 text-xs font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full uppercase tracking-wide shadow-sm">
                                            Premium
                                        </span>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="p-3 sm:p-4 flex flex-col flex-1">
                                    <h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-amber-600 transition-colors leading-snug">
                                        {{ $ficha->lugar->nombre }}
                                    </h3>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-2 leading-relaxed flex-1">
                                        {{ $ficha->descripcion }}
                                    </p>
                                    <div class="flex items-center justify-between mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-50">
                                        <span class="text-xs text-gray-400 truncate mr-2">
                                            {{ $ficha->lugar->categoria->nombre }} · {{ $ficha->lugar->zona?->nombre }}
                                        </span>
                                        <span class="text-xs text-amber-600 font-medium shrink-0">Ver →</span>
                                    </div>
                                </div>

                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Botón siguiente --}}
                <button
                    @click="next()"
                    class="shrink-0 w-8 h-8 sm:w-9 sm:h-9 bg-white rounded-full shadow border border-gray-200 flex items-center justify-center text-gray-500 hover:text-amber-600 hover:border-amber-300 transition cursor-pointer"
                    aria-label="Siguiente"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

            </div>

            {{-- Dots --}}
            <div class="flex items-center justify-center gap-2 mt-5">
                <template x-for="i in (max + 1)" :key="i">
                    <button
                        @click="goto(i - 1)"
                        :class="idx === i - 1 ? 'bg-amber-500 w-5' : 'bg-gray-300 w-2'"
                        class="h-2 rounded-full transition-all duration-300"
                        :aria-label="`Ir a posición ${i}`"
                    ></button>
                </template>
            </div>

        </div>
        {{-- ── /Carousel ───────────────────────────────────────────────────── --}}

    </div>
</section>
@endif

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
     SECCIÓN: mapa
     ============================================================ --}}
<section id="mapa" class="bg-white border-t border-gray-100 py-10 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mb-6 sm:mb-8">Mapa de negocios cercanos</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">

            {{-- Card info --}}
            <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-white flex flex-row lg:flex-col h-32 lg:h-80">

                {{-- Foto --}}
                <div class="w-28 lg:w-full lg:h-40 shrink-0 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=700&q=80&auto=format&fit=crop"
                         alt="Negocios en Atlántida"
                         class="w-full h-full object-cover">
                </div>

                {{-- Info --}}
                <div class="p-3 lg:p-5 flex flex-col gap-2 lg:gap-3 flex-1 min-h-0 justify-center">
                    <h3 class="font-bold text-gray-900 text-sm lg:text-base hidden lg:block">Mapa de negocios cercanos</h3>

                    <select id="home-mapa-zona" class="w-full px-3 py-1.5 lg:py-2 text-xs lg:text-sm border border-gray-200 rounded-xl bg-white text-gray-600 outline-none cursor-pointer focus:border-amber-400 transition-colors">
                        <option value="">Todas las zonas</option>
                        @foreach($zonas as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                        @endforeach
                    </select>

                    <div>
                        <a id="btn-ver-mapa" href="{{ route('mapa.index') }}"
                           class="inline-flex items-center gap-1.5 lg:gap-2 px-3 py-1.5 lg:px-4 lg:py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-xs lg:text-sm transition-colors shadow-sm">
                            Ver mapa completo
                            <svg class="w-3.5 h-3.5 lg:w-4 lg:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Mapa Leaflet — oculto en mobile, visible en lg --}}
            <div class="relative rounded-2xl overflow-hidden h-52 sm:h-64 lg:h-80">
                <div id="mapa-leaflet" class="absolute inset-0"></div>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================
     SECCIÓN: categorias
     ============================================================ --}}
<section id="categorias" class="bg-gray-50 border-t border-gray-100 py-10 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-lg sm:text-2xl font-bold text-gray-900 mb-6 sm:mb-8">Explorar por categoría</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($categorias as $categoria)
            <a href="{{ route('categorias.show', $categoria) }}"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-200 p-3 sm:p-5 flex flex-col items-center text-center">

                <div class="mb-2 sm:mb-3 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center text-amber-500">
                    <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-10 h-10 sm:w-12 sm:h-12" />
                </div>

                <p class="font-semibold text-gray-800 text-xs sm:text-sm leading-tight group-hover:text-amber-700 transition-colors">
                    {{ $categoria->nombre }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    {{ $categoria->negocios_count }} {{ $categoria->negocios_count === 1 ? 'negocio' : 'negocios' }}
                </p>

            </a>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================
     SECCIÓN: registro
     ============================================================ --}}
<section id="registro" class="bg-white border-t border-gray-100 py-12 sm:py-20">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-xl sm:text-3xl font-extrabold text-gray-900 mb-3 leading-tight">
            ¿Tenés un negocio en Atlántida?
        </h2>
        <p class="text-gray-500 text-sm sm:text-base mb-6 sm:mb-8 leading-relaxed">
            Sumalo gratis a la guía local y llegá a más clientes de la zona.
        </p>
        <a href="{{ route('contacto.show') }}"
           class="inline-block px-6 sm:px-8 py-3 sm:py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-full text-sm shadow-sm hover:shadow-md transition-all">
            Registrar mi negocio
        </a>
    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    var el = document.getElementById('mapa-leaflet');
    if (!el) return;

    var map = L.map('mapa-leaflet', {
        center: [-34.7667, -55.7621],
        zoom: 14,
        scrollWheelZoom: false,
        zoomControl: true,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    var pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">'
        + '<path d="M14 0C6.268 0 0 6.268 0 14c0 10.5 14 22 14 22S28 24.5 28 14C28 6.268 21.732 0 14 0z" fill="#f59e0b"/>'
        + '<circle cx="14" cy="14" r="6" fill="white"/>'
        + '</svg>';

    var pinIcon = L.divIcon({
        html: pinSvg,
        iconSize: [28, 36],
        iconAnchor: [14, 36],
        className: '',
    });

    var negocios = @json($negocios_mapa);
    var allMarkers = [];

    negocios.forEach(function (n) {
        var marker = L.marker([n.lat, n.lng], { icon: pinIcon });
        marker.bindTooltip(n.nombre, { direction: 'top', offset: [0, -36] });
        marker.bindPopup(
            '<div style="min-width:140px">'
            + '<strong style="font-size:13px">' + n.nombre + '</strong>'
            + (n.categoria ? '<br><span style="font-size:11px;color:#6b7280">' + n.categoria.nombre + '</span>' : '')
            + '<br><a href="/negocios/' + n.slug + '" style="font-size:12px;color:#d97706;font-weight:600">Ver negocio \u2192</a>'
            + '</div>'
        );
        marker.negocioZona = n.zona_id;
        marker.negocioData = n;
        marker.addTo(map);
        allMarkers.push(marker);
    });

    var container = map.getContainer();
    container.addEventListener('mouseenter', function () { map.scrollWheelZoom.enable(); });
    container.addEventListener('mouseleave', function () { map.scrollWheelZoom.disable(); });

    var zonaSelect = document.getElementById('home-mapa-zona');
    if (zonaSelect) {
        zonaSelect.addEventListener('change', function () {
            var zonaId = this.value ? parseInt(this.value) : null;
            var bounds = [];

            var btnMapa = document.getElementById('btn-ver-mapa');
            if (btnMapa) {
                btnMapa.href = zonaId ? '/mapa?zona=' + zonaId : '/mapa';
            }

            allMarkers.forEach(function (m) {
                if (!zonaId || m.negocioZona === zonaId) {
                    if (!map.hasLayer(m)) m.addTo(map);
                    bounds.push([m.negocioData.lat, m.negocioData.lng]);
                } else {
                    if (map.hasLayer(m)) map.removeLayer(m);
                }
            });

            if (bounds.length === 1) {
                map.setView(bounds[0], 16);
            } else if (bounds.length > 1) {
                map.fitBounds(bounds, { padding: [30, 30], maxZoom: 16 });
            }
        });
    }
}());
</script>
@endpush
