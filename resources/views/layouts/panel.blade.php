<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi negocio') — Panel</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full" x-data="{}">

    {{-- Navbar del panel --}}
    <header class="bg-white border-b border-gray-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" class="text-amber-500 hover:text-amber-600 transition-colors" title="Ir al sitio">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </a>
                <span class="text-gray-300">|</span>
                <span class="text-sm font-semibold text-gray-700">Panel de negocio</span>
            </div>
            <div class="flex items-center gap-4">
                @auth
                <span class="text-xs text-gray-400 hidden sm:block">{{ Auth::user()->name }}</span>
                <form method="POST" action="{{ route('panel.logout') }}">
                    @csrf
                    <button type="submit"
                            class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                        Cerrar sesión
                    </button>
                </form>
                @endauth
            </div>
        </div>
    </header>

    {{-- Contenido --}}
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-8">
        @yield('content')
    </main>

</body>
</html>
