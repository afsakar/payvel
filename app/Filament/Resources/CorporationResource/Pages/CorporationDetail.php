<?php

namespace App\Filament\Resources\CorporationResource\Pages;

use App\Filament\Resources\CorporationResource;
use App\Filament\Resources\CorporationResource\Widgets\RevenuesWidget;
use App\Models\Corporation;
use Filament\Resources\Pages\Page;
use Filament\Tables;

class CorporationDetail extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = CorporationResource::class;

    protected static string $view = 'filament.resources.corporation-resource.pages.corporation-detail';

    public $record;

    public $corporation;

    protected static ?string $title = 'Corporation Detail';

    public function mount($record)
    {
        $this->record = $record;
        $this->corporation = Corporation::find($record);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            RevenuesWidget::class,
        ];
    }
}
