<?php

namespace App\Filament\Resources\FeaturedSlotResource\Pages;

use App\Filament\Resources\FeaturedSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFeaturedSlot extends EditRecord
{
    protected static string $resource = FeaturedSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
