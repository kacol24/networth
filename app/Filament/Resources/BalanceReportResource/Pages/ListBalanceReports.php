<?php

namespace App\Filament\Resources\BalanceReportResource\Pages;

use App\Filament\Resources\BalanceReportResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBalanceReports extends ListRecords
{
    protected static string $resource = BalanceReportResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
