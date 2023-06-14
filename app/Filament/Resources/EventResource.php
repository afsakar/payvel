<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Check;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Grid;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(1)->schema([
                    Forms\Components\TextInput::make('title')
                        ->label(__('events.title'))
                        ->required()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('description')
                        ->label(__('events.description'))
                        ->rows(2)
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DateTimePicker::make('start')
                        ->label(__('events.start'))
                        ->displayFormat('d/m/Y H:i')
                        ->required(),
                    Forms\Components\DateTimePicker::make('end')
                        ->label(__('events.end'))
                        ->displayFormat('d/m/Y H:i'),
                    Forms\Components\Toggle::make('reminder')
                        ->label(__('events.reminder')),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('events.title'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('start')
                    ->label(__('events.start'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('end')
                    ->label(__('events.end'))
                    ->sortable()
                    ->dateTime('d/m/Y H:i'),
                Tables\Columns\TextColumn::make('check.number')
                    ->label(__('events.check')),
                Tables\Columns\ToggleColumn::make('reminder')
                    ->label(__('events.reminder')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getModelLabel(): string
    {
        return __('events.event');
    }

    public static function getNavigationLabel(): string
    {
        return __('events.events');
    }

    public static function getPluralModelLabel(): string
    {
        return __('events.events');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEvents::route('/'),
        ];
    }
}
