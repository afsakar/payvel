<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CorporationResource\Pages;
use App\Filament\Resources\CorporationResource\RelationManagers;
use App\Filament\Resources\CorporationResource\Widgets\ChecksWidget;
use App\Filament\Resources\CorporationResource\Widgets\InvoicesWidget;
use App\Filament\Resources\CorporationResource\Widgets\RevenuesWidget;
use App\Models\Corporation;
use App\Models\Currency;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;

class CorporationResource extends Resource
{
    protected static ?string $model = Corporation::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('currency_id')
                    ->label(__('corporations.currency'))
                    ->options(Currency::all()->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('corporations.type'))
                    ->options([
                        'customer' => __('corporations.customer'),
                        'vendor' => __('corporations.vendor')
                    ])
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label(__('corporations.corporation_name'))
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('owner')
                    ->label(__('corporations.corporation_owner'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('tel_number')
                    ->label(__('corporations.tel_number'))
                    ->tel()
                    ->maxLength(255),
                Forms\Components\TextInput::make('gsm_number')
                    ->label(__('corporations.gsm_number'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('fax_number')
                    ->label(__('corporations.fax_number'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->label(__('corporations.email'))
                    ->email()
                    ->maxLength(255),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('address')
                        ->label(__('corporations.address'))
                        ->rows(2)
                        ->maxLength(65535),
                ]),
                Forms\Components\TextInput::make('tax_office')
                    ->label(__('corporations.tax_office'))
                    ->maxLength(255),
                Forms\Components\TextInput::make('tax_number')
                    ->label(__('corporations.tax_number'))
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('corporations.corporation_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('currency.name')
                    ->label(__('corporations.currency')),
                Tables\Columns\TextColumn::make('tel_number')
                    ->label(__('corporations.tel_number'))
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tax_office')
                    ->label(__('corporations.tax_office'))
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\TextColumn::make('tax_number')
                    ->label(__('corporations.tax_number'))
                    ->searchable()
                    ->placeholder('-'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('corporations.type'))
                    ->enum([
                        'customer' => __('corporations.customer'),
                        'vendor' => __('corporations.vendor'),
                    ])
                    ->colors([
                        'primary',
                        'success' => 'vendor',
                    ]),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('corporations.type'))
                    ->options([
                        'customer' => __('corporations.customer'),
                        'vendor' => __('corporations.vendor')
                    ])
                    ->searchable(),
            ])
            ->actions([
                Action::make('detail')
                    ->label(__('corporations.detail'))
                    ->color('blue')
                    ->icon('heroicon-s-document')
                    ->url(function ($record) {
                        return route('filament.resources.corporations.detail', ['record' => $record]);
                    }),
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
            'index' => Pages\ManageCorporations::route('/'),
            'detail' => Pages\CorporationDetail::route('/{record}/detail'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            RevenuesWidget::class,
            InvoicesWidget::class,
            ChecksWidget::class,
        ];
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1;
    }

    public static function getModelLabel(): string
    {
        return __('corporations.corporation');
    }

    public static function getNavigationLabel(): string
    {
        return __('corporations.corporations');
    }

    public static function getPluralModelLabel(): string
    {
        return __('corporations.corporations');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
