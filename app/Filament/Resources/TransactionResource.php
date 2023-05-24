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
                        ->displayFormat('d/m/Y'),
                    Forms\Components\TextInput::make('amount')
                        ->required(),
                    Forms\Components\Select::make('from_account_id')
                        ->label('Sender Account')
                        ->reactive()
                        ->options(\App\Models\Account::query()->get()->pluck('name', 'id'))
                        ->afterStateUpdated(function ($state, Closure $set) {
                            $set('from_account_id', $state);
                            $currency_id = \App\Models\Account::query()->find($state)->currency_id;
                            $set('currency_id', $currency_id);
                        })
                        ->placeholder('Select Account')
                        ->required(),
                    Forms\Components\Select::make('to_account_id')
                        ->label('Sender Account')
                        ->reactive()
                        ->options(function (Closure $get) {
                            return \App\Models\Account::query()->where('id', '!=', $get('from_account_id'))->where('currency_id', $get('currency_id'))->get()->pluck('name', 'id');
                        })
                        ->disabled(function (Closure $get) {
                            return !$get('from_account_id');
                        })
                        ->placeholder('Select Account')
                        ->required(),
                ]),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
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
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('from_account.name')
                    ->label('Sender')
                    ->searchable(),
                Tables\Columns\TextColumn::make('to_account.name')
                    ->label('Receiver')
                    ->searchable(),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
