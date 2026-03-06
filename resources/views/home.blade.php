@extends('layouts.app')

@section('title', 'Guía Local — Tu barrio en un solo lugar')
@section('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="bg-white border-b border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24 text-center">

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
                    <option value="">Toda Atlántida</option>
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

        {{-- Quick actions --}}
        <div class="flex justify-center gap-10 sm:gap-16 mt-10">

            <a href="{{ route('negocios.index') }}" class="flex flex-col items-center gap-2.5 group">
                <div class="w-14 h-14 rounded-full bg-white border-2 border-gray-100 shadow-sm flex items-center justify-center group-hover:border-amber-300 group-hover:shadow-md transition-all">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-500 group-hover:text-amber-600 transition-colors">Buscar negocios</span>
            </a>

            <a href="{{ route('negocios.index') }}" class="flex flex-col items-center gap-2.5 group">
                <div class="w-14 h-14 rounded-full bg-white border-2 border-gray-100 shadow-sm flex items-center justify-center group-hover:border-amber-300 group-hover:shadow-md transition-all">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-500 group-hover:text-amber-600 transition-colors">Ver en el mapa</span>
            </a>

            <a href="{{ route('categorias.index') }}" class="flex flex-col items-center gap-2.5 group">
                <div class="w-14 h-14 rounded-full bg-white border-2 border-gray-100 shadow-sm flex items-center justify-center group-hover:border-amber-300 group-hover:shadow-md transition-all">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                    </svg>
                </div>
                <span class="text-xs font-medium text-gray-500 group-hover:text-amber-600 transition-colors">Explorar categorías</span>
            </a>

        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- NEGOCIOS DESTACADOS                                           --}}
{{-- ============================================================ --}}
@if($destacados->isNotEmpty())
<section class="bg-gray-50 border-t border-gray-100 py-12 sm:py-16">
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

                {{-- Imagen --}}
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

