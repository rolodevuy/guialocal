@extends('layouts.panel')
@section('title', 'Dashboard — ' . $ficha->lugar->nombre)

@php
    $plan           = $ficha->plan ?? 'gratuito';
    $esPremium      = $plan === 'premium';
    $esBasico       = $plan === 'basico';
    $esGratuito     = $plan === 'gratuito';
    $limPromos      = $ficha->planIncluye('promociones'); // 0 / 1 / PHP_INT_MAX
    $tieneVisitas   = $ficha->planIncluye('visitas');
    $tieneDestacado = $ficha->planIncluye('destacado');

    $planLabel = [
        'gratuito' => 'Gratuito',
        'basico'   => 'Básico',
        'premium'  => 'Premium ★',
    ][$plan] ?? ucfirst($plan);

    $planColor = match($plan) {
        'premium' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-300',
        'basico'  => 'bg-blue-50 text-blue-700 ring-1 ring-blue-200',
        default   => 'bg-gray-100 text-gray-500',
    };
@endphp

@section('content')

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $ficha->lugar->nombre }}</h1>
            <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
                    {{ $ficha->lugar->categoria->nombre }}
                </span>
                @if($ficha->lugar->zona)
                <span class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full">
                    {{ $ficha->lugar->zona->nombre }}
                </span>
                @endif
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $planColor }}">
                    {{ $planLabel }}
                </span>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('negocios.show', $ficha->lugar) }}"
               target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm border border-gray-200 rounded-xl text-gray-600 hover:border-gray-300 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                </svg>
                Ver mi ficha
            </a>
            <a href="{{ route('panel.edit') }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm bg-amber-500 hover:bg-amber-600 text-white font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar datos
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">

        {{-- Visitas --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden">
            @if($tieneVisitas)
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Visitas</p>
                <p class="text-3xl font-bold text-gray-800">{{ number_format($ficha->visitas) }}</p>
                <p class="text-xs text-gray-400 mt-0.5">en tu ficha</p>
            @else
                <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider mb-1">Visitas</p>
                <p class="text-3xl font-bold text-gray-200">—</p>
                <div class="absolute inset-0 bg-gray-50/80 rounded-2xl flex flex-col items-center justify-center gap-1">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span class="text-xs text-gray-400 font-medium">Plan Básico+</span>
                </div>
            @endif
        </div>

        {{-- Promociones --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden">
            @if($limPromos > 0)
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Promociones</p>
                <p class="text-3xl font-bold {{ $promocionesPendientes > 0 ? 'text-amber-600' : 'text-gray-800' }}">
                    {{ $promocionesPendientes }}
                    @if($limPromos !== PHP_INT_MAX)
                        <span class="text-base font-normal text-gray-300">/{{ $limPromos }}</span>
                    @endif
                </p>
                <p class="text-xs text-gray-400 mt-0.5">vigentes ahora</p>
            @else
                <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider mb-1">Promociones</p>
                <p class="text-3xl font-bold text-gray-200">—</p>
                <div class="absolute inset-0 bg-gray-50/80 rounded-2xl flex flex-col items-center justify-center gap-1">
                    <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span class="text-xs text-gray-400 font-medium">Plan Básico+</span>
                </div>
            @endif
        </div>

        {{-- Reseñas --}}
        @if(config('features.resenas'))
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Reseñas</p>
            <p class="text-3xl font-bold {{ $reseñasPendientes > 0 ? 'text-orange-500' : 'text-gray-800' }}">
                {{ $reseñasPendientes }}
            </p>
            <p class="text-xs text-gray-400 mt-0.5">pendientes</p>
        </div>
        @endif

        {{-- Estado --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Estado</p>
            <span class="inline-flex items-center gap-1.5 text-sm font-semibold mt-1
                {{ $ficha->activo ? 'text-green-600' : 'text-red-500' }}">
                <span class="w-2 h-2 rounded-full {{ $ficha->activo ? 'bg-green-500 animate-pulse' : 'bg-red-400' }}"></span>
                {{ $ficha->activo ? 'Activo' : 'Inactivo' }}
            </span>
        </div>

    </div>

    {{-- Métricas Premium: gráfico de visitas --}}
    @if($esPremium)
    @php
        $maxVisitas   = $visitasPorDia->max() ?: 1;
        $totalPeriodo = $visitasPorDia->sum();
        $mejorDia     = $visitasPorDia->filter(fn($v) => $v > 0)->sortDesc()->keys()->first();
        $hoy          = now()->toDateString();
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-5">
            <div>
                <h2 class="text-sm font-semibold text-gray-700">Visitas últimos 30 días</h2>
                <p class="text-xs text-gray-400 mt-0.5">
                    Total: <strong class="text-gray-700">{{ number_format($totalPeriodo) }}</strong>
                    @if($mejorDia)
                        · Mejor día: <strong class="text-gray-700">{{ \Carbon\Carbon::parse($mejorDia)->translatedFormat('j M') }}</strong>
                        ({{ number_format($visitasPorDia[$mejorDia]) }})
                    @endif
                </p>
            </div>
            <span class="text-xs bg-amber-50 text-amber-600 ring-1 ring-amber-200 px-2.5 py-1 rounded-full font-medium self-start sm:self-auto">
                Plan Premium ★
            </span>
        </div>

        {{-- Gráfico de barras CSS --}}
        <div class="flex items-end gap-px h-28 w-full">
            @foreach($visitasPorDia as $fecha => $cantidad)
            @php
                $pct      = round(($cantidad / $maxVisitas) * 100);
                $esHoy    = $fecha === $hoy;
                $barColor = $esHoy ? 'bg-amber-400' : ($cantidad > 0 ? 'bg-amber-200' : 'bg-gray-100');
            @endphp
            <div class="relative flex-1 flex flex-col justify-end h-full group">
                {{-- Tooltip --}}
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-800 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                    {{ \Carbon\Carbon::parse($fecha)->translatedFormat('j M') }}: {{ number_format($cantidad) }}
                </div>
                {{-- Barra --}}
                <div class="w-full {{ $barColor }} rounded-t transition-all"
                     style="height: {{ max($pct, $cantidad > 0 ? 3 : 1) }}%">
                </div>
            </div>
            @endforeach
        </div>

        {{-- Etiquetas del eje X (cada 5 días) --}}
        <div class="flex w-full mt-1.5 text-gray-300" style="font-size:9px;">
            @foreach($visitasPorDia->keys() as $i => $fecha)
                @if($i % 5 === 0)
                <div class="flex-1">{{ \Carbon\Carbon::parse($fecha)->format('j/n') }}</div>
                @else
                <div class="flex-1"></div>
                @endif
            @endforeach
        </div>

        {{-- Hoy y total --}}
        <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-sm bg-amber-400 inline-block"></span> Hoy
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-2.5 h-2.5 rounded-sm bg-amber-200 inline-block"></span> Otros días
            </span>
            <span class="ml-auto">Máximo: <strong class="text-gray-600">{{ number_format($maxVisitas) }}</strong></span>
        </div>
    </div>
    @elseif($esBasico)
    {{-- Teaser métricas para plan Básico --}}
    <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-100 rounded-2xl p-5 mb-6 flex items-center gap-4">
        <svg class="w-8 h-8 text-amber-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
        </svg>
        <div class="flex-1">
            <p class="text-sm font-semibold text-gray-700">Gráfico de visitas día a día</p>
            <p class="text-xs text-gray-500 mt-0.5">Con <strong>Premium</strong> ves el historial de los últimos 30 días y detectás tus mejores días.</p>
        </div>
        <a href="{{ route('contacto.show') }}?asunto=upgrade-premium"
           class="shrink-0 text-xs font-semibold bg-amber-500 hover:bg-amber-600 text-white px-3 py-1.5 rounded-lg transition-colors">
            Quiero Premium
        </a>
    </div>
    @endif

    {{-- Qué incluye mi plan --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4">¿Qué incluye tu plan <span class="{{ $planColor }} px-2 py-0.5 rounded-full text-xs">{{ $planLabel }}</span>?</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">

            @php
            $features = [
                ['key' => 'visitas',   'label' => 'Estadísticas de visitas',    'desde' => 'Básico'],
                ['key' => 'whatsapp',  'label' => 'Botón WhatsApp en tu ficha', 'desde' => 'Básico'],
                ['key' => 'logo',      'label' => 'Logo propio',                'desde' => 'Básico'],
                ['key' => 'destacado', 'label' => 'Primero en resultados',       'desde' => 'Premium'],
            ];
            @endphp

            @foreach($features as $f)
            @php $incluido = $ficha->planIncluye($f['key']); @endphp
            <div class="flex items-start gap-2.5 p-3 rounded-xl {{ $incluido ? 'bg-green-50' : 'bg-gray-50' }}">
                @if($incluido)
                    <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-xs text-gray-700 font-medium">{{ $f['label'] }}</span>
                @else
                    <svg class="w-4 h-4 text-gray-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <span class="text-xs text-gray-400 line-through">{{ $f['label'] }}</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Plan {{ $f['desde'] }}</span>
                    </div>
                @endif
            </div>
            @endforeach

            {{-- Promociones --}}
            <div class="flex items-start gap-2.5 p-3 rounded-xl {{ $limPromos > 0 ? 'bg-green-50' : 'bg-gray-50' }}">
                @if($limPromos > 0)
                    <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-xs text-gray-700 font-medium">
                        Promociones
                        <span class="text-gray-400 font-normal">(máx. {{ $limPromos === PHP_INT_MAX ? '∞' : $limPromos }})</span>
                    </span>
                @else
                    <svg class="w-4 h-4 text-gray-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <span class="text-xs text-gray-400 line-through">Promociones</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Plan Básico</span>
                    </div>
                @endif
            </div>

            {{-- Fotos --}}
            @php $limFotos = $ficha->planIncluye('fotos'); @endphp
            <div class="flex items-start gap-2.5 p-3 rounded-xl {{ $limFotos > 0 ? 'bg-green-50' : 'bg-gray-50' }}">
                @if($limFotos > 0)
                    <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-xs text-gray-700 font-medium">
                        Fotos <span class="text-gray-400 font-normal">(máx. {{ $limFotos }})</span>
                    </span>
                @else
                    <svg class="w-4 h-4 text-gray-300 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div>
                        <span class="text-xs text-gray-400 line-through">Galería de fotos</span>
                        <span class="block text-xs text-gray-400 mt-0.5">Plan Básico</span>
                    </div>
                @endif
            </div>

        </div>
    </div>

    {{-- Info del negocio --}}
    <div class="grid sm:grid-cols-2 gap-6 mb-6">

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Datos de contacto</h2>
                <a href="{{ route('panel.edit') }}" class="text-xs text-amber-600 hover:underline">Editar</a>
            </div>
            <ul class="space-y-3 text-sm">
                <li class="flex items-start gap-2">
                    <span class="text-gray-300 mt-0.5 shrink-0">📞</span>
                    <span class="text-gray-600">{{ $ficha->telefono ?: '—' }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-gray-300 mt-0.5 shrink-0">✉️</span>
                    <span class="text-gray-600 truncate">{{ $ficha->email ?: '—' }}</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-gray-300 mt-0.5 shrink-0">🌐</span>
                    @if($ficha->sitio_web)
                        <a href="{{ $ficha->sitio_web }}" target="_blank"
                           class="text-amber-600 hover:underline truncate">
                            {{ parse_url($ficha->sitio_web, PHP_URL_HOST) }}
                        </a>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </li>
            </ul>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-gray-700">Descripción</h2>
                <a href="{{ route('panel.edit') }}" class="text-xs text-amber-600 hover:underline">Editar</a>
            </div>
            @if($ficha->descripcion)
                <p class="text-sm text-gray-600 leading-relaxed line-clamp-4">{{ $ficha->descripcion }}</p>
            @else
                <p class="text-sm text-gray-400 italic">Sin descripción. Agregá una para que los usuarios conozcan tu negocio.</p>
            @endif
        </div>

    </div>

    {{-- Banner de upgrade (personalizado por plan) --}}
    @if(! $esPremium)
    <div class="rounded-2xl border p-6
        {{ $esGratuito
            ? 'bg-gradient-to-r from-amber-50 to-orange-50 border-amber-200'
            : 'bg-gradient-to-r from-blue-50 to-indigo-50 border-blue-200' }}">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                @if($esGratuito)
                    <p class="font-semibold text-gray-800 text-sm mb-1">Tu ficha está en plan Gratuito</p>
                    <ul class="text-xs text-gray-500 space-y-0.5">
                        <li>→ Con <strong>Básico</strong>: WhatsApp flotante, estadísticas, logo y 1 promoción</li>
                        <li>→ Con <strong>Premium</strong>: aparecés primero, hasta 10 fotos y promos ilimitadas</li>
                    </ul>
                @else
                    <p class="font-semibold text-gray-800 text-sm mb-1">Estás en plan Básico</p>
                    <ul class="text-xs text-gray-500 space-y-0.5">
                        <li>→ Con <strong>Premium</strong>: aparecés primero en búsquedas y listados</li>
                        <li>→ Galería de hasta 10 fotos + promociones ilimitadas</li>
                    </ul>
                @endif
            </div>
            <a href="{{ route('contacto.show') }}?asunto=upgrade-premium"
               class="shrink-0 px-5 py-2.5 text-sm font-semibold rounded-xl transition-colors whitespace-nowrap
                {{ $esGratuito
                    ? 'bg-amber-500 hover:bg-amber-600 text-white'
                    : 'bg-blue-600 hover:bg-blue-700 text-white' }}">
                {{ $esGratuito ? 'Ver planes disponibles' : 'Quiero Premium' }}
            </a>
        </div>
    </div>
    @endif

@endsection
