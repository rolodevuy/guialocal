@extends('layouts.app')

@section('title', 'Eventos — Guía Local')
@section('description', 'Descubrí los próximos eventos en tu barrio.')

@section('content')

<div class="bg-gray-50 border-b border-gray-100 py-8 sm:py-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-1">Eventos próximos</h1>
        <p class="text-gray-500 text-sm">Actividades, ferias, shows y más en tu zona.</p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">

    @if($eventos->isEmpty())
        <div class="text-center py-20">
            <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-50 flex items-center justify-center">
                <svg class="w-8 h-8 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-400 text-sm">No hay eventos próximos por el momento.</p>
            <a href="{{ route('home') }}"
               class="mt-4 inline-block text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors">
                ← Volver al inicio
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
            @foreach($eventos as $evento)
            @php
                $portada = $evento->getFirstMediaUrl('portada', 'webp') ?: $evento->getFirstMediaUrl('portada');
                $esHoy   = $evento->fecha_inicio->isToday();
                $esSemana = $evento->fecha_inicio->isCurrentWeek();
            @endphp

            <a href="{{ route('eventos.show', $evento) }}"
               class="group bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">

                {{-- Imagen --}}
                <div class="relative h-48 bg-amber-50 overflow-hidden">
                    @if($portada)
                        <img src="{{ $portada }}" alt="{{ $evento->titulo }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-amber-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                    @endif

                    @if($esHoy)
                        <span class="absolute top-3 left-3 text-xs font-bold bg-green-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                            Hoy
                        </span>
                    @elseif($esSemana)
                        <span class="absolute top-3 left-3 text-xs font-bold bg-amber-500 text-white px-2.5 py-1 rounded-full shadow-sm">
                            Esta semana
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4">
                    {{-- Fecha --}}
                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                        <span class="inline-flex items-center gap-1 text-xs font-semibold bg-amber-100 text-amber-700 px-2.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $evento->fecha_inicio->locale('es')->isoFormat('D [de] MMMM') }}
                            @if($evento->fecha_fin && ! $evento->fecha_fin->isSameDay($evento->fecha_inicio))
                                — {{ $evento->fecha_fin->locale('es')->isoFormat('D [de] MMMM') }}
                            @endif
                        </span>
                        @if($evento->hora_inicio)
                            <span class="text-xs text-gray-400">
                                {{ \Carbon\Carbon::parse($evento->hora_inicio)->format('H:i') }}h
                            </span>
                        @endif
                    </div>

                    <h2 class="font-bold text-gray-900 text-sm sm:text-base group-hover:text-amber-600 transition-colors leading-snug mb-1">
                        {{ $evento->titulo }}
                    </h2>

                    @if($evento->descripcion)
                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed mb-2">
                            {{ $evento->descripcion }}
                        </p>
                    @endif

                    @if($evento->lugar)
                        <p class="text-xs text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                            {{ $evento->lugar->nombre }}
                            @if($evento->lugar->zona)
                                · {{ $evento->lugar->zona->nombre }}
                            @endif
                        </p>
                    @endif
                </div>

            </a>
            @endforeach
        </div>

        {{-- Paginación --}}
        @if($eventos->hasPages())
            <div class="mt-10">
                {{ $eventos->links() }}
            </div>
        @endif
    @endif

</div>

@endsection
