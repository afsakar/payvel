<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEvents extends ManageRecords
{
    protected static string $resource = EventResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
