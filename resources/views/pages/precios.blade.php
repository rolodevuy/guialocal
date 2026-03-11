@extends('layouts.app')
@section('title', 'Planes y precios — Guía Local')
@section('description', 'Conocé los planes disponibles para negocios en Guía Local: Gratuito, Básico y Premium. Más visibilidad, más herramientas, más clientes.')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-16">

    {{-- Encabezado --}}
    <div class="text-center mb-12">
        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-3">Planes para negocios</h1>
        <p class="text-gray-500 text-lg max-w-xl mx-auto">
            Estar en la guía es gratis. Si querés más visibilidad y herramientas, tenemos planes que se adaptan a tu negocio.
        </p>
    </div>

    {{-- Cards de planes --}}
    <div class="grid sm:grid-cols-3 gap-6 mb-16">

        {{-- GRATUITO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col">
            <div class="mb-5">
                <span class="inline-block text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Gratuito</span>
                <div class="text-3xl font-bold text-gray-800">$0</div>
                <p class="text-sm text-gray-500 mt-1">Para estar presente en la guía.</p>
            </div>
            <ul class="space-y-2.5 text-sm flex-1 mb-6">
                @foreach([
                    ['ok' => true,  'txt' => 'Ficha pública con datos de contacto'],
                    ['ok' => true,  'txt' => 'Categoría y zona'],
                    ['ok' => true,  'txt' => 'Horarios y "Abierto ahora"'],
                    ['ok' => true,  'txt' => 'Panel de autogestión'],
                    ['ok' => false, 'txt' => 'Estadísticas de visitas'],
                    ['ok' => false, 'txt' => 'Logo propio'],
                    ['ok' => false, 'txt' => 'WhatsApp flotante en ficha'],
                    ['ok' => false, 'txt' => 'Galería de fotos'],
                    ['ok' => false, 'txt' => 'Promociones activas'],
                    ['ok' => false, 'txt' => 'Primero en resultados'],
                ] as $f)
                <li class="flex items-start gap-2.5">
                    @if($f['ok'])
                        <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-700">{{ $f['txt'] }}</span>
                    @else
                        <svg class="w-4 h-4 text-gray-200 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-gray-400">{{ $f['txt'] }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            <a href="{{ route('contacto.show') }}?asunto=alta-negocio"
               class="block text-center text-sm font-semibold border border-gray-200 text-gray-600 hover:border-gray-400 hover:text-gray-800 rounded-xl py-2.5 transition-colors">
                Registrar mi negocio
            </a>
        </div>

        {{-- BÁSICO --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 flex flex-col">
            <div class="mb-5">
                <span class="inline-block text-xs font-semibold uppercase tracking-widest text-blue-500 mb-2">Básico</span>
                <div class="text-3xl font-bold text-gray-800">Consultanos</div>
                <p class="text-sm text-gray-500 mt-1">Para negocios que quieren crecer.</p>
            </div>
            <ul class="space-y-2.5 text-sm flex-1 mb-6">
                @foreach([
                    ['ok' => true,  'txt' => 'Todo lo del plan Gratuito'],
                    ['ok' => true,  'txt' => 'Estadísticas de visitas'],
                    ['ok' => true,  'txt' => 'Logo propio en la ficha'],
                    ['ok' => true,  'txt' => 'WhatsApp flotante en ficha'],
                    ['ok' => true,  'txt' => 'Galería de fotos (hasta 3)'],
                    ['ok' => true,  'txt' => '1 promoción activa'],
                    ['ok' => false, 'txt' => 'Primero en resultados'],
                    ['ok' => false, 'txt' => 'Galería extendida (hasta 10)'],
                    ['ok' => false, 'txt' => 'Promociones ilimitadas'],
                ] as $f)
                <li class="flex items-start gap-2.5">
                    @if($f['ok'])
                        <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span class="text-gray-700">{{ $f['txt'] }}</span>
                    @else
                        <svg class="w-4 h-4 text-gray-200 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        <span class="text-gray-400">{{ $f['txt'] }}</span>
                    @endif
                </li>
                @endforeach
            </ul>
            <a href="{{ route('contacto.show') }}?asunto=upgrade-basico"
               class="block text-center text-sm font-semibold border border-blue-200 text-blue-600 hover:bg-blue-50 rounded-xl py-2.5 transition-colors">
                Me interesa el Básico
            </a>
        </div>

        {{-- PREMIUM --}}
        <div class="bg-gray-900 rounded-2xl border border-gray-800 p-6 flex flex-col relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 rounded-full -translate-y-8 translate-x-8 pointer-events-none"></div>
            <div class="mb-5">
                <span class="inline-block text-xs font-semibold uppercase tracking-widest text-amber-400 mb-2">Premium ★</span>
                <div class="text-3xl font-bold text-white">Consultanos</div>
                <p class="text-sm text-gray-400 mt-1">Máxima visibilidad en la guía.</p>
            </div>
            <ul class="space-y-2.5 text-sm flex-1 mb-6">
                @foreach([
                    ['txt' => 'Todo lo del plan Básico'],
                    ['txt' => 'Galería de fotos (hasta 10)'],
                    ['txt' => 'Promociones ilimitadas'],
                    ['txt' => 'Primero en resultados y en home'],
                    ['txt' => 'Badge Premium ★ en tu ficha'],
                    ['txt' => 'Gráfico de visitas (30 días)'],
                ] as $f)
                <li class="flex items-start gap-2.5">
                    <svg class="w-4 h-4 text-amber-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-gray-200">{{ $f['txt'] }}</span>
                </li>
                @endforeach
            </ul>
            <a href="{{ route('contacto.show') }}?asunto=upgrade-premium"
               class="block text-center text-sm font-bold bg-amber-500 hover:bg-amber-400 text-white rounded-xl py-2.5 transition-colors">
                Me interesa el Premium
            </a>
        </div>

    </div>

    {{-- Nota no hay precios fijos --}}
    <p class="text-center text-xs text-gray-400 -mt-10 mb-16">
        Los precios varían según el rubro y la zona. Escribinos y te respondemos sin compromiso.
    </p>

    {{-- ¿Tenés dudas? --}}
    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-8 sm:p-10 text-center">
        <h2 class="text-xl font-bold text-gray-800 mb-2">¿Tenés dudas?</h2>
        <p class="text-gray-500 text-sm mb-6 max-w-md mx-auto">
            Escribinos y te explicamos cómo funciona cada plan, qué conviene según tu negocio, y cómo empezar.
        </p>
        <a href="{{ route('contacto.show') }}?asunto=consulta-planes"
           class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white font-semibold px-6 py-3 rounded-xl transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            Escribinos
        </a>
    </div>

</div>
@endsection
