<?php

namespace App\Http\Livewire\Corporation;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use Livewire\Component;
use App\Models\Agreement;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class AgreementTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $corporationID;

    protected function getTableQuery(): Builder
    {
        return Agreement::query()->where('corporation_id', $this->corporationID)->where('company_id', session()->get('company_id'));
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->label(__('agreements.date'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('name')
                ->label(__('agreements.agreement_name'))
                ->searchable(),
            Tables\Columns\TextColumn::make('company.name')
                ->label(__('agreements.company_name'))
                ->searchable(),
        ];
    }


    protected function getTableFilters(): array
    {
        return [
            \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('date')
                ->label(__('agreements.date')),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('View')
                ->label(__('general.view'))
                ->color('blue')
                ->icon('heroicon-s-eye')
                ->url(function ($record) {
                    return route('filament.resources.agreements.view', ['record' => $record]);
                }),
        ];
    }

    public function render()
    {
        return view('livewire.corporation.agreement-table');
    }
}
