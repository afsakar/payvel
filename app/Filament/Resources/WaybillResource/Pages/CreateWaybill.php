<?php

namespace App\Filament\Resources\WaybillResource\Pages;

use App\Filament\Resources\WaybillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWaybill extends CreateRecord
{
    protected static string $resource = WaybillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('general.go_back'))
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.waybills.index'))
        ];
    }
}
