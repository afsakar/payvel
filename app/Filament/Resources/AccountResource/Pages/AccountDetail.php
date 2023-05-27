<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Pages\Actions;

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

    protected function getActions(): array
    {
        return [
            Actions\Action::make('Go Back')
                ->icon('heroicon-o-arrow-left')
                ->url(route('filament.resources.accounts.index'))
        ];
    }

    protected function getTitle(): string
    {
        return $this->account->name;
    }
}
