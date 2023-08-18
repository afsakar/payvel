<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WithHoldingResource\Pages;
use App\Filament\Resources\WithHoldingResource\RelationManagers;
use App\Models\WithHolding;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WithHoldingResource extends Resource
{
    protected static ?string $model = WithHolding::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';

    protected static ?string $navigationGroup = 'Settings';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('withholdings.name'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('rate')
                        ->label(__('withholdings.rate'))
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('withholdings.name')),
                Tables\Columns\TextColumn::make('rate')
                    ->formatStateUsing(fn ($state) => '%' . $state)
                    ->label(__('withholdings.rate')),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->has_any_relation),
                Tables\Actions\ForceDeleteAction::make()
                    ->hidden(fn ($record) => $record->has_any_relation),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageWithHoldings::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('withholdings.withholding');
    }

    public static function getNavigationLabel(): string
    {
        return __('withholdings.withholdings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('withholdings.withholdings');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
