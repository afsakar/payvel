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
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('unit_id')
                    ->label('Unit')
                    ->options(Unit::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('tax_id')
                    ->label('Tax')
                    ->options(Tax::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('currency_id')
                    ->label('Currency')
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('price')
                    ->required(),
                Forms\Components\Select::make('category')
                    ->label('Category')
                    ->options([
                        'construction' => 'Construction',
                        'electrical' => 'Electrical',
                        'plumbing' => 'Plumbing',

                    ])
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'service' => 'Service',
                        'procurement' => 'Procurement',
                        'service_procurement' => 'Service & Procurement',
                    ])
                    ->required(),
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
                Tables\Columns\TextColumn::make('code'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('unit.name'),
                Tables\Columns\TextColumn::make('tax.rate'),
                Tables\Columns\TextColumn::make('currency.code'),
                Tables\Columns\TextColumn::make('price'),
                Tables\Columns\BadgeColumn::make('category')
                    ->enum([
                        'construction' => 'Construction',
                        'electrical' => 'Electrical',
                        'plumbing' => 'Plumbing',
                    ]),
                Tables\Columns\BadgeColumn::make('type')
                    ->enum([
                        'service' => 'Service',
                        'procurement' => 'Procurement',
                        'service_procurement' => 'Service & Procurement',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
            ]);
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
