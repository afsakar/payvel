<?php

namespace App\Http\Livewire\Account;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Account;
use App\Models\Transaction;
use Livewire\Component;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class TransactionTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $accountID;

    public $record;

    protected function getTableQuery(): Builder
    {
        $incoming = Transaction::query()->where('to_account_id', $this->accountID);
        $outgoing = Transaction::query()->where('from_account_id', $this->accountID);
        return $incoming->union($outgoing);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('due_at')
                ->label(__('transactions.due_at'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('description')
                ->label(__('transactions.description'))
                ->searchable(),
            Tables\Columns\TextColumn::make('from_account.name')
                ->searchable()
                ->label(__('transactions.from_account')),
            Tables\Columns\TextColumn::make('to_account.name')
                ->searchable()
                ->label(__('transactions.to_account')),
            Tables\Columns\TextColumn::make('amount_with_currency')
                ->label(__('transactions.amount'))
                ->formatStateUsing(fn ($record, $state) => $record->from_account->id == $this->accountID ? '-' . $state : $state)
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('due_at')
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
                            fn (Builder $query, $date): Builder => $query->whereDate('due_at', '>=', $date),
                        )
                        ->when(
                            $data['due_until'],
                            fn (Builder $query, $date): Builder => $query->whereDate('due_at', '<=', $date),
                        );
                }),
            Filter::make('amount')
                ->form([
                    Forms\Components\TextInput::make('min_amount')
                        ->label('Min Amount'),
                    Forms\Components\TextInput::make('max_amount')
                        ->label('Max Amount')
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['min_amount'],
                            fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                        )
                        ->when(
                            $data['max_amount'],
                            fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                        );
                }),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 2;
    }

    protected function getTableBulkActions(): array
    {
        return [
            FilamentExportBulkAction::make('Export')
                ->pageOrientationFieldLabel(__('general.page_orientation'))
                ->defaultPageOrientation('landscape'),
        ];
    }

    public function render()
    {
        return view('livewire.account.transaction-table');
    }
}
