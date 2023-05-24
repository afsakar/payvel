<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBill extends ViewRecord
{
    protected static string $resource = BillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('secondary'),
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.bills.index'))
        ];
    }
}
