<?php

namespace App\Filament\Resources\SectorResource\Pages;

use App\Filament\Resources\SectorResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSector extends CreateRecord
{
    protected static string $resource = SectorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (! empty($data['color_preset']) && empty($data['color_classes'])) {
            $data['color_classes'] = SectorResource::buildColorClasses($data['color_preset']);
        }
        unset($data['color_preset']);

        return $data;
    }
}
