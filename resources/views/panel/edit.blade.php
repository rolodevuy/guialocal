@extends('layouts.panel')
@section('title', 'Editar datos — ' . $ficha->lugar->nombre)

@section('content')

    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('panel.index') }}"
           class="text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h1 class="text-xl font-bold text-gray-800">Editar datos del negocio</h1>
    </div>

    {{-- Flash guardado --}}
    @if(session('guardado'))
    <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
        <svg class="w-5 h-5 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        ¡Cambios guardados correctamente!
    </div>
    @endif

    <form method="POST" action="{{ route('panel.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Descripción --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Descripción del negocio</h2>
            <textarea
                name="descripcion"
                rows="5"
                placeholder="Contá qué hace tu negocio, qué lo hace especial, qué ofrecés..."
                class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none
                       {{ $errors->has('descripcion') ? 'border-red-300' : '' }}"
            >{{ old('descripcion', $ficha->descripcion) }}</textarea>
            <p class="text-xs text-gray-400 mt-1.5">Máx. 2000 caracteres. Aparece en tu ficha pública.</p>
            @error('descripcion') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Contacto --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-4">Datos de contacto</h2>
            <div class="grid sm:grid-cols-2 gap-4">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Teléfono</label>
                    <input type="text" name="telefono"
                           value="{{ old('telefono', $ficha->telefono) }}"
                           placeholder="+598 99 000 000"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    @error('telefono') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Email de contacto</label>
                    <input type="email" name="email"
                           value="{{ old('email', $ficha->email) }}"
                           placeholder="hola@minegocio.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 mb-1.5">Sitio web</label>
                    <input type="text" name="sitio_web"
                           value="{{ old('sitio_web', $ficha->sitio_web) }}"
                           placeholder="www.minegocio.com"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('sitio_web') ? 'border-red-300' : '' }}">
                    @error('sitio_web') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Redes sociales --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-700 mb-1">Redes sociales</h2>
            <p class="text-xs text-gray-400 mb-4">Pegá la URL completa de tu perfil.</p>

            @php
                $redesActuales = collect($ficha->redes_sociales ?? [])->keyBy('red');
            @endphp

            <div class="space-y-3">

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                          style="background:#E1306C">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </span>
                    <input type="url" name="instagram"
                           value="{{ old('instagram', $redesActuales['instagram']['url'] ?? '') }}"
                           placeholder="https://instagram.com/tunegocio"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('instagram') ? 'border-red-300' : '' }}">
                    @error('instagram') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                          style="background:#1877F2">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </span>
                    <input type="url" name="facebook"
                           value="{{ old('facebook', $redesActuales['facebook']['url'] ?? '') }}"
                           placeholder="https://facebook.com/tunegocio"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('facebook') ? 'border-red-300' : '' }}">
                    @error('facebook') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <span class="w-8 h-8 flex items-center justify-center rounded-lg shrink-0"
                          style="background:#25D366">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </span>
                    <input type="url" name="whatsapp"
                           value="{{ old('whatsapp', $redesActuales['whatsapp']['url'] ?? '') }}"
                           placeholder="https://wa.me/59899000000"
                           class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                  {{ $errors->has('whatsapp') ? 'border-red-300' : '' }}">
                    @error('whatsapp') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>
        </div>

        {{-- Nota sobre lo que maneja el admin --}}
        <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4 text-xs text-blue-700">
            <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>Para cambiar <strong>fotos, horarios, dirección o plan</strong>, contactá al equipo de Guía Local.</p>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('panel.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600 transition-colors">
                ← Cancelar
            </a>
            <button type="submit"
                    class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-semibold text-sm rounded-xl transition-colors shadow-sm">
                Guardar cambios
            </button>
        </div>

    </form>

@endsection
