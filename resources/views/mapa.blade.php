@extends('layouts.app')

@section('title', 'Mapa de negocios — Atlántida')
@section('description', 'Explorá los negocios de Atlántida en el mapa. Filtrá por zona y categoría.')

@section('content')

{{-- Barra de filtros sticky --}}
<div class="bg-white border-b border-gray-200 sticky top-0 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
        <div class="flex flex-wrap items-center gap-3">

            <span class="font-bold text-gray-800 text-sm hidden sm:inline">Mapa de negocios</span>

            {{-- Paso 1: Zona (siempre visible) --}}
            <select id="filtro-zona"
                    class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none cursor-pointer focus:border-amber-400 transition-colors">
                <option value="">📍 Elegí una zona...</option>
                @foreach($zonas as $zona)
                    <option value="{{ $zona->id }}" {{ $zonaInicial == $zona->id ? 'selected' : '' }}>
                        {{ $zona->nombre }}
                    </option>
                @endforeach
            </select>

            {{-- Paso 2: Categoría + búsqueda (aparece al elegir zona) --}}
            <div id="extra-filtros" class="hidden flex flex-wrap items-center gap-3">
                <select id="filtro-categoria"
                        class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none cursor-pointer focus:border-amber-400 transition-colors">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                    @endforeach
                </select>

                <input id="filtro-busqueda" type="text" placeholder="Buscar negocio..."
                       class="px-3 py-2 text-sm border border-gray-200 rounded-xl bg-white text-gray-700 outline-none focus:border-amber-400 transition-colors w-44">

                <button id="btn-limpiar"
                        class="px-3 py-2 text-xs text-gray-400 hover:text-gray-600 border border-gray-200 rounded-xl hover:border-gray-300 transition-colors">
                    Limpiar
                </button>
            </div>

        </div>
    </div>
</div>

{{-- Mapa: 55vh para que siempre se vea el listado debajo --}}
<div class="relative w-full" style="height: clamp(320px, 55vh, 500px);">
    <div id="mapa-pagina" class="absolute inset-0"></div>
    {{-- Pill de estado --}}
    <div id="mapa-estado"
         class="absolute top-3 right-3 z-[1000] bg-white rounded-full px-3 py-1.5 text-xs text-gray-500 shadow border border-gray-100 hidden">
    </div>
</div>

