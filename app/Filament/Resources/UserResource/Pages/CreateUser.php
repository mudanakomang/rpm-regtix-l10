<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected array $eventIds = [];

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ambil event_ids lalu keluarkan dari $data agar tidak error saat mass assignment
        $this->eventIds = $data['event_ids'] ?? [];
        unset($data['event_ids']);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->events()->sync($this->eventIds);
    }
}
