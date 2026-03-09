@extends('layouts.app')

@section('title', 'Baja del newsletter — Guía Local')

@section('content')
<div class="max-w-lg mx-auto px-4 py-20 text-center">
    <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-6">
        <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-gray-800 mb-3">Te diste de baja</h1>
    <p class="text-gray-500 text-sm mb-8 leading-relaxed">
        Ya no vas a recibir el newsletter de Guía Local.<br>
        Si cambiás de idea, podés suscribirte de nuevo desde la página principal.
    </p>

    <a href="{{ route('home') }}"
       class="inline-block px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-colors">
        Volver al inicio
    </a>
</div>
@endsection
