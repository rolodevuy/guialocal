@extends('layouts.app')

@section('title', 'Artículos — Guía Local')
@section('description', 'Notas, guías y contenido editorial sobre el barrio y sus negocios.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Artículos</h1>
        <p class="text-gray-500 mt-1">Notas y guías sobre el barrio.</p>
    </div>

    @if($articulos->isEmpty())
        <div class="text-center py-20 text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
            </svg>
            <p class="font-medium text-gray-500">Aún no hay artículos publicados.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articulos as $articulo)
            <a href="{{ route('articulos.show', $articulo) }}"
               class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">

                {{-- Portada --}}
                <div class="h-44 bg-amber-50 overflow-hidden">
                    @if($articulo->getFirstMediaUrl('portada'))
                        <img src="{{ $articulo->getFirstMediaUrl('portada') }}"
                             alt="{{ $articulo->titulo }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-amber-200">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    @endif
                </div>

                <div class="p-4">
                    {{-- Categoría y fecha --}}
                    <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                        @if($articulo->categoria)
                            <span class="bg-amber-100 text-amber-700 px-2 py-0.5 rounded font-medium">{{ $articulo->categoria->nombre }}</span>
                            <span>·</span>
                        @endif
                        @if($articulo->publicado_en)
                            <span>{{ $articulo->publicado_en->format('d/m/Y') }}</span>
                        @endif
                    </div>

                    <h2 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-snug mb-2">
                        {{ $articulo->titulo }}
                    </h2>

                    @if($articulo->extracto)
                        <p class="text-xs text-gray-400 line-clamp-2">{{ $articulo->extracto }}</p>
                    @endif
                </div>
            </a>
            @endforeach
        </div>

        @if($articulos->hasPages())
        <div class="mt-10">
            {{ $articulos->links() }}
        </div>
        @endif
    @endif

</div>
@endsection
