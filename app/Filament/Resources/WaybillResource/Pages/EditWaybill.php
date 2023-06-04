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
            Actions\DeleteAction::make()
                ->hidden(fn ($record) => $record->has_any_relation),
            Actions\ForceDeleteAction::make()
                ->before(function ($record) {
                    if ($record->items && $record->items->count() > 0) {
                        foreach ($record->items as $item) {
                            $item->delete();
                        }
                    }
                }),
            Actions\RestoreAction::make(),
            Actions\Action::make('back')
                ->label(__('general.go_back'))
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.waybills.index'))
        ];
    }
}
