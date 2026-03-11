<x-filament-panels::page>
    {{-- Acciones --}}
    <div class="flex flex-wrap gap-3">
        <x-filament::button wire:click="createBackupDb" icon="heroicon-o-circle-stack" wire:loading.attr="disabled">
            Backup solo BD
        </x-filament::button>

        <x-filament::button wire:click="createBackupFull" color="warning" icon="heroicon-o-archive-box" wire:loading.attr="disabled">
            Backup completo (BD + archivos)
        </x-filament::button>

        <x-filament::button wire:click="cleanOldBackups" color="gray" icon="heroicon-o-trash">
            Limpiar antiguos
        </x-filament::button>
    </div>

    <div wire:loading class="text-sm text-gray-500 mt-2">
        Creando backup, puede tardar unos segundos...
    </div>

    {{-- Lista de backups --}}
    <div class="mt-6">
        @php $backups = $this->getBackups(); @endphp

        @if(empty($backups))
            <div class="fi-ta-empty-state flex flex-1 flex-col items-center justify-center p-6 text-center">
                <div class="fi-ta-empty-state-icon-ctn mb-4">
                    <x-heroicon-o-circle-stack class="h-12 w-12 text-gray-400" />
                </div>
                <p class="text-sm text-gray-500">No hay backups todavia. Crea uno con los botones de arriba.</p>
            </div>
        @else
            <div class="fi-ta-ctn rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
                <table class="fi-ta-table w-full table-auto divide-y divide-gray-200 text-start dark:divide-white/5">
                    <thead>
                        <tr>
                            <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">Archivo</th>
                            <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">Fecha</th>
                            <th class="fi-ta-header-cell px-4 py-3 text-start text-sm font-semibold text-gray-950 dark:text-white">Peso</th>
                            <th class="fi-ta-header-cell px-4 py-3 text-end text-sm font-semibold text-gray-950 dark:text-white">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-white/5">
                        @foreach($backups as $backup)
                            <tr class="fi-ta-row">
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                    {{ $backup['name'] }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500">
                                    {{ $backup['date'] }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-sm text-gray-500">
                                    {{ $backup['size'] }}
                                </td>
                                <td class="fi-ta-cell px-4 py-3 text-end">
                                    <div class="flex justify-end gap-2">
                                        <x-filament::button size="xs" color="gray" icon="heroicon-o-arrow-down-tray" wire:click="downloadBackup('{{ $backup['path'] }}')">
                                            Descargar
                                        </x-filament::button>
                                        <x-filament::button size="xs" color="danger" icon="heroicon-o-trash" wire:click="deleteBackup('{{ $backup['path'] }}')" wire:confirm="Seguro que queres eliminar este backup?">
                                            Eliminar
                                        </x-filament::button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="mt-3 text-xs text-gray-400">
                Rotacion automatica: se mantienen los ultimos 3 dias (max 500 MB). Cron diario a la 01:30 (solo BD).
            </p>
        @endif
    </div>
</x-filament-panels::page>
