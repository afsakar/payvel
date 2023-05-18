<?php

namespace App\Filament\Resources\WaybillResource\Pages;

use App\Filament\Resources\WaybillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWaybill extends EditRecord
{
    protected static string $resource = WaybillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.waybills.index'))
        ];
    }
}
