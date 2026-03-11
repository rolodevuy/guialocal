@extends('layouts.panel')
@section('title', 'Dashboard — ' . $ficha->lugar->nombre)

@php
    $plan           = $ficha->plan ?? 'gratuito';
    $esPremium      = $plan === 'premium';
    $esBasico       = $plan === 'basico';
    $esGratuito     = $plan === 'gratuito';
    $limPromos    = $ficha->planIncluye('promociones');
    $tieneVisitas = $ficha->planIncluye('visitas');
    $publicado      = $ficha->activo && $ficha->estado === 'activa';

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

    // Horarios: convertir rangos a día‑por‑día
    $diasNombres = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];
    $diasCortos  = ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
    $horariosFlat = [];
    foreach ($ficha->horarios ?? [] as $franja) {
        $inicio = array_search($franja['dia_inicio'] ?? '', $diasNombres);
        $fin    = !empty($franja['dia_fin']) ? array_search($franja['dia_fin'], $diasNombres) : $inicio;
        if ($inicio === false) continue;
        if ($fin === false) $fin = $inicio;
        for ($i = $inicio; $i <= $fin; $i++) {
            $horariosFlat[$diasNombres[$i]] = [
                'cerrado'  => (bool)($franja['cerrado'] ?? false),
                'apertura' => $franja['apertura'] ?? '',
                'cierre'   => $franja['cierre'] ?? '',
            ];
        }
    }
    $cantEspeciales = count($ficha->horarios_especiales ?? []);

    // Agrupar días consecutivos con igual horario
    $grupos = [];
    $gi = 0;
    while ($gi < 7) {
        $gh = $horariosFlat[$diasNombres[$gi]] ?? ['cerrado' => true, 'apertura' => '', 'cierre' => ''];
        $gj = $gi + 1;
        while ($gj < 7) {
            $ghn = $horariosFlat[$diasNombres[$gj]] ?? ['cerrado' => true, 'apertura' => '', 'cierre' => ''];
            if ($gh['cerrado'] == $ghn['cerrado'] && $gh['apertura'] == $ghn['apertura'] && $gh['cierre'] == $ghn['cierre']) {
                $gj++;
            } else { break; }
        }
        $grupos[] = [
            'label'    => ($gj - 1 > $gi) ? $diasCortos[$gi] . ' – ' . $diasCortos[$gj - 1] : $diasCortos[$gi],
            'cerrado'  => $gh['cerrado'],
            'apertura' => $gh['apertura'],
            'cierre'   => $gh['cierre'],
        ];
        $gi = $gj;
    }
    // ¿El horario es compacto? → layout en dos columnas
    $horariosCompacto = !empty($horariosFlat) && count($grupos) <= 4;

    // Abierto ahora + cierre de hoy
    $abiertoAhora = $ficha->isAbiertoAhora();
    $diaHoyNum    = (int) now()->isoFormat('E');
    $diasMapRev   = array_combine(range(1,7), $diasNombres);
    $horaHoy      = $horariosFlat[$diasMapRev[$diaHoyNum] ?? ''] ?? null;
    $cierraHoy    = ($abiertoAhora && $horaHoy && !$horaHoy['cerrado']) ? $horaHoy['cierre'] : null;
    $abreHoy      = (!$abiertoAhora && $horaHoy && !$horaHoy['cerrado']) ? $horaHoy['apertura'] : null;
@endphp

