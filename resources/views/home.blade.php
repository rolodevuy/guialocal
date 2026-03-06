@extends('layouts.app')

@section('title', 'Guía Local — Tu barrio en un solo lugar')
@section('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')

@section('content')

{{-- ============================================================
     SECCIÓN: hero
     "Descubrí negocios cerca tuyo"
     Contiene: h1, buscador, 3 quick actions (overlap hacia #destacados)
     ============================================================ --}}
<section id="hero" class="bg-gray-50 relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-12 sm:pt-16 pb-20 sm:pb-24 text-center">

        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 tracking-tight leading-tight mb-8">
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
                        class="px-4 py-4 text-sm text-gray-600 bg-transparent outline-none sm:w-44 shrink-0 border-t sm:border-t-0 border-gray-100 cursor-pointer">
                    <option value="">Atlántida</option>
                    @foreach($zonas as $zona)
                        <option value="{{ $zona->slug }}" {{ request('zona') === $zona->slug ? 'selected' : '' }}>
                            {{ $zona->nombre }}
                        </option>
                    @endforeach
                </select>
                <button type="submit"
                        class="m-2 px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-xl font-semibold text-sm whitespace-nowrap transition-colors">
                    Buscar
                </button>
            </div>
        </form>

        {{-- Quick actions: 50/50 exacto entre hero y destacados --}}
        <div class="absolute left-1/2 bottom-0 z-30 -translate-x-1/2 translate-y-1/2">
            <div class="flex items-start gap-6 sm:gap-16">

                <a href="{{ route('negocios.index') }}" class="group flex flex-col items-center text-center w-36 sm:w-44">
                    <span class="flex items-center justify-center bg-white border border-gray-200 shadow-md" style="width:64px;height:64px;border-radius:9999px;">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                        </svg>
                    </span>
                    <span class="mt-3 text-sm sm:text-base font-medium text-slate-800 group-hover:text-amber-600 transition-colors whitespace-nowrap">Buscar negocios</span>
                </a>

                <a href="{{ route('mapa.index') }}" class="group flex flex-col items-center text-center w-36 sm:w-44">
                    <span class="flex items-center justify-center bg-white border border-gray-200 shadow-md" style="width:64px;height:64px;border-radius:9999px;">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                        </svg>
                    </span>
                    <span class="mt-3 text-sm sm:text-base font-medium text-slate-800 group-hover:text-amber-600 transition-colors whitespace-nowrap">Ver en el mapa</span>
                </a>

                <a href="{{ route('categorias.index') }}" class="group flex flex-col items-center text-center w-36 sm:w-44">
                    <span class="flex items-center justify-center bg-white border border-gray-200 shadow-md" style="width:64px;height:64px;border-radius:9999px;">
                        <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                  d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                    </span>
                    <span class="mt-3 text-sm sm:text-base font-medium text-slate-800 group-hover:text-amber-600 transition-colors whitespace-nowrap">Explorar categorías</span>
                </a>

            </div>
        </div>
    </div>
</section>

@if($destacados->isNotEmpty())
<section id="destacados" class="relative z-10 bg-gray-50 border-t border-gray-200 pt-24 sm:pt-28 pb-12 sm:pb-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Negocios destacados</h2>
            <a href="{{ route('negocios.index') }}"
               class="text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors">
                Ver todos →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($destacados as $negocio)
            <a href="{{ route('negocios.show', $negocio) }}"
               class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg transition-all duration-200">

                {{-- Imagen con badge PREMIUM --}}
                <div class="relative h-48 bg-amber-50 overflow-hidden">
                    @if($negocio->getFirstMediaUrl('portada'))
                        <img src="{{ $negocio->getFirstMediaUrl('portada') }}"
                             alt="{{ $negocio->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-14 h-14 text-amber-200" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                        </div>
                    @endif

                    @if($negocio->plan === 'premium')
                        <span class="absolute top-3 right-3 text-xs font-bold bg-amber-500 text-white px-2.5 py-1 rounded-full uppercase tracking-wide shadow-sm">
                            Premium
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4">
                    <h3 class="font-bold text-gray-900 text-base group-hover:text-amber-600 transition-colors leading-snug">
                        {{ $negocio->nombre }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-2 leading-relaxed">
                        {{ $negocio->descripcion }}
                    </p>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-400">
                            {{ $negocio->categoria->nombre }} · {{ $negocio->zona->nombre }}
                        </span>
                        <span class="text-xs text-amber-600 font-medium">Ver más →</span>
                    </div>
                </div>

            </a>
            @endforeach
        </div>

    </div>
</section>
@endif

{{-- ============================================================
     SECCIÓN: mapa
     Placeholder de mapa interactivo (Etapa 2)
     Izquierda: card amber con zonas como pills + CTA
     Derecha: mapa SVG placeholder con pines
     ============================================================ --}}
<section id="mapa" class="bg-white border-t border-gray-100 py-12 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-8">Mapa de negocios cercanos</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Izquierda: imagen (50%) + info (50%), altura fija --}}
            <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm bg-white flex flex-col h-80">

                {{-- Foto: 160px = 50% de h-80 (320px), sin porcentajes --}}
                <div class="h-40 shrink-0 overflow-hidden">
                    <img src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=700&q=80&auto=format&fit=crop"
                         alt="Negocios en Atlántida"
                         class="w-full h-full object-cover">
                </div>

                {{-- Info: 50% restante --}}
                <div class="p-5 flex flex-col gap-3 flex-1 min-h-0">
                    <h3 class="font-bold text-gray-900">Mapa de negocios cercanos</h3>

                    <select id="home-mapa-zona" class="w-full px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white text-gray-600 outline-none cursor-pointer focus:border-amber-400 transition-colors">
                        <option value="">Todas las zonas</option>
                        @foreach($zonas as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                        @endforeach
                    </select>

                    <div class="mt-auto">
                        <a id="btn-ver-mapa" href="{{ route('mapa.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                            Ver mapa completo
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Derecha: mapa Leaflet, misma altura --}}
            <div class="relative rounded-2xl overflow-hidden h-80">
                <div id="mapa-leaflet" class="absolute inset-0"></div>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================
     SECCIÓN: categorias
     Grid de categorías: número top-left, ícono 48×48 centrado, nombre, count
     ============================================================ --}}
