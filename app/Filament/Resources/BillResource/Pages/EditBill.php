<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBill extends EditRecord
{
    protected static string $resource = BillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->hidden(fn ($record) => $record->has_any_relation),
            Actions\ForceDeleteAction::make()
                ->before(function ($record) {
                    if ($record->items()->count() > 0) {
                        $record->items()->delete();
                    }
                    if ($record->payments()->count() > 0) {
                        $record->payments()->delete();
                    }
                }),
            Actions\RestoreAction::make(),
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.bills.index'))
        ];
    }
}
