<?php

namespace App\Filament\Resources\WithHoldingResource\Pages;

use App\Filament\Resources\WithHoldingResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageWithHoldings extends ManageRecords
{
    protected static string $resource = WithHoldingResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
