<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Actions\Action as BaseAction;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class TopNegativeBalanceWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static function canView(): bool
    {
        return Auth::user()?->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->with(['accounts', 'visas'])
                    ->select(['users.*'])
                    ->selectSub($this->getBalanceSubquery(), 'calculated_balance')
                    ->having('calculated_balance', '<', 0)
                    ->orderBy('calculated_balance')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('👤 Name')
                    ->searchable()
                    ->url(fn ($record): ?string => $record->id
                                ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl([
                                    'record' => $record->id,
                                ])
                                : null
                    )
                    ->color('primary')
                    ->weight('bold')
                    ->size('sm')
                    ->icon('heroicon-o-user-circle'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('📱 Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: '-')
                    ->size('sm')
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('💰 Balance')
                    ->formatStateUsing(fn ($state) => number_format($state, 0) . ' ৳')
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->icon(fn ($state) => $state < 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up')
                    ->size('sm')
                    ->weight('bold')
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),

                // WhatsApp Button - Final Design
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('📲 Action')
                    ->formatStateUsing(function ($record) {
                        if (empty($record->phone1)) {
                            return '<span style="color: #9ca3af;">No Phone</span>';
                        }
                        
                        if ($record->calculated_balance >= 0) {
                            return '<span style="color: #10b981;">✅ Paid</span>';
                        }
                        
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        return '<span style="color: #059669; font-weight: 600;">📱 Send (-' . $formattedBalance . '৳)</span>';
                    })
                    ->html()
                    ->url(function ($record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return null;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        $message = "*Visa Office Chapai International*\n";
                        $message .= "*Balance Reminder*\n\n";
                        $message .= "Dear {$record->name},\n\n";
                        $message .= "Current Balance: *-{$formattedBalance}৳*\n";
                        $message .= "Status: Payment Due\n\n";
                        $message .= "Please clear your outstanding balance.\n";
                        $message .= "Thank you.";
                        
                        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->iconColor('success')
                    ->alignCenter()
                    ->size('sm')
                    ->tooltip('Send WhatsApp reminder')
                    ->extraAttributes([
                        'class' => 'px-4 py-2 rounded-lg hover:shadow-sm transition-all',
                        'style' => 'background-color: #f0fdf4; border: 1px solid #bbf7d0; min-width: 140px;'
                    ]),
            ])
            ->heading('📊 Top 10 Negative Balance Users')
            ->description('Users with outstanding dues • Click WhatsApp to send reminder')
            ->emptyStateHeading('🎉 All Clear!')
            ->emptyStateDescription('No negative balances found.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated(false);
    }

    private function getBalanceSubquery(): string
    {
        return "
            COALESCE((
                SELECT SUM(amount) 
                FROM accounts 
                WHERE user_id = users.id 
                AND transaction_type = 'deposit'
            ), 0) 
            - COALESCE((
                SELECT SUM(visa_cost) 
                FROM visas 
                WHERE user_id = users.id
            ), 0)
            - COALESCE((
                SELECT SUM(amount) 
                FROM accounts 
                WHERE user_id = users.id 
                AND transaction_type = 'withdrawal'
            ), 0)
            - COALESCE((
                SELECT SUM(amount) 
                FROM accounts 
                WHERE user_id = users.id 
                AND transaction_type = 'refund'
            ), 0)
        ";
    }
}