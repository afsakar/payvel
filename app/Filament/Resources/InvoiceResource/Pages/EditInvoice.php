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
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.invoices.index'))
        ];
    }
}
