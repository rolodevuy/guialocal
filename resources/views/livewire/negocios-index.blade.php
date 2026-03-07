<div
    x-data
    @guardar-zona.window="
        if ($event.detail.slug) {
            document.cookie = 'zona_preferida=' + $event.detail.slug + '; path=/; max-age=' + (60 * 60 * 24 * 30);
        } else {
            document.cookie = 'zona_preferida=; path=/; max-age=0';
        }
    "
>
    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Negocios</h1>
        <p class="text-gray-500 mt-1">
            {{ $fichas->total() }} resultado{{ $fichas->total() !== 1 ? 's' : '' }}
            @if(trim($q))
                para "<span class="font-medium text-gray-700">{{ $q }}</span>"
            @endif
        </p>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR FILTROS --}}
        <aside class="lg:w-60 shrink-0">

            {{-- Buscador --}}
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Buscar</label>
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="q"
                        placeholder="Nombre o descripción..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                    >
                    <span class="absolute right-2.5 top-2.5 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                </div>
            </div>

            {{-- Categorías --}}
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Categoría</label>
                <div class="space-y-1">
                    <button
                        wire:click="$set('categoria', '')"
                        class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors {{ $categoria === '' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}"
                    >Todas</button>
                    @foreach($categorias as $cat)
                    <button
                        wire:click="$set('categoria', '{{ $cat->slug }}')"
                        class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors {{ $categoria === $cat->slug ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}"
                    >{{ $cat->nombre }}</button>
                    @endforeach
                </div>
            </div>

            {{-- Zonas --}}
            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Zona</label>
                <div class="space-y-1">
                    <button
                        wire:click="$set('zona', '')"
                        class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors {{ $zona === '' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}"
                    >Todas</button>
                    @foreach($zonas as $z)
                    <button
                        wire:click="$set('zona', '{{ $z->slug }}')"
                        class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors {{ $zona === $z->slug ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}"
                    >{{ $z->nombre }}</button>
                    @endforeach
                </div>
            </div>

            @if($q !== '' || $categoria !== '' || $zona !== '')
            <button
                wire:click="limpiar"
                class="block w-full text-center text-xs text-gray-400 hover:text-red-500 transition-colors mt-2"
            >× Limpiar filtros</button>
            @endif

        </aside>

        {{-- GRID NEGOCIOS --}}
        <div class="flex-1">
            @if($fichas->isEmpty())
                <div class="text-center py-20 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-4 opacity-40" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                    <p class="font-medium text-gray-500">No encontramos negocios con esos filtros.</p>
                    <button wire:click="limpiar" class="mt-3 inline-block text-sm text-amber-600 hover:underline">Ver todos</button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                    @foreach($fichas as $ficha)
                    <a href="{{ route('negocios.show', $ficha->lugar) }}"
                       wire:key="ficha-{{ $ficha->id }}"
                       class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all">
                        <div class="h-36 bg-amber-50 overflow-hidden">
                            @php $portadaUrl = $ficha->getPortadaUrl(); @endphp
                            @if($portadaUrl)
                                <img src="{{ $portadaUrl }}"
                                     alt="{{ $ficha->lugar->nombre }}"
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
                                    {{ $ficha->lugar->nombre }}
                                </h3>
                                @if($ficha->featured)
                                    <span class="shrink-0 text-xs bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded font-medium">★</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400 mb-3 line-clamp-2">{{ $ficha->descripcion }}</p>
                            <div class="flex items-center gap-2 text-xs text-gray-400">
                                <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $ficha->lugar->categoria->nombre }}</span>
                                <span>·</span>
                                <span>{{ $ficha->lugar->zona?->nombre }}</span>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>

                {{-- Paginación --}}
                @if($fichas->hasPages())
                <div class="mt-10">
                    {{ $fichas->links() }}
                </div>
                @endif
            @endif
        </div>

    </div>
</div>
