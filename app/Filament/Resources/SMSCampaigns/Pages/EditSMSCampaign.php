<?php

namespace App\Filament\Resources\SMSCampaigns\Pages;

use App\Filament\Resources\SMSCampaigns\SMSCampaignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSMSCampaign extends EditRecord
{
    protected static string $resource = SMSCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
