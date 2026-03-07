@extends('layouts.app')

@section('title', $guia->titulo . ' — Guía Local')
@section('description', $guia->intro ? Str::limit($guia->intro, 155) : Str::limit(strip_tags($guia->cuerpo), 155))
@section('og_type', 'article')

@push('meta')
@if($guia->getFirstMediaUrl('portada'))
<meta property="og:image" content="{{ $guia->getFirstMediaUrl('portada') }}">
@endif
@if($guia->publicado_en)
<meta property="article:published_time" content="{{ $guia->publicado_en->toIso8601String() }}">
@endif
@endpush

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-500 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('guias.index') }}" class="hover:text-amber-500 transition-colors">Guías</a>
        <span>›</span>
        <span class="text-gray-500 truncate">{{ $guia->titulo }}</span>
    </nav>

    {{-- Meta: categoría y fecha --}}
    <div class="flex items-center gap-3 text-sm text-gray-400 mb-4">
        @if($guia->categoria)
            <a href="{{ route('categorias.show', $guia->categoria) }}"
               class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full font-medium hover:bg-amber-200 transition-colors text-xs">
                {{ $guia->categoria->nombre }}
            </a>
        @endif
        @if($guia->publicado_en)
            <span>{{ $guia->publicado_en->translatedFormat('j \d\e F \d\e Y') }}</span>
        @endif
        <span class="text-gray-300">·</span>
        <span>{{ $guia->negocios->count() }} {{ Str::plural('negocio', $guia->negocios->count()) }}</span>
    </div>

    {{-- Título --}}
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 leading-tight mb-4">
        {{ $guia->titulo }}
    </h1>

    {{-- Intro --}}
    @if($guia->intro)
    <p class="text-lg text-gray-500 leading-relaxed mb-6 border-l-4 border-amber-400 pl-4">
        {{ $guia->intro }}
    </p>
    @endif

    {{-- Portada --}}
    @if($guia->getFirstMediaUrl('portada'))
    <div class="mb-8 rounded-xl overflow-hidden">
        <img src="{{ $guia->getFirstMediaUrl('portada') }}"
             alt="{{ $guia->titulo }}"
             class="w-full object-cover max-h-80">
    </div>
    @endif

    {{-- Cuerpo editorial --}}
    @if($guia->cuerpo)
    <div class="prose prose-gray prose-amber max-w-none mb-10
                prose-headings:font-bold prose-headings:text-gray-800
                prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline
                prose-blockquote:border-amber-400 prose-blockquote:text-gray-500">
        {!! $guia->cuerpo !!}
    </div>
    @endif

    {{-- Negocios incluidos --}}
    @if($guia->negocios->isNotEmpty())
    <div class="mt-4">
        <h2 class="text-xl font-bold text-gray-800 mb-4">
            Negocios en esta guía
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($guia->negocios as $negocio)
            <a href="{{ route('negocios.show', $negocio->slug) }}"
               class="group flex items-center gap-4 bg-white border border-gray-100 rounded-xl p-4 hover:shadow-md hover:border-amber-200 transition-all">

                {{-- Imagen del negocio --}}
                <div class="shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-amber-50">
                    @if($negocio->getFirstMediaUrl('portada'))
                        <img src="{{ $negocio->getFirstMediaUrl('portada') }}"
                             alt="{{ $negocio->nombre }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-amber-50 to-amber-100">
                            <x-cat-icon :name="$negocio->categoria?->icono" class="w-7 h-7 text-amber-300"/>
                        </div>
                    @endif
                </div>

                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm truncate">
                        {{ $negocio->nombre }}
                    </h3>
                    <div class="flex items-center gap-2 mt-1">
                        @if($negocio->categoria)
                            <span class="text-xs text-amber-600">{{ $negocio->categoria->nombre }}</span>
                        @endif
                        @if($negocio->zona)
                            <span class="text-xs text-gray-300">·</span>
                            <span class="text-xs text-gray-400">{{ $negocio->zona->nombre }}</span>
                        @endif
                    </div>
                    @if($negocio->direccion)
                        <p class="text-xs text-gray-400 mt-0.5 truncate">{{ $negocio->direccion }}</p>
                    @endif
                </div>

                <svg class="w-4 h-4 text-gray-300 group-hover:text-amber-400 shrink-0 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Volver --}}
    <div class="mt-10 pt-6 border-t border-gray-100">
        <a href="{{ route('guias.index') }}" class="text-sm text-gray-400 hover:text-amber-500 transition-colors">
            ← Volver a guías
        </a>
    </div>

</div>
@endsection
