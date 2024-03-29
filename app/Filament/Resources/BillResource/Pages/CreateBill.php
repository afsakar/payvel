<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label(__('general.go_back'))
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.bills.index'))
        ];
    }
}
