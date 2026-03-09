@extends('layouts.app')

@section('title', 'Categorías — Guía Local')
@section('description', 'Explorá todas las categorías de negocios y servicios de tu barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <span class="text-gray-600">Categorías</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Categorías</h1>
    <p class="text-gray-500 mb-8">Encontrá negocios y servicios por rubro.</p>

    <div class="space-y-10">
        @foreach($sectores as $sector)
        @if($sector->categorias->isNotEmpty())
        <div>
            <div class="flex items-center gap-2 mb-4">
                <span class="w-8 h-8 {{ $sector->color('bg', 'bg-gray-100') }} rounded-lg flex items-center justify-center {{ $sector->color('icon', 'text-gray-500') }}">
                    <x-cat-icon :name="$sector->icono ?? 'default'" class="w-5 h-5" />
                </span>
                <h2 class="text-lg font-bold text-gray-800">{{ $sector->nombre }}</h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($sector->categorias as $categoria)
                <a href="{{ route('categorias.show', $categoria) }}"
                   class="group bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md
                          hover:{{ $sector->color('border', 'border-amber-200') }} transition-all text-center flex flex-col items-center gap-3">
                    <div class="w-12 h-12 {{ $sector->color('bg_light', 'bg-amber-50') }} rounded-xl flex items-center justify-center
                                group-hover:{{ $sector->color('bg', 'bg-amber-100') }} transition-colors {{ $sector->color('icon', 'text-amber-500') }}">
                        <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-7 h-7" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 group-hover:{{ $sector->color('text', 'text-amber-600') }} transition-colors text-sm leading-tight">
                            {{ $categoria->nombre }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $categoria->negocios_count }} {{ $categoria->negocios_count === 1 ? 'negocio' : 'negocios' }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        @endforeach

        {{-- Categorías sin sector (fallback) --}}
        @if($sinSector->isNotEmpty())
        <div>
            <h2 class="text-lg font-bold text-gray-800 mb-4">Otras categorías</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($sinSector as $categoria)
                <a href="{{ route('categorias.show', $categoria) }}"
                   class="group bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all text-center flex flex-col items-center gap-3">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center group-hover:bg-amber-100 transition-colors text-amber-500">
                        <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-7 h-7" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-tight">
                            {{ $categoria->nombre }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $categoria->negocios_count }} {{ $categoria->negocios_count === 1 ? 'negocio' : 'negocios' }}
                        </p>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    @if($sectores->every(fn ($s) => $s->categorias->isEmpty()) && $sinSector->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <p class="text-lg">No hay categorías disponibles.</p>
        </div>
    @endif

</div>
@endsection
