<!DOCTYPE html>
<html lang="es" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso para negocios — Guía Local</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center px-4">

    <div class="w-full max-w-sm">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-amber-500 hover:text-amber-600 transition-colors">
                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                </svg>
                <span class="font-bold text-xl text-gray-800">Guía Local</span>
            </a>
            <p class="text-sm text-gray-500 mt-2">Panel de gestión de negocios</p>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <h1 class="text-lg font-bold text-gray-800 mb-6">Ingresá a tu cuenta</h1>

            <form method="POST" action="{{ route('panel.login.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-medium text-gray-600 mb-1.5">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        autofocus
                        class="w-full border rounded-xl px-4 py-2.5 text-sm outline-none transition-colors
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50 focus:border-red-400' : 'border-gray-200 focus:border-amber-400 focus:ring-2 focus:ring-amber-100' }}"
                    >
                    @error('email')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-xs font-medium text-gray-600 mb-1.5">Contraseña</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="current-password"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 transition-colors"
                    >
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-amber-500 focus:ring-amber-400">
                    <label for="remember" class="text-xs text-gray-500">Recordarme</label>
                </div>

                <button type="submit"
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white font-semibold py-2.5 rounded-xl transition-colors text-sm">
                    Ingresar
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-gray-400 mt-6">
            ¿No tenés acceso?
            <a href="{{ route('contacto.show') }}" class="text-amber-600 hover:underline">Contactanos</a>
        </p>

    </div>

</body>
</html>
