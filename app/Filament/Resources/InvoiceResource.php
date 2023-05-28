<?php

namespace App\Filament\Resources;

use Akaunting\Money\View\Components\Money;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Company;
use App\Models\Corporation;
use App\Models\Invoice;
use App\Models\Material;
use App\Models\Revenue;
use App\Models\Waybill;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput\Mask;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;


class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'number';

    public ?Model $record = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('number')
                        ->label(__('invoices.invoice_number'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('issue_date')
                        ->label(__('invoices.issue_date'))
                        ->displayFormat('d/m/Y'),
                    Forms\Components\Select::make('company_id')
                        ->label(__('invoices.company_name'))
                        ->reactive()
                        ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                        ->searchable()
                        ->default(session()->get('company_id'))
                        ->disabled(),
                    Forms\Components\Select::make('corporation_id')
                        ->label(__('invoices.corporation'))
                        ->reactive()
                        ->options(\App\Models\Corporation::all()->pluck('name', 'id'))
                        ->afterStateUpdated(function ($state, Closure $set, Closure $get) {
                            $set('corporation_id', $state);
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('waybill_id')
                        ->label(__('invoices.waybill'))
                        ->reactive()
                        ->options(function (Closure $get, ?Model $record) {
                            $waybills = Waybill::where('company_id', $get('company_id'))->where('corporation_id', $get('corporation_id'))->whereDoesntHave('invoices')->get()->pluck('number', 'id');
                            if ($record && $record->waybill_id) {
                                $waybills->put($record->waybill_id, $record->waybill->number);
                                return $waybills;
                            } else {
                                return Waybill::where('company_id', $get('company_id'))->where('corporation_id', $get('corporation_id'))->whereDoesntHave('invoices')->get()->pluck('number', 'id');
                            }
                        })
                        ->afterStateUpdated(function ($state, Closure $set, ?Model $record) {
                            $set('waybill_id', $state);
                            if ($record && $state !== null) {
                                $record->items()->delete();
                            }
                        })
                        ->searchable()
                        ->nullable(),
                    Forms\Components\Select::make('with_holding_id')
                        ->label(__('invoices.withholding_tax'))
                        ->options(\App\Models\Withholding::all()->pluck('name', 'id', 'rate'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label(__('invoices.status'))
                        ->options([
                            'pending' => 'Pending',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('discount')
                        ->label(__('invoices.discount'))
                        ->default(0)
                        ->numeric()
                        ->nullable(),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\RichEditor::make('notes')
                        ->label(__('invoices.notes'))
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->nullable()
                        ->maxLength(65535),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\Repeater::make('items')
                        ->label(__('invoices.items'))
                        ->relationship()->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Select::make('material_id')
                                    ->label(__('invoices.material'))
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
                                    ->label(__('invoices.quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('price')
                                    ->label(__('invoices.price'))
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                            ])
                        ])
                        ->requiredWithoutAll('waybill_id')
                        ->disabled(function (Closure $get) {
                            return $get('corporation_id') === null;
                        })
                        ->defaultItems(0)
                ])
                    ->hidden(function (Closure $get) {
                        return $get('waybill_id') !== null;
                    }),
                Card::make()->columns(1)->schema([
                    Forms\Components\Repeater::make('payments')
                        ->label(__('invoices.payments'))
                        ->relationship()->schema([
                            Grid::make(1)->schema([
                                Forms\Components\Select::make('company_id')
                                    ->label(__('invoices.company_name'))
                                    ->reactive()
                                    ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->default(session()->get('company_id'))
                                    ->disabled()
                                    ->hidden(),
                                Forms\Components\Select::make('corporation_id')
                                    ->label(__('invoices.corporation'))
                                    ->disabled()
                                    ->options(\App\Models\Corporation::query()->pluck('name', 'id'))
                                    ->reactive()
                                    ->default(function (Closure $get, ?Model $record) {
                                        if ($record && $record->corporation_id) {
                                            return Corporation::query()->find($record->corporation_id)->id;
                                        } else {
                                            return Corporation::query()->find($get('../../corporation_id'))->id;
                                        }
                                    })->hidden(),
                                Forms\Components\Select::make('revenue_id')
                                    ->label(__('invoices.revenue'))
                                    ->reactive()
                                    ->searchable()
                                    ->options(function (?Model $record, Closure $get) {
                                        if ($record && $record->revenue->company_id && $record->revenue->company_id) {
                                            $revenues = Revenue::query()->where([
                                                'company_id' => $record->revenue->company_id,
                                                'corporation_id' => $record->revenue->corporation_id,
                                            ])->whereDoesntHave('invoices')->pluck('amount', 'id');

                                            return $revenues->put($record->revenue_id, $record->revenue->amount);
                                        }
                                        return Revenue::query()->where([
                                            'company_id' => $get('../../company_id'),
                                            'corporation_id' => $get('../../corporation_id'),
                                        ])->whereDoesntHave('invoices')->pluck('amount', 'id');
                                    })
                                    ->required(),
                            ])
                        ])
                        ->disabled(function (Closure $get) {
                            return $get('corporation_id') === null;
                        })
                        ->defaultItems(0)
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('invoices.issue_date'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('invoices.invoice_number')),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('invoices.company_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->label(__('invoices.corporation'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waybill.number')
                    ->label(__('invoices.waybill'))
                    ->url(fn ($record) => $record->waybill_id ? route('filament.resources.waybills.view', $record->waybill_id) : null)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('with_holding.rate')
                    ->label(__('invoices.withholding_tax'))
                    ->formatStateUsing(fn ($state) => "%$state"),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('invoices.status'))
                    ->enum([
                        'pending' => 'Pending',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ])
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'delivered',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('discount')
                    ->label(__('invoices.discount'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('invoices.total'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol),
                Tables\Columns\TextColumn::make('invoice_payments_sum')
                    ->label(__('invoices.payments'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol)
                    ->sortable(),
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

    public static function getModelLabel(): string
    {
        return __('invoices.invoice');
    }

    public static function getNavigationLabel(): string
    {
        return __('invoices.invoices');
    }

    public static function getPluralModelLabel(): string
    {
        return __('invoices.invoices');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
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
