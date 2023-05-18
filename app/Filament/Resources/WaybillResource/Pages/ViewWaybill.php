<?php

namespace App\Filament\Resources\WaybillResource\Pages;

use App\Filament\Resources\WaybillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWaybill extends ViewRecord
{
    protected static string $resource = WaybillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('secondary'),
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.waybills.index'))
        ];
    }
}
