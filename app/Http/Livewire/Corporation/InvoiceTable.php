<?php

namespace App\Http\Livewire\Corporation;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\Invoice;
use App\Models\Corporation;
use Filament\Tables;
use Livewire\Component;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;

class InvoiceTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $corporationID;

    protected function getTableQuery(): Builder
    {
        return Invoice::query()->where('corporation_id', $this->corporationID)->where('company_id', session()->get('company_id'));
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('issue_date')
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('number'),
            Tables\Columns\TextColumn::make('waybill.number')
                ->url(fn ($record) => $record->waybill_id ? route('filament.resources.waybills.view', $record->waybill_id) : null)
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('with_holding.rate')
                ->formatStateUsing(fn ($state) => "%$state"),
            Tables\Columns\BadgeColumn::make('status')
                ->enum([
                    'pending' => 'Pending',
                    'delivered' => 'Delivered',
                    'cancelled' => 'Cancelled',
                ])
                ->colors([
                    'primary' => 'pending',
                    'success' => 'delivered',
                    'danger' => 'cancelled',
                ]),
            Tables\Columns\TextColumn::make('discount')
                ->formatStateUsing(fn ($state) => $this->formatMoney($state)),
            Tables\Columns\TextColumn::make('total')
                ->formatStateUsing(fn ($state) => $this->formatMoney($state)),
            Tables\Columns\TextColumn::make('invoice_payments_sum')
                ->formatStateUsing(fn ($state) => $this->formatMoney($state))
                ->label('Payments')
                ->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('issue_date')
                ->form([
                    Forms\Components\DatePicker::make('due_from')
                        ->default(Carbon::now()->subYear())
                        ->closeOnDateSelection()
                        ->timezone('Europe/Istanbul')
                        ->label('From Date'),
                    Forms\Components\DatePicker::make('due_until')
                        ->closeOnDateSelection()
                        ->timezone('Europe/Istanbul')
                        ->label('To Date')
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['due_from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('issue_date', '>=', $date),
                        )
                        ->when(
                            $data['due_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('issue_date', '<=', $date),
                        );
                }),
            Filter::make('number')
                ->form([
                    Forms\Components\TextInput::make('number')
                        ->label('Invoice Number')
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query->where('number', 'like', '%' . $data['number'] . '%');
                }),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 2;
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('View')
                ->color('blue')
                ->icon('heroicon-s-eye')
                ->url(function ($record) {
                    return route('filament.resources.invoices.view', ['record' => $record]);
                }),
        ];
    }

    protected function formatMoney($amount)
    {
        $corporation = Corporation::find($this->corporationID);

        return $corporation->currency->position == 'left' ? $corporation->currency->symbol . number_format($amount, 2) : number_format($amount, 2) . $corporation->currency->symbol;
    }

    public function render()
    {
        return view('livewire.corporation.invoice-table');
    }
}
