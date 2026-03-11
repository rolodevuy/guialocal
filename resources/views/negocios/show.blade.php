@extends('layouts.app')

@section('title', $lugar->nombre . ' — Guía Local')
@section('description', Str::limit($ficha?->descripcion, 155))

@section('og_type', 'article')

@php
    $ogImage = $ficha?->hasMedia('portada')
        ? $ficha->getFirstMediaUrl('portada')
        : asset('images/og-default.jpg');
@endphp
@section('og_image', $ogImage)

@push('meta')
    {{-- JSON-LD: schema.org LocalBusiness --}}
    @php
        $schemaTypes = [
            'utensils'      => 'Restaurant',
            'coffee'        => 'CafeOrCoffeeShop',
            'cake'          => 'Bakery',
            'pill'          => 'Pharmacy',
            'shopping-cart' => 'GroceryStore',
            'heart-pulse'   => 'HealthAndBeautyBusiness',
            'briefcase'     => 'ProfessionalService',
            'shirt'         => 'ClothingStore',
        ];
        $schemaType = $schemaTypes[$lugar->categoria->icono ?? ''] ?? 'LocalBusiness';

        $schema = [
            '@context' => 'https://schema.org',
            '@type'    => $schemaType,
            'name'     => $lugar->nombre,
            'url'      => route('negocios.show', $lugar),
        ];

        if ($ficha?->descripcion)
            $schema['description'] = Str::limit($ficha->descripcion, 300);

        if ($lugar->direccion)
            $schema['address'] = [
                '@type'           => 'PostalAddress',
                'streetAddress'   => $lugar->direccion,
                'addressLocality' => $lugar->zona?->nombre,
                'addressCountry'  => 'UY',
            ];

        if ($ficha?->telefono)
            $schema['telephone'] = $ficha->telefono;

        if ($ficha?->email)
            $schema['email'] = $ficha->email;

        $sameAs = array_filter(array_merge(
            $ficha?->sitio_web ? [$ficha->sitio_web] : [],
            collect($ficha?->redes_sociales ?? [])->pluck('url')->all()
        ));
        if ($sameAs) $schema['sameAs'] = count($sameAs) === 1 ? array_values($sameAs)[0] : array_values($sameAs);

        if ($lugar->lat && $lugar->lng)
            $schema['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => $lugar->lat,
                'longitude' => $lugar->lng,
            ];

        if ($ficha?->hasMedia('portada'))
            $schema['image'] = $ficha->getFirstMediaUrl('portada');

        if ($promociones->isNotEmpty()) {
            $schema['hasOfferCatalog'] = [
                '@type' => 'OfferCatalog',
                'name'  => 'Promociones vigentes',
                'itemListElement' => $promociones->map(fn($p) => array_filter([
                    '@type'       => 'Offer',
                    'name'        => $p->titulo,
                    'description' => $p->descripcion ?? null,
                    'validFrom'   => $p->fecha_inicio?->toDateString(),
                    'validThrough'=> $p->fecha_fin?->toDateString(),
                ]))->values()->all(),
            ];
        }

        if (!empty($ficha?->horarios)) {
            $dayMap = [
                'Lunes'     => 'Mo', 'Martes'    => 'Tu', 'Miércoles' => 'We',
                'Jueves'    => 'Th', 'Viernes'   => 'Fr', 'Sábado'    => 'Sa',
                'Domingo'   => 'Su',
            ];
            $openingHours = collect($ficha->horarios)
                ->filter(fn($f) => !($f['cerrado'] ?? false) && !empty($f['apertura']) && !empty($f['cierre']))
                ->map(function ($f) use ($dayMap) {
                    $inicio = $dayMap[$f['dia_inicio']] ?? null;
                    $fin    = !empty($f['dia_fin']) ? ($dayMap[$f['dia_fin']] ?? null) : null;
                    $days   = $inicio . ($fin ? '-' . $fin : '');
                    return $days . ' ' . $f['apertura'] . '-' . $f['cierre'];
                })
                ->values()
                ->all();
            if ($openingHours) $schema['openingHours'] = $openingHours;
        }

        if (!empty($ficha?->horarios_especiales)) {
            $hoy = now()->startOfDay();
            $especiales = collect($ficha->horarios_especiales)
                ->filter(fn($h) => ($h['activo'] ?? false) && !empty($h['fecha']))
                ->map(function ($h) use ($hoy) {
                    $fecha = \Carbon\Carbon::parse($h['fecha']);
                    if ($h['se_repite'] ?? false) {
                        $fecha = $fecha->setYear(now()->year);
                        if ($fecha->lt($hoy)) $fecha = $fecha->addYear();
                    }
                    $spec = [
                        '@type'        => 'OpeningHoursSpecification',
                        'validFrom'    => $fecha->toDateString(),
                        'validThrough' => $fecha->toDateString(),
                    ];
                    if ($h['cerrado'] ?? false) {
                        $spec['opens']  = '00:00';
                        $spec['closes'] = '00:00';
                    } else {
                        $spec['opens']  = $h['apertura'] ?? '00:00';
                        $spec['closes'] = $h['cierre'] ?? '23:59';
                    }
                    return $spec;
                })
                ->values()
                ->all();
            if ($especiales) $schema['specialOpeningHoursSpecification'] = $especiales;
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}</script>
@endpush

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-400 mb-6 flex items-center gap-1.5">
        <a href="{{ route('home') }}" class="hover:text-amber-600 transition-colors">Inicio</a>
        <span>›</span>
        <a href="{{ route('negocios.index') }}" class="hover:text-amber-600 transition-colors">Negocios</a>
        <span>›</span>
        <a href="{{ route('negocios.index', ['categoria' => $lugar->categoria->slug]) }}"
           class="hover:text-amber-600 transition-colors">{{ $lugar->categoria->nombre }}</a>
        <span>›</span>
        <span class="text-gray-600">{{ $lugar->nombre }}</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- COLUMNA PRINCIPAL --}}
        <div class="flex-1 min-w-0">

            {{-- Imagen portada --}}
            <div class="rounded-2xl overflow-hidden bg-amber-50 mb-6 h-56 sm:h-72">
                @php $portadaUrl = $ficha?->getPortadaUrl() ?? ''; @endphp
                @if($portadaUrl)
                    <img src="{{ $portadaUrl }}"
                         alt="{{ $lugar->nombre }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-20 h-20 text-amber-200" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Encabezado --}}
            <div class="flex flex-wrap items-start gap-3 mb-4">
                <div class="flex-1 min-w-0">
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 leading-tight flex items-center gap-1.5">
                        {{ $lugar->nombre }}
                        @if($ficha?->is_verified)
                            <x-verified-badge size="md" />
                        @endif
                    </h1>
                    <div class="flex flex-wrap items-center gap-2 mt-2">
                        <a href="{{ route('negocios.index', ['categoria' => $lugar->categoria->slug]) }}"
                           class="text-xs bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full font-medium hover:bg-amber-200 transition-colors">
                            {{ $lugar->categoria->nombre }}
                        </a>
                        @if($lugar->zona)
                        <a href="{{ route('zonas.show', $lugar->zona) }}"
                           class="text-xs bg-gray-100 text-gray-600 px-2.5 py-1 rounded-full font-medium hover:bg-gray-200 transition-colors">
                            {{ $lugar->zona->nombre }}
                        </a>
                        @endif
                        @if($ficha?->featured)
                            <span class="text-xs bg-amber-400 text-white px-2.5 py-1 rounded-full font-medium">★ Destacado</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Descripción --}}
            @if($ficha?->descripcion)
            <div class="prose prose-gray max-w-none text-gray-600 leading-relaxed mb-8">
                <p>{{ $ficha->descripcion }}</p>
            </div>
            @endif

            {{-- Promociones vigentes --}}
            @if($promociones->isNotEmpty())
            <div class="mb-8">
                <div class="flex items-center gap-2 mb-3">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-amber-500 shrink-0">
                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                  d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">
                        Promociones vigentes
                    </h2>
                </div>
                <div class="flex flex-col gap-3">
                    @foreach($promociones as $promo)
                    @php $imagenPromo = $promo->getFirstMediaUrl('imagen'); @endphp
                    <div class="relative overflow-hidden rounded-xl border border-amber-200 bg-gradient-to-r from-amber-50 to-white">
                        {{-- Acento lateral --}}
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-amber-400 rounded-l-xl"></div>

                        <div class="flex items-center gap-4 pl-5 pr-4 py-4">
                            {{-- Imagen o ícono --}}
                            @if($imagenPromo)
                                <img src="{{ $imagenPromo }}"
                                     alt="{{ $promo->titulo }}"
                                     loading="lazy"
                                     class="w-14 h-14 rounded-lg object-cover shrink-0 shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-lg bg-amber-100 flex items-center justify-center shrink-0">
                                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Texto --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm leading-snug">{{ $promo->titulo }}</p>
                                @if($promo->descripcion)
                                    <p class="text-xs text-gray-500 mt-0.5 leading-relaxed">{{ $promo->descripcion }}</p>
                                @endif
                                <div class="flex items-center gap-3 mt-1.5 flex-wrap">
                                    @if($promo->fecha_fin)
                                        <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                            Hasta el {{ $promo->fecha_fin->translatedFormat('j \d\e F') }}
                                        </span>
                                    @else
                                        <span class="text-xs text-green-600 font-medium">Sin fecha de vencimiento</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Galería --}}
            @php $galeria = $ficha?->getMedia('galeria') ?? collect(); @endphp
            @if($galeria->isNotEmpty())
            <div class="mb-8">
                <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Galería</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($galeria as $imagen)
                    <div class="aspect-square rounded-xl overflow-hidden bg-gray-100">
                        <img src="{{ $imagen->getUrl() }}"
                             alt="{{ $lugar->nombre }}"
                             class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- ── Reseñas (feature flag) ────────────────────────────────── --}}
            @if(config('features.resenas'))
            <div class="mb-8" id="resenas">

                {{-- Encabezado --}}
                <div class="flex items-center gap-2 mb-4">
                    <span class="flex items-center justify-center w-5 h-5 rounded-full bg-gray-200 shrink-0">
                        <svg class="w-3 h-3 text-gray-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </span>
                    <h2 class="text-sm font-bold text-gray-700 uppercase tracking-wider">
                        Reseñas
                        @if($resenas->isNotEmpty())
                            <span class="text-gray-400 font-normal normal-case tracking-normal ml-1">({{ $resenas->count() }})</span>
                        @endif
                    </h2>
                </div>

                {{-- Flash: reseña enviada --}}
                @if(session('resena_enviada'))
                <div class="mb-4 flex items-start gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800">
                    <svg class="w-5 h-5 shrink-0 text-green-500 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="font-semibold">¡Gracias por tu reseña!</p>
                        <p class="text-green-700 text-xs mt-0.5">La revisaremos antes de publicarla.</p>
                    </div>
                </div>
                @endif

                {{-- Lista de reseñas aprobadas --}}
                @if($resenas->isNotEmpty())
                <div class="space-y-4 mb-6">
                    @foreach($resenas as $resena)
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $resena->nombre }}</p>
                                <p class="text-xs text-gray-400">{{ $resena->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="text-amber-400 shrink-0 text-sm tracking-tight" title="{{ $resena->rating }}/5">
                                @for($i = 1; $i <= 5; $i++)
                                    {{ $i <= $resena->rating ? '★' : '☆' }}
                                @endfor
                            </span>
                        </div>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $resena->cuerpo }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-gray-400 mb-6">Todavía no hay reseñas. ¡Sé el primero!</p>
                @endif

                {{-- Formulario nueva reseña --}}
                <div class="border border-gray-100 rounded-xl p-5"
                     x-data="{ rating: 0, hover: 0 }">
                    <h3 class="text-sm font-semibold text-gray-700 mb-4">Dejá tu reseña</h3>
                    <form action="{{ route('negocios.resenas.store', $lugar->slug) }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Estrellas interactivas --}}
                        <div>
                            <label class="block text-xs text-gray-500 mb-1.5">Puntuación <span class="text-red-400">*</span></label>
                            <div class="flex gap-1">
                                @for($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        @click="rating = {{ $i }}"
                                        @mouseenter="hover = {{ $i }}"
                                        @mouseleave="hover = 0"
                                        class="text-2xl transition-colors focus:outline-none"
                                        :class="(hover || rating) >= {{ $i }} ? 'text-amber-400' : 'text-gray-200'">
                                    ★
                                </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" :value="rating">
                            @error('rating')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Nombre <span class="text-red-400">*</span></label>
                                <input type="text" name="nombre" value="{{ old('nombre') }}"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400
                                              {{ $errors->has('nombre') ? 'border-red-300' : '' }}">
                                @error('nombre') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs text-gray-500 mb-1">Email <span class="text-gray-300">(opcional, privado)</span></label>
                                <input type="email" name="email" value="{{ old('email') }}"
                                       class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Reseña <span class="text-red-400">*</span></label>
                            <textarea name="cuerpo" rows="3"
                                      placeholder="Contá tu experiencia..."
                                      class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none
                                             {{ $errors->has('cuerpo') ? 'border-red-300' : '' }}">{{ old('cuerpo') }}</textarea>
                            @error('cuerpo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <button type="submit"
                                class="px-5 py-2.5 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-xl transition-colors">
                            Enviar reseña
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>

        {{-- SIDEBAR CONTACTO --}}
        <aside class="lg:w-72 shrink-0">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">

                {{-- Logo (solo si existe) --}}
                @if($ficha?->hasMedia('logo'))
                <div class="flex justify-center pb-1">
                    <img src="{{ $ficha->getFirstMediaUrl('logo') }}"
                         alt="Logo {{ $lugar->nombre }}"
                         class="max-h-20 max-w-full object-contain rounded-2xl">
                </div>
                @endif

                {{-- Contacto --}}
                <div>
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Contacto</h2>
                    <ul class="space-y-3">
                        @if($ficha?->telefono)
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                </svg>
                            </span>
                            <a href="tel:{{ $ficha->telefono }}" class="text-sm text-gray-700 hover:text-amber-600 transition-colors">
                                {{ $ficha->telefono }}
                            </a>
                        </li>
                        @endif

                        @if($ficha?->email)
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                            <a href="mailto:{{ $ficha->email }}" class="text-sm text-gray-700 hover:text-amber-600 transition-colors truncate">
                                {{ $ficha->email }}
                            </a>
                        </li>
                        @endif

                        @if($ficha?->sitio_web)
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                                </svg>
                            </span>
                            <a href="{{ $ficha->sitio_web }}" target="_blank" rel="noopener noreferrer"
                               class="text-sm text-amber-600 hover:underline truncate">
                                {{ parse_url($ficha->sitio_web, PHP_URL_HOST) ?: $ficha->sitio_web }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- Redes sociales --}}
                @if(!empty($ficha?->redes_sociales))
                @php
                    $redesConfig = [
                        'instagram' => ['label' => 'Instagram', 'bg' => '#E1306C', 'text' => '#ffffff'],
                        'facebook'  => ['label' => 'Facebook',  'bg' => '#1877F2', 'text' => '#ffffff'],
                        'tiktok'    => ['label' => 'TikTok',    'bg' => '#010101', 'text' => '#ffffff'],
                        'youtube'   => ['label' => 'YouTube',   'bg' => '#FF0000', 'text' => '#ffffff'],
                        'twitter'   => ['label' => 'X',         'bg' => '#000000', 'text' => '#ffffff'],
                        'linkedin'  => ['label' => 'LinkedIn',  'bg' => '#0A66C2', 'text' => '#ffffff'],
                        'whatsapp'  => ['label' => 'WhatsApp',  'bg' => '#25D366', 'text' => '#ffffff'],
                    ];
                @endphp
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Redes sociales</h2>
                    <div class="flex flex-wrap gap-2">
                        @foreach($ficha->redes_sociales as $red)
                        @php $cfg = $redesConfig[$red['red']] ?? ['label' => ucfirst($red['red']), 'bg' => '#6B7280', 'text' => '#ffffff']; @endphp
                        <a href="{{ $red['url'] }}"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
                            <x-social-icon :red="$red['red']" class="w-3.5 h-3.5" style="color: {{ $cfg['bg'] }}" />
                            {{ $cfg['label'] }}
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Dirección --}}
                @if($lugar->direccion)
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Dirección</h2>
                    <div class="flex items-start gap-3">
                        <span class="w-8 h-8 bg-amber-50 rounded-lg flex items-center justify-center shrink-0 mt-0.5">
                            <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </span>
                        <p class="text-sm text-gray-700 leading-relaxed">{{ $lugar->direccion }}</p>
                    </div>
                </div>
                @endif

                {{-- Horarios --}}
                @if(!empty($ficha?->horarios))
                <div class="border-t border-gray-100 pt-5">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Horarios</h2>
                        @if($ficha->isAbiertoAhora())
                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Abierto ahora
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 bg-gray-50 border border-gray-200 px-2 py-0.5 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                                Cerrado
                            </span>
                        @endif
                    </div>
                    <ul class="space-y-1.5">
                        @foreach($ficha->horarios as $franja)
                        <li class="flex items-start justify-between gap-2 text-sm">
                            <span class="text-gray-500 shrink-0">
                                {{ $franja['dia_inicio'] }}{{ !empty($franja['dia_fin']) ? ' a ' . $franja['dia_fin'] : '' }}
                            </span>
                            @if($franja['cerrado'] ?? false)
                                <span class="text-gray-400 italic">Cerrado</span>
                            @else
                                <span class="text-gray-700 text-right">{{ $franja['apertura'] }} – {{ $franja['cierre'] }}</span>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Fechas especiales activas --}}
                @php
                    $hoy = now()->startOfDay();
                    $fechasActivas = collect($ficha?->horarios_especiales ?? [])
                        ->filter(function ($h) use ($hoy) {
                            if (!($h['activo'] ?? false) || empty($h['fecha'])) return false;
                            return true;
                        })
                        ->map(function ($h) {
                            $fecha = \Carbon\Carbon::parse($h['fecha']);
                            if ($h['se_repite'] ?? false) {
                                $fecha = $fecha->setYear(now()->year);
                                if ($fecha->lt(now()->startOfDay())) $fecha = $fecha->addYear();
                            }
                            return array_merge($h, ['_fecha_obj' => $fecha]);
                        })
                        ->sortBy(fn($h) => $h['_fecha_obj'])
                        ->values();
                @endphp
                @if($fechasActivas->isNotEmpty())
                <div class="border-t border-gray-100 pt-5">
                    <h2 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Fechas especiales</h2>
                    <ul class="space-y-2">
                        @foreach($fechasActivas as $fe)
                        <li class="text-sm">
                            <div class="flex items-start justify-between gap-2">
                                <span class="font-medium text-gray-700">{{ $fe['nombre'] }}</span>
                                @if($fe['cerrado'] ?? false)
                                    <span class="text-gray-400 italic shrink-0">Cerrado</span>
                                @else
                                    <span class="text-gray-700 shrink-0">{{ $fe['apertura'] }} – {{ $fe['cierre'] }}</span>
                                @endif
                            </div>
                            <div class="text-xs text-gray-400 mt-0.5">
                                {{ $fe['_fecha_obj']->translatedFormat('j \d\e F') }}
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- CTA plan-aware --}}
                <div class="border-t border-gray-100 pt-5">
                    @if($ficha?->plan === 'premium')

                        {{-- Premium: mostrar badge, sin CTA agresivo --}}
                        <div class="flex items-center justify-center gap-2 py-1">
                            <span class="flex items-center gap-1.5 text-xs font-semibold text-amber-600 bg-amber-50 border border-amber-200 px-3 py-1.5 rounded-full">
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Plan Premium activo
                            </span>
                        </div>

                    @elseif($ficha?->plan === 'basico')

                        {{-- Básico: nudge suave a Premium --}}
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 text-center">¿Es tu negocio?</p>
                        <div class="bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-3.5 mb-3">
                            <p class="text-xs font-bold text-gray-800 mb-2">Pasate a Premium</p>
                            <ul class="space-y-1.5">
                                @foreach([
                                    'Mayor posición en los resultados',
                                    'Promociones destacadas',
                                    'Galería ampliada + logo',
                                ] as $beneficio)
                                <li class="flex items-start gap-2 text-xs text-gray-600">
                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    {{ $beneficio }}
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        <a href="{{ route('contacto.show') }}?asunto=upgrade-premium"
                           class="block w-full text-center px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                            Quiero el plan Premium
                        </a>

                    @else

                        {{-- Gratuito / sin ficha: CTA para reclamar --}}
                        @if(!$ficha?->user_id)
                            <p class="text-xs text-gray-400 mb-2 text-center">¿Es tu negocio?</p>
                            <a href="{{ route('negocios.claim', $lugar) }}"
                               class="block w-full text-center px-4 py-2.5 border border-amber-400 text-amber-600 hover:bg-amber-50 text-sm font-medium rounded-xl transition-colors">
                                Reclamalo y gestionalo
                            </a>
                        @endif

                    @endif
                </div>

            </div>
        </aside>

    </div>

    {{-- ── Otros [categoría] cerca (Haversine, si hay lat/lng) ────────────── --}}
    @if($cerca->isNotEmpty())
    <div class="mt-10 pt-8 border-t border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="text-base sm:text-lg font-bold text-gray-900">
                    Otros {{ $categoriaRaizNombre ?? $lugar->categoria->nombre }} cerca
                </h2>
                <p class="text-xs text-gray-400 mt-0.5">Ordenados por distancia desde este local</p>
            </div>
            <a href="{{ route('negocios.index', ['categoria' => $lugar->categoria->slug]) }}"
               class="text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors shrink-0">
                Ver todos →
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($cerca as $sim)
            @php $simPortada = $sim->getPortadaUrl(); @endphp
            <a href="{{ route('negocios.show', $sim->lugar) }}"
               class="group bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">

                {{-- Imagen --}}
                <div class="relative h-28 sm:h-32 bg-amber-50 overflow-hidden shrink-0">
                    @if($simPortada)
                        <img src="{{ $simPortada }}"
                             alt="{{ $sim->lugar->nombre }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-amber-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                    @endif

                    {{-- Badge abierto --}}
                    @php $abiertoSim = !empty($sim->horarios) ? $sim->isAbiertoAhora() : null; @endphp
                    @if($abiertoSim === true)
                        <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-0.5 text-xs font-semibold text-white px-1.5 py-0.5 rounded-full" style="background:#22c55e">
                            <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                            Abierto
                        </span>
                    @endif

                    {{-- Badge distancia --}}
                    @if(isset($sim->distancia_km))
                        <span class="absolute top-1.5 right-1.5 inline-flex items-center gap-0.5 text-xs font-medium text-white bg-black/50 backdrop-blur-sm px-1.5 py-0.5 rounded-full">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $sim->distancia_km < 1
                                ? round($sim->distancia_km * 1000) . ' m'
                                : $sim->distancia_km . ' km' }}
                        </span>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-3 flex flex-col flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-xs sm:text-sm leading-tight line-clamp-2">
                        {{ $sim->lugar->nombre }}
                    </h3>
                    @if($sim->lugar->zona)
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ $sim->lugar->zona->nombre }}</p>
                    @endif
                </div>

            </a>
            @endforeach
        </div>
    </div>

    {{-- Fallback: similares por categoría cuando no hay datos de ubicación --}}
    @elseif($similares->isNotEmpty())
    <div class="mt-10 pt-8 border-t border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base sm:text-lg font-bold text-gray-900">
                Más negocios de {{ $lugar->categoria->nombre }}
            </h2>
            <a href="{{ route('negocios.index', ['categoria' => $lugar->categoria->slug]) }}"
               class="text-sm text-amber-600 hover:text-amber-700 font-medium transition-colors shrink-0">
                Ver todos →
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
            @foreach($similares as $sim)
            @php $simPortada = $sim->getPortadaUrl(); @endphp
            <a href="{{ route('negocios.show', $sim->lugar) }}"
               class="group bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">

                <div class="relative h-28 sm:h-32 bg-amber-50 overflow-hidden shrink-0">
                    @if($simPortada)
                        <img src="{{ $simPortada }}"
                             alt="{{ $sim->lugar->nombre }}"
                             loading="lazy"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-amber-200">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                            </svg>
                        </div>
                    @endif
                    @php $abiertoSim = !empty($sim->horarios) ? $sim->isAbiertoAhora() : null; @endphp
                    @if($abiertoSim === true)
                        <span class="absolute top-1.5 left-1.5 inline-flex items-center gap-0.5 text-xs font-semibold text-white px-1.5 py-0.5 rounded-full" style="background:#22c55e">
                            <span class="w-1 h-1 rounded-full bg-white animate-pulse"></span>
                            Abierto
                        </span>
                    @endif
                </div>

                <div class="p-3 flex flex-col flex-1">
                    <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-xs sm:text-sm leading-tight line-clamp-2">
                        {{ $sim->lugar->nombre }}
                    </h3>
                    @if($sim->lugar->zona)
                        <p class="text-xs text-gray-400 mt-1 truncate">{{ $sim->lugar->zona->nombre }}</p>
                    @endif
                </div>

            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

{{-- ── Botón flotante WhatsApp (solo plan Básico+) ────────────────────── --}}
@php
    $whatsappUrl = $ficha?->planIncluye('whatsapp')
        ? (collect($ficha?->redes_sociales ?? [])->firstWhere('red', 'whatsapp')['url'] ?? null)
        : null;
@endphp
@if($whatsappUrl)
<a href="{{ $whatsappUrl }}"
   target="_blank"
   rel="noopener noreferrer"
   title="Contactar por WhatsApp"
   class="fixed bottom-6 right-6 z-50 flex items-center gap-2.5
          text-white text-sm font-semibold
          pl-4 pr-5 py-3 rounded-full shadow-lg
          transition-all duration-200 hover:scale-105 active:scale-95 hover:shadow-xl"
   style="background:#25D366">
    <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="currentColor">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
    WhatsApp
</a>
@endif

@endsection
