<div
    x-data="{
        sheetOpen: false,
        pickerOpen: false,
        desktopZonaOpen: false,
        search: '',
        dSearch: '',
        zonasList: {{ $zonas->map(fn ($z) => ['slug' => $z->slug, 'nombre' => $z->nombre])->toJson() }},
        get filtradas() {
            if (!this.search) return this.zonasList;
            const q = this.search.toLowerCase();
            return this.zonasList.filter(z => z.nombre.toLowerCase().includes(q));
        },
        get filtradas2() {
            if (!this.dSearch) return this.zonasList;
            const q = this.dSearch.toLowerCase();
            return this.zonasList.filter(z => z.nombre.toLowerCase().includes(q));
        }
    }"
    @guardar-zona.window="
        if ($event.detail.slug) {
            document.cookie = 'zona_preferida=' + $event.detail.slug + '; path=/; max-age=' + (60 * 60 * 24 * 30);
        } else {
            document.cookie = 'zona_preferida=; path=/; max-age=0';
        }
    "
>

    {{-- Header --}}
    <div class="mb-5">
        <h1 class="text-3xl font-bold text-gray-800">Negocios</h1>
        <p class="text-gray-500 mt-1">
            {{ $fichas->total() }} resultado{{ $fichas->total() !== 1 ? 's' : '' }}
            @if(trim($q))
                para "<span class="font-medium text-gray-700">{{ $q }}</span>"
            @endif
        </p>
    </div>

    {{-- ═══════════════════════════════════════════════════════════
         BARRA DE FILTROS RÁPIDOS — visible en MOBILE y DESKTOP
         ═══════════════════════════════════════════════════════════ --}}
    <div class="flex items-center gap-2 mb-5 flex-wrap">

        {{-- ── Pill de zona ─────────────────────────────────────── --}}

        {{-- MOBILE: abre picker modal (igual que antes) --}}
        <button @click="pickerOpen = true"
                class="lg:hidden inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm border transition-colors
                       {{ $zona ? 'bg-amber-50 border-amber-300 text-amber-700 font-medium' : 'bg-white border-gray-200 text-gray-500' }}">
            <svg class="w-3.5 h-3.5 shrink-0 {{ $zona ? 'text-amber-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
            </svg>
            <span>{{ $zona ? ($zonas->firstWhere('slug', $zona)?->nombre ?? $zona) : 'Todas las zonas' }}</span>
            @if($zona)
                <span wire:click.stop="$set('zona', '')" @click.stop
                      class="flex items-center justify-center w-3.5 h-3.5 rounded-full hover:bg-amber-200 transition-colors">
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </span>
            @else
                <svg class="w-3 h-3 opacity-40 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                </svg>
            @endif
        </button>

        {{-- DESKTOP: pill con dropdown integrado --}}
        <div class="hidden lg:block relative" @click.outside="desktopZonaOpen = false">
            {{-- Pill button --}}
            <div class="inline-flex items-center rounded-full text-sm border transition-colors
                        {{ $zona ? 'bg-amber-50 border-amber-300 text-amber-700 font-medium' : 'bg-white border-gray-200 text-gray-600 hover:border-gray-300' }}">
                <button @click="desktopZonaOpen = !desktopZonaOpen"
                        class="flex items-center gap-1.5 select-none pl-3.5 py-2 {{ $zona ? 'pr-1.5' : 'pr-3' }}">
                    <svg class="w-3.5 h-3.5 shrink-0 {{ $zona ? 'text-amber-500' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                    </svg>
                    <span>{{ $zona ? ($zonas->firstWhere('slug', $zona)?->nombre ?? $zona) : 'Zona' }}</span>
                    @if(!$zona)
                        <svg class="w-3.5 h-3.5 shrink-0 opacity-40 transition-transform duration-150"
                             :class="desktopZonaOpen ? 'rotate-180' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                        </svg>
                    @endif
                </button>
                {{-- × para limpiar zona activa --}}
                @if($zona)
                <button wire:click="$set('zona', '')"
                        @click.stop="desktopZonaOpen = false"
                        class="pr-2.5 py-2 opacity-60 hover:opacity-100 transition-opacity">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                @endif
            </div>

            {{-- Dropdown panel --}}
            <div x-show="desktopZonaOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                 x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                 x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute top-full left-0 mt-1.5 z-30 bg-white rounded-xl shadow-xl border border-gray-100 w-60 overflow-hidden origin-top-left">
                {{-- Buscador --}}
                <div class="p-2 border-b border-gray-100">
                    <input
                        x-model="dSearch"
                        x-ref="dSearchInput"
                        x-effect="desktopZonaOpen && $nextTick(() => $refs.dSearchInput?.focus())"
                        type="text"
                        placeholder="Buscar zona..."
                        class="w-full px-3 py-2 rounded-lg border border-gray-200 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100"
                    >
                </div>
                {{-- Lista de zonas --}}
                <ul class="max-h-56 overflow-y-auto py-1">
                    <li>
                        <button @click="$wire.$set('zona', ''); desktopZonaOpen = false; dSearch = ''"
                                class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm transition-colors
                                       {{ $zona === '' ? 'bg-amber-50 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            <svg class="w-3.5 h-3.5 shrink-0 opacity-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2"/></svg>
                            Todas las zonas
                        </button>
                    </li>
                    <template x-for="z in filtradas2" :key="z.slug">
                        <li>
                            <button @click="$wire.$set('zona', z.slug); desktopZonaOpen = false; dSearch = ''"
                                    class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                                </svg>
                                <span x-text="z.nombre"></span>
                            </button>
                        </li>
                    </template>
                    <template x-if="filtradas2.length === 0">
                        <li class="px-4 py-3 text-sm text-gray-400 text-center">Sin resultados</li>
                    </template>
                </ul>
            </div>
        </div>

        {{-- ── Pill "Abierto ahora" — mobile y desktop ─────────── --}}
        <button wire:click="$toggle('soloAbiertos')"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 lg:px-3.5 lg:py-2 rounded-full text-sm border transition-colors select-none
                       {{ $soloAbiertos ? 'bg-green-50 border-green-400 text-green-700 font-semibold' : 'bg-white border-gray-200 text-gray-500 hover:border-gray-300' }}">
            <span class="relative flex h-2 w-2 shrink-0">
                @if($soloAbiertos)
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                @else
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-300"></span>
                @endif
            </span>
            Abierto ahora
        </button>

        {{-- ── Chip de categoría activa (ambos) ────────────────── --}}
        @if($categoria)
        @php
            $catActiva = $categorias->firstWhere('slug', $categoria)
                      ?? $categorias->flatMap->children->firstWhere('slug', $categoria);
        @endphp
        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm bg-gray-100 border border-gray-200 text-gray-700">
            {{ $catActiva?->nombre ?? $categoria }}
            <button wire:click="$set('categoria', '')"
                    class="ml-0.5 flex items-center justify-center w-3.5 h-3.5 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </span>
        @endif

        {{-- ── Limpiar todo ─────────────────────────────────────── --}}
        @if($zona || $categoria || $q || $soloAbiertos)
        <button wire:click="limpiar"
                class="text-xs text-gray-400 hover:text-red-500 transition-colors underline underline-offset-2 ml-1">
            Limpiar todo
        </button>
        @endif

    </div>

    {{-- ── Layout principal ────────────────────────────────────────────────── --}}
    <div class="flex flex-col lg:flex-row gap-8">

        {{-- SIDEBAR (solo desktop) — solo búsqueda y categorías --}}
        <aside class="hidden lg:block lg:w-56 xl:w-60 shrink-0">

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Buscar</label>
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="q"
                        placeholder="Nombre, descripción..."
                        class="w-full border border-gray-200 rounded-lg px-3 py-2 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                    >
                    <span class="absolute right-2.5 top-2.5 text-gray-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </span>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Categoría</label>
                <div class="space-y-0.5">
                    <button wire:click="$set('categoria', '')"
                            class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors {{ $categoria === '' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-100' }}">
                        Todas
                    </button>
                    @foreach($categorias as $cat)
                        @php
                            $isParentActive = $categoria === $cat->slug;
                            $childSlugs     = $cat->children->pluck('slug')->toArray();
                            $isChildActive  = in_array($categoria, $childSlugs);
                            $isExpanded     = $isParentActive || $isChildActive;
                            $catIds         = collect([$cat->id])->merge($cat->children->pluck('id'));
                            $catCount       = $catIds->sum(fn ($id) => $conteosPorCat[$id] ?? 0);
                        @endphp
                        <button wire:click="$set('categoria', '{{ $cat->slug }}')"
                                class="w-full text-left px-3 py-1.5 rounded-lg text-sm transition-colors flex items-center justify-between
                                       {{ $isParentActive ? 'bg-amber-100 text-amber-700 font-medium' : ($isChildActive ? 'text-amber-600 font-medium' : 'text-gray-600 hover:bg-gray-100') }}">
                            <span>{{ $cat->nombre }}</span>
                            @if($catCount > 0)
                                <span class="text-[10px] min-w-[1.25rem] text-center rounded-full px-1 {{ $isParentActive ? 'bg-amber-200/70 text-amber-800' : 'bg-gray-100 text-gray-400' }}">{{ $catCount }}</span>
                            @endif
                        </button>
                        @if($cat->children->isNotEmpty() && $isExpanded)
                            @foreach($cat->children as $sub)
                            @php $subCount = $conteosPorCat[$sub->id] ?? 0; @endphp
                            <button wire:click="$set('categoria', '{{ $sub->slug }}')"
                                    class="w-full text-left pl-7 pr-3 py-1 rounded-lg text-xs transition-colors flex items-center justify-between
                                           {{ $categoria === $sub->slug ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-500 hover:bg-gray-50' }}">
                                <span>{{ $sub->nombre }}</span>
                                @if($subCount > 0)
                                    <span class="text-[10px] min-w-[1.25rem] text-center rounded-full px-1 {{ $categoria === $sub->slug ? 'bg-amber-200/70 text-amber-800' : 'bg-gray-100 text-gray-400' }}">{{ $subCount }}</span>
                                @endif
                            </button>
                            @endforeach
                        @endif
                    @endforeach
                </div>
            </div>

            @if($q !== '' || $categoria !== '')
            <button wire:click="limpiar"
                    class="block w-full text-center text-xs text-gray-400 hover:text-red-500 transition-colors mt-2">
                × Limpiar búsqueda
            </button>
            @endif

        </aside>

        {{-- GRID de resultados --}}
        <div class="flex-1 pb-24 lg:pb-0" id="resultados">

            {{-- Skeleton --}}
            <div wire:loading.flex class="flex-wrap gap-5" style="display:none">
                @for($i = 0; $i < 6; $i++)
                <div class="w-full sm:w-[calc(50%-10px)] xl:w-[calc(33.333%-14px)] bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden animate-pulse">
                    <div class="h-36 bg-gray-200"></div>
                    <div class="p-4 space-y-3">
                        <div class="h-4 bg-gray-200 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-100 rounded w-full"></div>
                        <div class="h-3 bg-gray-100 rounded w-1/2"></div>
                        <div class="flex gap-2">
                            <div class="h-4 bg-gray-100 rounded w-20"></div>
                            <div class="h-4 bg-gray-100 rounded w-16"></div>
                        </div>
                    </div>
                </div>
                @endfor
            </div>

            {{-- Contenido real --}}
            <div wire:loading.remove>
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
                    @php
                        $portadaUrl   = $ficha->getPortadaUrl();
                        $abiertoAhora = !empty($ficha->horarios) ? $ficha->isAbiertoAhora() : null;
                    @endphp
                    <a href="{{ route('negocios.show', $ficha->lugar) }}"
                       wire:key="ficha-{{ $ficha->id }}"
                       class="group bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:-translate-y-0.5 transition-all flex flex-col">

                        {{-- Imagen --}}
                        <div class="relative h-36 bg-amber-50 overflow-hidden shrink-0">
                            @if($portadaUrl)
                                <img src="{{ $portadaUrl }}"
                                     alt="{{ $ficha->lugar->nombre }}"
                                     loading="lazy"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center text-amber-200 gap-1">
                                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                    </svg>
                                </div>
                            @endif

                            {{-- Badges superpuestos --}}
                            <div class="absolute top-2 left-2 flex gap-1.5">
                                @if($ficha->plan === 'premium')
                                    <span class="text-xs font-bold text-white px-2 py-0.5 rounded-full shadow-sm" style="background:#f59e0b">Premium</span>
                                @endif
                                @if($abiertoAhora === true)
                                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-white px-2 py-0.5 rounded-full shadow-sm" style="background:#22c55e">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                        Abierto
                                    </span>
                                @elseif($abiertoAhora === false)
                                    <span class="text-xs font-medium text-white px-2 py-0.5 rounded-full" style="background:rgba(0,0,0,0.5)">Cerrado</span>
                                @endif
                            </div>
                        </div>

                        {{-- Info --}}
                        <div class="p-4 flex flex-col flex-1">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h3 class="font-semibold text-gray-800 group-hover:text-amber-600 transition-colors text-sm leading-tight">
                                    {{ $ficha->lugar->nombre }}
                                </h3>
                                @if($ficha->featured)
                                    <span class="shrink-0 text-xs bg-amber-100 text-amber-600 px-1.5 py-0.5 rounded font-medium">★</span>
                                @endif
                            </div>

                            <p class="text-xs text-gray-400 mb-3 line-clamp-2 flex-1">{{ $ficha->descripcion }}</p>

                            <div class="flex items-center justify-between gap-2 pt-2 border-t border-gray-50">
                                <div class="flex items-center gap-1.5 text-xs text-gray-400 min-w-0">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded truncate">{{ $ficha->lugar->categoria->raiz->nombre }}</span>
                                    @if($ficha->lugar->zona)
                                        <span class="truncate">· {{ $ficha->lugar->zona->nombre }}</span>
                                    @endif
                                </div>
                                @if($ficha->telefono)
                                    <span class="shrink-0 text-gray-300">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </div>

                    </a>
                    @endforeach
                </div>

                @if($fichas->hasPages())
                <div class="mt-10">
                    {{ $fichas->links() }}
                </div>
                @endif
            @endif
            </div>

        </div>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════
         MOBILE ONLY — FAB + Bottom sheet + Zone picker modal
         ════════════════════════════════════════════════════════════════════ --}}

    {{-- FAB "Filtros" (mobile) — muestra solo filtros del sheet: q y categoria --}}
    @php $activeCount = ($categoria !== '' ? 1 : 0) + ($q !== '' ? 1 : 0); @endphp
    <div class="lg:hidden fixed bottom-6 right-6 z-40">
        @if($activeCount === 0)
        <span class="absolute inset-0 rounded-full bg-gray-700 animate-ping opacity-40"></span>
        @endif
        <button @click="sheetOpen = true"
                class="relative w-14 h-14 flex items-center justify-center bg-gray-900 hover:bg-gray-800 text-white rounded-full shadow-2xl transition-colors active:scale-95">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
            </svg>
            @if($activeCount > 0)
            <span class="absolute -top-1 -right-1 w-5 h-5 flex items-center justify-center bg-amber-500 text-white text-xs font-bold rounded-full ring-2 ring-white">
                {{ $activeCount }}
            </span>
            @endif
        </button>
    </div>

    {{-- Bottom sheet (mobile) — búsqueda y categorías --}}
    <div x-show="sheetOpen"
         x-cloak
         class="lg:hidden fixed inset-0 z-50"
         @keydown.escape.window="sheetOpen = false">

        {{-- Backdrop --}}
        <div x-show="sheetOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sheetOpen = false"
             class="absolute inset-0 bg-black/40">
        </div>

        {{-- Sheet panel --}}
        <div x-show="sheetOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full"
             x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="translate-y-0"
             x-transition:leave-end="translate-y-full"
             class="absolute bottom-0 left-0 right-0 bg-white rounded-t-2xl max-h-[80vh] flex flex-col shadow-2xl">

            {{-- Handle --}}
            <div class="flex justify-center pt-3 pb-1 shrink-0">
                <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
            </div>

            {{-- Header del sheet --}}
            <div class="flex items-center justify-between px-5 py-3 border-b border-gray-100 shrink-0">
                <h3 class="font-bold text-gray-900">Buscar y filtrar</h3>
                <div class="flex items-center gap-3">
                    @if($categoria !== '' || $q !== '')
                    <button wire:click="limpiar" @click="sheetOpen = false"
                            class="text-xs text-gray-400 hover:text-red-500 transition-colors">
                        Limpiar
                    </button>
                    @endif
                    <button @click="sheetOpen = false"
                            class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Contenido scrolleable --}}
            <div class="overflow-y-auto flex-1 px-5 py-4">

                {{-- Buscador --}}
                <div class="mb-5">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Buscar</label>
                    <div class="relative">
                        <input
                            type="text"
                            wire:model.live.debounce.300ms="q"
                            placeholder="Nombre o descripción..."
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400"
                        >
                        <span class="absolute right-3 top-2.5 text-gray-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                    </div>
                </div>

                {{-- Categorías --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Categoría</label>
                    <div class="space-y-0.5">
                        <button wire:click="$set('categoria', '')" @click="sheetOpen = false"
                                class="w-full text-left px-3 py-2.5 rounded-xl text-sm transition-colors
                                       {{ $categoria === '' ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                            Todas
                        </button>
                        @foreach($categorias as $cat)
                        @php
                            $catIdsMobile  = collect([$cat->id])->merge($cat->children->pluck('id'));
                            $catCountMobile = $catIdsMobile->sum(fn ($id) => $conteosPorCat[$id] ?? 0);
                            $isActiveMobile = $categoria === $cat->slug;
                            $isChildActiveMobile = in_array($categoria, $cat->children->pluck('slug')->toArray());
                        @endphp
                        <button wire:click="$set('categoria', '{{ $cat->slug }}')" @click="sheetOpen = false"
                                class="w-full text-left px-3 py-2.5 rounded-xl text-sm transition-colors flex items-center justify-between
                                       {{ $isActiveMobile ? 'bg-amber-100 text-amber-700 font-medium' : ($isChildActiveMobile ? 'text-amber-600 font-medium' : 'text-gray-600 hover:bg-gray-50') }}">
                            <span>{{ $cat->nombre }}</span>
                            @if($catCountMobile > 0)
                                <span class="text-[10px] min-w-[1.25rem] text-center rounded-full px-1 {{ $isActiveMobile ? 'bg-amber-200/70 text-amber-800' : 'bg-gray-100 text-gray-400' }}">{{ $catCountMobile }}</span>
                            @endif
                        </button>
                        @if($cat->children->isNotEmpty())
                            @foreach($cat->children as $sub)
                            @php $subCountMobile = $conteosPorCat[$sub->id] ?? 0; @endphp
                            <button wire:click="$set('categoria', '{{ $sub->slug }}')" @click="sheetOpen = false"
                                    class="w-full text-left pl-7 pr-3 py-2 rounded-xl text-xs transition-colors flex items-center justify-between
                                           {{ $categoria === $sub->slug ? 'bg-amber-100 text-amber-700 font-medium' : 'text-gray-500 hover:bg-gray-50' }}">
                                <span>{{ $sub->nombre }}</span>
                                @if($subCountMobile > 0)
                                    <span class="text-[10px] min-w-[1.25rem] text-center rounded-full px-1 {{ $categoria === $sub->slug ? 'bg-amber-200/70 text-amber-800' : 'bg-gray-100 text-gray-400' }}">{{ $subCountMobile }}</span>
                                @endif
                            </button>
                            @endforeach
                        @endif
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Zone picker modal (mobile pill de zona) --}}
    <div x-show="pickerOpen"
         x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click.self="pickerOpen = false"
         @keydown.escape.window="pickerOpen = false"
         class="lg:hidden fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6" @click.stop>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-gray-900 text-base">¿En qué zona?</h3>
                <button @click="pickerOpen = false"
                        class="p-1 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <input
                type="text"
                x-model="search"
                x-ref="zonaBuscador"
                x-effect="pickerOpen && $nextTick(() => { if (!window.matchMedia('(hover: none)').matches) $refs.zonaBuscador?.focus() })"
                placeholder="Buscar zona..."
                class="w-full px-4 py-2.5 rounded-xl border border-gray-200 text-sm outline-none focus:border-amber-400 focus:ring-2 focus:ring-amber-100 mb-3"
            >
            <ul class="max-h-64 overflow-y-auto -mx-1 space-y-0.5">
                <li>
                    <button @click="$wire.$set('zona', ''); pickerOpen = false"
                            class="w-full text-left px-4 py-2.5 rounded-xl text-sm transition-colors flex items-center gap-2
                                   {{ $zona === '' ? 'bg-amber-50 text-amber-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                        <span class="w-3.5 h-3.5 shrink-0"></span>
                        Todas las zonas
                    </button>
                </li>
                <template x-for="z in filtradas" :key="z.slug">
                    <li>
                        <button @click="$wire.$set('zona', z.slug); pickerOpen = false"
                                class="w-full text-left px-4 py-2.5 rounded-xl text-sm text-gray-700 hover:bg-amber-50 hover:text-amber-700 transition-colors flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                            </svg>
                            <span x-text="z.nombre"></span>
                        </button>
                    </li>
                </template>
                <template x-if="filtradas.length === 0">
                    <li class="px-4 py-3 text-sm text-gray-400 text-center">Sin resultados</li>
                </template>
            </ul>
        </div>
    </div>

</div>
