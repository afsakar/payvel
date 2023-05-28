<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Models\Account;
use App\Models\AccountType;
use Filament\Tables\Actions\Action;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-cash';

    public ?Model $record = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make([
                    'default' => 1,
                    'sm' => 2,
                ])->schema([
                    Forms\Components\Select::make('account_type_id')
                        ->label(__('accounts.account_type'))
                        ->options(AccountType::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('currency_id')
                        ->label(__('accounts.currency'))
                        ->options(Currency::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->label(__('accounts.account_name'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('starting_balance')
                        ->label(__('accounts.starting_balance'))
                        ->required(),
                ]),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
                        ->label(__('accounts.description'))
                        ->rows(2)
                        ->maxLength(255),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('accounts.account_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('accountType.name')
                    ->label(__('accounts.account_type'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency.code')
                    ->label(__('accounts.currency'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('balance')
                    ->label(__('accounts.balance')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('detail')
                    ->label(__('accounts.detail'))
                    ->color('blue')
                    ->icon('heroicon-s-document')
                    ->url(function ($record) {
                        return route('filament.resources.accounts.detail', ['record' => $record]);
                    }),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->has_any_relation),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageAccounts::route('/'),
            'detail' => Pages\AccountDetail::route('/{record}/detail'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('accounts.account');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounts.accounts');
    }

    public static function getPluralModelLabel(): string
    {
        return __('accounts.accounts');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
