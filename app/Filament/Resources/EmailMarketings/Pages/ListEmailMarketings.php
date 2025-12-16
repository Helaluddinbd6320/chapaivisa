<?php

namespace App\Filament\Resources\EmailMarketings\Pages;

use App\Filament\Resources\EmailMarketings\EmailMarketingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEmailMarketings extends ListRecords
{
    protected static string $resource = EmailMarketingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
