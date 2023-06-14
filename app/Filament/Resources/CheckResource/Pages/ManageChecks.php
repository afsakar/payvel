<?php

namespace App\Filament\Resources\CheckResource\Pages;

use App\Filament\Resources\CheckResource;
use App\Models\Event;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageChecks extends ManageRecords
{
    protected static string $resource = CheckResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->after(function ($record) {
                    if ($record->status != 'paid') {
                        $record->paid_date = null;
                        $record->save();
                    }

                    Event::create([
                        'title' => 'Check Payment',
                        'description' => $record->number . ' - ' . $record->corporation->name,
                        'start' => $record->due_date,
                        'end' => null,
                        'reminder' => true,
                        'check_id' => $record->id,
                    ]);
                }),
        ];
    }
}
