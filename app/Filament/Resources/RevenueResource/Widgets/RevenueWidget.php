<?php

namespace App\Filament\Resources\RevenueResource\Widgets;

use App\Models\Currency;
use App\Models\Revenue;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class RevenueWidget extends BaseWidget
{
    protected static ?string $pollingInterval = '3s';

    protected function getCards(): array
    {
        $currencies = Currency::all()->pluck('code', 'id');

        $groupedRevenues = [];

        foreach ($currencies as $currencyId => $currencyCode) {
            $groupedRevenues[] = Card::make($currencyCode, number_format(Revenue::query()
                ->whereHas('corporation', function ($query) use ($currencyId) {
                    $query->where('currency_id', $currencyId);
                })
                ->whereDate('due_at', now())
                ->sum('amount'), 2));
        }

        return $groupedRevenues;
    }
}
