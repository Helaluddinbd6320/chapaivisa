<?php

namespace App\Filament\Resources\MedicalCenters\Pages;

use App\Filament\Resources\MedicalCenters\MedicalCenterResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditMedicalCenter extends EditRecord
{
    protected static string $resource = MedicalCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
