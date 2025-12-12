<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TopNegativeBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 2; // প্রথমে দেখাবে

    protected int|string|array $columnSpan = 'full';

    /**
     * ✅ Only these roles can see the widget
     */
    public static function canView(): bool
    {
        $user = Auth::user();

        return $user && $user->hasAnyRole([
            'super_admin',
            'admin',
            'manager',
        ]);
    }

    protected function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $currentUser = Auth::user();

        $query = User::query()->with(['visas', 'accounts']);

        if ($currentUser->hasRole('user')) {
            $query->whereRaw('1=0'); // Empty for normal users
        }

        return $query;
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Name')
                ->searchable()
                ->url(fn ($record): ?string => $record->id
                        ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl([
                            'record' => $record->id,
                        ])
                        : null
                )
                ->color('primary')
                ->tooltip('View user profile'),

            TextColumn::make('current_balance')
                ->label('Balance')
                ->formatStateUsing(fn ($state) => number_format($state).' ৳')
                ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                ->disabledClick(),
        ];
    }

    protected function getTableQueryModifiers($query)
    {
        // Only top 10 negative balances
        return $query->get()
            ->filter(fn ($user) => $user->current_balance < 0)
            ->take(10);
    }
}
