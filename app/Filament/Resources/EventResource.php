<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Filament\Resources\EventResource\RelationManagers;
use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationGroup = 'Event Management';

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?int $navigationSort = 2;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Event Name')
                    ->maxLength(255),
                DateTimePicker::make('start_date')
                    ->required()
                    ->label('Start Date')
                    ->placeholder('Select Start Date')
                    ->minDate(now()),
                DateTimePicker::make('end_date')
                    ->required()
                    ->label('End Date')
                    ->placeholder('Select End Date')
                    ->minDate(now()),
                TextInput::make('location')
                    ->required()
                    ->label('Location')
                    ->maxLength(255),
                TextInput::make('code_prefix')
                    ->label('Code Prefix')
                    ->maxLength(255),
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
                    ->label('Event Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->label('Start Date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('End Date')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('code_prefix')
                    ->label('Code Prefix')
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                //
                Filter::make('name')
                    ->form([
                        Forms\Components\TextInput::make('name')
                            ->label('Event Name')
                            ->placeholder('Search Event Name'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['name'], function ($query, $name) {
                            $query->where('name', 'like', "%{$name}%");
                        });
                    }),
                Filter::make('start_date')
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->placeholder('Select Start Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['start_date'], function ($query, $date) {
                            $query->whereDate('start_date', '>=', $date); // Pastikan field database benar
                        });
                    }),
                Filter::make('end_date')
                    ->form([
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->placeholder('Select End Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['end_date'], function ($query, $date) {
                            $query->whereDate('end_date', '<=', $date); // Pastikan field database benar
                        });
                    }),

            ])
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
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}