{{-- ============================================================ --}}
{{-- MAPA DE NEGOCIOS                                             --}}
{{-- ============================================================ --}}
<section class="bg-white border-t border-gray-100 py-12 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-8">Mapa de negocios cercanos</h2>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">

            {{-- Columna izquierda: visual card con overlay --}}
            <div class="relative rounded-2xl overflow-hidden min-h-72 lg:min-h-0 bg-gradient-to-br from-amber-400 via-amber-500 to-orange-500">

                {{-- Patrón decorativo de fondo --}}
                <div class="absolute inset-0 opacity-10">
                    <svg class="w-full h-full" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="dots" width="24" height="24" patternUnits="userSpaceOnUse">
                                <circle cx="2" cy="2" r="1.5" fill="white"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#dots)"/>
                    </svg>
                </div>

                {{-- Círculos decorativos --}}
                <div class="absolute -top-16 -right-16 w-64 h-64 bg-white/10 rounded-full"></div>
                <div class="absolute -bottom-10 -left-10 w-48 h-48 bg-white/10 rounded-full"></div>

                {{-- Contenido --}}
                <div class="relative h-full flex flex-col justify-between p-7 sm:p-8">
                    <div>
                        <div class="w-11 h-11 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center mb-5">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-white mb-2">Mapa de negocios cercanos</h3>
                        <p class="text-sm text-amber-100 leading-relaxed">
                            Explorá los negocios de cada zona de Atlántida. Filtrá por barrio y encontrá lo que necesitás cerca tuyo.
                        </p>

                        {{-- Zonas como pills --}}
                        <div class="flex flex-wrap gap-2 mt-5">
                            @foreach($zonas as $zona)
                                <a href="{{ route('zonas.show', $zona) }}"
                                   class="px-3 py-1 bg-white/20 hover:bg-white/30 text-xs text-white font-medium rounded-full border border-white/30 transition-colors">
                                    {{ $zona->nombre }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('negocios.index') }}"
                           class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-amber-600 hover:bg-amber-50 font-semibold rounded-xl text-sm transition-colors shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                            </svg>
                            Ver mapa completo
                        </a>
                    </div>
                </div>

            </div>

            {{-- Columna derecha: mapa placeholder --}}
            <div class="relative bg-gray-100 rounded-2xl overflow-hidden min-h-72 lg:min-h-0">

                {{-- Fondo tipo mapa --}}
                <div class="absolute inset-0 bg-gradient-to-br from-slate-100 via-gray-50 to-amber-50/40">

                    {{-- Grilla de calles --}}
                    <svg class="absolute inset-0 w-full h-full" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <pattern id="mapgrid" width="48" height="48" patternUnits="userSpaceOnUse">
                                <path d="M 48 0 L 0 0 0 48" fill="none" stroke="#e5e7eb" stroke-width="0.5"/>
                            </pattern>
                        </defs>
                        <rect width="100%" height="100%" fill="url(#mapgrid)"/>

                        {{-- Avenidas principales --}}
                        <rect x="0" y="37%" width="100%" height="7" fill="#dde1e7" rx="2"/>
                        <rect x="0" y="62%" width="100%" height="5" fill="#e9ecf0" rx="2"/>
                        <rect x="27%" y="0" width="7" height="100%" fill="#dde1e7" rx="2"/>
                        <rect x="63%" y="0" width="5" height="100%" fill="#e9ecf0" rx="2"/>
                        <rect x="0" y="20%" width="100%" height="3" fill="#edf0f3" rx="1"/>
                        <rect x="47%" y="0" width="3" height="100%" fill="#edf0f3" rx="1"/>
                    </svg>

                    {{-- Pins de negocios --}}
                    <div class="absolute" style="top:30%;left:27%">
                        <div class="w-8 h-8 bg-amber-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center -translate-x-1/2 -translate-y-1/2 ring-4 ring-amber-100">
                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="absolute" style="top:55%;left:65%">
                        <div class="w-8 h-8 bg-amber-500 rounded-full border-2 border-white shadow-lg flex items-center justify-center -translate-x-1/2 -translate-y-1/2 ring-4 ring-amber-100">
                            <div class="w-2.5 h-2.5 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="absolute" style="top:22%;left:62%">
                        <div class="w-6 h-6 bg-amber-400 rounded-full border-2 border-white shadow-md flex items-center justify-center -translate-x-1/2 -translate-y-1/2">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="absolute" style="top:72%;left:37%">
                        <div class="w-6 h-6 bg-amber-400 rounded-full border-2 border-white shadow-md flex items-center justify-center -translate-x-1/2 -translate-y-1/2">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="absolute" style="top:44%;left:48%">
                        <div class="w-6 h-6 bg-gray-400 rounded-full border-2 border-white shadow-md flex items-center justify-center -translate-x-1/2 -translate-y-1/2">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                        </div>
                    </div>
                    <div class="absolute" style="top:67%;left:75%">
                        <div class="w-5 h-5 bg-gray-300 rounded-full border-2 border-white shadow flex items-center justify-center -translate-x-1/2 -translate-y-1/2">
                            <div class="w-1.5 h-1.5 bg-white rounded-full"></div>
                        </div>
                    </div>

                </div>

                {{-- Chip "próximamente" --}}
                <div class="absolute bottom-4 inset-x-0 flex justify-center">
                    <span class="px-4 py-2 bg-white/90 backdrop-blur-sm rounded-full text-xs font-semibold text-gray-600 shadow-sm border border-gray-100">
                        Mapa interactivo — próximamente
                    </span>
                </div>

            </div>
        </div>

    </div>
</section>

{{-- ============================================================ --}}
{{-- EXPLORAR POR CATEGORÍA                                        --}}
{{-- ============================================================ --}}
<section class="bg-gray-50 border-t border-gray-100 py-12 sm:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-xl sm:text-2xl font-bold text-gray-900">Explorar por categoría</h2>
            <a href="{{ route('categorias.index') }}"
               class="text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors">
                Ver todas →
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categorias as $categoria)
            <a href="{{ route('categorias.show', $categoria) }}"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all p-5 flex flex-col">

                {{-- Número decorativo grande --}}
                <span class="text-5xl font-extrabold text-gray-100 leading-none mb-2 self-start group-hover:text-amber-100 transition-colors select-none">
                    {{ $categoria->negocios_count }}
                </span>

                {{-- Icono --}}
                <div class="w-10 h-10 rounded-xl bg-amber-50 group-hover:bg-amber-100 flex items-center justify-center mb-3 transition-colors">
                    <x-cat-icon :name="$categoria->icono" class="w-5 h-5 text-amber-500" />
                </div>

                {{-- Nombre y cantidad --}}
                <p class="font-semibold text-gray-800 text-sm leading-tight group-hover:text-amber-700 transition-colors">
                    {{ $categoria->nombre }}
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $categoria->negocios_count }} {{ $categoria->negocios_count === 1 ? 'negocio' : 'negocios' }}
                </p>

            </a>
            @endforeach
        </div>

    </div>
</section>

{{-- ============================================================ --}}
{{-- CTA                                                           --}}
{{-- ============================================================ --}}
<section class="bg-white border-t border-gray-100 py-14 sm:py-20">
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
