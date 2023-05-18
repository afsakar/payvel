<?php

namespace App\Filament\Resources;

use Akaunting\Money\View\Components\Money;
use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Material;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public ?Model $record = null;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->columns(2)->schema([
                    Forms\Components\TextInput::make('number')
                        ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('issue_date')->displayFormat('d/m/Y'),
                    Forms\Components\Select::make('company_id')
                        ->label('Company')
                        ->options(Company::all()->pluck('name', 'id'))
                        ->reactive()
                        ->afterStateUpdated(function ($state, Closure $set) {
                            $set('company_id', $state);
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('corporation_id')
                        ->label('Corporation')
                        ->options(\App\Models\Corporation::all()->pluck('name', 'id'))
                        ->afterStateUpdated(function ($state, Closure $set, Closure $get) {
                            $set('corporation_id', $state);
                        })
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('waybill_id')
                        ->label('Waybill')
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
                        ->afterStateUpdated(function ($state, Closure $set, Closure $get) {
                            $set('waybill_id', $state);
                        })
                        ->searchable()
                        ->nullable(),
                    Forms\Components\Select::make('with_holding_id')
                        ->label('Withholding')
                        ->options(\App\Models\Withholding::all()->pluck('name', 'id', 'rate'))
                        ->searchable()
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Pending',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('discount')
                        ->default(0)
                        ->numeric()
                        ->nullable(),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\RichEditor::make('notes')
                        ->disableToolbarButtons([
                            'attachFiles',
                            'codeBlock',
                        ])
                        ->nullable()
                        ->maxLength(65535),
                ]),
                Card::make()->columns(1)->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()->schema([
                            Grid::make(3)->schema([
                                Forms\Components\Select::make('material_id')
                                    ->label('Material')
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
                                    ->required()
                                    ->numeric()
                                    ->default(1),
                                Forms\Components\TextInput::make('price')
                                    ->required()
                                    ->numeric()
                                    ->disabled()
                            ])
                        ])
                        ->disabled(function (Closure $get) {
                            return $get('corporation_id') === null;
                        })
                        ->hidden(function (Closure $get) {
                            return $get('waybill_id') !== null;
                        })
                        ->defaultItems(0)
                        ->createItemButtonLabel('Add Invoice Item')
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('issue_date')
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('number'),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('waybill.number')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('with_holding.rate')
                    ->formatStateUsing(fn ($state) => "%$state"),
                Tables\Columns\BadgeColumn::make('status')
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
                    ->money(function ($record) {
                        return $record->corporation->currency->code;
                    }, true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('total')
                    ->money(function ($record) {
                        return $record->corporation->currency->code;
                    }, true)
                    ->searchable()
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
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ForceDeleteBulkAction::make(),
                Tables\Actions\RestoreBulkAction::make(),
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
            ]);
    }
}
