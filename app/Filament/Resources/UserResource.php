<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\Event;
use App\Models\Role;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Name')
                    ->maxLength(255),
                TextInput::make('email')
                    ->required()
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->required(fn(string $context) => $context === 'create') // hanya required saat create
                    ->dehydrateStateUsing(fn($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn($state) => filled($state)) // hanya dehydrate jika diisi
                    ->maxLength(255)
                    ->same('password_confirmation'), // tambahkan validasi sama dengan konfirmasi

                TextInput::make('password_confirmation')
                    ->label('Confirm Password')
                    ->password()
                    ->visible(fn(string $context) => $context === 'create' || $context === 'edit') // hanya tampil saat create/edit
                    ->dehydrated(false), // tidak dikirim ke model

                Radio::make('role_id')  // Gunakan Radio untuk memilih role
                    ->label('Role')
                    ->options(Role::pluck('label', 'id')->toArray())  // Mengambil semua role
                    ->required(),
            Select::make('event_id')
                ->label('Event')
                ->options(Event::pluck('name', 'id'))
                ->searchable()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            TextColumn::make('event.name')
                ->label('Event')
                ->sortable()
                ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->role?->name === 'admin';
    }

    public static function canCreate(): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->role?->name === 'admin';
    }

    public static function canEdit($record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->role?->name === 'admin';
    }

    public static function canDelete($record): bool
    {
        return \Illuminate\Support\Facades\Auth::user()?->role?->name === 'admin';
    }
}
