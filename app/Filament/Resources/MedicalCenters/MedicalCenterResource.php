<?php

namespace App\Filament\Resources\MedicalCenters;

use App\Filament\Resources\MedicalCenters\Pages\CreateMedicalCenter;
use App\Filament\Resources\MedicalCenters\Pages\EditMedicalCenter;
use App\Filament\Resources\MedicalCenters\Pages\ListMedicalCenters;
use App\Filament\Resources\MedicalCenters\Schemas\MedicalCenterForm;
use App\Filament\Resources\MedicalCenters\Tables\MedicalCentersTable;
use App\Models\MedicalCenter;
use BackedEnum;
use UnitEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MedicalCenterResource extends Resource
{
    protected static ?string $model = MedicalCenter::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHeart;
    protected static string | UnitEnum | null $navigationGroup = 'Management';

    public static function form(Schema $schema): Schema
    {
        return MedicalCenterForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MedicalCentersTable::configure($table);
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
            'index' => ListMedicalCenters::route('/'),
            'create' => CreateMedicalCenter::route('/create'),
            'edit' => EditMedicalCenter::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
