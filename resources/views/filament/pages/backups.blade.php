<x-filament-panels::page>
    {{-- Configuración --}}
    <div class="fi-ta-ctn rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white mb-4">Configuración</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Hora del backup automático --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Hora del backup diario</label>
                <input type="time" wire:model="backupTime"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white" />
            </div>

            {{-- Días de retención --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Días de retención</label>
                <input type="number" wire:model="retentionDays" min="1" max="30"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white" />
            </div>

            {{-- Toggle contraseña --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Proteger con contraseña</label>
                <label class="relative inline-flex items-center cursor-pointer mt-1.5">
                    <input type="checkbox" wire:model.live="passwordEnabled" class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:after:border-gray-600 peer-checked:bg-primary-600"></div>
                    <span class="ms-2 text-sm text-gray-500">{{ $passwordEnabled ? 'Sí' : 'No' }}</span>
                </label>
            </div>

            {{-- Input contraseña --}}
            @if($passwordEnabled)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contraseña del zip</label>
                <input type="password" wire:model="backupPassword" placeholder="Mínimo 4 caracteres"
                    class="fi-input block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white" />
            </div>
            @endif
        </div>

        <div class="mt-4">
            <x-filament::button wire:click="saveConfig" icon="heroicon-o-check" size="sm">
                Guardar configuración
            </x-filament::button>
        </div>
    </div>

    {{-- Acciones manuales --}}
    <div class="flex flex-wrap gap-3 mt-6">
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
                <p class="text-sm text-gray-500">No hay backups todavía. Creá uno con los botones de arriba.</p>
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
                                        <x-filament::button size="xs" color="danger" icon="heroicon-o-trash" wire:click="deleteBackup('{{ $backup['path'] }}')" wire:confirm="¿Seguro que querés eliminar este backup?">
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
                Rotación automática: se mantienen los últimos {{ $retentionDays }} días (máx 500 MB). Backup diario a las {{ $backupTime }} (solo BD).
            </p>
        @endif
    </div>
</x-filament-panels::page>
