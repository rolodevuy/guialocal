@props(['size' => 'md'])

@php
    $classes = match($size) {
        'sm' => 'w-4 h-4',
        'md' => 'w-5 h-5',
        'lg' => 'w-6 h-6',
        default => 'w-5 h-5',
    };
@endphp

<span title="Negocio verificado" class="inline-flex items-center shrink-0">
    <svg class="{{ $classes }} text-blue-500" viewBox="0 0 24 24" fill="none">
        <circle cx="12" cy="12" r="10" fill="currentColor"/>
        <path d="M7.5 12l3 3 6-6" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</span>
