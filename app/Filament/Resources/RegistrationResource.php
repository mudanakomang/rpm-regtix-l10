<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegistrationResource\Pages;
use App\Helpers\CountryListHelper;
use App\Models\Registration;
use App\Models\TicketType;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class RegistrationResource extends Resource
{
    protected static ?string $model = Registration::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationGroup = 'Race Pack Management';


    private static function calculateNextRegId(): string
    {
        // Ambil reg_id terakhir (misalnya '001', '002')
        $lastRegId = Registration::orderByDesc('id')->first()->id ?? '000';

        // Ambil angka dari reg_id (misalnya '001' => 1)
        $lastNumber = (int) $lastRegId;

        // Increment angka
        $next = $lastNumber + 1;

        // Format menjadi 3 digit (misalnya 2 => '002')
        return str_pad($next, 3, '0', STR_PAD_LEFT);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('ticket_type_id')
                    ->label('Ticket Type') // Label untuk field
                    ->options(
                        TicketType::with('event')->get()->mapWithKeys(function ($ticketType) {
                            return [
                                $ticketType->id => $ticketType->name . ' - ' . ($ticketType->event->name ?? 'No Event'),
                            ];
                        })->toArray()
                    ) // Mengambil nama dan ID event dari model Event
                    ->required()
                    ->searchable() // Membolehkan pencarian event
                    ->placeholder('Pick a Ticket Type'),
                TextInput::make('full_name')
                    ->label('Full Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->required()
                    ->regex('/^(\+62|62|0)8[1-9][0-9]{6,9}$/')
                    ->maxLength(15),
                Radio::make('gender')
                    ->label('Gender') // Label untuk field
                    ->options(Registration::GENDER) // Mengambil nama dan ID event dari model Event
                    ->required()
                    ->inline() // Menampilkan pilihan secara horizontal
                    ->reactive(),
                TextInput::make('place_of_birth')
                    ->label('Place of Birth')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('dob')
                    ->label('Date of Birth')
                    ->required()
                    ->maxDate(now())
                    ->placeholder('Select Date of Birth'),
                TextInput::make('address')
                    ->label('Address')
                    ->required()
                    ->maxLength(255),
                TextInput::make('district')
                    ->label('District')
                    ->required()
                    ->maxLength(255),
                TextInput::make('province')
                    ->label('Province')
                    ->required()
                    ->maxLength(255),
                Select::make('country')
                    ->label('Country')
                    ->required()
                    ->options(CountryListHelper::get('id', true))
                    ->searchable()
                    ->placeholder('Select Country')
                    ->reactive(),
                Select::make('id_card_type')
                    ->label('ID Card Type') // Label untuk field
                    ->options(Registration::ID_CARD_TYPE) // Mengambil nama dan ID event dari model Event
                    ->required()
                    ->searchable() // Membolehkan pencarian event
                    ->placeholder('Pick an ID Card Type'),
                TextInput::make('id_card_number')
                    ->label('ID Card Number')
                    ->required()
                    ->maxLength(255),
                TextInput::make('emergency_contact_name')
                    ->label('Emergency Contact Name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('emergency_contact_phone')
                    ->label('Emergency Contact Phone')
                    ->required()
                    ->tel()
                    ->regex('/^(\+62|62|0)8[1-9][0-9]{6,9}$/')
                    ->maxLength(15),
                Select::make('nationality')
                    ->label('Nationality')
                    ->required()
                    ->options(CountryListHelper::get('id', true))
                    ->searchable()
                    ->placeholder('Select Nationality')
                    ->reactive(),
                Select::make('jersey_size')
                    ->label('Jersey Size') // Label untuk field
                    ->options(Registration::JERSEY_SIZES) // Mengambil nama dan ID event dari model Event
                    ->required()
                    ->searchable() // Membolehkan pencarian event
                    ->placeholder('Pick a Jersey Size'),
                Select::make('blood_type')
                    ->label('Blood Type') // Label untuk field
                    ->options(Registration::BLOOD_TYPE) // Mengambil nama dan ID event dari model Event
                    ->required()
                    ->searchable() // Membolehkan pencarian event
                    ->placeholder('Pick a Blood Type'),
                TextInput::make('community_name')
                    ->label('Community Name')
                    ->maxLength(255),
                TextInput::make('bib_name')
                    ->label('BIB Name')
                    ->maxLength(255),
                TextInput::make('reg_id')
                    ->label('Registration ID')
                    ->required()
                    ->maxLength(255)
                    ->default(fn($record) => $record ? $record->reg_id : self::calculateNextRegId())
                    ->readOnly(fn($record) => $record && $record->exists),
                Hidden::make('registration_date')
                    ->default(now())
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('is_validated')
                    ->badge()
                    ->icon(fn($record) => $record->is_validated ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->formatStateUsing(fn($state) => $state ? 'Validated' : 'Not Validated')
                    ->color(fn($record) => $record->is_validated ? 'success' : 'danger')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('registration_date')
                    ->label('Registration Date')
                    ->sortable()
                    ->searchable()
                    ->dateTime(),
                TextColumn::make('full_name')
                    ->label('Full Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('phone')
                    ->label('Phone')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('gender')
                    ->label('Gender')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('place_of_birth')
                    ->label('Place of Birth')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('dob')
                    ->label('Date of Birth')
                    ->sortable()
                    ->searchable()
                    ->date(),
                TextColumn::make('address')
                    ->label('Address')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('district')
                    ->label('District')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('province')
                    ->label('Province')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('country')
                    ->label('Country')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('id_card_type')
                    ->label('ID Card Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('id_card_number')
                    ->label('ID Card Number')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('emergency_contact_name')
                    ->label('Emergency Contact Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('emergency_contact_phone')
                    ->label('Emergency Contact Phone')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('blood_type')
                    ->label('Blood Type')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('nationality')
                    ->label('Nationality')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('jersey_size')
                    ->label('Jersey Size')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('community_name')
                    ->label('Community Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('bib_name')
                    ->label('Bib Name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('reg_id')
                    ->label('Registration ID')
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Filter::make('reg_id')
                    ->columns(1)
                    ->label('Registration ID')
                    ->form([
                        TextInput::make('reg_id')
                            ->label('Registration ID'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['reg_id'], function ($query, $reg_id) {
                            $query->where('reg_id', 'like', "%{$reg_id}%");
                        });
                    }),
                SelectFilter::make('ticket_type_id')
                    ->label('Ticket Type')
                    ->columns(1)
                    ->searchable()
                    ->options(
                        TicketType::with('event')->get()->mapWithKeys(function ($ticketType) {
                            return [
                                $ticketType->id => $ticketType->name . ' - ' . ($ticketType->event->name ?? 'No Event'),
                            ];
                        })->toArray()
                    ),
                SelectFilter::make('is_validated')
                    ->label('Status')
                    ->columns(1)
                    ->options([
                        '1' => 'Validated',
                        '0' => 'Not Validated',
                    ]),
                Filter::make('start_date')
                    ->columns(2)
                    ->form([
                        DatePicker::make('start_date')
                            ->label('Start Date')
                            ->placeholder('Select Start Date'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['start_date'], function ($query, $date) {
                            $query->whereDate('registration_date', '>=', $date); // Pastikan field database benar
                        });
                    }),
                Filter::make('end_date')
                    ->columns(2)
                    ->form([
                        DatePicker::make('end_date')
                            ->label('End Date')
                            ->placeholder('Select End Date')
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query->when($data['end_date'], function ($query, $date) {
                            $query->whereDate('registration_date', '<=', $date); // Pastikan field database benar
                        });
                    }),

            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)
            ->actions([
                Action::make('print')
                    ->label('Print')
                    ->icon('heroicon-m-printer')
                    ->url(function ($record) {
                        return route('registration.print', $record->id);
                    })
                    ->openUrlInNewTab()
                    ->visible(function ($record) {
                        return $record->is_validated;
                    }),
                Tables\Actions\ViewAction::make()
                    ->color('success')
                    ->icon('heroicon-o-eye')
                    ->label('View'),
            ], position: ActionsPosition::BeforeColumns)
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
            'index' => Pages\ListRegistrations::route('/'),
            'create' => Pages\CreateRegistration::route('/create'),
            'edit' => Pages\EditRegistration::route('/{record}/edit'),
            'view' => Pages\ViewRegistration::route('/{record}'),
        ];
    }
}