{{-- Lista de negocios visibles --}}
<div class="bg-gray-50 border-t border-gray-100 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-5">
            <h2 id="lista-titulo" class="text-lg font-bold text-gray-900">
                Elegí una zona para ver los negocios
            </h2>
        </div>

        <div id="lista-negocios" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
            {{-- Renderizado por JS según el viewport del mapa --}}
        </div>

        <p id="lista-vacia" class="hidden text-sm text-gray-400 text-center py-8">
            No hay negocios visibles en esta área del mapa.
        </p>

    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
    .leaflet-popup-content-wrapper { border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.12); }
    .leaflet-popup-content { margin: 12px 14px; }
    .negocio-card-lista { transition: box-shadow 0.15s, border-color 0.15s; }
    .negocio-card-lista:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); border-color: #fcd34d; }
    .negocio-card-lista.activo { border-color: #f59e0b; box-shadow: 0 0 0 2px #fde68a; }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {

    // ── Datos ────────────────────────────────────────────────────────────────
    var negocios = @json($lugares);

    // ── Mapa ─────────────────────────────────────────────────────────────────
    var zonaInicial = {{ $zonaInicial ?? 'null' }};

    var map = L.map('mapa-pagina', {
        center: [-34.7667, -55.7621],
        zoom: 13,
        scrollWheelZoom: false,
        zoomControl: true,
    });

    L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png', {
        attribution: '\u00a9 OpenStreetMap contributors \u00a9 CARTO',
        subdomains: 'abcd',
        maxZoom: 19,
    }).addTo(map);

    // Scroll zoom solo en hover
    var mapContainer = map.getContainer();
    mapContainer.addEventListener('mouseenter', function () { map.scrollWheelZoom.enable(); });
    mapContainer.addEventListener('mouseleave', function () { map.scrollWheelZoom.disable(); });

    // ── Pin SVG ───────────────────────────────────────────────────────────────
    var pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" width="28" height="36" viewBox="0 0 28 36">'
        + '<path d="M14 0C6.268 0 0 6.268 0 14c0 10.5 14 22 14 22S28 24.5 28 14C28 6.268 21.732 0 14 0z" fill="#f59e0b"/>'
        + '<circle cx="14" cy="14" r="6" fill="white"/>'
        + '</svg>';

    var pinIcon = L.divIcon({ html: pinSvg, iconSize: [28, 36], iconAnchor: [14, 36], className: '' });

    // ── Marcadores ────────────────────────────────────────────────────────────
    var markers = [];

    negocios.forEach(function (n) {
        var marker = L.marker([n.lat, n.lng], { icon: pinIcon });

        marker.bindTooltip(n.nombre, { direction: 'top', offset: [0, -36] });

        marker.bindPopup(
            '<div style="min-width:150px">'
            + '<strong style="font-size:13px;display:block;margin-bottom:4px">' + esc(n.nombre) + '</strong>'
            + (n.categoria ? '<span style="font-size:11px;color:#6b7280">' + esc(n.categoria.nombre) + '</span><br>' : '')
            + (n.zona ? '<span style="font-size:11px;color:#9ca3af">' + esc(n.zona.nombre) + '</span><br>' : '')
            + '<a href="/negocios/' + n.slug + '" style="font-size:12px;color:#d97706;font-weight:600;display:inline-block;margin-top:6px">Ver negocio \u2192</a>'
            + '</div>'
        );

        marker.negocioId    = n.id;
        marker.negocioZona  = n.zona_id;
        marker.negocioCat   = n.categoria_id;
        marker.negocioNombre = n.nombre.toLowerCase();
        marker.negocioData  = n;

        markers.push(marker);
    });

    // ── Estado de filtros ─────────────────────────────────────────────────────
    var zonaActiva     = null;
    var categoriaActiva = null;
    var busqueda       = '';

    // ── Aplicar filtros y actualizar mapa ─────────────────────────────────────
    function aplicarFiltros() {
        var bounds = [];

        markers.forEach(function (m) {
            var mostrar = true;

            if (zonaActiva     && m.negocioZona  !== zonaActiva)     mostrar = false;
            if (categoriaActiva && m.negocioCat   !== categoriaActiva) mostrar = false;
            if (busqueda        && m.negocioNombre.indexOf(busqueda) === -1) mostrar = false;

            if (mostrar) {
                if (!map.hasLayer(m)) m.addTo(map);
                bounds.push([m.negocioData.lat, m.negocioData.lng]);
            } else {
                if (map.hasLayer(m)) map.removeLayer(m);
            }
        });

        // Zoom a la selección solo cuando cambia la zona
        if (zonaActiva && bounds.length > 0) {
            if (bounds.length === 1) {
                map.setView(bounds[0], 17);
            } else {
                map.fitBounds(bounds, { padding: [50, 50], maxZoom: 17 });
            }
        }

        actualizarEstado(bounds.length);
        actualizarLista();
    }

    // ── Lista de negocios visibles en el viewport ─────────────────────────────
    function actualizarLista() {
        var bounds   = map.getBounds();
        var visibles = [];

        markers.forEach(function (m) {
            if (map.hasLayer(m) && bounds.contains(m.getLatLng())) {
                visibles.push(m.negocioData);
            }
        });

        var lista  = document.getElementById('lista-negocios');
        var vacia  = document.getElementById('lista-vacia');
        var titulo = document.getElementById('lista-titulo');

        if (!zonaActiva) {
            titulo.textContent = 'Elegí una zona para ver los negocios';
            lista.innerHTML = '';
            vacia.classList.add('hidden');
            return;
        }

        titulo.textContent = visibles.length + (visibles.length === 1 ? ' negocio en esta área' : ' negocios en esta área');

        if (visibles.length === 0) {
            lista.innerHTML = '';
            vacia.classList.remove('hidden');
        } else {
            vacia.classList.add('hidden');
            lista.innerHTML = visibles.map(renderCard).join('');
        }
    }

    function renderCard(n) {
        return '<a href="/negocios/' + n.slug + '" '
            + 'class="negocio-card-lista bg-white rounded-xl border border-gray-100 p-4 flex flex-col gap-1.5 text-left">'
            + '<p class="font-semibold text-sm text-gray-800 leading-tight">' + esc(n.nombre) + '</p>'
            + (n.categoria ? '<p class="text-xs text-amber-600">' + esc(n.categoria.nombre) + '</p>' : '')
            + (n.zona ? '<p class="text-xs text-gray-400">' + esc(n.zona.nombre) + '</p>' : '')
            + '</a>';
    }

    // ── Estado pill ───────────────────────────────────────────────────────────
    function actualizarEstado(count) {
        var el = document.getElementById('mapa-estado');
        if (zonaActiva) {
            el.textContent = count + (count === 1 ? ' negocio' : ' negocios');
            el.classList.remove('hidden');
        } else {
            el.classList.add('hidden');
        }
    }

    // ── Listeners de filtros ──────────────────────────────────────────────────
    document.getElementById('filtro-zona').addEventListener('change', function () {
        zonaActiva      = this.value ? parseInt(this.value) : null;
        categoriaActiva = null;
        busqueda        = '';

        var extras = document.getElementById('extra-filtros');
        if (zonaActiva) {
            extras.classList.remove('hidden');
        } else {
            extras.classList.add('hidden');
            // Sin zona: mostrar todos
            markers.forEach(function (m) { if (!map.hasLayer(m)) m.addTo(map); });
            map.setView([-34.7667, -55.7621], 13);
        }

        document.getElementById('filtro-categoria').value = '';
        document.getElementById('filtro-busqueda').value  = '';

        aplicarFiltros();
    });

    document.getElementById('filtro-categoria').addEventListener('change', function () {
        categoriaActiva = this.value ? parseInt(this.value) : null;
        aplicarFiltros();
    });

    var busqTimer;
    document.getElementById('filtro-busqueda').addEventListener('input', function () {
        clearTimeout(busqTimer);
        var val = this.value.trim().toLowerCase();
        busqTimer = setTimeout(function () {
            busqueda = val;
            aplicarFiltros();
        }, 250);
    });

    document.getElementById('btn-limpiar').addEventListener('click', function () {
        zonaActiva      = null;
        categoriaActiva = null;
        busqueda        = '';
        document.getElementById('filtro-zona').value      = '';
        document.getElementById('filtro-categoria').value = '';
        document.getElementById('filtro-busqueda').value  = '';
        document.getElementById('extra-filtros').classList.add('hidden');
        markers.forEach(function (m) { if (!map.hasLayer(m)) m.addTo(map); });
        map.setView([-34.7667, -55.7621], 13);
        actualizarEstado(0);
        actualizarLista();
    });

    // Actualizar lista al mover/zoom el mapa
    map.on('moveend', actualizarLista);

    // ── Auto-aplicar zona inicial (desde ?zona=ID) ────────────────────────────
    if (zonaInicial) {
        zonaActiva = zonaInicial;
        document.getElementById('extra-filtros').classList.remove('hidden');
        aplicarFiltros();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────
    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

}());
</script>
@endpush
