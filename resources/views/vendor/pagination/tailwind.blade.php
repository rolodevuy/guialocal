@if ($paginator->hasPages())
<nav class="flex items-center justify-between gap-2 mt-2" aria-label="Paginación">

    {{-- Anterior --}}
    @if ($paginator->onFirstPage())
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm text-gray-300 bg-white border border-gray-100 cursor-not-allowed select-none">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Anterior
        </span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm text-gray-600 bg-white border border-gray-200 hover:border-amber-300 hover:text-amber-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Anterior
        </a>
    @endif

    {{-- Números (solo desktop) --}}
    <div class="hidden sm:flex items-center gap-1">
        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-2 py-1 text-sm text-gray-400">{{ $element }}</span>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-semibold bg-amber-500 text-white">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $url }}"
                           class="w-9 h-9 flex items-center justify-center rounded-lg text-sm text-gray-600 hover:bg-amber-50 hover:text-amber-600 transition-colors">
                            {{ $page }}
                        </a>
                    @endif
                @endforeach
            @endif
        @endforeach
    </div>

    {{-- Página X de Y (solo mobile) --}}
    <span class="sm:hidden text-sm text-gray-400">
        Página {{ $paginator->currentPage() }} de {{ $paginator->lastPage() }}
    </span>

    {{-- Siguiente --}}
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm text-gray-600 bg-white border border-gray-200 hover:border-amber-300 hover:text-amber-600 transition-colors">
            Siguiente
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @else
        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm text-gray-300 bg-white border border-gray-100 cursor-not-allowed select-none">
            Siguiente
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </span>
    @endif

</nav>
@endif
