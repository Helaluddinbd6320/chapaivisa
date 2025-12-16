<?php

namespace App\Filament\Resources\EmailMarketings\Pages;

use App\Filament\Resources\EmailMarketings\EmailMarketingResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEmailMarketing extends EditRecord
{
    protected static string $resource = EmailMarketingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
