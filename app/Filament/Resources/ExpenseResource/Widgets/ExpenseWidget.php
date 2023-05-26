<?php

namespace App\Filament\Resources\ExpenseResource\Widgets;

use App\Models\Currency;
use App\Models\Expense;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class ExpenseWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '3s';

    protected function getCards(): array
    {
        $currencies = Currency::all()->pluck('code', 'id');

        $groupedExpenses = [];

        foreach ($currencies as $currencyId => $currencyCode) {
            $groupedExpenses[] = Card::make($currencyCode, number_format(Expense::query()
                ->whereHas('corporation', function ($query) use ($currencyId) {
                    $query->where('currency_id', $currencyId);
                })
                ->whereDate('due_at', now())
                ->sum('amount'), 2));
        }

        return $groupedExpenses;
    }
}
