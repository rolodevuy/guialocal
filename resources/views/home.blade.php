@extends('layouts.app')

@section('title', 'Guía Local — Tu barrio en un solo lugar')
@section('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')

@section('content')

{{-- HERO --}}
<section class="bg-gradient-to-br from-amber-400 to-amber-500 text-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
        <h1 class="text-4xl sm:text-5xl font-bold mb-4 leading-tight">
            Tu barrio en un solo lugar
        </h1>
        <p class="text-amber-100 text-lg mb-10 max-w-xl mx-auto">
            Encontrá restaurantes, farmacias, comercios y servicios cerca tuyo.
        </p>

        {{-- Buscador --}}
        <form action="{{ route('negocios.index') }}" method="GET" class="max-w-xl mx-auto">
            <div class="flex gap-2 bg-white rounded-xl p-2 shadow-lg">
                <input
                    type="text"
                    name="q"
                    placeholder="Buscar negocio, categoría..."
                    class="flex-1 px-4 py-2 text-gray-800 bg-transparent outline-none text-sm"
                    value="{{ request('q') }}"
                >
                <button type="submit"
                        class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-medium text-sm transition-colors">
                    Buscar
                </button>
            </div>
        </form>
    </div>
</section>

{{-- NEGOCIOS DESTACADOS --}}
@if($destacados->isNotEmpty())
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Negocios destacados</h2>
        <a href="{{ route('negocios.index') }}" class="text-sm text-amber-600 hover:text-amber-700 font-medium">
            Ver todos →
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($destacados as $negocio)
        <a href="{{ route('negocios.show', $negocio) }}"
           class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">

            {{-- Imagen portada --}}
            <div class="h-40 bg-amber-50 overflow-hidden">
                @if($negocio->getFirstMediaUrl('portada'))
                    <img src="{{ $negocio->getFirstMediaUrl('portada') }}"
                         alt="{{ $negocio->nombre }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="w-full h-full flex items-center justify-center text-amber-300">
                        <svg class="w-14 h-14" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                @endif
            </div>

            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors leading-tight">
                        {{ $negocio->nombre }}
                    </h3>
                    @if($negocio->plan === 'premium')
                        <span class="shrink-0 text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">Premium</span>
                    @endif
                </div>
                <p class="text-xs text-gray-500 mb-3 line-clamp-2">{{ $negocio->descripcion }}</p>
                <div class="flex items-center gap-3 text-xs text-gray-400">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M17 13h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/></svg>
                        {{ $negocio->categoria->nombre }}
                    </span>
                    <span>·</span>
                    <span>{{ $negocio->zona->nombre }}</span>
                </div>
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif

{{-- CATEGORÍAS --}}
<section class="bg-white border-t border-gray-100">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <h2 class="text-2xl font-bold text-gray-800 mb-8">Explorar por categoría</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categorias as $categoria)
            <a href="{{ route('categorias.show', $categoria) }}"
               class="group flex flex-col items-center gap-3 p-5 bg-gray-50 rounded-xl border border-gray-100 hover:border-amber-300 hover:bg-amber-50 transition-all text-center">
                <div class="w-12 h-12 rounded-full bg-amber-100 group-hover:bg-amber-200 flex items-center justify-center transition-colors">
                    <span class="text-amber-600 text-lg font-semibold">
                        {{ strtoupper(substr($categoria->nombre, 0, 1)) }}
                    </span>
                </div>
                <div>
                    <div class="font-medium text-gray-800 text-sm leading-tight">{{ $categoria->nombre }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $categoria->negocios_count }} negocios</div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="bg-gradient-to-r from-amber-500 to-amber-400 rounded-2xl p-8 sm:p-12 text-center text-white">
        <h2 class="text-2xl sm:text-3xl font-bold mb-3">¿Tenés un negocio en el barrio?</h2>
        <p class="text-amber-100 mb-6 max-w-md mx-auto">
            Sumá tu local a la guía y llegá a más clientes de la zona. Es gratis.
        </p>
        <a href="{{ route('contacto.show') }}"
           class="inline-block px-6 py-3 bg-white text-amber-600 font-semibold rounded-xl hover:bg-amber-50 transition-colors shadow-sm">
            Registrar mi negocio
        </a>
    </div>
</section>

@endsection
