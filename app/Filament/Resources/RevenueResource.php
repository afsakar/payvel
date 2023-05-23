<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use App\Filament\Resources\RevenueResource\Pages;
use App\Filament\Resources\RevenueResource\RelationManagers;
use App\Filament\Resources\RevenueResource\Widgets\RevenueWidget;
use App\Models\Account;
use App\Models\Company;
use App\Models\Revenue;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Routing\Route;

class RevenueResource extends Resource
{
    protected static ?string $model = Revenue::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Forms\Components\TextInput::make('amount')
                        ->required(),
                ]),
                Forms\Components\DatePicker::make('due_at')
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('company_id')
                    ->label('Company')
                    ->reactive()
                    ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->default(session()->get('company_id'))
                    ->disabled(),
                Forms\Components\Select::make('corporation_id')
                    ->label('Corporation')
                    ->reactive()
                    ->options(\App\Models\Corporation::query()->get()->pluck('name', 'id'))
                    ->afterStateUpdated(function ($state, Closure $set) {
                        $corporation = \App\Models\Corporation::query()->find($state);
                        $corporation ? $set('currency_id', $corporation->currency_id) : null;
                    })
                    ->searchable()
                    ->placeholder('Select Corporation')
                    ->required(),
                Forms\Components\Select::make('account_id')
                    ->label('Account')
                    ->reactive()
                    ->options(function (Closure $get, ?Model $record) {
                        if ($record && $record->account_id) {
                            return Account::query()->where('currency_id', $record->corporation->currency_id)->get()->pluck('name', 'id');
                        }
                        return Account::query()->where('currency_id', $get('currency_id'))->get()->pluck('name', 'id');
                    })
                    ->afterStateUpdated(function ($state, Closure $set) {
                        $set('account_id', $state);
                    })
                    ->placeholder('Select Account')
                    ->disabled(function (Closure $get) {
                        return !$get('corporation_id');
                    })
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->reactive()
                    ->options(function (Closure $get) {
                        return \App\Models\Category::query()->where('type', 'income')->get()->pluck('name', 'id');
                    })
                    ->placeholder('Select Category')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label('Type')
                    ->options([
                        'formal' => 'Formal',
                        'informal' => 'Informal',
                    ])
                    ->placeholder('Select Type')
                    ->required(),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
                        ->rows(2)
                        ->required()
                        ->maxLength(65535),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('due_at')
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label('Invoice Number'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('account.name')
                    ->searchable()
                    ->label('Account'),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->label('Company'),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->searchable()
                    ->label('Corporation'),
                Tables\Columns\BadgeColumn::make('category.name')
                    ->color('success')
                    ->label('Category'),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->has_any_relation)
            ])
            ->bulkActions([]);
    }

    public static function getWidgets(): array
    {
        return [
            RevenueWidget::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRevenues::route('/'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', session()->get('company_id'));
    }
}
