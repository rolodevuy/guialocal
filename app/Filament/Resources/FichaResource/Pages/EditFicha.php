<?php

namespace App\Filament\Resources\FichaResource\Pages;

use App\Filament\Resources\FichaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFicha extends EditRecord
{
    protected static string $resource = FichaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
