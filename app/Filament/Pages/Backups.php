<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class Backups extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-circle-stack';
    protected static ?string $navigationLabel = 'Backups';
    protected static ?string $title           = 'Gestión de Backups';
    protected static ?string $navigationGroup = 'Herramientas';
    protected static ?int    $navigationSort  = 11;
    protected static string  $view            = 'filament.pages.backups';

    public bool $running = false;

    // Config properties
    public string $backupTime = '01:30';
    public bool $passwordEnabled = false;
    public string $backupPassword = '';
    public int $retentionDays = 3;

    public function mount(): void
    {
        $this->backupTime = Setting::get('backup_time', '01:30');
        $this->passwordEnabled = (bool) Setting::get('backup_password_enabled', '0');
        $this->backupPassword = Setting::get('backup_password', '');
        $this->retentionDays = (int) Setting::get('backup_retention_days', '3');
    }

    public function saveConfig(): void
    {
        if ($this->passwordEnabled && strlen($this->backupPassword) < 4) {
            Notification::make()
                ->title('La contraseña debe tener al menos 4 caracteres')
                ->danger()
                ->send();
            return;
        }

        if (! preg_match('/^\d{2}:\d{2}$/', $this->backupTime)) {
            Notification::make()
                ->title('Formato de hora inválido (usar HH:MM)')
                ->danger()
                ->send();
            return;
        }

        Setting::set('backup_time', $this->backupTime);
        Setting::set('backup_password_enabled', $this->passwordEnabled ? '1' : '0');
        Setting::set('backup_password', $this->passwordEnabled ? $this->backupPassword : '');
        Setting::set('backup_retention_days', (string) max(1, $this->retentionDays));

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }

    private function applyPasswordConfig(): void
    {
        if ($this->passwordEnabled && $this->backupPassword) {
            config(['backup.backup.destination.password' => $this->backupPassword]);
        } else {
            config(['backup.backup.destination.password' => null]);
        }
    }

    public function getBackups(): array
    {
        $disk = Storage::disk('local');
        $appName = config('app.name');

        if (! $disk->exists($appName)) {
            return [];
        }

        $files = collect($disk->files($appName))
            ->filter(fn ($f) => str_ends_with($f, '.zip'))
            ->sortByDesc(fn ($f) => $disk->lastModified($f))
            ->values();

        return $files->map(fn ($file) => [
            'path'     => $file,
            'name'     => basename($file),
            'size'     => $this->formatBytes($disk->size($file)),
            'date'     => date('d/m/Y H:i', $disk->lastModified($file)),
        ])->toArray();
    }

    public function createBackupDb(): void
    {
        $this->running = true;

        try {
            $this->applyPasswordConfig();
            Artisan::call('backup:run', ['--only-db' => true]);
            Notification::make()
                ->title('Backup de BD creado correctamente')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error al crear backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->running = false;
    }

    public function createBackupFull(): void
    {
        $this->running = true;

        try {
            $this->applyPasswordConfig();
            Artisan::call('backup:run');
            Notification::make()
                ->title('Backup completo creado (BD + archivos)')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error al crear backup')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->running = false;
    }

    public function downloadBackup(string $path): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::disk('local')->download($path);
    }

    public function deleteBackup(string $path): void
    {
        Storage::disk('local')->delete($path);

        Notification::make()
            ->title('Backup eliminado')
            ->success()
            ->send();
    }

    public function cleanOldBackups(): void
    {
        try {
            Artisan::call('backup:clean');
            Notification::make()
                ->title('Limpieza completada')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Error en limpieza')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
