<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CurrencyResource\Pages;
use App\Filament\Resources\CurrencyResource\RelationManagers;
use App\Models\Currency;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CurrencyResource extends Resource
{
    protected static ?string $model = Currency::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->label(__('currencies.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label(__('currencies.code'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(3),
                Forms\Components\TextInput::make('symbol')
                    ->label(__('currencies.symbol'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(3),
                Forms\Components\Select::make('position')
                    ->label(__('currencies.position'))
                    ->required()
                    ->options([
                        'left' => __('currencies.left'),
                        'right' => __('currencies.right'),
                    ])
                    ->disablePlaceholderSelection()
                    ->searchable()
                    ->placeholder(__('currencies.select_position')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->label(__('currencies.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(__('currencies.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('symbol')
                    ->label(__('currencies.symbol')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
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
            'index' => Pages\ManageCurrencies::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('currencies.currency');
    }

    public static function getNavigationLabel(): string
    {
        return __('currencies.currencies');
    }

    public static function getPluralModelLabel(): string
    {
        return __('currencies.currencies');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
