<?php

namespace App\Filament\Resources\MedicalCenters\Pages;

use App\Filament\Resources\MedicalCenters\MedicalCenterResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMedicalCenters extends ListRecords
{
    protected static string $resource = MedicalCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
