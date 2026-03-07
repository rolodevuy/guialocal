<?php

namespace App\Filament\Resources\GuiaResource\Pages;

use App\Filament\Resources\GuiaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGuias extends ListRecords
{
    protected static string $resource = GuiaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
