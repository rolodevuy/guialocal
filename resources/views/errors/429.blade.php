@extends('layouts.app')

@section('title', 'Demasiados intentos — Guía Local')
@section('description', 'Esperá unos minutos antes de intentar de nuevo.')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">

    <p class="text-8xl font-extrabold text-amber-300 leading-none mb-2 select-none">429</p>

    <h1 class="text-2xl font-bold text-gray-800 mb-2">Demasiados intentos</h1>
    <p class="text-gray-500 mb-10 max-w-sm mx-auto leading-relaxed">
        Detectamos varios intentos seguidos desde tu conexión. Esperá unos minutos y volvé a intentarlo.
    </p>

    <div class="flex flex-wrap justify-center gap-3 text-sm">
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
           class="px-5 py-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl transition-colors">
            ← Volver
        </a>
        <a href="{{ route('home') }}"
           class="px-5 py-2 border border-gray-200 hover:border-amber-300 text-gray-600 hover:text-amber-600 rounded-xl transition-colors">
            Ir al inicio
        </a>
    </div>

</div>
@endsection
