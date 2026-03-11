@extends('layouts.app')

@section('title', 'Reclamar ' . $lugar->nombre . ' — Guía Local')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('negocios.show', $lugar) }}" class="hover:text-amber-600 transition-colors">{{ $lugar->nombre }}</a>
        <span>›</span>
        <span class="text-gray-600">Reclamar negocio</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Reclamá tu negocio</h1>
    <p class="text-gray-500 mb-6">
        Estás reclamando <strong class="text-gray-700">{{ $lugar->nombre }}</strong>.
        Para verificar que sos el titular, necesitamos tu constancia de inscripción en DGI.
    </p>

    {{-- Gratuito --}}
    <div class="mb-6 flex items-center gap-2 bg-green-50 border border-green-200 rounded-xl px-4 py-3">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
        </svg>
        <p class="text-sm text-green-700 font-medium">Este proceso es 100% gratuito. No tiene ningún costo reclamar tu negocio.</p>
    </div>

    {{-- Info box --}}
    <div class="mb-6 flex items-start gap-3 bg-blue-50 border border-blue-200 rounded-xl px-4 py-3">
        <svg class="w-5 h-5 text-blue-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="text-sm text-blue-700">
            <p class="font-medium mb-1">¿Cómo obtener la constancia de RUT?</p>
            <p>Podés descargarla desde <a href="https://www.dgi.gub.uy" target="_blank" rel="noopener" class="underline font-medium hover:text-blue-900">dgi.gub.uy</a> en la sección de consulta de RUT. Es un documento que muestra tu número de RUT y razón social.</p>
        </div>
    </div>

    @if($claimPendiente)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl px-4 py-3">
            <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
            <p class="text-sm text-amber-700 font-medium">Ya hay una solicitud pendiente de revisión para este negocio. Te contactaremos pronto.</p>
        </div>
    @else
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

        <form action="{{ route('negocios.claim.store', $lugar) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            {{-- Nombre --}}
            <div>
                <label for="nombre_completo" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Nombre completo <span class="text-red-400">*</span>
                </label>
                <input type="text" id="nombre_completo" name="nombre_completo" value="{{ old('nombre_completo') }}"
                       placeholder="Tu nombre y apellido"
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400 transition-colors @error('nombre_completo') border-red-300 @enderror">
                @error('nombre_completo')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Email de contacto <span class="text-red-400">*</span>
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}"
                       placeholder="tu@email.com"
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400 transition-colors @error('email') border-red-300 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Teléfono --}}
            <div>
                <label for="telefono" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Teléfono <span class="text-red-400">*</span>
                </label>
                <input type="tel" id="telefono" name="telefono" value="{{ old('telefono') }}"
                       placeholder="099 123 456"
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400 transition-colors @error('telefono') border-red-300 @enderror">
                @error('telefono')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- RUT --}}
            <div>
                <label for="rut_numero" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Número de RUT <span class="text-red-400">*</span>
                </label>
                <input type="text" id="rut_numero" name="rut_numero" value="{{ old('rut_numero') }}"
                       placeholder="123456789012" maxlength="12" inputmode="numeric"
                       class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400 transition-colors @error('rut_numero') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-400">12 dígitos, sin guiones ni puntos.</p>
                @error('rut_numero')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Constancia --}}
            <div>
                <label for="constancia" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Constancia de RUT <span class="text-red-400">*</span>
                </label>
                <input type="file" id="constancia" name="constancia" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100 transition-colors @error('constancia') border-red-300 @enderror">
                <p class="mt-1 text-xs text-gray-400">JPG, PNG o PDF. Máximo 5 MB.</p>
                @error('constancia')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mensaje --}}
            <div>
                <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Mensaje adicional <span class="text-gray-400">(opcional)</span>
                </label>
                <textarea id="mensaje" name="mensaje" rows="3"
                          placeholder="¿Algo que quieras agregar?"
                          class="w-full rounded-xl border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400 transition-colors @error('mensaje') border-red-300 @enderror">{{ old('mensaje') }}</textarea>
                @error('mensaje')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Por qué lo pedimos --}}
            <div class="bg-gray-50 rounded-xl px-4 py-3 text-xs text-gray-500 leading-relaxed">
                Verificamos la titularidad para proteger tu negocio y que nadie más pueda modificar tu ficha.
                Revisaremos tu solicitud y te responderemos en <strong>24-48 horas</strong>.
            </div>

            <button type="submit"
                    class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-3 px-6 rounded-xl transition-colors text-sm">
                Enviar solicitud
            </button>
        </form>
    @endif
</div>
@endsection
