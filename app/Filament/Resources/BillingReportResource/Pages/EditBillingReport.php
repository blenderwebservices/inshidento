<?php

namespace App\Filament\Resources\BillingReportResource\Pages;

use App\Filament\Resources\BillingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBillingReport extends EditRecord
{
    protected static string $resource = BillingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
