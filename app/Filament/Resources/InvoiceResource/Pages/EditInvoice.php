<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

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
                    if($record->payments()->count() > 0) {
                        $record->payments()->delete();
                    }
                }),
            Actions\RestoreAction::make(),
            Actions\Action::make('back')
                ->label(__('general.go_back'))
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.invoices.index'))
        ];
    }
}
