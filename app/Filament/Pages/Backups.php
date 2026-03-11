<?php

namespace App\Filament\Pages;

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

    public function getBackups(): array
    {
        $disk = Storage::disk('local');
        $appName = config('app.name');
        $path = "private/{$appName}";

        if (! $disk->exists($path)) {
            return [];
        }

        $files = collect($disk->files($path))
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
