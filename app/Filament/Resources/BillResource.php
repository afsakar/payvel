<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Filament\Resources\BillResource\RelationManagers;
use App\Models\Bill;
use App\Models\BillItem;
use App\Models\Corporation;
use App\Models\Expense;
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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Carbon\Carbon;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $recordTitleAttribute = 'number';

    public ?Model $record = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)->schema([
                    Grid::make(1)->schema([
                        SpatieMediaLibraryFileUpload::make('file')
                            ->label(__('general.file'))
                            ->maxSize(10240)
                            ->nullable()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('bills')
                            ->rules([
                                'mimes:pdf,png,jpg,jpeg,tiff',
                            ])
                            ->collection('bills')
                    ]),
                ]),
                Card::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('number')
                        ->label(__('bills.bill_number'))
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('issue_date')
                        ->label(__('bills.issue_date'))
                        ->displayFormat('d/m/Y'),
                    Forms\Components\Select::make('company_id')
                        ->label(__('bills.company_name'))
                        ->reactive()
                        ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                        ->searchable()
                        ->default(session()->get('company_id'))
                        ->disabled(),
                    Forms\Components\Select::make('corporation_id')
                        ->label(__('bills.corporation'))
                        ->reactive()
                        ->options(\App\Models\Corporation::all()->pluck('name', 'id'))
                        ->afterStateUpdated(function ($state, Closure $set, Closure $get) {
                            $set('corporation_id', $state);
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('waybill_id')
                        ->label(__('bills.waybill'))
                        ->reactive()
                        ->options(function (Closure $get, ?Model $record) {
                            $waybills = Waybill::where('company_id', $get('company_id'))->where('corporation_id', $get('corporation_id'))->whereDoesntHave('bills')->whereDoesntHave('invoices')->get()->pluck('number', 'id');
                            if ($record && $record->waybill_id) {
                                $waybills->put($record->waybill_id, $record->waybill->number);
                                return $waybills;
                            } else {
                                return Waybill::where('company_id', $get('company_id'))->where('corporation_id', $get('corporation_id'))->whereDoesntHave('bills')->whereDoesntHave('invoices')->get()->pluck('number', 'id');
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
                        ->label(__('bills.withholding_tax'))
                        ->options(\App\Models\WithHolding::all()->pluck('name', 'id', 'rate'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label(__('bills.status'))
                        ->options([
                            'pending' => __('bills.pending'),
                            'delivered' => __('bills.delivered'),
                            'cancelled' => __('bills.cancelled'),
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('discount')
                        ->label(__('bills.discount'))
                        ->default(0)
                        ->numeric()
                        ->nullable(),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\RichEditor::make('notes')
                        ->label(__('bills.notes'))
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->nullable()
                        ->maxLength(65535),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\Repeater::make('items')
                        ->label(__('bills.items'))
                        ->relationship()->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Select::make('material_id')
                                    ->label(__('bills.material'))
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
                                    ->label(__('bills.quantity'))
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('price')
                                    ->label(__('bills.price'))
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
                        ->label(__('bills.payments'))
                        ->relationship()->schema([
                            Grid::make(1)->schema([
                                Forms\Components\Select::make('company_id')
                                    ->label('Company')
                                    ->reactive()
                                    ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                                    ->searchable()
                                    ->default(session()->get('company_id'))
                                    ->disabled()
                                    ->hidden(),
                                Forms\Components\Select::make('corporation_id')
                                    ->label('Corporation')
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
                                Forms\Components\Select::make('expense_id')
                                    ->label(__('bills.expense'))
                                    ->reactive()
                                    ->searchable()
                                    ->options(function (?Model $record, Closure $get) {
                                        if ($record && $record->expense->company_id && $record->expense->company_id) {
                                            $expenses = Expense::query()->where([
                                                'company_id' => $record->expense->company_id,
                                                'corporation_id' => $record->expense->corporation_id,
                                            ])->whereDoesntHave('bills')->pluck('amount', 'id');

                                            return $expenses->put($record->expense_id, $record->expense->amount);
                                        }
                                        return Expense::query()->where([
                                            'company_id' => $get('../../company_id'),
                                            'corporation_id' => $get('../../corporation_id'),
                                        ])->whereDoesntHave('bills')->pluck('amount', 'id');
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
                    ->label(__('bills.issue_date'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('number')
                    ->label(__('bills.bill_number'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('bills.company_name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->label(__('bills.corporation'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waybill.number')
                    ->label(__('bills.waybill'))
                    ->url(fn ($record) => $record->waybill_id ? route('filament.resources.waybills.view', $record->waybill_id) : null)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('with_holding.rate')
                    ->label(__('bills.withholding_tax'))
                    ->formatStateUsing(fn ($state) => "%$state"),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('bills.status'))
                    ->enum([
                        'pending' => __('bills.pending'),
                        'delivered' => __('bills.delivered'),
                        'cancelled' => __('bills.cancelled'),
                    ])
                    ->colors([
                        'primary' => __('bills.pending'),
                        'success' => __('bills.delivered'),
                        'danger' => __('bills.cancelled'),
                    ]),
                Tables\Columns\TextColumn::make('discount')
                    ->label(__('bills.discount'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol),
                Tables\Columns\TextColumn::make('total')
                    ->label(__('bills.total'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol),
                Tables\Columns\TextColumn::make('bill_payments_sum')
                    ->label(__('bills.payments'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
                \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('issue_date')
                    ->label(__('bills.issue_date'))
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label(__('general.download'))
                    ->icon('heroicon-o-download')
                    ->action(function ($record) {
                        return response()->download($record->getMedia('bills')[0]->getPath(), $record->number . " " . $record->corporation->name, [
                            'Content-Type' => $record->getMedia('bills')[0]->mime_type,
                        ]);
                    })
                    ->hidden(fn ($record) => !$record->getMedia() || !$record->getMedia('bills')->count()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
                    ->pageOrientationFieldLabel(__('general.page_orientation'))
                    ->defaultPageOrientation('landscape')
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
        return __('bills.bill');
    }

    public static function getNavigationLabel(): string
    {
        return __('bills.bills');
    }

    public static function getPluralModelLabel(): string
    {
        return __('bills.bills');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'view' => Pages\ViewBill::route('/{record}'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
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
