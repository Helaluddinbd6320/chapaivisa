<?php

namespace App\Filament\Resources\SMSCampaigns\Pages;

use App\Filament\Resources\SMSCampaigns\SMSCampaignResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSMSCampaigns extends ListRecords
{
    protected static string $resource = SMSCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
