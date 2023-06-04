<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages\ViewInvoice;
use App\Filament\Resources\RevenueResource\Pages;
use App\Filament\Resources\RevenueResource\RelationManagers;
use App\Filament\Resources\RevenueResource\Widgets\RevenueWidget;
use App\Models\Account;
use App\Models\Company;
use App\Models\Invoice;
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
use AlperenErsoy\FilamentExport\Actions\FilamentExportBulkAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;

class RevenueResource extends Resource
{
    protected static ?string $model = Revenue::class;

    protected static ?string $navigationIcon = 'heroicon-o-login';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Forms\Components\TextInput::make('amount')
                        ->label(__('revenues.amount'))
                        ->required(),
                ]),
                Forms\Components\DatePicker::make('due_at')
                    ->label(__('revenues.due_at'))
                    ->displayFormat('d/m/Y'),
                Forms\Components\Select::make('company_id')
                    ->label(__('revenues.company_name'))
                    ->reactive()
                    ->options(\App\Models\Company::where('id', session()->get('company_id'))->pluck('name', 'id'))
                    ->searchable()
                    ->default(session()->get('company_id'))
                    ->disabled(),
                Forms\Components\Select::make('corporation_id')
                    ->label(__('revenues.corporation'))
                    ->reactive()
                    ->options(\App\Models\Corporation::query()->get()->pluck('name', 'id'))
                    ->afterStateUpdated(function ($state, Closure $set) {
                        $corporation = \App\Models\Corporation::query()->find($state);
                        $corporation ? $set('currency_id', $corporation->currency_id) : null;
                    })
                    ->searchable()
                    ->placeholder(__('revenues.select_corporation'))
                    ->required(),
                Forms\Components\Select::make('account_id')
                    ->label(__('revenues.account_name'))
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
                    ->placeholder(__('revenues.select_account'))
                    ->disabled(function (Closure $get) {
                        return !$get('corporation_id');
                    })
                    ->required(),
                Forms\Components\Select::make('category_id')
                    ->label(__('revenues.category'))
                    ->reactive()
                    ->options(function (Closure $get) {
                        return \App\Models\Category::query()->where('type', 'income')->get()->pluck('name', 'id');
                    })
                    ->placeholder(__('revenues.select_category'))
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->label(__('revenues.type'))
                    ->options([
                        'formal' => __('revenues.formal'),
                        'informal' => __('revenues.informal'),
                    ])
                    ->placeholder(__('revenues.select_type'))
                    ->required(),
                Grid::make(1)->schema([
                    Forms\Components\Textarea::make('description')
                        ->label(__('revenues.description'))
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
                        ->directory('revenues')
                        ->rules([
                            'mimes:pdf,png,jpg,jpeg,tiff',
                        ])
                        ->collection('revenues')
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('due_at')
                    ->label(__('revenues.due_at'))
                    ->sortable()
                    ->dateTime('d/m/Y'),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('revenues.amount'))
                    ->formatStateUsing(fn ($record, $state) => $record->corporation->currency->position == 'left' ? $record->corporation->currency->symbol . number_format($state, 2) : number_format($state, 2) . $record->corporation->currency->symbol),
                Tables\Columns\TextColumn::make('invoice_number')
                    ->label(__('revenues.invoice_number'))
                    ->url(fn ($record) => $record->invoice_number !== null ? route('filament.resources.invoices.view', Invoice::where('number', $record->invoice_number)->first()->id) : null),
                Tables\Columns\TextColumn::make('description')
                    ->label(__('revenues.description'))
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('account.name')
                    ->label(__('revenues.account_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->label(__('revenues.company_name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('corporation.name')
                    ->searchable()
                    ->label(__('revenues.corporation')),
                Tables\Columns\BadgeColumn::make('category.name')
                    ->color('success')
                    ->label(__('revenues.category')),
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
                DateRangeFilter::make('due_at')
                    ->label(__('revenues.due_at')),
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
                        return response()->download($record->getMedia('revenues')[0]->getPath(), __('general.file'), [
                            'Content-Type' => $record->getMedia('revenues')[0]->mime_type,
                        ]);
                    })
                    ->hidden(fn ($record) => !$record->getMedia()),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn ($record) => $record->has_any_relation)
            ])
            ->bulkActions([
                FilamentExportBulkAction::make('export')
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            RevenueWidget::class,
        ];
    }

    public static function getModelLabel(): string
    {
        return __('revenues.revenue');
    }

    public static function getNavigationLabel(): string
    {
        return __('revenues.revenues');
    }

    public static function getPluralModelLabel(): string
    {
        return __('revenues.revenues');
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
