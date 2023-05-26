<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use App\Models\AccountType;
use Filament\Tables\Actions\Action;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Card;
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
                        ->label('Account Type')
                        ->options(AccountType::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('currency_id')
                        ->label('Currency')
                        ->options(Currency::all()->pluck('name', 'id'))
                        ->searchable()
                        ->required(),
                    Forms\Components\TextInput::make('name')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('starting_balance')
                        ->required(),
                ]),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
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
                    ->searchable(),
                Tables\Columns\TextColumn::make('accountType.name'),
                Tables\Columns\TextColumn::make('currency.code'),
                Tables\Columns\TextColumn::make('balance'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Action::make('detail')
                    ->label('Detail')
                    ->color('blue')
                    ->icon('heroicon-s-document')
                    ->hidden(fn ($record) => !$record->has_any_relation)
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
