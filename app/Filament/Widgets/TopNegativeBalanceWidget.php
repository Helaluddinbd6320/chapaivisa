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
                    ->tooltip('View user profile')
                    ->weight('bold')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: 'N/A')
                    ->icon('heroicon-o-phone')
                    ->iconColor('gray')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('gray')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        return $state < 0 ? 
                            "<span style='color: #dc2626; font-weight: bold;'>-{$formattedBalance} ৳</span>" : 
                            "<span style='color: #059669; font-weight: bold;'>{$formattedBalance} ৳</span>";
                    })
                    ->html()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->icon(fn ($state) => $state < 0 ? 'heroicon-o-arrow-trending-down' : 'heroicon-o-arrow-trending-up')
                    ->iconColor(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                // WhatsApp Button using IconColumn
                Tables\Columns\IconColumn::make('whatsapp_send')
                    ->label('Send WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->size('md')
                    ->url(function ($record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return null;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        $message = "*Visa Office Chapai International*\n";
                        $message .= "*Balance Reminder*\n\n";
                        $message .= "Dear {$record->name},\n\n";
                        $message .= "Your current balance: *-{$formattedBalance}৳*\n";
                        $message .= "Status: *Payment Due*\n\n";
                        $message .= "Please clear your outstanding balance at your earliest convenience.\n\n";
                        $message .= "Thank you,\n";
                        $message .= "Visa Office Chapai International";
                        
                        return "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->tooltip('Send WhatsApp reminder')
                    ->visible(fn ($record) => !empty($record->phone1) && $record->calculated_balance < 0)
                    ->alignCenter()
                    ->extraAttributes(['class' => 'cursor-pointer']),

                // Alternative: Text button column
                Tables\Columns\TextColumn::make('whatsapp_reminder')
                    ->label('Reminder')
                    ->formatStateUsing(function ($record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return '-';
                        }
                        
                        return 'Send Message';
                    })
                    ->color('success')
                    ->weight('medium')
                    ->size('sm')
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
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->iconPosition('after')
                    ->alignCenter()
                    ->tooltip('Click to send WhatsApp message'),
            ])
            ->heading('📋 Top 10 Negative Balance Users')
            ->description('Users with outstanding dues - Send WhatsApp reminders')
            ->emptyStateHeading('🎉 No negative balance found!')
            ->emptyStateDescription('All users have positive balance or cleared dues.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated(false)
            ->actions([
                // Row action for WhatsApp
                Tables\Actions\Action::make('send_whatsapp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->label('WhatsApp')
                    ->url(function ($record) {
                        if (empty($record->phone1)) {
                            return null;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = $record->calculated_balance < 0 ? 
                            number_format(abs($record->calculated_balance), 0) : '0';
                        
                        $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.";
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