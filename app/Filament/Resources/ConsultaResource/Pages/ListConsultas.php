<?php

namespace App\Filament\Resources\ConsultaResource\Pages;

use App\Filament\Resources\ConsultaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConsultas extends ListRecords
{
    protected static string $resource = ConsultaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
