@extends('layouts.app')

@section('title', $evento->titulo . ' — Guía Local')
@section('description', $evento->descripcion ?? 'Evento en Guía Local.')

@section('content')

@php
    $portada = $evento->getFirstMediaUrl('portada', 'optimized') ?: $evento->getFirstMediaUrl('portada', 'webp') ?: $evento->getFirstMediaUrl('portada');
@endphp

{{-- Header --}}
<div class="bg-gray-50 border-b border-gray-100">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-10">

        {{-- Breadcrumb --}}
        <nav class="text-xs text-gray-400 mb-4 flex items-center gap-1.5">
            <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
            <span>›</span>
            <a href="{{ route('eventos.index') }}" class="hover:text-amber-600 transition-colors">Eventos</a>
            <span>›</span>
            <span class="text-gray-500 truncate max-w-xs">{{ $evento->titulo }}</span>
        </nav>

        {{-- Fecha badge --}}
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="inline-flex items-center gap-1.5 text-sm font-semibold bg-amber-100 text-amber-700 px-3 py-1 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                {{ $evento->fecha_inicio->locale('es')->isoFormat('dddd D [de] MMMM [de] YYYY') }}
                @if($evento->fecha_fin && ! $evento->fecha_fin->isSameDay($evento->fecha_inicio))
                    — {{ $evento->fecha_fin->locale('es')->isoFormat('D [de] MMMM') }}
                @endif
            </span>
            @if($evento->hora_inicio)
                <span class="text-sm text-gray-500 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}h
                    @if($evento->hora_fin)
                        – {{ \Carbon\Carbon::parse($evento->hora_fin)->format('H:i') }}h
                    @endif
                </span>
            @endif
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">
            {{ $evento->titulo }}
        </h1>

    </div>
</div>

{{-- Contenido --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Columna principal --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Imagen --}}
            @if($portada)
                <div class="rounded-2xl overflow-hidden shadow-sm border border-gray-100 aspect-video">
                    <img src="{{ $portada }}" alt="{{ $evento->titulo }}"
                         loading="lazy"
                         class="w-full h-full object-cover">
                </div>
            @else
                <div class="rounded-2xl overflow-hidden bg-amber-50 border border-amber-100 h-48 flex items-center justify-center">
                    <svg class="w-16 h-16 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            @endif

            {{-- Descripción --}}
            @if($evento->descripcion)
                <div class="prose prose-sm max-w-none text-gray-600 leading-relaxed">
                    {!! nl2br(e($evento->descripcion)) !!}
                </div>
            @endif

        </div>

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Cuándo --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Cuándo</h3>
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $evento->fecha_inicio->locale('es')->isoFormat('dddd D [de] MMMM') }}
                            </p>
                            @if($evento->fecha_fin && ! $evento->fecha_fin->isSameDay($evento->fecha_inicio))
                                <p class="text-xs text-gray-500">
                                    Hasta el {{ $evento->fecha_fin->locale('es')->isoFormat('D [de] MMMM') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    @if($evento->hora_inicio)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-sm text-gray-700">
                                {{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}h
                                @if($evento->hora_fin)
                                    – {{ \Carbon\Carbon::parse($evento->hora_fin)->format('H:i') }}h
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Dónde --}}
            @if($evento->lugar)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dónde</h3>
                    <a href="{{ route('negocios.show', $evento->lugar) }}"
                       class="flex items-start gap-2 group">
                        <svg class="w-4 h-4 text-amber-400 mt-0.5 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                        </svg>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 group-hover:text-amber-600 transition-colors">
                                {{ $evento->lugar->nombre }}
                            </p>
                            @if($evento->lugar->zona)
                                <p class="text-xs text-gray-500">{{ $evento->lugar->zona->nombre }}</p>
                            @endif
                            <p class="text-xs text-amber-600 mt-1 font-medium">Ver negocio →</p>
                        </div>
                    </a>
                </div>
            @endif

            {{-- Volver --}}
            <a href="{{ route('eventos.index') }}"
               class="flex items-center gap-2 text-sm text-gray-500 hover:text-amber-600 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Ver todos los eventos
            </a>

        </div>
    </div>
</div>

@endsection
