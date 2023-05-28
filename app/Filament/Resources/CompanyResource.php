<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Filament\Resources\CompanyResource\RelationManagers;
use App\Models\Company;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    protected static ?string $navigationIcon = 'heroicon-o-library';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    FileUpload::make('logo')
                        ->label(__('companies.logo'))
                        ->image()
                        ->maxSize(1024)
                        ->nullable()
                        ->directory('company_logos')
                ]),
                Grid::make([
                    'default' => 1,
                    'sm' => 2,
                ])->schema([
                    Forms\Components\TextInput::make('name')
                        ->label(__('companies.company_name'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('owner')
                        ->label(__('companies.company_owner'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('tel_number')
                        ->label(__('companies.tel_number'))
                        ->tel()
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('gsm_number')
                        ->label(__('companies.gsm_number'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('fax_number')
                        ->label(__('companies.fax_number'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('email')
                        ->label(__('companies.email'))
                        ->email()
                        ->maxLength(255),
                ]),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('address')
                        ->label(__('companies.address'))
                        ->rows(3)
                        ->maxLength(65535),
                ]),
                Grid::make([
                    'default' => 1,
                    'sm' => 2,
                ])->schema([
                    Forms\Components\TextInput::make('tax_office')
                        ->label(__('companies.tax_office'))
                        ->maxLength(255),
                    Forms\Components\TextInput::make('tax_number')
                        ->label(__('companies.tax_number'))
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label(__('companies.logo'))
                    ->height('auto')
                    ->width('80px'),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('companies.company_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tel_number')
                    ->label(__('companies.tel_number')),
                Tables\Columns\TextColumn::make('email')
                    ->label(__('companies.email'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('tax_number')
                    ->label(__('companies.tax_number'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->id === Company::find(session()->get('company_id'))->id || $record->has_any_relation),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCompanies::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('companies.company');
    }

    public static function getNavigationLabel(): string
    {
        return __('companies.companies');
    }

    public static function getPluralModelLabel(): string
    {
        return __('companies.companies');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
