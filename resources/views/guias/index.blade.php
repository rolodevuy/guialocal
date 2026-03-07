@extends('layouts.app')

@section('title', 'Guías — Guía Local')
@section('description', 'Guías temáticas sobre negocios y servicios del barrio. Encontrá las mejores opciones por categoría.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Guías</h1>
        <p class="text-gray-500 mt-1">Selecciones temáticas de negocios y servicios del barrio.</p>
    </div>

    @if($guias->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <p class="font-medium text-gray-500">Aún no hay guías publicadas.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($guias as $guia)
            <a href="{{ route('guias.show', $guia) }}"
               class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">

                {{-- Portada --}}
                <div class="h-44 bg-gradient-to-br from-amber-50 to-amber-100 overflow-hidden relative">
                    @if($guia->getFirstMediaUrl('portada'))
                        <img src="{{ $guia->getFirstMediaUrl('portada') }}"
                             alt="{{ $guia->titulo }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-amber-300">
                            <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>
                    @endif
                    {{-- Badge count negocios --}}
                    <div class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-0.5 rounded-full backdrop-blur-sm">
                        {{ $guia->lugares_count }} {{ Str::plural('negocio', $guia->lugares_count) }}
                    </div>
                </div>

                <div class="p-4">
                    {{-- Categoría --}}
                    @if($guia->categoria)
                    <div class="mb-2">
                        <span class="bg-amber-100 text-amber-700 text-xs px-2 py-0.5 rounded font-medium">
                            {{ $guia->categoria->nombre }}
                        </span>
                    </div>
                    @endif

                    <h2 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-snug mb-2">
                        {{ $guia->titulo }}
                    </h2>

                    @if($guia->intro)
                        <p class="text-xs text-gray-400 line-clamp-2">{{ $guia->intro }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        @if($guias->hasPages())
        <div class="mt-10">
            {{ $guias->links() }}
        </div>
        @endif
    @endif

</div>
@endsection
