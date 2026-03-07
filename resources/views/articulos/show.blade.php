@extends('layouts.app')

@section('title', $articulo->titulo . ' — Guía Local')
@section('description', $articulo->extracto ? Str::limit($articulo->extracto, 155) : Str::limit(strip_tags($articulo->cuerpo), 155))
@section('og_type', 'article')

@push('meta')
@if($articulo->getFirstMediaUrl('portada'))
<meta property="og:image" content="{{ $articulo->getFirstMediaUrl('portada') }}">
@endif
@if($articulo->publicado_en)
<meta property="article:published_time" content="{{ $articulo->publicado_en->toIso8601String() }}">
@endif
@endpush

@section('content')
<article class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-xs text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-500 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('articulos.index') }}" class="hover:text-amber-500 transition-colors">Artículos</a>
        <span>›</span>
        <span class="text-gray-500 truncate">{{ $articulo->titulo }}</span>
    </nav>

    {{-- Meta: categoría y fecha --}}
    <div class="flex items-center gap-3 text-sm text-gray-400 mb-4">
        @if($articulo->categoria)
            <a href="{{ route('categorias.show', $articulo->categoria) }}"
               class="bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full font-medium hover:bg-amber-200 transition-colors text-xs">
                {{ $articulo->categoria->nombre }}
            </a>
        @endif
        @if($articulo->publicado_en)
            <span>{{ $articulo->publicado_en->translatedFormat('j \d\e F \d\e Y') }}</span>
        @endif
    </div>

    {{-- Título --}}
    <h1 class="text-3xl sm:text-4xl font-bold text-gray-800 leading-tight mb-4">
        {{ $articulo->titulo }}
    </h1>

    {{-- Extracto --}}
    @if($articulo->extracto)
    <p class="text-lg text-gray-500 leading-relaxed mb-6 border-l-4 border-amber-400 pl-4">
        {{ $articulo->extracto }}
    </p>
    @endif

    {{-- Portada --}}
    @if($articulo->getFirstMediaUrl('portada'))
    <div class="mb-8 rounded-xl overflow-hidden">
        <img src="{{ $articulo->getFirstMediaUrl('portada') }}"
             alt="{{ $articulo->titulo }}"
             class="w-full object-cover max-h-96">
    </div>
    @endif

    {{-- Cuerpo del artículo --}}
    @if($articulo->cuerpo)
    <div class="prose prose-gray prose-amber max-w-none
                prose-headings:font-bold prose-headings:text-gray-800
                prose-a:text-amber-600 prose-a:no-underline hover:prose-a:underline
                prose-blockquote:border-amber-400 prose-blockquote:text-gray-500">
        {!! $articulo->cuerpo !!}
    </div>
    @endif

    {{-- Negocio relacionado --}}
    @if($articulo->lugar)
    @php
        $fichaRelacionada = $articulo->lugar->fichas()
            ->where('activo', true)->where('estado', 'activa')->first();
    @endphp
    <div class="mt-10 p-4 bg-amber-50 border border-amber-100 rounded-xl flex items-center gap-4">
        @php $portadaRelacionada = $fichaRelacionada?->getPortadaUrl() ?? ''; @endphp
        @if($portadaRelacionada)
            <img src="{{ $portadaRelacionada }}"
                 alt="{{ $articulo->lugar->nombre }}"
                 class="w-14 h-14 rounded-lg object-cover shrink-0">
        @endif
        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-400 mb-0.5">Negocio relacionado</p>
            <a href="{{ route('negocios.show', $articulo->lugar) }}"
               class="font-semibold text-gray-800 hover:text-amber-600 transition-colors">
                {{ $articulo->lugar->nombre }}
            </a>
        </div>
        <a href="{{ route('negocios.show', $articulo->lugar) }}"
           class="shrink-0 text-sm bg-amber-500 text-white px-3 py-1.5 rounded-lg hover:bg-amber-600 transition-colors">
            Ver ficha
        </a>
    </div>
    @endif

    {{-- Volver --}}
    <div class="mt-10 pt-6 border-t border-gray-100">
        <a href="{{ route('articulos.index') }}" class="text-sm text-gray-400 hover:text-amber-500 transition-colors">
            ← Volver a artículos
        </a>
    </div>

</article>
@endsection
