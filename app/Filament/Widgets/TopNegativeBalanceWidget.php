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
                    ->selectRaw($this->getBalanceSubquery().' as calculated_balance')
                    ->having('calculated_balance', '<', 0)
                    ->orderBy('calculated_balance')
                    ->limit(10)
            )
            ->columns([
                // Name Column
                Tables\Columns\TextColumn::make('name')
                    ->label('👤 Name')
                    ->searchable()
                    ->url(fn ($record) => $record->id
                        ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl(['record' => $record->id])
                        : null)
                    ->color('primary')
                    ->weight('bold')
                    ->size('sm')
                    ->tooltip(fn ($record) => "View profile of {$record->name}"),

                // Phone Column
                Tables\Columns\TextColumn::make('phone1')
                    ->label('📱 Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: '-')
                    ->size('sm')
                    ->copyable()
                    ->tooltip('Click to copy phone number'),

                // Balance Column
                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('💰 Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $colorClass = $state < 0 ? 'text-red-600' : 'text-green-600';
                        $icon = $state < 0 ? '🔻' : '🔺';
                        $badge = $state < 0
                            ? '<span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full ml-2">DUE</span>'
                            : '';

                        return "<div class='flex items-center gap-2'>
                                    <span class='{$colorClass} font-bold text-sm'>{$icon} {$formattedBalance} ৳</span>
                                    {$badge}
                                </div>";
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                // WhatsApp Action Column
                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('Action')
                    ->getStateUsing(function ($record) {
                        $phone = $record->phone1 ?? null;
                        $balance = $record->calculated_balance ?? 0;
                        $name = $record->name ?? '';
                        $formattedBalance = number_format(abs($balance), 0);

                        if (!$phone) {
                            return '<span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-500">No Phone</span>';
                        }

                        $cleanPhone = $this->formatPhoneForWhatsApp($phone);
                        $message = $this->createWhatsAppMessage($name, $formattedBalance);
                        $encodedMessage = rawurlencode($message);

                        return <<<HTML
<div class="transition-transform duration-200 hover:scale-105">
    <a href="https://wa.me/{$cleanPhone}?text={$encodedMessage}"
       target="_blank"
       class="inline-flex items-center justify-center px-4 py-2 text-sm font-semibold text-white bg-[#25D366] rounded-lg shadow-sm hover:bg-[#075E54]">
        ⏰ WhatsApp Remind
    </a>
</div>
HTML;
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-40']),

            ])
            ->heading('📊 Top 10 Negative Balance Users')
            ->description('Users with outstanding dues • Click WhatsApp to send reminder')
            ->emptyStateHeading('🎉 All Clear!')
            ->emptyStateDescription('No negative balances found.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            // ->bordered()
            ->paginated(false);
    }

    private function createWhatsAppMessage(string $name, string $formattedBalance): string
    {
        $currentDate = now()->format('d/m/Y');
        return <<<MSG
🏢 *Visa Office Chapai International*

🔔 *BALANCE REMINDER NOTIFICATION*

Dear *{$name}*,

Your account has an outstanding balance:

💰 *Amount Due:* -{$formattedBalance}৳
⚠️ *Status:* Payment Required
📅 *Date:* {$currentDate}

━━━━━━━━━━━━━━━━━━━━
💳 *PAYMENT OPTIONS:*
• Cash payment at our office
• Bank transfer
• Mobile banking (bKash, Nagad, Rocket)

🏛️ *OFFICE INFORMATION:*
Visa Office Chapai International
[Your Office Address]
[Office Phone Number]

━━━━━━━━━━━━━━━━━━━━
Please clear your dues at the earliest to avoid any inconvenience.

Thank you for your cooperation.

Best regards,
*Visa Office Chapai International*
MSG;
    }

    private function formatPhoneForWhatsApp(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $clean = ltrim($clean, '+');

        if (strlen($clean) == 11 && substr($clean, 0, 2) == '01') return '880'.substr($clean, 1);
        if (strlen($clean) == 10 && substr($clean, 0, 1) == '1') return '880'.$clean;

        return $clean;
    }

    private function getBalanceSubquery(): string
    {
        return "
            COALESCE((SELECT SUM(amount) FROM accounts WHERE user_id = users.id AND transaction_type = 'deposit'), 0)
            - COALESCE((SELECT SUM(visa_cost) FROM visas WHERE user_id = users.id), 0)
            - COALESCE((SELECT SUM(amount) FROM accounts WHERE user_id = users.id AND transaction_type = 'withdrawal'), 0)
            - COALESCE((SELECT SUM(amount) FROM accounts WHERE user_id = users.id AND transaction_type = 'refund'), 0)
        ";
    }
}
