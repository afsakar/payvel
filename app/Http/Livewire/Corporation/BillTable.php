<?php

namespace App\Http\Livewire\Corporation;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Bill;
use App\Models\Corporation;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class BillTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $corporationID;

    protected function getTableQuery(): Builder
    {
        return Bill::query()->where('corporation_id', $this->corporationID)->where('company_id', session()->get('company_id'));
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('issue_date')
                ->label(__('bills.issue_date'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('number')
                ->label(__('bills.bill_number'))
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('waybill.number')
                ->label(__('bills.waybill'))
                ->url(fn ($record) => $record->waybill_id ? route('filament.resources.waybills.view', $record->waybill_id) : null)
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('with_holding.rate')
                ->label(__('bills.withholding_tax'))
                ->formatStateUsing(fn ($state) => "%$state"),
            Tables\Columns\BadgeColumn::make('status')
                ->label(__('bills.status'))
                ->enum([
                    'pending' => __('bills.pending'),
                    'delivered' => __('bills.delivered'),
                    'cancelled' => __('bills.cancelled'),
                ])
                ->colors([
                    'primary' => 'pending',
                    'success' => 'delivered',
                    'danger' => 'cancelled',
                ]),
            Tables\Columns\TextColumn::make('discount')
                ->label(__('bills.discount'))
                ->formatStateUsing(fn ($state) => $this->formatMoney($state)),
            Tables\Columns\TextColumn::make('total')
                ->label(__('bills.total'))
                ->formatStateUsing(fn ($state) => $this->formatMoney($state)),
            Tables\Columns\TextColumn::make('bill_payments_sum')
                ->formatStateUsing(fn ($state) => $this->formatMoney($state))
                ->label(__('bills.payments'))
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('issue_date')
                ->label(__('bills.issue_date')),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 1;
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('View')
                ->label(__('general.view'))
                ->color('blue')
                ->icon('heroicon-s-eye')
                ->url(function ($record) {
                    return route('filament.resources.bills.view', ['record' => $record]);
                }),
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            FilamentExportBulkAction::make('Export')
                ->pageOrientationFieldLabel(__('general.page_orientation'))
                ->defaultPageOrientation('landscape'),
        ];
    }

    protected function formatMoney($amount)
    {
        $corporation = Corporation::find($this->corporationID);

        return $corporation->currency->position == 'left' ? $corporation->currency->symbol . number_format($amount, 2) : number_format($amount, 2) . $corporation->currency->symbol;
    }

    public function render()
    {
        return view('livewire.corporation.bill-table');
    }
}
