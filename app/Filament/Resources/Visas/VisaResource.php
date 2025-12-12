<?php

namespace App\Filament\Resources\Visas;

use App\Filament\Resources\Visas\Pages\CreateVisa;
use App\Filament\Resources\Visas\Pages\EditVisa;
use App\Filament\Resources\Visas\Pages\ListVisas;
use App\Filament\Resources\Visas\Pages\ViewVisa;
use App\Filament\Resources\Visas\Schemas\VisaForm;
use App\Filament\Resources\Visas\Tables\VisasTable;
use App\Models\Visa;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use UnitEnum;

class VisaResource extends Resource
{
    protected static ?string $model = Visa::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedGlobeAsiaAustralia;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return VisaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VisasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVisas::route('/'),
            'create' => CreateVisa::route('/create'),
            'edit' => EditVisa::route('/{record}/edit'),
            'view' => ViewVisa::route('/{record}'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /* -----------------------------
       Global Search Configuration
       ----------------------------- */

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'passport',
        ];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        $details = [
            // 'Name' => $record->name,
            'Passport' => $record->passport ?? 'N/A',
        ];

        if ($record->phone_1) {
            $details['Phone'] = $record->phone_1;
        }

        if ($record->visa_number) {
            $details['Visa No'] = $record->visa_number;
        }

        $user = Auth::user();

        // ✅ Only admin / manager / super_admin can see agent info
        if (
            $user
            && (
                $user->hasRole('super_admin')
                || $user->hasRole('admin')
                || $user->hasRole('manager')
            )
        ) {
            if ($record->user) {
                $details['Agent'] = $record->user->name;
            } elseif ($record->user_id) {
                $details['User ID'] = $record->user_id;
            }
        }

        return $details;
    }

    public static function getGlobalSearchResultUrl($record): string
    {
        return static::getUrl('view', ['record' => $record]);
    }

    public static function getGlobalSearchResultTitle($record): string|Htmlable
    {
        return $record->name;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        $user = Auth::user();

        $query = parent::getGlobalSearchEloquentQuery()
            ->select([
                'id',
                'name',
                'passport',
                'user_id',
                'phone_1',
                'visa_number',
                'created_at',
            ])
            ->with(['user:id,name'])
            ->latest();

        // ✅ Normal user হলে শুধু নিজের ডাটা
        if (
            $user
            && ! $user->hasRole('super_admin')
            && ! $user->hasRole('admin')
            && ! $user->hasRole('manager')
        ) {
            $query->where('user_id', $user->id);
        }

        return $query;
    }

    public static function canGloballySearch(): bool
    {
        return true;
    }

    public static function getGlobalSearchResultsLimit(): int
    {
        return 10;
    }

    public static function getGlobalSearchGroupLabel(): string
    {
        return 'Visas';
    }
}
