<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-switch-horizontal';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Forms\Components\DatePicker::make('due_at')
                        ->required()
                        ->label(__('transactions.due_at'))
                        ->displayFormat('d/m/Y'),
                    Forms\Components\TextInput::make('amount')
                        ->label(__('transactions.amount'))
                        ->required(),
                    Forms\Components\Select::make('from_account_id')
                        ->label(__('transactions.from_account'))
                        ->reactive()
                        ->options(\App\Models\Account::query()->get()->pluck('name', 'id'))
                        ->afterStateUpdated(function ($state, Closure $set) {
                            $set('from_account_id', $state);
                            $currency_id = \App\Models\Account::query()->find($state)->currency_id;
                            $set('currency_id', $currency_id);
                        })
                        ->placeholder(__('transactions.select_account'))
                        ->required(),
                    Forms\Components\Select::make('to_account_id')
                        ->label(__('transactions.to_account'))
                        ->reactive()
                        ->options(function (Closure $get, $record) {
                            return \App\Models\Account::query()->where('id', '!=', $record ? $record->from_account_id : $get('from_account_id'))->where('currency_id', $record ? $record->from_account_id : $get('currency_id'))->get()->pluck('name', 'id');
                        })
                        ->disabled(function (Closure $get) {
                            return !$get('from_account_id');
                        })
                        ->placeholder(__('transactions.select_account'))
                        ->required(),
                ]),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
                        ->label(__('transactions.description'))
                        ->rows(2)
                        ->required()
                        ->maxLength(255),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('due_at')
                    ->label(__('transactions.due_at'))
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('transactions.amount'))
                    ->formatStateUsing(fn ($record, $state) => $record->from_account->currency->position == 'left' ? $record->from_account->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->from_account->currency->symbol),
                Tables\Columns\TextColumn::make('from_account.name')
                    ->label(__('transactions.from_account'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_account.name')
                    ->label(__('transactions.to_account'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('transactions.description'))
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
                ->pageOrientationFieldLabel(__('general.page_orientation'))
                ->defaultPageOrientation('landscape')
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('transactions.transaction');
    }

    public static function getNavigationLabel(): string
    {
        return __('transactions.transactions');
    }

    public static function getPluralModelLabel(): string
    {
        return __('transactions.transactions');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
