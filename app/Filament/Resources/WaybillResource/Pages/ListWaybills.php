<?php

namespace App\Filament\Resources\WaybillResource\Pages;

use App\Filament\Resources\WaybillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWaybills extends ListRecords
{
    protected static string $resource = WaybillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
