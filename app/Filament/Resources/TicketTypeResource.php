<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketTypeResource\Pages;
use App\Filament\Resources\TicketTypeResource\RelationManagers;
use App\Models\Event;
use App\Models\TicketType;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\RawJs;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketTypeResource extends Resource
{
    protected static ?string $model = TicketType::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = 'Event Management';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('event_id')
                    ->label('Event') // Label untuk field
                    ->options(Event::all()->pluck('name', 'id')) // Mengambil nama dan ID event dari model Event
                    ->required()
                    ->searchable() // Membolehkan pencarian event
                    ->placeholder('Pick an Event')
                    ->reactive(),
                TextInput::make('name')
                    ->required()
                    ->label('Ticket Type Name')
                    ->maxLength(255),
                TextInput::make('price')
                    ->required()
                    ->label('Price')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10000000)
                    ->mask(RawJs::make('$money($input)'))
                    ->stripCharacters(',')
                    ->prefix('Rp ')
                    ->reactive(),
                TextInput::make('quota')
                    ->required()
                    ->label('Quota')
                    ->numeric()
                    ->minValue(0)
                    ->maxValue(10000000),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('index')
                    ->rowIndex()
                    ->label('#'),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('event.name')
                    ->label('Event')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('quota')
                    ->label('Quota')
                    ->sortable()
                    ->searchable()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('event_id')
                    ->label('Event')
                    ->searchable()
                    ->options(Event::all()->pluck('name', 'id'))
                    ->placeholder('Select Event'),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListTicketTypes::route('/'),
            'create' => Pages\CreateTicketType::route('/create'),
            'edit' => Pages\EditTicketType::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->role?->name === 'admin';
    }
}
