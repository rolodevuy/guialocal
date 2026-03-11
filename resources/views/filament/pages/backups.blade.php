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

        <x-filament::button wire:click="$set('showConfig', true)" color="gray" icon="heroicon-o-cog-6-tooth">
            Configuración
        </x-filament::button>
    </div>

    <div wire:loading class="text-sm text-gray-500 mt-2">
        Creando backup, puede tardar unos segundos...
    </div>

    {{-- Modal de configuración --}}
    @if($showConfig)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" wire:click.self="$set('showConfig', false)">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 w-full max-w-lg p-6 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-950 dark:text-white">Configuración de Backups</h3>
                <button wire:click="$set('showConfig', false)" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <x-heroicon-o-x-mark class="w-5 h-5" />
                </button>
            </div>

            <div class="space-y-4">
                {{-- Prefijo del archivo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Prefijo del archivo</label>
                    <input type="text" wire:model="filenamePrefix" placeholder="ej: guialocal-backup"
                        class="fi-input block w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-white/10 dark:bg-white/5 dark:text-white" />
                    <p class="text-xs text-gray-400 mt-1">Resultado: {{ $filenamePrefix ? $filenamePrefix . '-' : '' }}2026-03-11-01-30-00.zip</p>
                </div>

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
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Proteger con contraseña</span>
                    <button type="button" wire:click="$toggle('passwordEnabled')"
                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-600 focus:ring-offset-2 {{ $passwordEnabled ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700' }}"
                        role="switch" aria-checked="{{ $passwordEnabled ? 'true' : 'false' }}">
                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $passwordEnabled ? 'translate-x-5' : 'translate-x-0' }}"></span>
                    </button>
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

            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200 dark:border-white/10">
                <x-filament::button color="gray" wire:click="$set('showConfig', false)">
                    Cancelar
                </x-filament::button>
                <x-filament::button wire:click="saveConfig" icon="heroicon-o-check">
                    Guardar
                </x-filament::button>
            </div>
        </div>
    </div>
    @endif

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
