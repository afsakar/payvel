<?php

namespace App\Filament\Resources\CorporationResource\Widgets;

use App\Models\Corporation;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class ChecksWidget extends BaseWidget
{
    //protected static string $view = 'filament.resources.corporation-resource.widgets.revenues-widget';

    protected static ?string $pollingInterval = '3s';

    public $corporationID;

    protected function getCards(): array
    {
        $corporation = Corporation::with('revenues')->find($this->corporationID);

        $paidSaleChecks = Card::make(__('checks.check_out'), $this->formatMoney($corporation->paid_sale_checks));
        $paidPurchaseChecks = Card::make(__('checks.check_in'), $this->formatMoney($corporation->paid_purchase_checks));

        return [
            $paidPurchaseChecks,
            $paidSaleChecks,
        ];
    }

    protected function formatMoney($amount)
    {
        $corporation = Corporation::find($this->corporationID);

        return $corporation->currency->position == 'left' ? $corporation->currency->symbol . number_format($amount, 2) : number_format($amount, 2) . $corporation->currency->symbol;
    }
}
