<?php

namespace App\Filament\Resources\CorporationResource\Widgets;

use App\Models\Corporation;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class InvoicesWidget extends BaseWidget
{
    //protected static string $view = 'filament.resources.corporation-resource.widgets.revenues-widget';

    protected static ?string $pollingInterval = '3s';

    public $corporationID;

    protected function getCards(): array
    {
        $corporation = Corporation::with('revenues')->find($this->corporationID);

        $invoiceTotal = Card::make(__('corporations.invoice_total'), $this->formatMoney($corporation->invoice_total));
        $billTotal = Card::make(__('corporations.bill_total'), $this->formatMoney($corporation->bill_total));

        return [
            $billTotal,
            $invoiceTotal,
        ];
    }

    protected function formatMoney($amount)
    {
        $corporation = Corporation::find($this->corporationID);

        return $corporation->currency->position == 'left' ? $corporation->currency->symbol . number_format($amount, 2) : number_format($amount, 2) . $corporation->currency->symbol;
    }
}
