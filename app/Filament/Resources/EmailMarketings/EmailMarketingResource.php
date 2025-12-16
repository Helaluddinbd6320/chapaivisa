<?php

namespace App\Filament\Resources\EmailMarketings;

use App\Filament\Resources\EmailMarketings\Pages\CreateEmailMarketing;
use App\Filament\Resources\EmailMarketings\Pages\EditEmailMarketing;
use App\Filament\Resources\EmailMarketings\Pages\ListEmailMarketings;
use App\Filament\Resources\EmailMarketings\Schemas\EmailMarketingForm;
use App\Filament\Resources\EmailMarketings\Tables\EmailMarketingsTable;
use App\Models\EmailMarketing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailMarketingResource extends Resource
{
    protected static ?string $model = EmailMarketing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return EmailMarketingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmailMarketingsTable::configure($table);
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
            'index' => ListEmailMarketings::route('/'),
            'create' => CreateEmailMarketing::route('/create'),
            'edit' => EditEmailMarketing::route('/{record}/edit'),
        ];
    }
}
