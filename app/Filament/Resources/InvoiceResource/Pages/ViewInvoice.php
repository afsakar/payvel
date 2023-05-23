<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make()
                ->color('secondary'),
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.invoices.index'))
        ];
    }
}
