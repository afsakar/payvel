<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Currency;
use App\Models\Material;
use App\Models\Tax;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-color-swatch';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->label(__('materials.unit'))
                    ->options(Unit::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('tax_id')
                    ->label(__('materials.tax'))
                    ->options(Tax::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('currency_id')
                    ->label(__('materials.currency'))
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('materials.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->label(__('materials.code'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->label(__('materials.price'))
                    ->required(),
                Forms\Components\Select::make('category')
                    ->label(__('materials.category'))
                    ->options([
                        'construction' => __('materials.construction'),
                        'electrical' => __('materials.electrical'),
                        'plumbing' => __('materials.plumbing'),
                    ])
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('materials.type'))
                    ->options([
                        'service' => __('materials.service'),
                        'procurement' => __('materials.procurement'),
                        'service_procurement' => __('materials.service_procurement'),
                    ])
                    ->required(),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
                        ->label(__('materials.description'))
                        ->rows(2)
                        ->maxLength(255),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(__('materials.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('materials.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label(__('materials.unit'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax.rate')
                    ->label(__('materials.tax')),
                Tables\Columns\TextColumn::make('currency.code')
                    ->label(__('materials.currency')),
                Tables\Columns\TextColumn::make('price')
                    ->label(__('materials.price'))
                    ->formatStateUsing(function (?Model $record) {
                        return $record->currency->position === 'left'
                            ? $record->currency->symbol . ' ' . number_format($record->price, 2)
                            : number_format($record->price, 2) . ' ' . $record->currency->symbol;
                    }),
                Tables\Columns\BadgeColumn::make('category')
                    ->label(__('materials.category'))
                    ->enum([
                        'construction' => __('materials.construction'),
                        'electrical' => __('materials.electrical'),
                        'plumbing' => __('materials.plumbing'),
                    ]),
                Tables\Columns\BadgeColumn::make('type')
                    ->enum([
                        'service' => __('materials.service'),
                        'procurement' => __('materials.procurement'),
                        'service_procurement' => __('materials.service_procurement'),
                    ]),
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

    public static function getModelLabel(): string
    {
        return __('materials.material');
    }

    public static function getNavigationLabel(): string
    {
        return __('materials.materials');
    }

    public static function getPluralModelLabel(): string
    {
        return __('materials.materials');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageMaterials::route('/'),
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
