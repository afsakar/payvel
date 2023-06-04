<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Account;
use App\Models\Bill;
use App\Models\Expense;
use Closure;
use Filament\Forms;
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
use Filament\Tables\Filters\Filter;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-logout';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label(__('expenses.amount'))
                        ->required(),
                ]),
                Forms\Components\DatePicker::make('due_at')
                    ->label(__('expenses.due_at'))
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('company_id')
                    ->label(__('expenses.company_name'))
                    ->reactive()
                    ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->default(session()->get('company_id'))
                    ->disabled(),
                Forms\Components\Select::make('corporation_id')
                    ->label(__('expenses.corporation'))
                    ->reactive()
                    ->options(\App\Models\Corporation::query()->get()->pluck('name', 'id'))
                    ->afterStateUpdated(function ($state, Closure $set) {
                        $corporation = \App\Models\Corporation::query()->find($state);
                        $corporation ? $set('currency_id', $corporation->currency_id) : null;
                    })
                    ->searchable()
                    ->placeholder(__('expenses.select_corporation'))
                    ->required(),
                Forms\Components\Select::make('account_id')
                    ->label(__('expenses.account_name'))
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
                    ->label(__('expenses.select_account'))
                    ->disabled(function (Closure $get) {
                        return !$get('corporation_id');
                    })
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label(__('expenses.category'))
                    ->reactive()
                    ->options(function (Closure $get) {
                        return \App\Models\Category::query()->where('type', 'expense')->get()->pluck('name', 'id');
                    })
                    ->placeholder(__('expenses.select_category'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('expenses.type'))
                    ->options([
                        'formal' => 'Formal',
                        'informal' => 'Informal',
                    ])
                    ->placeholder(__('expenses.select_type'))
                    ->required(),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
                        ->label(__('expenses.description'))
                        ->rows(2)
                        ->required()
                        ->maxLength(65535),
                    SpatieMediaLibraryFileUpload::make('image')
                        ->imagePreviewHeight('150')
                        ->loadingIndicatorPosition('left')
                        ->panelAspectRatio('2:1')
                        ->panelLayout('integrated')
                        ->label(__('general.file'))
                        ->maxSize(10240)
                        ->nullable()
                        ->disk('public')
                        ->visibility('public')
                        ->directory('expenses')
                        ->rules([
                            'mimes:pdf,png,jpg,jpeg,tiff',
                        ])
                        ->collection('expenses')
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('due_at')
                    ->label(__('expenses.due_at'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('expenses.amount'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol),
                Tables\Columns\TextColumn::make('bill_number')
                    ->label(__('expenses.bill_number'))
                    ->url(fn ($record) => $record->bill_number !== null ? route('filament.resources.bills.view', Bill::where('number', $record->bill_number)->first()->id) : null),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('expenses.description'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('account.name')
                    ->searchable()
                    ->label(__('expenses.account_name')),
                Tables\Columns\TextColumn::make('company.name')
                    ->searchable()
                    ->label(__('expenses.company_name')),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->searchable()
                    ->label(__('expenses.corporation')),
                Tables\Columns\BadgeColumn::make('category.name')
                    ->color('danger')
                    ->label(__('expenses.category')),
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('expenses.type'))
                    ->enum([
                        'formal' => __('expenses.formal'),
                        'informal' => __('expenses.informal'),
                    ])
                    ->colors([
                        'primary',
                        'secondary' => 'informal',
                    ]),

            ])
            ->filters([
                //
                \Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter::make('due_at')
                    ->label(__('expenses.due_at')),
                Filter::make('currency_id')
                    ->label(__('currencies.currency'))
                    ->form([
                        Forms\Components\Select::make('currency_id')
                            ->label(__('currencies.currency'))
                            ->options(\App\Models\Currency::all()->pluck('name', 'id'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['currency_id'], fn ($query, $currency_id) => $query->whereHas('corporation', fn ($query) => $query->where('currency_id', $currency_id)));
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label(__('general.download'))
                    ->icon('heroicon-o-download')
                    ->action(function ($record) {
                        return response()->download($record->getMedia('expenses')[0]->getPath(), __('general.file'), [
                            'Content-Type' => $record->getMedia('expenses')[0]->mime_type,
                        ]);
                    })
                    ->hidden(fn ($record) => !$record->getMedia() || !$record->getMedia('expenses')->count()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->has_any_relation)
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
                    ->pageOrientationFieldLabel(__('general.page_orientation'))
                    ->defaultPageOrientation('landscape')
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageExpenses::route('/'),
        ];
    }

    public static function getModelLabel(): string
    {
        return __('expenses.expense');
    }

    public static function getNavigationLabel(): string
    {
        return __('expenses.expenses');
    }

    public static function getPluralModelLabel(): string
    {
        return __('expenses.expenses');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', session()->get('company_id'));
    }
}
