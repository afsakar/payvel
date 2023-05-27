<?php

namespace App\Filament\Resources\CorporationResource\Widgets;

use App\Models\Corporation;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class RevenuesWidget extends BaseWidget
{
    //protected static string $view = 'filament.resources.corporation-resource.widgets.revenues-widget';

    protected static ?string $pollingInterval = '3s';

    public $corporationID;

    protected function getCards(): array
    {
        $corporation = Corporation::with('revenues')->find($this->corporationID);

        $formalRevenues = Card::make('Total Formal Revenues', $this->formatMoney($corporation->total_formal_revenue));
        $informalRevenues = Card::make('Total Informal Revenues', $this->formatMoney($corporation->total_informal_revenue));
        $formalExpenses = Card::make('Total Formal Expenses', $this->formatMoney($corporation->total_formal_expense));
        $informalExpenses = Card::make('Total Informal Expenses', $this->formatMoney($corporation->total_informal_expense));

        return [
            $formalRevenues,
            $informalRevenues,
            $formalExpenses,
            $informalExpenses,
        ];
    }

    protected function formatMoney($amount)
    {
        $corporation = Corporation::find($this->corporationID);

        return $corporation->currency->position == 'left' ? $corporation->currency->symbol . number_format($amount, 2) : number_format($amount, 2) . $corporation->currency->symbol;
    }
}
