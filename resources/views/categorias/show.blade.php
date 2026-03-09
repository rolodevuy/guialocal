@extends('layouts.app')

@section('title', $categoria->nombre . ' — Guía Local')
@section('description', $categoria->descripcion
    ? Str::limit($categoria->descripcion, 155)
    : 'Negocios de ' . $categoria->nombre . ' en tu barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('categorias.index') }}" class="hover:text-amber-600 transition-colors">Categorías</a>
        @if($categoria->sector)
        <span>›</span>
        <a href="{{ route('sectores.show', $categoria->sector) }}" class="hover:text-amber-600 transition-colors">{{ $categoria->sector->nombre }}</a>
        @endif
        <span>›</span>
        <span class="text-gray-600">{{ $categoria->nombre }}</span>
    </nav>

    {{-- Header categoría --}}
    <div class="mb-8 flex items-center gap-4">
        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center shrink-0 text-amber-500">
            <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-8 h-8" />
        </div>
        <div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $categoria->nombre }}</h1>
        @if($categoria->descripcion)
            <p class="text-gray-500 mt-2 text-base">{{ $categoria->descripcion }}</p>
        @endif
        <p class="text-sm text-gray-400 mt-1">
            {{ $fichas->total() }} {{ $fichas->total() === 1 ? 'negocio' : 'negocios' }} encontrados
        </p>
        </div>
    </div>

    {{-- Filtro por zona --}}
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('categorias.show', $categoria) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ !$zonaId ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Todas
        </a>
        @foreach($zonas as $zona)
        <a href="{{ route('categorias.show', $categoria) }}?zona={{ $zona->id }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ $zonaId == $zona->id ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $zona->nombre }}
        </a>
        @endforeach
    </div>

    {{-- Grid de fichas --}}
    @if($fichas->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($fichas as $ficha)
            <a href="{{ route('negocios.show', $ficha->lugar) }}"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">

                {{-- Imagen --}}
                <div class="h-40 bg-amber-50 overflow-hidden relative">
                    @php $portadaUrl = $ficha->getPortadaUrl(); @endphp
                    @if($portadaUrl)
                        <img src="{{ $portadaUrl }}"
                             alt="{{ $ficha->lugar->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-amber-200" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                    @endif
                    @if($ficha->featured)
                        <span class="absolute top-2 right-2 bg-amber-400 text-white text-xs font-semibold px-2 py-0.5 rounded-full">★ Destacado</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col flex-1">
                    <h2 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors leading-snug">
                        {{ $ficha->lugar->nombre }}
                    </h2>
                    @if($ficha->descripcion)
                        <p class="text-sm text-gray-500 mt-1 leading-relaxed line-clamp-2">{{ $ficha->descripcion }}</p>
                    @endif
                    <div class="mt-auto pt-3 flex items-center gap-2 flex-wrap">
                        @if($ficha->lugar->zona)
                            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                                {{ $ficha->lugar->zona->nombre }}
                            </span>
                        @endif
                        @if($ficha->telefono)
                            <span class="text-xs text-gray-400">{{ $ficha->telefono }}</span>
                        @endif
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($fichas->hasPages())
            <div class="mt-10">
                {{ $fichas->links() }}
            </div>
        @endif

    @else
        <div class="text-center py-20 text-gray-400">
            <svg class="w-14 h-14 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-lg font-medium text-gray-500">No hay negocios en esta categoría todavía.</p>
            <a href="{{ route('negocios.index') }}" class="mt-4 inline-block text-amber-600 hover:underline text-sm font-medium">
                Ver todos los negocios →
            </a>
        </div>
    @endif

</div>
@endsection
