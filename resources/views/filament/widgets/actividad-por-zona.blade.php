<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">Actividad por zona</x-slot>
        <x-slot name="description">Fichas activas y visitas acumuladas por barrio</x-slot>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left py-2 px-3 font-semibold text-gray-600 dark:text-gray-300">Zona</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-300">Fichas</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-300">Premium</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-300">Básico</th>
                        <th class="text-right py-2 px-3 font-semibold text-gray-600 dark:text-gray-300">Visitas</th>
                        <th class="py-2 px-3 font-semibold text-gray-600 dark:text-gray-300">Distribución</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zonas as $zona)
                        @php
                            $porcentaje = $totalVisitas > 0
                                ? round(($zona->total_visitas / $totalVisitas) * 100)
                                : 0;
                        @endphp
                        <tr class="border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="py-2 px-3 font-medium text-gray-800 dark:text-gray-200">
                                {{ $zona->nombre }}
                            </td>
                            <td class="py-2 px-3 text-right text-gray-700 dark:text-gray-300">
                                {{ number_format($zona->total_fichas) }}
                            </td>
                            <td class="py-2 px-3 text-right">
                                @if($zona->fichas_premium > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                        {{ $zona->fichas_premium }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-right">
                                @if($zona->fichas_basico > 0)
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                        {{ $zona->fichas_basico }}
                                    </span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="py-2 px-3 text-right font-bold text-gray-800 dark:text-gray-100">
                                {{ number_format($zona->total_visitas ?? 0) }}
                            </td>
                            <td class="py-2 px-3 min-w-[120px]">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div
                                            class="bg-amber-500 h-1.5 rounded-full"
                                            style="width: {{ $porcentaje }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-500 w-8 text-right">{{ $porcentaje }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-gray-400">
                                No hay zonas con fichas activas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
