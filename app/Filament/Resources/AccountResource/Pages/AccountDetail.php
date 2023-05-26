<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables;
use Illuminate\Contracts\View\View;
use Webbingbrasil\FilamentDateFilter\DateFilter;
use Filament\Forms;
use Filament\Tables\Filters\Filter;

class AccountDetail extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static string $resource = AccountResource::class;

    protected static string $view = 'filament.resources.account-resource.pages.account-detail';

    public $record;

    public $account;

    public function mount($record)
    {
        $this->record = $record;
        $this->account = Account::find($record);
    }
}