@section('content')

    {{-- ① ESTADO: lo primero que ve el dueño --}}
    <div class="rounded-2xl border p-5 mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4
        {{ $publicado ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <div class="flex items-center gap-3">
            <span class="w-10 h-10 rounded-full flex items-center justify-center shrink-0
                {{ $publicado ? 'bg-green-100' : 'bg-red-100' }}">
                @if($publicado)
                    <span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
                @else
                    <span class="w-3 h-3 rounded-full bg-red-400"></span>
                @endif
            </span>
            <div>
                <p class="font-semibold text-sm text-gray-800 flex items-center gap-2">
                    {{ $publicado ? 'Tu negocio está activo' : 'Tu negocio está inactivo' }}
                    <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $planColor }}">{{ $planLabel }}</span>
                </p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $publicado
                        ? 'Está visible en Guía Local y los clientes pueden encontrarte.'
                        : 'Tu ficha no es visible públicamente. Contactá al equipo para más información.' }}
                </p>
            </div>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('negocios.show', $ficha->lugar) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-4 py-2 text-sm border border-gray-200 bg-white rounded-xl text-gray-600 hover:border-gray-300 transition-colors">
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
                Editar
            </a>
        </div>
    </div>

    {{-- ③ STATS DE LA SEMANA --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">

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

        {{-- Promociones activas --}}
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
            <p class="text-3xl font-bold {{ $reseñasPendientes > 0 ? 'text-orange-500' : 'text-gray-800' }}">{{ $reseñasPendientes }}</p>
            <p class="text-xs text-gray-400 mt-0.5">pendientes</p>
        </div>
        @endif

        {{-- Rating placeholder --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 relative overflow-hidden">
            <p class="text-xs font-semibold text-gray-200 uppercase tracking-wider mb-1">Rating</p>
            <p class="text-3xl font-bold text-gray-200">—</p>
            <div class="absolute inset-0 bg-gray-50/80 rounded-2xl flex flex-col items-center justify-center gap-1">
                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <span class="text-xs text-gray-400 font-medium">Próximamente</span>
            </div>
        </div>

    </div>

    {{-- Gráfico premium --}}
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
            <span class="text-xs bg-amber-50 text-amber-600 ring-1 ring-amber-200 px-2.5 py-1 rounded-full font-medium self-start sm:self-auto">Plan Premium ★</span>
        </div>
        <div class="flex items-end gap-px h-28 w-full">
            @foreach($visitasPorDia as $fecha => $cantidad)
            @php
                $pct      = round(($cantidad / $maxVisitas) * 100);
                $esHoy    = $fecha === $hoy;
                $barColor = $esHoy ? 'bg-amber-400' : ($cantidad > 0 ? 'bg-amber-200' : 'bg-gray-100');
            @endphp
            <div class="relative flex-1 flex flex-col justify-end h-full group">
                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-1.5 px-2 py-1 bg-gray-800 text-white text-xs rounded-lg whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none z-10">
                    {{ \Carbon\Carbon::parse($fecha)->translatedFormat('j M') }}: {{ number_format($cantidad) }}
                </div>
                <div class="w-full {{ $barColor }} rounded-t transition-all" style="height: {{ max($pct, $cantidad > 0 ? 3 : 1) }}%"></div>
            </div>
            @endforeach
        </div>
        <div class="flex w-full mt-1.5 text-gray-300" style="font-size:9px;">
            @foreach($visitasPorDia->keys() as $i => $fecha)
                @if($i % 5 === 0)
                <div class="flex-1">{{ \Carbon\Carbon::parse($fecha)->format('j/n') }}</div>
                @else
                <div class="flex-1"></div>
                @endif
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-3 text-xs text-gray-400">
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-amber-400 inline-block"></span> Hoy</span>
            <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm bg-amber-200 inline-block"></span> Otros días</span>
            <span class="ml-auto">Máximo: <strong class="text-gray-600">{{ number_format($maxVisitas) }}</strong></span>
        </div>
    </div>
    @elseif($esBasico)
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

    {{-- ④ HORARIO + ⑤ DATOS — grid 2 cols, col-span condicional --}}
    {{-- Compacto:    [Horario col1] [Contacto col2] / [Descripción col-span-2] --}}
    {{-- No compacto: [Horario col-span-2] / [Contacto col1] [Descripción col2] --}}
    <div class="grid sm:grid-cols-2 gap-6 items-start mb-6">

    {{-- ④ HORARIO --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 {{ $horariosCompacto ? '' : 'sm:col-span-2' }}">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3 flex-wrap">
                <h2 class="text-sm font-semibold text-gray-700 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Horario
                </h2>
                @if(!empty($horariosFlat))
                    @if($abiertoAhora)
                        <span class="flex items-center gap-1.5 text-xs font-medium text-green-700 bg-green-50 ring-1 ring-green-200 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                            Abierto ahora
                            @if($cierraHoy)· Cierra a las {{ $cierraHoy }}@endif
                        </span>
                    @else
                        <span class="flex items-center gap-1.5 text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                            Cerrado ahora
                            @if($abreHoy)· Abre a las {{ $abreHoy }}@endif
                        </span>
                    @endif
                @endif
            </div>
            <a href="{{ route('panel.edit') }}" class="text-xs text-amber-600 hover:underline shrink-0">Editar</a>
        </div>

        @if(empty($horariosFlat))
            <p class="text-sm text-gray-400 italic">No hay horario configurado aún. <a href="{{ route('panel.edit') }}" class="text-amber-600 hover:underline">Configurar ahora</a></p>
        @else
            <div class="space-y-2">
                @foreach($grupos as $g)
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-32 text-xs font-semibold {{ $g['cerrado'] ? 'text-gray-300' : 'text-gray-500' }} shrink-0">{{ $g['label'] }}</span>
                    @if($g['cerrado'])
                        <span class="text-xs text-gray-300">Cerrado</span>
                    @else
                        <span class="text-xs text-gray-700">{{ $g['apertura'] }} – {{ $g['cierre'] }}</span>
                    @endif
                </div>
                @endforeach
            </div>
            @if($cantEspeciales > 0)
                <p class="text-xs text-gray-400 mt-3 pt-3 border-t border-gray-50">
                    + {{ $cantEspeciales }} {{ $cantEspeciales === 1 ? 'día especial' : 'días especiales' }} configurado{{ $cantEspeciales === 1 ? '' : 's' }}
                </p>
            @endif
        @endif
    </div>{{-- /④ HORARIO --}}

    {{-- ⑤a CONTACTO --}}
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
                        @php
                            $url     = $ficha->sitio_web;
                            $urlNorm = preg_match('/^https?:\/\//i', $url) ? $url : 'https://' . $url;
                            $host    = parse_url($urlNorm, PHP_URL_HOST) ?: $url;
                        @endphp
                        <a href="{{ $urlNorm }}" target="_blank" class="text-amber-600 hover:underline truncate">{{ $host }}</a>
                    @else
                        <span class="text-gray-400">—</span>
                    @endif
                </li>
            </ul>
        </div>

    {{-- ⑤b DESCRIPCIÓN — fila entera si compacto, col2 si no --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 {{ $horariosCompacto ? 'sm:col-span-2' : '' }}">
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

    </div>{{-- /grid ④+⑤ --}}

    {{-- ⑥ BANNER DE UPGRADE --}}
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
