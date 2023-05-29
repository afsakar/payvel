<?php

namespace App\Http\Livewire\Corporation;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Corporation;
use App\Models\Expense;
use Livewire\Component;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class ExpenseTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $corporationID;

    protected function getTableQuery(): Builder
    {
        return Expense::query()->where('corporation_id', $this->corporationID)->where('company_id', session()->get('company_id'));
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('due_at')
                ->label(__('expenses.due_at'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('description')
                ->label(__('expenses.description'))
                ->searchable(),
            Tables\Columns\TextColumn::make('company.name')
                ->searchable()
                ->label(__('expenses.company_name')),
            Tables\Columns\TextColumn::make('corporation.name')
                ->searchable()
                ->label(__('expenses.corporation')),
            Tables\Columns\TextColumn::make('amount_with_currency')
                ->label(__('expenses.amount'))
                ->formatStateUsing(fn ($state) => '-' . $state)
                ->sortable(),
            Tables\Columns\BadgeColumn::make('type')
                ->label(__('expenses.type'))
                ->enum([
                    'formal' => __('expenses.formal'),
                    'informal' => __('expenses.informal'),
                ])
                ->colors([
                    'primary',
                    'secondary' => 'informal',
                ])
                ->sortable(),
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
                        ->label(__('general.min_amount')),
                    Forms\Components\TextInput::make('max_amount')
                        ->label(__('general.max_amount')),
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

    protected function getTableBulkActions(): array
    {
        return [
            FilamentExportBulkAction::make('Export')
                ->pageOrientationFieldLabel(__('general.page_orientation'))
                ->defaultPageOrientation('landscape'),
        ];
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 2;
    }

    public function render()
    {
        return view('livewire.corporation.expense-table');
    }
}
