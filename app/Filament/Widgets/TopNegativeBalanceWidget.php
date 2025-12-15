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
                    ->description(fn ($record) => 'ID: ' . $record->id)
                    ->tooltip('View user profile'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('📱 Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: 'N/A')
                    ->icon('heroicon-o-phone')
                    ->iconColor('blue')
                    ->size('sm')
                    ->description(fn ($record) => $record->phone2 ? 'Secondary: ' . $record->phone2 : null)
                    ->tooltip('Primary phone number'),

                Tables\Columns\TextColumn::make('email')
                    ->label('📧 Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('gray')
                    ->size('sm')
                    ->copyable()
                    ->tooltip('Click to copy email'),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('💰 Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $isNegative = $state < 0;
                        
                        $badgeColor = $isNegative ? 'danger' : 'success';
                        $icon = $isNegative ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up';
                        
                        return $isNegative ? 
                            "⚠️ -{$formattedBalance} ৳" : 
                            "✅ {$formattedBalance} ৳";
                    })
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->badge(fn ($state) => $state < 0)
                    ->badgeColor(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->icon(fn ($state) => $state < 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                    ->iconColor(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm')
                    ->weight('bold')
                    ->tooltip(fn ($state) => $state < 0 ? 'Negative balance - Payment due' : 'Positive balance'),

                // WhatsApp Button with beautiful design
                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('📲 Send Reminder')
                    ->formatStateUsing(function ($record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return '✅ Cleared';
                        }
                        
                        return 'Send Message';
                    })
                    ->color('success')
                    ->weight('semibold')
                    ->size('sm')
                    ->url(function ($record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return null;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        // Professional WhatsApp message
                        $message = "🔔 *Balance Reminder*\n";
                        $message .= "————————————\n";
                        $message .= "👤 *Name:* {$record->name}\n";
                        $message .= "💰 *Balance Due:* -{$formattedBalance}৳\n";
                        $message .= "📞 *Phone:* {$record->phone1}\n";
                        $message .= "————————————\n";
                        $message .= "Please clear your outstanding balance at your earliest convenience.\n\n";
                        $message .= "💳 *Payment Methods:*\n";
                        $message .= "• Cash at Office\n";
                        $message .= "• Bank Transfer\n";
                        $message .= "• Mobile Banking\n\n";
                        $message .= "Thank you,\n";
                        $message .= "*Visa Office Chapai International*";
                        
                        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->iconPosition('before')
                    ->iconColor('success')
                    ->alignCenter()
                    ->badge()
                    ->badgeColor('success')
                    ->tooltip('Click to send WhatsApp reminder')
                    ->extraAttributes(['class' => 'hover:bg-green-50 hover:shadow-sm transition-all duration-200 px-3 py-2 rounded-lg']),
            ])
            ->heading('📊 Top 10 Negative Balance Users')
            ->description('🔄 Users with outstanding payment dues - Send automated reminders')
            ->emptyStateHeading('🎉 Congratulations!')
            ->emptyStateDescription('All users have cleared their balances. No dues found.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateIconColor('success')
            ->striped()
            ->deferLoading()
            ->paginated(false)
            ->actions([
                Tables\Actions\Action::make('view_profile')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->label('Profile')
                    ->url(fn ($record): ?string => $record->id
                        ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl([
                            'record' => $record->id,
                        ])
                        : null
                    )
                    ->tooltip('View user profile'),
                    
                Tables\Actions\Action::make('send_sms')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->label('WhatsApp')
                    ->url(function ($record) {
                        if (empty($record->phone1)) {
                            return null;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due.";
                        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => !empty($record->phone1))
                    ->tooltip('Send WhatsApp message'),
            ]);
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