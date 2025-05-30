<?php

namespace App\Filament\Resources\VoucherCodeResource\Pages;

use App\Filament\Resources\VoucherCodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVoucherCodes extends ListRecords
{
    protected static string $resource = VoucherCodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
