<?php

namespace App\Filament\Resources\RevenueResource\Pages;

use App\Filament\Resources\RevenueResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRevenues extends ManageRecords
{
    protected static string $resource = RevenueResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RevenueResource\Widgets\RevenueWidget::class,
        ];
    }
}
