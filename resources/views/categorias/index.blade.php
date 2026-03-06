@extends('layouts.app')

@section('title', 'Categorías — Guía Local')
@section('description', 'Explorá todas las categorías de negocios y servicios de tu barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <span class="text-gray-600">Categorías</span>
    </nav>

    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-2">Categorías</h1>
    <p class="text-gray-500 mb-8">Encontrá negocios y servicios por rubro.</p>

    @if($categorias->isNotEmpty())
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($categorias as $categoria)
            <a href="{{ route('categorias.show', $categoria) }}"
               class="group bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:border-amber-200 transition-all text-center flex flex-col items-center gap-3">
                <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center group-hover:bg-amber-100 transition-colors text-amber-500">
                    <x-cat-icon :name="$categoria->icono ?? 'default'" class="w-7 h-7" />
                </div>
                <div>
                    <p class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-tight">
                        {{ $categoria->nombre }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ $categoria->negocios_count }} {{ $categoria->negocios_count === 1 ? 'negocio' : 'negocios' }}
                    </p>
                </div>
            </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 text-gray-400">
            <p class="text-lg">No hay categorías disponibles.</p>
        </div>
    @endif

</div>
@endsection
