<?php

namespace App\Filament\Resources\SMSCampaigns;

use App\Filament\Resources\SMSCampaigns\Pages\CreateSMSCampaign;
use App\Filament\Resources\SMSCampaigns\Pages\EditSMSCampaign;
use App\Filament\Resources\SMSCampaigns\Pages\ListSMSCampaigns;
use App\Filament\Resources\SMSCampaigns\Schemas\SMSCampaignForm;
use App\Filament\Resources\SMSCampaigns\Tables\SMSCampaignsTable;
use App\Models\SMSCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SMSCampaignResource extends Resource
{
    protected static ?string $model = SMSCampaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return SMSCampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SMSCampaignsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSMSCampaigns::route('/'),
            'create' => CreateSMSCampaign::route('/create'),
            'edit' => EditSMSCampaign::route('/{record}/edit'),
        ];
    }
}
