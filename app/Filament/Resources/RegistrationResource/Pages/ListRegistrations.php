<?php

namespace App\Filament\Resources\RegistrationResource\Pages;

use App\Filament\Exports\RegistrationExporter;
use App\Filament\Resources\RegistrationResource;
use App\Models\TicketType;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListRegistrations extends ListRecords
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('Export')
                ->exporter(RegistrationExporter::class)
                ->icon('heroicon-o-arrow-down-tray')
                ->fileName('registration-' . now()->format('Y-m-d-his'))
                ->color('success'),
            Actions\CreateAction::make(),
        ];
    }

    public function tableQuery(Builder $query)
    {
        return $query->when(request()->has('event_id'), function ($query) {
            return $query->where('event_id', request('event_id'));
        });
    }
}
