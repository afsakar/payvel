<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CheckResource\Pages;
use App\Filament\Resources\CheckResource\RelationManagers;
use App\Models\Account;
use App\Models\Check;
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
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Forms\Components\FileUpload;

class CheckResource extends Resource
{
    protected static ?string $model = Check::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    FileUpload::make('image')
                        ->imagePreviewHeight('150')
                        ->loadingIndicatorPosition('left')
                        ->panelAspectRatio('2:1')
                        ->panelLayout('integrated')
                        ->label(__('checks.image'))
                        ->image()
                        ->maxSize(10240)
                        ->nullable()
                        ->image()
                        ->directory('checks')
                ]),
                Grid::make(2)->schema([
                    Forms\Components\TextInput::make('number')
                        ->unique(ignoreRecord: true)
                        ->label(__('checks.number'))
                        ->required()
                        ->maxLength(20),
                    Forms\Components\TextInput::make('amount')
                        ->label(__('checks.amount'))
                        ->required(),
                ]),
                Forms\Components\DatePicker::make('due_date')
                    ->minDate(now()->addDay())
                    ->label(__('checks.due_date'))
                    ->required()
                    ->displayFormat('d/m/Y'),
                Forms\Components\DatePicker::make('issue_date')
                    ->label(__('checks.issue_date'))
                    ->required()
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('company_id')
                    ->label(__('checks.company'))
                    ->reactive()
                    ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->default(session()->get('company_id'))
                    ->disabled(),
                Forms\Components\Select::make('corporation_id')
                    ->label(__('checks.corporation'))
                    ->reactive()
                    ->options(\App\Models\Corporation::query()->get()->pluck('name', 'id'))
                    ->afterStateUpdated(function ($state, Closure $set) {
                        $corporation = \App\Models\Corporation::query()->find($state);
                        $corporation ? $set('currency_id', $corporation->currency_id) : null;
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('account_id')
                    ->label(__('checks.account'))
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
                    ->disabled(function (Closure $get) {
                        return !$get('corporation_id');
                    })
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('checks.type'))
                    ->options([
                        'purchase' => __('checks.purchase'),
                        'sale' => __('checks.sale'),
                    ])
                    ->required(),
                Grid::make(1)->schema([
                    Forms\Components\Select::make('status')
                        ->label(__('checks.status'))
                        ->reactive()
                        ->afterStateUpdated(function ($state, Closure $set) {
                            $set('status', $state);
                        })
                        ->options([
                            'pending' => __('checks.pending'),
                            'paid' => __('checks.paid'),
                            'cancelled' => __('checks.cancelled'),
                        ])
                        ->required(),
                    Forms\Components\DatePicker::make('paid_date')
                        ->minDate(now()->subDay())
                        ->default(null)
                        ->required(function (Closure $get) {
                            return $get('status') == 'paid';
                        })
                        ->label(__('checks.paid_date'))
                        ->closeOnDateSelection()
                        ->disabled(function (Closure $get) {
                            return $get('status') != 'paid';
                        })
                        ->displayFormat('d/m/Y'),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->maxLength(65535),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label(__('checks.image'))
                    ->height('auto')
                    ->width('80px'),
                Tables\Columns\TextColumn::make('due_date')
                    ->label(__('checks.due_date'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('paid_date')
                    ->label(__('checks.paid_date'))
                    ->dateTime('d/m/Y'),
                Tables\Columns\BadgeColumn::make('type')
                    ->label(__('checks.type'))
                    ->enum([
                        'purchase' => __('checks.purchase'),
                        'sale' => __('checks.sale'),
                    ])
                    ->colors([
                        'success' => 'purchase',
                        'danger' => 'sale',
                    ]),
                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('checks.status'))
                    ->enum([
                        'pending' => __('checks.pending'),
                        'paid' => __('checks.paid'),
                        'cancelled' => __('checks.cancelled'),
                    ])
                    ->colors([
                        'primary' => 'pending',
                        'success' => 'paid',
                        'danger' => 'cancelled',
                    ]),
                Tables\Columns\TextColumn::make('number')
                    ->searchable()
                    ->sortable()
                    ->label(__('checks.number')),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol)
                    ->label(__('checks.amount')),
                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('checks.account')),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('checks.company')),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->searchable()
                    ->label(__('checks.corporation')),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label(__('checks.description')),
                Tables\Columns\TextColumn::make('issue_date')
                    ->label(__('checks.issue_date'))
                    ->dateTime('d/m/Y'),
            ])
            ->filters([
                Filter::make('amount')
                    ->form([
                        Forms\Components\TextInput::make('min_amount')
                            ->label(__('general.min_amount')),
                        Forms\Components\TextInput::make('max_amount')
                            ->label(__('general.max_amount')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['min_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '>=', $amount),
                            )
                            ->when(
                                $data['max_amount'],
                                fn (Builder $query, $amount): Builder => $query->where('amount', '<=', $amount),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->after(function ($record) {
                        if ($record->status != 'paid') {
                            $record->paid_date = null;
                            $record->save();
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('checks.check');
    }

    public static function getNavigationLabel(): string
    {
        return __('checks.checks');
    }

    public static function getPluralModelLabel(): string
    {
        return __('checks.checks');
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 2;
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageChecks::route('/'),
        ];
    }
}
