<?php

namespace App\Filament\Resources\BillingReportResource\Pages;

use App\Filament\Resources\BillingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBillingReports extends ListRecords
{
    protected static string $resource = BillingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
