<?php

namespace App\Filament\Resources\VoucherResource\Pages;

use App\Filament\Resources\VoucherResource;
use App\Models\Voucher;
use Filament\Actions;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTableQuery(): Builder
    {
        $user = Auth::user();

        if ($user->role->name === 'admin') {
            return Voucher::query()->withCount('voucherCodes');
        }

        return Voucher::query()
            ->withCount('voucherCodes')
            ->whereHas('categoryTicketType.category.event', function ($query) use ($user) {
                $query->where('id', $user->event_id);
            });
    }
}
