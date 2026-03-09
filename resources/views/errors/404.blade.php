@extends('layouts.app')

@section('title', 'Página no encontrada — Guía Local')
@section('description', 'La página que buscás no existe o fue movida.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">

    {{-- Número grande --}}
    <p class="text-8xl font-extrabold text-amber-300 leading-none mb-2 select-none">404</p>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">No encontramos lo que buscás</h1>
    <p class="text-gray-500 mb-10 max-w-sm mx-auto leading-relaxed">
        La página no existe, el negocio fue dado de baja o el link está mal escrito.
    </p>

    {{-- Buscador integrado --}}
    <form action="{{ route('negocios.index') }}" method="GET" class="mb-8">
        <label class="block text-sm font-semibold text-gray-600 mb-3">
            ¿Qué negocio buscabas?
        </label>
        <div class="flex gap-2 max-w-sm mx-auto">
            <input
                type="text"
                name="busqueda"
                placeholder="Ej: panadería, farmacia…"
                autofocus
                class="flex-1 px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-transparent"
            >
            <button type="submit"
                class="px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm transition-colors">
                Buscar
            </button>
        </div>
    </form>

    {{-- Links alternativos --}}
    <div class="flex flex-wrap justify-center gap-3 text-sm">
        <a href="{{ route('home') }}"
           class="px-5 py-2 border border-gray-200 hover:border-amber-300 text-gray-600 hover:text-amber-600 rounded-xl transition-colors">
            ← Ir al inicio
        </a>
        <a href="{{ route('negocios.index') }}"
           class="px-5 py-2 border border-gray-200 hover:border-amber-300 text-gray-600 hover:text-amber-600 rounded-xl transition-colors">
            Ver todos los negocios
        </a>
        <a href="{{ route('mapa.index') }}"
           class="px-5 py-2 border border-gray-200 hover:border-amber-300 text-gray-600 hover:text-amber-600 rounded-xl transition-colors">
            Ver mapa
        </a>
    </div>

</div>
@endsection
