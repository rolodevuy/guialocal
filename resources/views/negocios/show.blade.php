@extends('layouts.app')

@section('title', $negocio->nombre . ' — Guía Local')
@section('description', Str::limit($negocio->descripcion, 155))

@section('og_type', 'article')

@push('meta')
    @if($negocio->hasMedia('portada'))
        <meta property="og:image" content="{{ $negocio->getFirstMediaUrl('portada') }}">
    @endif
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('negocios.index') }}" class="hover:text-amber-600 transition-colors">Negocios</a>
        <span>›</span>
        <a href="{{ route('negocios.index', ['categoria' => $negocio->categoria->slug]) }}"
           class="hover:text-amber-600 transition-colors">{{ $negocio->categoria->nombre }}</a>
        <span>›</span>
        <span class="text-gray-600">{{ $negocio->nombre }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="flex-1 min-w-0">

            {{-- Imagen portada --}}
            <div class="rounded-2xl overflow-hidden bg-amber-50 mb-6 h-56 sm:h-72">
                @if($negocio->getFirstMediaUrl('portada'))
                    <img src="{{ $negocio->getFirstMediaUrl('portada') }}"
                         alt="{{ $negocio->nombre }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-amber-200" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Encabezado --}}
            <div class="flex flex-wrap items-start gap-3 mb-4">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 leading-tight">
                        {{ $negocio->nombre }}
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <a href="{{ route('negocios.index', ['categoria' => $negocio->categoria->slug]) }}"
                           class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-medium hover:bg-amber-200 transition-colors">
                            {{ $negocio->categoria->nombre }}
                        </a>
                        <a href="{{ route('zonas.show', $negocio->zona) }}"
                           class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium hover:bg-gray-200 transition-colors">
                            {{ $negocio->zona->nombre }}
                        </a>
                        @if($negocio->featured)
                            <span class="text-xs bg-amber-400 text-white px-2.5 py-1 rounded-full font-medium">★ Destacado</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed mb-8">
                <p>{{ $negocio->descripcion }}</p>
            </div>

            {{-- Galería --}}
            @php $galeria = $negocio->getMedia('galeria'); @endphp
            @if($galeria->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Galería</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($galeria as $imagen)
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100">
                        <img src="{{ $imagen->getUrl() }}"
                             alt="{{ $negocio->nombre }}"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- SIDEBAR CONTACTO --}}
        <aside class="lg:w-72 shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">

                {{-- Contacto --}}
                <div>
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Contacto</h2>
                    <ul class="space-y-3">
                        @if($negocio->telefono)
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </span>
                            <a href="tel:{{ $negocio->telefono }}" class="text-sm text-gray-700 hover:text-amber-600 transition-colors">
                                {{ $negocio->telefono }}
                            </a>
                        </li>
                        @endif

                        @if($negocio->email)
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <a href="mailto:{{ $negocio->email }}" class="text-sm text-gray-700 hover:text-amber-600 transition-colors truncate">
                                {{ $negocio->email }}
                            </a>
                        </li>
                        @endif

                        @if($negocio->sitio_web)
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                                </svg>
                            </span>
                            <a href="{{ $negocio->sitio_web }}" target="_blank" rel="noopener noreferrer"
                               class="text-sm text-amber-600 hover:underline truncate">
                                {{ parse_url($negocio->sitio_web, PHP_URL_HOST) ?: $negocio->sitio_web }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Dirección --}}
                @if($negocio->direccion)
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dirección</h2>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </span>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $negocio->direccion }}</p>
                    </div>
                </div>
                @endif

                {{-- Horarios --}}
                @if(!empty($negocio->horarios))
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Horarios</h2>
                    <ul class="space-y-1.5">
                        @foreach($negocio->horarios as $periodo => $horario)
                        <li class="flex items-start justify-between gap-2 text-sm">
                            <span class="text-gray-500 shrink-0">{{ $periodo }}</span>
                            <span class="text-gray-700 text-right">{{ $horario }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- CTA contacto --}}
                <div class="border-t border-gray-100 pt-5">
                    <a href="{{ route('contacto.show') }}"
                       class="block w-full text-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl transition-colors">
                        Registrar mi negocio
                    </a>
                </div>

            </div>
        </aside>

    </div>

    {{-- Volver al listado --}}
    <div class="mt-10 pt-6 border-t border-gray-100">
        <a href="{{ route('negocios.index', ['categoria' => $negocio->categoria->slug]) }}"
           class="text-sm text-amber-600 hover:text-amber-700 font-medium inline-flex items-center gap-1">
            ← Más negocios de {{ $negocio->categoria->nombre }}
        </a>
    </div>

</div>
@endsection
