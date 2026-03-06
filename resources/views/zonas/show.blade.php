@extends('layouts.app')

@section('title', $zona->nombre . ' — Guía Local')
@section('description', 'Negocios y servicios en ' . $zona->nombre . '. Encontrá lo que necesitás cerca tuyo.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('negocios.index') }}" class="hover:text-amber-600 transition-colors">Negocios</a>
        <span>›</span>
        <span class="text-gray-600">{{ $zona->nombre }}</span>
    </nav>

    {{-- Header zona --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                </svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $zona->nombre }}</h1>
        </div>
        <p class="text-sm text-gray-400 mt-1 ml-12">
            {{ $negocios->total() }} {{ $negocios->total() === 1 ? 'negocio' : 'negocios' }} en esta zona
        </p>
    </div>

    {{-- Filtro por categoría (solo si hay más de una) --}}
    @if($categorias->count() > 1)
    <div class="mb-6 flex flex-wrap items-center gap-2">
        <a href="{{ route('zonas.show', $zona) }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ !$categoriaId ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Todas
        </a>
        @foreach($categorias as $categoria)
        <a href="{{ route('zonas.show', $zona) }}?categoria={{ $categoria->id }}"
           class="px-4 py-1.5 rounded-full text-sm font-medium transition-colors
                  {{ $categoriaId == $categoria->id ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            {{ $categoria->nombre }}
        </a>
        @endforeach
    </div>
    @endif

    {{-- Grid de negocios --}}
    @if($negocios->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($negocios as $negocio)
            <a href="{{ route('negocios.show', $negocio) }}"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-shadow overflow-hidden flex flex-col">

                {{-- Imagen --}}
                <div class="h-40 overflow-hidden relative">
                    @if($negocio->getFirstMediaUrl('portada'))
                        <img src="{{ $negocio->getFirstMediaUrl('portada') }}"
                             alt="{{ $negocio->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-amber-50 to-amber-100 flex items-center justify-center">
                            <x-cat-icon :name="$negocio->categoria->icono ?? 'default'" class="w-14 h-14 text-amber-300" />
                        </div>
                    @endif
                    @if($negocio->featured)
                        <span class="absolute top-2 right-2 bg-amber-400 text-white text-xs font-semibold px-2 py-0.5 rounded-full">★ Destacado</span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col flex-1">
                    <h2 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors leading-snug">
                        {{ $negocio->nombre }}
                    </h2>
                    @if($negocio->descripcion)
                        <p class="text-sm text-gray-500 mt-1 leading-relaxed line-clamp-2">{{ $negocio->descripcion }}</p>
                    @endif
                    <div class="mt-auto pt-3">
                        <span class="text-xs bg-amber-50 text-amber-600 px-2 py-0.5 rounded-full">
                            {{ $negocio->categoria->nombre }}
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($negocios->hasPages())
            <div class="mt-10">
                {{ $negocios->links() }}
            </div>
        @endif

    @else
        <div class="text-center py-20 text-gray-400">
            <svg class="w-14 h-14 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
            </svg>
            <p class="text-lg font-medium text-gray-500">No hay negocios en {{ $zona->nombre }} todavía.</p>
            <a href="{{ route('negocios.index') }}" class="mt-4 inline-block text-amber-600 hover:underline text-sm font-medium">
                Ver todos los negocios →
            </a>
        </div>
    @endif

</div>
@endsection
