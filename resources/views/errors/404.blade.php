@extends('layouts.app')

@section('title', 'Página no encontrada — Guía Local')
@section('description', 'La página que buscás no existe o fue movida.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">

    <p class="text-7xl font-extrabold text-amber-400 mb-4">404</p>
    <h1 class="text-2xl font-bold text-gray-800 mb-3">Página no encontrada</h1>
    <p class="text-gray-500 mb-10 max-w-md mx-auto">
        La dirección que ingresaste no existe o el negocio fue dado de baja.
        Probá buscando desde el inicio.
    </p>

    <div class="flex flex-wrap justify-center gap-3">
        <a href="{{ route('home') }}"
           class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm transition-colors">
            Ir al inicio
        </a>
        <a href="{{ route('negocios.index') }}"
           class="px-6 py-2.5 bg-white border border-gray-200 hover:border-amber-300 text-gray-700 font-semibold rounded-xl text-sm transition-colors">
            Ver negocios
        </a>
    </div>

</div>
@endsection
