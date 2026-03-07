<?php

namespace App\Filament\Resources\GuiaResource\Pages;

use App\Filament\Resources\GuiaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGuia extends EditRecord
{
    protected static string $resource = GuiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
