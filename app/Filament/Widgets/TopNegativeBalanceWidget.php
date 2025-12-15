<?php

namespace App\Filament\Widgets;

use App\Models\User;
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

                Tables\Columns\TextColumn::make('phone1')
                    ->label('Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: 'N/A'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        return $state < 0 ? 
                            "<span style='color: #dc2626; font-weight: bold;'>-{$formattedBalance} ৳</span>" : 
                            "<span style='color: #059669; font-weight: bold;'>{$formattedBalance} ৳</span>";
                    })
                    ->html()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),

                // WhatsApp Button - Using IconColumn instead
                Tables\Columns\IconColumn::make('whatsapp')
                    ->label('Send')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return null;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.";
                        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->tooltip('Send WhatsApp reminder')
                    ->visible(fn ($record) => !empty($record->phone1) && $record->calculated_balance < 0),
                    
                // Alternative: Button Column
                Tables\Columns\TextColumn::make('whatsapp_btn')
                    ->label('Reminder')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return '-';
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.";
                        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                        
                        return "<a href='{$whatsappUrl}' target='_blank' style='display: inline-block; padding: 4px 12px; background: #25D366; color: white; border-radius: 4px; text-decoration: none; font-size: 12px;'>Send</a>";
                    })
                    ->html()
                    ->alignCenter(),
            ])
            ->heading('Top 10 Negative Balance Users')
            ->emptyStateHeading('No negative balance found')
            ->emptyStateDescription('All users have positive balance.')
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