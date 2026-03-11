@extends('layouts.app')

@section('title', 'Contacto — Guía Local')
@section('description', 'Contactanos o registrá tu negocio en la guía local del barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <span class="text-gray-600">Contacto</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-10">

        {{-- FORMULARIO --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Contacto</h1>
            <p class="text-gray-500 mb-8">
                ¿Querés registrar tu negocio o tenés alguna consulta? Completá el formulario y te responderemos a la brevedad.
            </p>

            {{-- Flash success --}}
            @if(session('success'))
                <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
                    <svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <p class="text-sm text-green-700 font-medium">{{ session('success') }}</p>
                </div>
            @endif

            {{-- Errores globales --}}
            @if($errors->any())
                <div class="mb-6 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
                    <p class="text-sm font-medium text-red-700 mb-1">Por favor corregí los siguientes errores:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        @foreach($errors->all() as $error)
                            <li class="text-sm text-red-600">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('contacto.store') }}" method="POST" class="space-y-5">
                @csrf

                {{-- Nombre --}}
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Nombre <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre') }}"
                        placeholder="Tu nombre completo"
                        class="w-full px-4 py-3 rounded-xl border text-sm text-gray-800 placeholder-gray-400 outline-none transition-colors
                               {{ $errors->has('nombre') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 bg-white focus:border-amber-400' }}"
                    >
                    @error('nombre')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email <span class="text-red-400">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="tu@email.com"
                        class="w-full px-4 py-3 rounded-xl border text-sm text-gray-800 placeholder-gray-400 outline-none transition-colors
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 bg-white focus:border-amber-400' }}"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mensaje --}}
                <div>
                    <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Mensaje <span class="text-red-400">*</span>
                    </label>
                    <textarea
                        id="mensaje"
                        name="mensaje"
                        rows="5"
                        placeholder="Contanos en qué podemos ayudarte o describí tu negocio..."
                        class="w-full px-4 py-3 rounded-xl border text-sm text-gray-800 placeholder-gray-400 outline-none transition-colors resize-none
                               {{ $errors->has('mensaje') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 bg-white focus:border-amber-400' }}"
                    >{{ old('mensaje', match(request('asunto')) {
                        'upgrade-premium'  => 'Hola, me interesa conocer más sobre el plan Premium para mi negocio.',
                        'upgrade-basico'   => 'Hola, me interesa conocer más sobre el plan Básico para mi negocio.',
                        'consulta-planes'  => 'Hola, me gustaría conocer más sobre los planes disponibles para mi negocio.',
                        'alta-negocio'     => 'Hola, me gustaría registrar mi negocio en la guía.',
                        default            => '',
                    }) }}</textarea>
                    @error('mensaje')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                        class="w-full sm:w-auto px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white font-semibold rounded-xl text-sm transition-colors shadow-sm">
                    Enviar mensaje
                </button>
            </form>
        </div>

        {{-- SIDEBAR INFO --}}
        <aside class="lg:w-72 shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">

                <div>
                    <h2 class="text-sm font-semibold text-gray-800 mb-3">¿Para qué podés contactarnos?</h2>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">✓</span>
                            Registrar tu negocio en la guía
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">✓</span>
                            Actualizar datos de un negocio
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">✓</span>
                            Reportar información incorrecta
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="text-amber-500 mt-0.5">✓</span>
                            Consultas generales
                        </li>
                    </ul>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-sm font-semibold text-gray-800 mb-3">Tiempo de respuesta</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Respondemos consultas en un plazo de 24 a 48 horas hábiles.
                    </p>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <p class="text-xs text-gray-400 leading-relaxed">
                        Tu información es confidencial y no será compartida con terceros.
                    </p>
                </div>

            </div>
        </aside>

    </div>

</div>
@endsection
