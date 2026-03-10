@extends('layouts.app')

@section('title', $sector->nombre . ' — Guía Local')
@section('description', $sector->descripcion ?: 'Negocios de ' . $sector->nombre)
@section('og_image', asset('images/og-default.jpg'))

@section('content')

{{-- ============================================================
     HERO del sector (fondo tintado)
     ============================================================ --}}
<section class="{{ $sector->color('bg_light', 'bg-gray-50') }}">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-6 pb-10 sm:pt-8 sm:pb-14">

        {{-- Breadcrumb --}}
        <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
            <a href="{{ route('home') }}" class="hover:{{ $sector->color('text', 'text-amber-600') }} transition-colors">Inicio</a>
            <span>›</span>
            <span class="text-gray-600">{{ $sector->nombre }}</span>
        </nav>

        <div class="flex flex-col sm:flex-row sm:items-center gap-4 sm:gap-6">
            {{-- Icono grande --}}
            @if($sector->icono)
            <div class="w-16 h-16 sm:w-20 sm:h-20 {{ $sector->color('bg', 'bg-gray-100') }} rounded-2xl flex items-center justify-center {{ $sector->color('icon', 'text-gray-500') }} shrink-0">
                <x-cat-icon :name="$sector->icono" class="w-9 h-9 sm:w-11 sm:h-11" />
            </div>
            @endif
            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-gray-900 leading-tight">
                    {{ $sector->nombre }}
                </h1>
                @if($sector->descripcion)
                    <p class="text-gray-500 mt-1 sm:mt-2 text-sm sm:text-base max-w-xl">{{ $sector->descripcion }}</p>
                @endif
                <p class="mt-2 text-xs {{ $sector->color('text', 'text-amber-600') }} font-medium">
                    {{ $totalNegocios }} {{ $totalNegocios === 1 ? 'negocio' : 'negocios' }} · {{ $categorias->count() }} {{ $categorias->count() === 1 ? 'categoría' : 'categorías' }}
                </p>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================
     CATEGORÍAS del sector
     ============================================================ --}}
<section class="{{ $sector->color('bg_light', 'bg-gray-50') }} pb-10 sm:pb-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-4">Categorías</h2>

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
            <div class="text-center py-16 text-gray-400">
                <p class="text-lg">No hay categorías en este sector todavía.</p>
            </div>
        @endif

    </div>
</section>

{{-- ============================================================
     DESTACADOS del sector
     ============================================================ --}}
@if($destacados->isNotEmpty())
<section class="bg-white border-t border-gray-100 py-10 sm:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex items-center justify-between mb-6 sm:mb-8">
            <h2 class="text-lg sm:text-2xl font-bold text-gray-900">Destacados en {{ $sector->nombre }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($destacados as $ficha)
            <a href="{{ route('negocios.show', $ficha->lugar) }}"
               class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:{{ $sector->color('border', 'border-amber-200') }} transition-all duration-200">

                {{-- Imagen --}}
                <div class="relative h-40 sm:h-44 {{ $sector->color('bg_light', 'bg-amber-50') }} overflow-hidden shrink-0">
                    @php $portadaUrl = $ficha->getPortadaUrl(); @endphp
                    @if($portadaUrl)
                        <img src="{{ $portadaUrl }}"
                             alt="{{ $ficha->lugar->nombre }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-14 h-14 {{ $sector->color('icon', 'text-amber-200') }} opacity-40" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                        </div>
                    @endif
                    @if($ficha->plan === 'premium')
                        <span class="absolute top-2 right-2 sm:top-3 sm:right-3 text-xs font-bold {{ $sector->color('bg', 'bg-amber-500') }} text-white px-2 py-0.5 rounded-full uppercase tracking-wide shadow-sm">
                            Premium
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:{{ $sector->color('text', 'text-amber-600') }} transition-colors leading-snug">
                        {{ $ficha->lugar->nombre }}
                    </h3>
                    @if($ficha->descripcion)
                    <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-2 leading-relaxed flex-1">
                        {{ $ficha->descripcion }}
                    </p>
                    @endif
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                        <span class="text-xs text-gray-400 truncate mr-2">
                            {{ $ficha->lugar->categoria->nombre }}
                            @if($ficha->lugar->zona) · {{ $ficha->lugar->zona->nombre }} @endif
                        </span>
                        <span class="text-xs {{ $sector->color('text', 'text-amber-600') }} font-medium shrink-0">Ver →</span>
                    </div>
                </div>

            </a>
            @endforeach
        </div>

    </div>
</section>
@endif

@endsection
