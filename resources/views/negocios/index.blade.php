@extends('layouts.app')

@section('title', 'Negocios — Guía Local')
@section('description', 'Explorá todos los negocios y servicios del barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Negocios</h1>
        <p class="text-gray-500 mt-1">{{ $negocios->total() }} resultado{{ $negocios->total() !== 1 ? 's' : '' }}
            @if(request('q')) para "<span class="font-medium text-gray-700">{{ request('q') }}</span>"@endif
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR FILTROS --}}
        <aside class="lg:w-60 shrink-0">
            <form method="GET" action="{{ route('negocios.index') }}" id="filtros-form">

                {{-- Buscador --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Buscar</label>
                    <div class="flex gap-1">
                        <input type="text" name="q" value="{{ request('q') }}"
                               placeholder="Nombre o descripción..."
                               class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <button type="submit" class="px-3 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Categorías --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Categoría</label>
                    <div class="space-y-1">
                        <a href="{{ route('negocios.index', array_merge(request()->except('categoria', 'page'), [])) }}"
                           class="block px-3 py-1.5 rounded-lg text-sm transition-colors {{ !request('categoria') ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                            Todas
                        </a>
                        @foreach($categorias as $cat)
                        <a href="{{ route('negocios.index', array_merge(request()->except('categoria', 'page'), ['categoria' => $cat->slug])) }}"
                           class="block px-3 py-1.5 rounded-lg text-sm transition-colors {{ request('categoria') === $cat->slug ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $cat->nombre }}
                        </a>
                        @endforeach
                    </div>
                </div>

                {{-- Zonas --}}
                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Zona</label>
                    <div class="space-y-1">
                        <a href="{{ route('negocios.index', array_merge(request()->except('zona', 'page'), [])) }}"
                           class="block px-3 py-1.5 rounded-lg text-sm transition-colors {{ !request('zona') ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                            Todas
                        </a>
                        @foreach($zonas as $zona)
                        <a href="{{ route('negocios.index', array_merge(request()->except('zona', 'page'), ['zona' => $zona->slug])) }}"
                           class="block px-3 py-1.5 rounded-lg text-sm transition-colors {{ request('zona') === $zona->slug ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                            {{ $zona->nombre }}
                        </a>
                        @endforeach
                    </div>
                </div>

                @if(request()->hasAny(['q', 'categoria', 'zona']))
                <a href="{{ route('negocios.index') }}"
                   class="block text-center text-xs text-gray-400 hover:text-red-500 transition-colors mt-2">
                    × Limpiar filtros
                </a>
                @endif
            </form>
        </aside>

        {{-- GRID NEGOCIOS --}}
        <div class="flex-1">
            @if($negocios->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <p class="font-medium text-gray-500">No encontramos negocios con esos filtros.</p>
                    <a href="{{ route('negocios.index') }}" class="mt-3 inline-block text-sm text-amber-600 hover:underline">Ver todos</a>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($negocios as $negocio)
                    <a href="{{ route('negocios.show', $negocio) }}"
                       class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="h-36 bg-amber-50 overflow-hidden">
                            @if($negocio->getFirstMediaUrl('portada'))
                                <img src="{{ $negocio->getFirstMediaUrl('portada') }}"
                                     alt="{{ $negocio->nombre }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-amber-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-tight">
                                    {{ $negocio->nombre }}
                                </h3>
                                @if($negocio->featured)
                                    <span class="shrink-0 text-xs bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded font-medium">★</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $negocio->descripcion }}</p>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $negocio->categoria->nombre }}</span>
                                <span>·</span>
                                <span>{{ $negocio->zona->nombre }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Paginación --}}
                @if($negocios->hasPages())
                <div class="mt-10">
                    {{ $negocios->links() }}
                </div>
                @endif
            @endif
        </div>

    </div>
</div>
@endsection
