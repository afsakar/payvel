<?php

namespace App\Filament\Resources\AgreementResource\Pages;

use App\Filament\Resources\AgreementResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAgreement extends ViewRecord
{
    protected static string $resource = AgreementResource::class;

    protected function getActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('Go Back')
                ->color('secondary')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.agreements.index'))
        ];
    }
}
