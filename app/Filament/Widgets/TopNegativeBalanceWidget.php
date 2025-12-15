<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TopNegativeBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function table(Table $table): Table
    {
        $totalNegative = User::where('current_balance', '<', 0)->sum('current_balance');
        $negativeCount = User::where('current_balance', '<', 0)->count();

        return $table
            ->query(
                User::query()
                    ->where('current_balance', '<', 0)
                    ->orderBy('current_balance')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('current_balance')
                    ->label('Balance')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' ৳')
                    ->color('danger')
                    ->sortable()
                    ->description(fn ($record) => 
                        $record->accounts->count() . ' transactions'
                    ),
            ])
            ->headerActions([
                Tables\Actions\Action::make('stats')
                    ->label(fn () => "Negative: " . number_format(abs($totalNegative)) . ' ৳')
                    ->color('danger')
                    ->outlined()
                    ->disabled(),
                    
                Tables\Actions\Action::make('count')
                    ->label(fn () => "Users: {$negativeCount}")
                    ->color('warning')
                    ->outlined()
                    ->disabled(),
            ])
            ->heading('Top 10 Negative Balance Users')
            ->description('Most negative balances first')
            ->emptyStateHeading('No negative balance found')
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateDescription('All users have positive balance!')
            ->deferLoading()
            ->striped()
            ->paginated(false);
    }
}