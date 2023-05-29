<?php

namespace App\Http\Livewire\Corporation;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Waybill;
use App\Models\Corporation;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class WaybillTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $corporationID;

    protected function getTableQuery(): Builder
    {
        return Waybill::query()->where('corporation_id', $this->corporationID)->where('company_id', session()->get('company_id'));
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('due_date')
                ->label(__('waybills.due_date'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('number')
                ->label(__('waybills.waybill_number'))
                ->searchable(),
            Tables\Columns\BadgeColumn::make('status')
                ->label(__('waybills.status'))
                ->enum([
                    'pending' => __('waybills.pending'),
                    'delivered' => __('waybills.delivered'),
                    'cancelled' => __('waybills.cancelled'),
                ])
                ->colors([
                    'primary' => 'pending',
                    'success' => 'delivered',
                    'danger' => 'cancelled',
                ]),
            Tables\Columns\TextColumn::make('waybill_date')
                ->label(__('waybills.waybill_date'))
                ->sortable()
                ->dateTime('d/m/Y'),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('due_date')
                ->form([
                    Forms\Components\DatePicker::make('due_from')
                        ->default(Carbon::now()->subYear())
                        ->closeOnDateSelection()
                        ->timezone('Europe/Istanbul')
                        ->label(__('general.from_date')),
                    Forms\Components\DatePicker::make('due_until')
                        ->closeOnDateSelection()
                        ->timezone('Europe/Istanbul')
                        ->label(__('general.to_date')),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['due_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('due_date', '>=', $date),
                        )
                        ->when(
                            $data['due_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('due_date', '<=', $date),
                        );
                }),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 1;
    }

    protected function getTableBulkActions(): array
    {
        return [
            FilamentExportBulkAction::make('Export')
                ->pageOrientationFieldLabel(__('general.page_orientation'))
                ->defaultPageOrientation('landscape'),
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
                    return route('filament.resources.waybills.view', ['record' => $record]);
                }),
        ];
    }

    public function render()
    {
        return view('livewire.corporation.waybill-table');
    }
}
