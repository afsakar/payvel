<?php

namespace App\Http\Livewire\Account;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Account;
use App\Models\Check;
use Livewire\Component;
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class CheckTable extends Component implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $accountID;

    protected function getTableQuery(): Builder
    {
        return Check::query()->where('account_id', $this->accountID)->where('status', 'paid');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('due_date')
                ->label(__('checks.due_date'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\TextColumn::make('paid_date')
                ->label(__('checks.paid_date'))
                ->sortable()
                ->dateTime('d/m/Y'),
            Tables\Columns\BadgeColumn::make('status')
                ->label(__('checks.status'))
                ->enum([
                    'pending' => __('checks.pending'),
                    'paid' => __('checks.paid'),
                    'cancelled' => __('checks.cancelled'),
                ])
                ->colors([
                    'primary' => 'pending',
                    'success' => 'paid',
                    'danger' => 'cancelled',
                ]),
            Tables\Columns\TextColumn::make('description')
                ->label(__('checks.description'))
                ->searchable(),
            Tables\Columns\TextColumn::make('company.name')
                ->searchable()
                ->label(__('checks.company')),
            Tables\Columns\TextColumn::make('corporation.name')
                ->searchable()
                ->label(__('checks.corporation')),
            Tables\Columns\BadgeColumn::make('type')
                ->label(__('checks.type'))
                ->enum([
                    'purchase' => __('checks.purchase'),
                    'sale' => __('checks.sale'),
                ])
                ->colors([
                    'primary' => 'purchase',
                    'danger' => 'sale',
                ]),
            Tables\Columns\TextColumn::make('amount_with_currency')
                ->label(__('checks.amount'))
                ->formatStateUsing(fn ($record, $state) => $record->type === 'purchase' ? $state : '-' . $state)
                ->sortable(),
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

    public function render()
    {
        return view('livewire.account.check-table');
    }
}
