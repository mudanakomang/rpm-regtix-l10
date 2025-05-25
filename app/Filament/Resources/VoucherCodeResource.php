<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VoucherCodeResource\Pages;
use App\Filament\Resources\VoucherCodeResource\RelationManagers;
use App\Models\VoucherCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VoucherCodeResource extends Resource
{
    protected static ?string $model = VoucherCode::class;
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationGroup = 'Event Management';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('voucher.name')->label('Voucher Name')->searchable(),
            TextColumn::make('voucher.categoryTicketType.category.event.name')->searchable()
                ->label('Event Name'),
            TextColumn::make('voucher.categoryTicketType.category.name')->searchable()
                ->label('Category Name'),
            TextColumn::make('voucher.categoryTicketType.ticketType.name')->searchable()
                ->label('Ticket Type Name'),
                TextColumn::make('code')->label('Voucher Code')->searchable(),
                TextColumn::make('used')->label('Used')->formatStateUsing(fn(bool $state) => $state ? 'Yes' : 'No')
                ->badge()
                ->color(fn(bool $state) => $state ? 'success' : 'danger'),
                TextColumn::make('registration_id')->label('Registration ID'),
                // Kolom lain yang dibutuhkan
            ])
            ->filters([
            // Filter berdasarkan voucher_id jika ada
            SelectFilter::make('voucher_id')
                ->label('Voucher Name')
                ->options(function () {
                    return \App\Models\Voucher::all()->pluck('name', 'id');
                })
                ->searchable()
                ->multiple(),
               
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
            'index' => Pages\ListVoucherCodes::route('/'),
            'create' => Pages\CreateVoucherCode::route('/create'),
            // 'edit' => Pages\EditVoucherCode::route('/{record}/edit'),
        ];
    }
}
