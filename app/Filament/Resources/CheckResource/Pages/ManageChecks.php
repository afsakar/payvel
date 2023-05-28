<?php

namespace App\Filament\Resources\CheckResource\Pages;

use App\Filament\Resources\CheckResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageChecks extends ManageRecords
{
    protected static string $resource = CheckResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
