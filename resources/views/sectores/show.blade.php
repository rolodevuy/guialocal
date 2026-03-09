@extends('layouts.app')

@section('title', $sector->nombre . ' — Guía Local')
@section('description', $sector->descripcion ?: 'Negocios de ' . $sector->nombre)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('categorias.index') }}" class="hover:text-amber-600 transition-colors">Categorías</a>
        <span>›</span>
        <span class="text-gray-600">{{ $sector->nombre }}</span>
    </nav>

    {{-- Header --}}
    <div class="mb-8 flex items-center gap-4">
        @if($sector->icono)
        <div class="w-14 h-14 {{ $sector->color('bg', 'bg-gray-100') }} rounded-2xl flex items-center justify-center {{ $sector->color('icon', 'text-gray-500') }}">
            <x-cat-icon :name="$sector->icono" class="w-8 h-8" />
        </div>
        @endif
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $sector->nombre }}</h1>
            @if($sector->descripcion)
                <p class="text-gray-500 mt-1">{{ $sector->descripcion }}</p>
            @endif
        </div>
    </div>

    {{-- Grid de categorías --}}
    @if($categorias->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($categorias as $categoria)
            <a href="{{ route('categorias.show', $categoria) }}"
               class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md
                      hover:{{ $sector->color('border', 'border-amber-200') }} transition-all duration-200 p-3 sm:p-5 flex flex-col items-center text-center">

                <div class="mb-2 sm:mb-3 w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center {{ $sector->color('icon', 'text-amber-500') }}">
                    <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-10 h-10 sm:w-12 sm:h-12" />
                </div>

                <p class="font-semibold text-gray-800 text-xs sm:text-sm leading-tight group-hover:{{ $sector->color('text_hover', 'text-amber-700') }} transition-colors">
                    {{ $categoria->nombre }}
                </p>

                <p class="text-xs text-gray-400 mt-1">
                    {{ $categoria->negocios_count }} {{ $categoria->negocios_count === 1 ? 'negocio' : 'negocios' }}
                </p>

            </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 text-gray-400">
            <p class="text-lg">No hay categorías en este sector todavía.</p>
        </div>
    @endif

</div>
@endsection
