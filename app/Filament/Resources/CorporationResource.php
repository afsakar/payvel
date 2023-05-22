<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporationResource\Pages;
use App\Filament\Resources\CorporationResource\RelationManagers;
use App\Models\Corporation;
use App\Models\Currency;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CorporationResource extends Resource
{
    protected static ?string $model = Corporation::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('currency_id')
                    ->label('Currency')
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'customer' => 'Customer',
                        'vendor' => 'Vendor',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('owner')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tel_number')
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('gsm_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('fax_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->maxLength(255),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('address')
                        ->rows(2)
                        ->maxLength(65535),
                ]),
                Forms\Components\TextInput::make('tax_office')
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_number')
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency.name'),
                Tables\Columns\TextColumn::make('tel_number')
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tax_office')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tax_number')
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('type')
                    ->enum([
                        'customer' => 'Customer',
                        'vendor' => 'Vendor',
                    ])
                    ->colors([
                        'primary',
                        'success' => 'vendor',
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
            'index' => Pages\ManageCorporations::route('/'),
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
