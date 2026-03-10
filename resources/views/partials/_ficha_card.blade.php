@php
    $sectorObj = $sector ?? null;
    $bgLight = $sectorObj ? $sectorObj->color('bg_light', 'bg-amber-50') : 'bg-amber-50';
    $textColor = $sectorObj ? $sectorObj->color('text', 'text-amber-600') : 'text-amber-600';
    $borderColor = $sectorObj ? $sectorObj->color('border', 'border-amber-200') : 'border-amber-200';
    $badgeBg = $sectorObj ? $sectorObj->color('bg', 'bg-amber-100') : 'bg-amber-500';
    $iconColor = $sectorObj ? $sectorObj->color('icon', 'text-amber-200') : 'text-amber-200';
@endphp

<a href="{{ route('negocios.show', $ficha->lugar) }}"
   class="group flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-lg hover:{{ $borderColor }} transition-all duration-200 h-full">

    {{-- Imagen --}}
    <div class="relative h-36 sm:h-40 {{ $bgLight }} overflow-hidden shrink-0">
        @php $portadaUrl = $ficha->getPortadaUrl(); @endphp
        @if($portadaUrl)
            <img src="{{ $portadaUrl }}"
                 alt="{{ $ficha->lugar->nombre }}"
                 loading="lazy"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center">
                <svg class="w-14 h-14 {{ $iconColor }} opacity-40" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/>
                </svg>
            </div>
        @endif
        @if($ficha->plan === 'premium')
            <span class="absolute top-2 right-2 sm:top-3 sm:right-3 text-xs font-bold bg-amber-500 text-white px-2 py-0.5 rounded-full uppercase tracking-wide shadow-sm">
                Premium
            </span>
        @endif
    </div>

    {{-- Info --}}
    <div class="p-3 sm:p-4 flex flex-col flex-1">
        <h3 class="font-bold text-gray-900 text-sm sm:text-base group-hover:{{ $textColor }} transition-colors leading-snug">
            {{ $ficha->lugar->nombre }}
        </h3>
        @if($ficha->descripcion)
        <p class="text-xs sm:text-sm text-gray-500 mt-1 line-clamp-2 leading-relaxed flex-1">
            {{ $ficha->descripcion }}
        </p>
        @endif
        <div class="flex items-center justify-between mt-2 sm:mt-3 pt-2 sm:pt-3 border-t border-gray-50">
            <span class="text-xs text-gray-400 truncate mr-2">
                {{ $ficha->lugar->categoria->nombre }}
                @if($ficha->lugar->zona) · {{ $ficha->lugar->zona->nombre }} @endif
            </span>
            <span class="text-xs {{ $textColor }} font-medium shrink-0">Ver →</span>
        </div>
    </div>

</a>
