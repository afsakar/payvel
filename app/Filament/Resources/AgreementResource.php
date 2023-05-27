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
                            ->disableToolbarButtons([
                                'attachFiles',
                                'codeBlock',
                            ])
                            ->maxLength(65535),
                    ]),
                    Card::make()->columns(1)->schema([
                        Grid::make(2)->schema([
                            Forms\Components\TextInput::make('name')
                                ->required()
                                ->maxLength(255),
                            Forms\Components\DatePicker::make('date')->displayFormat('d/m/Y'),
                        ]),
                        Forms\Components\Select::make('company_id')
                            ->label('Company')
                            ->reactive()
                            ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                            ->searchable()
                            ->default(session()->get('company_id'))
                            ->disabled(),
                        Forms\Components\Select::make('corporation_id')
                            ->label('Corporation')
                            ->options(\App\Models\Corporation::all()->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                    ]),
                    Card::make()->columns(1)->schema([
                        Grid::make(1)->schema([
                            SpatieMediaLibraryFileUpload::make('Files')
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
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('company.name'),
                Tables\Columns\TextColumn::make('corporation.name'),
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('company_id', session()->get('company_id'));
    }
}
