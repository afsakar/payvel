<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgreementResource\Pages;
use App\Filament\Resources\AgreementResource\RelationManagers;
use App\Models\Agreement;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AgreementResource extends Resource
{
    protected static ?string $model = Agreement::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Card::make()->columns(1)->schema([
                        Forms\Components\RichEditor::make('content')
                            ->label(__('agreements.agreement_content'))
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->maxLength(65535),
                    ]),
                    Card::make()->columns(1)->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->label(__('agreements.agreement_name'))
                                ->required()
                                ->maxLength(255),
                            Forms\Components\DatePicker::make('date')
                                ->label(__('agreements.date'))
                                ->displayFormat('d/m/Y'),
                        ]),
                        Forms\Components\Select::make('company_id')
                            ->label(__('agreements.company_name'))
                            ->reactive()
                            ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->default(session()->get('company_id'))
                            ->disabled(),
                        Forms\Components\Select::make('corporation_id')
                            ->label(__('agreements.corporation'))
                            ->options(\App\Models\Corporation::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ]),
                    Card::make()->columns(1)->schema([
                        Grid::make(1)->schema([
                            SpatieMediaLibraryFileUpload::make('Files')
                                ->label(__('agreements.files'))
                                ->collection('agreement')
                                ->rules([
                                    'mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg,svg',
                                ]),
                        ])
                    ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label(__('agreements.date'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('agreements.company_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->label(__('agreements.corporation'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('agreements.agreement_name'))
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('date')
                    ->label(__('agreements.date'))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgreements::route('/'),
            'create' => Pages\CreateAgreement::route('/create'),
            'view' => Pages\ViewAgreement::route('/{record}'),
            'edit' => Pages\EditAgreement::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('agreements.agreement');
    }

    public static function getNavigationLabel(): string
    {
        return __('agreements.agreements');
    }

    public static function getPluralModelLabel(): string
    {
        return __('agreements.agreements');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('company_id', session()->get('company_id'));
    }
}
