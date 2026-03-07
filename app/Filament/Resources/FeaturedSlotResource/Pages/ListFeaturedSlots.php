<?php

namespace App\Filament\Resources\FeaturedSlotResource\Pages;

use App\Filament\Resources\FeaturedSlotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFeaturedSlots extends ListRecords
{
    protected static string $resource = FeaturedSlotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