<section id="categorias" class="bg-gray-50 border-t border-gray-100 py-12 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-8">Explorar por categoría</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categorias as $categoria)
            <a href="{{ route('categorias.show', $categoria) }}"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all duration-200 p-5 flex flex-col items-center text-center">

                <div class="mb-3 w-12 h-12 flex items-center justify-center text-amber-500">
                    <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-12 h-12" />
                </div>

                {{-- Nombre --}}
                <p class="font-semibold text-gray-800 text-sm leading-tight group-hover:text-amber-700 transition-colors">
                    {{ $categoria->nombre }}
                </p>

                {{-- Cantidad --}}
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
     CTA para que los negocios se registren
     ============================================================ --}}
<section id="registro" class="bg-white border-t border-gray-100 py-14 sm:py-20">
    <div class="max-w-2xl mx-auto px-4 text-center">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-3 leading-tight">
            ¿Tenés un negocio en Atlántida?
        </h2>
        <p class="text-gray-500 text-base mb-8 leading-relaxed">
            Sumalo gratis a la guía local y llegá a más clientes de la zona.
        </p>
        <a href="{{ route('contacto.show') }}"
           class="inline-block px-8 py-3.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-full text-sm shadow-sm hover:shadow-md transition-all">
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

    // CartoDB Voyager — mapa con colores completos, sin API key
    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> © <a href="https://carto.com/">CARTO</a>',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    // Pin teardrop SVG amber
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

    // Negocios reales con coordenadas
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

    // Scroll zoom solo cuando el mouse está sobre el mapa
    var container = map.getContainer();
    container.addEventListener('mouseenter', function () { map.scrollWheelZoom.enable(); });
    container.addEventListener('mouseleave', function () { map.scrollWheelZoom.disable(); });

    // Filtro por zona desde el select de la card izquierda
    var zonaSelect = document.getElementById('home-mapa-zona');
    if (zonaSelect) {
        zonaSelect.addEventListener('change', function () {
            var zonaId = this.value ? parseInt(this.value) : null;
            var bounds = [];

            // Actualizar href del botón "Ver mapa completo"
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









