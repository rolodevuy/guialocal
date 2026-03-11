<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <title>@yield('title', 'Guía Local — Tu barrio en un solo lugar')</title>
    <meta name="description" content="@yield('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')">
    <link rel="canonical" href="{{ url()->current() }}">
    {{-- Open Graph --}}
    <meta property="og:site_name" content="Guía Local">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', 'Guía Local — Tu barrio en un solo lugar')">
    <meta property="og:description" content="@yield('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')">
    <meta property="og:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="@yield('title', 'Guía Local')">
    {{-- Twitter / X --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Guía Local — Tu barrio en un solo lugar')">
    <meta name="twitter:description" content="@yield('description', 'Encontrá los mejores negocios, restaurantes, farmacias y servicios de tu barrio.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/og-default.jpg'))">
    <link rel="alternate" type="application/rss+xml" title="{{ config('app.name') }} — Artículos" href="{{ route('feed') }}">
    @stack('meta')
    @stack('styles')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 text-gray-800 antialiased">

    {{-- NAVBAR --}}
    <header class="bg-white shadow-sm sticky top-0 z-50" x-data="{ open: false }">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-1.5 font-extrabold hover:opacity-90 transition-opacity">
                    <svg class="w-7 h-7 text-amber-500 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <span class="text-xl"><span class="text-amber-500" style="font-size:0.9em">GUÍA</span><span class="text-gray-900">LOCAL</span></span>
                </a>

                {{-- Nav desktop --}}
                <nav class="hidden md:flex items-center gap-1">
                    <a href="{{ route('home') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-amber-600 hover:bg-amber-50 transition-colors {{ request()->routeIs('home') ? 'text-amber-600 bg-amber-50' : '' }}">
                        Inicio
                    </a>
                    @foreach($sectoresNav as $sectorNav)
                    <a href="{{ route('sectores.show', $sectorNav) }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:{{ $sectorNav->color('text', 'text-amber-600') }} hover:bg-gray-50 transition-colors {{ request()->is('sectores/' . $sectorNav->slug) ? $sectorNav->color('text', 'text-amber-600') . ' bg-gray-50' : '' }}">
                        {{ $sectorNav->nombre_corto ?? $sectorNav->nombre }}
                    </a>
                    @endforeach
                    @if($hayArticulos)
                    <a href="{{ route('articulos.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-amber-600 hover:bg-amber-50 transition-colors {{ request()->routeIs('articulos.*') ? 'text-amber-600 bg-amber-50' : '' }}">
                        Artículos
                    </a>
                    @endif
                    @if($hayGuias)
                    <a href="{{ route('guias.index') }}"
                       class="px-3 py-2 rounded-lg text-sm font-medium text-gray-600 hover:text-amber-600 hover:bg-amber-50 transition-colors {{ request()->routeIs('guias.*') ? 'text-amber-600 bg-amber-50' : '' }}">
                        Guías
                    </a>
                    @endif
                    <a href="{{ route('contacto.show') }}"
                       class="ml-2 px-4 py-2 rounded-lg text-sm font-medium bg-amber-500 text-white hover:bg-amber-600 transition-colors">
                        Contacto
                    </a>
                    <a href="{{ route('negocios.index') }}"
                       class="ml-1 p-2 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                       title="Buscar">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803 7.5 7.5 0 0016.803 15.803z"/>
                        </svg>
                    </a>
                </nav>

                {{-- Hamburger mobile --}}
                <button @click="open = !open"
                        class="md:hidden p-2 rounded-lg text-gray-500 hover:text-amber-600 hover:bg-amber-50 transition-colors"
                        aria-label="Menú">
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg x-show="open" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak
             class="md:hidden border-t border-gray-100 bg-white">
            <nav class="px-4 py-3 flex flex-col gap-1">
                <a href="{{ route('home') }}" @click="open = false"
                   class="px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                    Inicio
                </a>
                @foreach($sectoresNav as $sectorNav)
                <a href="{{ route('sectores.show', $sectorNav) }}" @click="open = false"
                   class="px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:{{ $sectorNav->color('text', 'text-amber-600') }} hover:bg-gray-50 transition-colors flex items-center gap-2">
                    <span class="w-5 h-5 {{ $sectorNav->color('bg', 'bg-gray-100') }} rounded flex items-center justify-center {{ $sectorNav->color('icon', 'text-gray-500') }}">
                        <x-cat-icon :name="$sectorNav->icono ?? 'default'" class="w-3.5 h-3.5" />
                    </span>
                    {{ $sectorNav->nombre }}
                </a>
                @endforeach
                @if($hayArticulos)
                <a href="{{ route('articulos.index') }}" @click="open = false"
                   class="px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                    Artículos
                </a>
                @endif
                @if($hayGuias)
                <a href="{{ route('guias.index') }}" @click="open = false"
                   class="px-4 py-2.5 rounded-lg text-sm font-medium text-gray-700 hover:text-amber-600 hover:bg-amber-50 transition-colors">
                    Guías
                </a>
                @endif
                <a href="{{ route('contacto.show') }}" @click="open = false"
                   class="px-4 py-2.5 rounded-lg text-sm font-medium text-amber-600 hover:bg-amber-50 transition-colors">
                    Contacto
                </a>
            </nav>
        </div>
    </header>

    {{-- MAIN CONTENT --}}
    <main>
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-gray-800 text-gray-300">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                {{-- Brand --}}
                <div>
                    <div class="flex items-center gap-1.5 font-extrabold mb-3">
                        <svg class="w-6 h-6 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                        </svg>
                        <span class="text-lg"><span class="text-amber-400" style="font-size:0.9em">GUÍA</span><span class="text-white">LOCAL</span></span>
                    </div>
                    <p class="text-sm text-gray-400 leading-relaxed mb-4">
                        Tu guía de negocios y servicios del barrio. Encontrá lo que necesitás cerca tuyo.
                    </p>
                    <div class="flex items-center gap-3">
                        <a href="https://www.instagram.com/guialocal.uy" target="_blank" rel="noopener" class="text-gray-400 hover:text-amber-400 transition-colors" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                        </a>
                        <a href="https://www.facebook.com/guialocal.uy" target="_blank" rel="noopener" class="text-gray-400 hover:text-amber-400 transition-colors" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="https://x.com/guialocaluy" target="_blank" rel="noopener" class="text-gray-400 hover:text-amber-400 transition-colors" aria-label="X (Twitter)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>
                    </div>
                </div>

                {{-- Links --}}
                <div>
                    <h3 class="text-white font-semibold mb-3 text-sm uppercase tracking-wider">Explorar</h3>
                    <ul class="space-y-2 text-sm">
                        <li><a href="{{ route('negocios.index') }}" class="hover:text-amber-400 transition-colors">Todos los negocios</a></li>
                        @foreach($sectoresNav as $sectorNav)
                        <li><a href="{{ route('sectores.show', $sectorNav) }}" class="hover:text-amber-400 transition-colors">{{ $sectorNav->nombre }}</a></li>
                        @endforeach
                        <li><a href="{{ route('categorias.index') }}" class="hover:text-amber-400 transition-colors">Todas las categorías</a></li>
                        <li><a href="{{ route('contacto.show') }}" class="hover:text-amber-400 transition-colors">Contacto</a></li>
                    </ul>
                </div>

                {{-- CTA --}}
                <div>
                    <h3 class="text-white font-semibold mb-3 text-sm uppercase tracking-wider">¿Tenés un negocio?</h3>
                    <p class="text-sm text-gray-400 mb-4">Sumá tu negocio a la guía y llegá a más clientes del barrio.</p>
                    <a href="{{ route('contacto.show') }}"
                       class="inline-block px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Registrar mi negocio
                    </a>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-10 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Guía Local. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
