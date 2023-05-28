<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WaybillResource\Pages;
use App\Filament\Resources\WaybillResource\RelationManagers;
use App\Models\Company;
use App\Models\Corporation;
use App\Models\Material;
use App\Models\Waybill;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;

class WaybillResource extends Resource
{
    protected static ?string $model = Waybill::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?string $recordTitleAttribute = 'number';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)->schema([
                    Forms\Components\Select::make('company_id')
                        ->label(__('waybills.company'))
                        ->reactive()
                        ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                        ->searchable()
                        ->default(session()->get('company_id'))
                        ->disabled(),
                    Forms\Components\Select::make('corporation_id')
                        ->label(__('waybills.corporation'))
                        ->options(\App\Models\Corporation::all()->pluck('name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(function ($state, $set) {
                            $set('corporation_id', $state);
                        })
                        ->searchable()
                        ->afterStateHydrated(fn ($component) => $component->callAfterStateUpdated())
                        ->required(),
                    Forms\Components\TextInput::make('number')
                        ->label(__('waybills.waybill_number'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Select::make('status')
                        ->label(__('waybills.status'))
                        ->options([
                            'pending' => __('waybills.pending'),
                            'delivered' => __('waybills.delivered'),
                            'cancelled' => __('waybills.cancelled'),
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('due_date')
                        ->label(__('waybills.due_date'))
                        ->displayFormat('d/m/Y'),
                    Forms\Components\DatePicker::make('waybill_date')
                        ->label(__('waybills.waybill_date'))
                        ->displayFormat('d/m/Y'),
                    Grid::make(1)->schema([
                        Forms\Components\Textarea::make('address')
                            ->label(__('waybills.address'))
                            ->rows(2)
                            ->required()
                            ->maxLength(65535),
                    ])
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\RichEditor::make('content')
                        ->label(__('waybills.content'))
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->nullable()
                        ->maxLength(65535),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\Repeater::make('items')
                        ->label(__('waybills.items'))
                        ->relationship()->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Select::make('material_id')
                                    ->label(__('waybills.material'))
                                    ->reactive()
                                    ->options(function (Closure $get) {
                                        $corporation = \App\Models\Corporation::query()->find($get('../../corporation_id'));
                                        return Material::query()->where('currency_id', $corporation->currency->id)->get()->pluck('name', 'id');
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        $material = Material::query()->find($state);
                                        if ($material) {
                                            $set('price', $material->price);
                                        }
                                    })
                                    ->required(),
                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('waybills.quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(1)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('price')
                                    ->label(__('waybills.price'))
                                    ->required()
                                    ->disabled()
                                    ->maxLength(255),
                            ])
                        ])
                        ->disabled(function (Closure $get) {
                            return $get('corporation_id') === null;
                        })
                        ->defaultItems(0)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('number')
                    ->label(__('waybills.waybill_number'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('waybills.company'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->label(__('waybills.corporation'))
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('waybills.status'))
                    ->enum([
                        'pending' => __('waybills.pending'),
                        'delivered' => __('waybills.delivered'),
                        'cancelled' => __('waybills.cancelled'),
                    ])
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('waybills.due_date'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('waybill_date')
                    ->label(__('waybills.waybill_date'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
            ]);
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
            'index' => Pages\ListWaybills::route('/'),
            'create' => Pages\CreateWaybill::route('/create'),
            'view' => Pages\ViewWaybill::route('/{record}'),
            'edit' => Pages\EditWaybill::route('/{record}/edit'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('waybills.waybill');
    }

    public static function getNavigationLabel(): string
    {
        return __('waybills.waybills');
    }

    public static function getPluralModelLabel(): string
    {
        return __('waybills.waybills');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])->where('company_id', session()->get('company_id'));
    }
}
