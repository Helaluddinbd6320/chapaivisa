<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Support\RawJs;

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
                    ->selectRaw($this->getBalanceSubquery() . ' as calculated_balance')
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
                    ->size('sm'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('📱 Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: '-')
                    ->size('sm')
                    ->copyable(),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('💰 Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $colorClass = $state < 0 ? 'text-red-600' : 'text-green-600';
                        $icon = $state < 0 ? '🔻' : '🔺';
                        
                        return "<div class='flex items-center gap-1'>
                            <span class='{$colorClass} font-bold'>{$icon} {$formattedBalance} ৳</span>
                            " . ($state < 0 ? '<span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">DUE</span>' : '') . "
                        </div>";
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                // Action Column ব্যবহার করুন
                Tables\Columns\IconColumn::make('whatsapp_action')
    ->label('📲 Send Reminder')
    ->icon('heroicon-o-chat-bubble-bottom-center-text')
    ->color('success')
    ->size('lg')
    ->tooltip('Send WhatsApp reminder')
    ->action(function ($record) {
        if (empty($record->phone1) || $record->calculated_balance >= 0) {
            return;
        }
        
        $phone = preg_replace('/[^0-9]/', '', $record->phone1);
        $name = $record->name;
        $balance = number_format(abs($record->calculated_balance), 0);
        
        $message = "🌟 *Visa Office Chapai International* 🌟

📋 *BALANCE REMINDER NOTIFICATION*

Dear *{$name}*,

Your account has an outstanding balance:

💰 *Amount Due:* -{$balance}৳
📊 *Status:* Payment Required
📅 *Date:* " . now()->format('d/m/Y') . "

━━━━━━━━━━━━━━━━━━━━
💳 *PAYMENT OPTIONS:*
• Cash payment at our office
• Bank transfer
• Mobile banking (bKash, Nagad, Rocket)

🏢 *OFFICE INFORMATION:*
Visa Office Chapai International
[Your Office Address]
[Office Phone Number]

━━━━━━━━━━━━━━━━━━━━
Please clear your dues at the earliest to avoid any inconvenience.

Thank you for your cooperation.

Best regards,
*Visa Office Chapai International*";
        
        $url = "https://wa.me/{$phone}?text=" . urlencode($message);
        
        $this->js(<<<JS
            window.open('{$url}', '_blank', 'noopener,noreferrer');
        JS);
    })
    ->visible(fn ($record) => !empty($record->phone1) && $record->calculated_balance < 0)
    ->extraAttributes(['class' => 'cursor-pointer hover:text-green-600']),
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