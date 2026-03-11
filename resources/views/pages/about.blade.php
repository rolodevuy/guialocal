@extends('layouts.app')

@section('title', 'Quiénes somos — Guía Local')
@section('description', 'Conocé la historia y el equipo detrás de Guía Local, el directorio de negocios de tu barrio.')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <span class="text-gray-600">Quiénes somos</span>
    </nav>

    {{-- Header --}}
    <div class="max-w-2xl mb-12">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">Quiénes somos</h1>
        <p class="text-gray-500 text-base leading-relaxed">
            Guía Local nació con una idea simple: ayudar a los vecinos a encontrar lo que necesitan cerca de su casa,
            y a los negocios del barrio a ser más visibles.
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-10">

        {{-- Contenido principal --}}
        <div class="flex-1 min-w-0 space-y-10">

            {{-- Misión --}}
            <div class="flex gap-4">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0zM19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 mb-2">Nuestra misión</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Conectar a los vecinos con los negocios y servicios de su barrio. Creemos en el comercio local
                        como motor de la comunidad: cuando comprás en el barrio, fortalecés tu entorno y apoyás a
                        emprendedores que conocés de cerca.
                    </p>
                </div>
            </div>

            {{-- Cómo funciona --}}
            <div class="flex gap-4">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 mb-2">Cómo funciona</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Los negocios se registran a través de nuestro formulario de contacto. Nuestro equipo verifica
                        la información y publica la ficha en la guía. Los vecinos pueden buscar por nombre, categoría
                        o zona, y acceder a los datos de contacto directamente.
                    </p>
                </div>
            </div>

            {{-- Para negocios --}}
            <div class="flex gap-4">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0 mt-0.5">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 mb-2">Para negocios del barrio</h2>
                    <p class="text-sm text-gray-500 leading-relaxed">
                        Registrar tu negocio en Guía Local es <strong class="text-gray-700">completamente gratuito</strong>.
                        Publicamos tu nombre, categoría, zona, datos de contacto y horarios. Los planes premium
                        te dan mayor visibilidad con posición destacada y galería de fotos.
                    </p>
                </div>
            </div>

        </div>

        {{-- Sidebar --}}
        <aside class="lg:w-64 shrink-0 space-y-4">

            {{-- Stats --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4">La guía en números</h3>
                <div class="space-y-4">
                    @php
                        $totalNegocios   = App\Models\Lugar::where('activo', true)->count();
                        $totalCategorias = App\Models\Categoria::where('activo', true)->count();
                        $totalZonas      = App\Models\Zona::count();
                    @endphp
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Negocios</span>
                        <span class="text-xl font-extrabold text-amber-500">{{ $totalNegocios }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Categorías</span>
                        <span class="text-xl font-extrabold text-amber-500">{{ $totalCategorias }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">Zonas</span>
                        <span class="text-xl font-extrabold text-amber-500">{{ $totalZonas }}</span>
                    </div>
                </div>
            </div>

            {{-- CTA --}}
            <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5 text-center">
                <p class="text-sm font-semibold text-gray-800 mb-1">¿Tenés un negocio?</p>
                <p class="text-xs text-gray-500 mb-4">Sumalo gratis a la guía.</p>
                <a href="{{ route('contacto.show') }}"
                   class="inline-block w-full px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-colors text-center">
                    Registrar mi negocio
                </a>
            </div>

        </aside>

    </div>

</div>
@endsection
