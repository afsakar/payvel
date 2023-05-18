<?php

namespace App\Filament\Resources\CorporationResource\Pages;

use App\Filament\Resources\CorporationResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCorporations extends ManageRecords
{
    protected static string $resource = CorporationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
