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
                Tables\Columns\TextColumn::make('custom_card')
                    ->label('')
                    ->getStateUsing(function ($record) {
                        $name = $record->name ?? '-';
                        $phone = $record->phone1 ?? '-';
                        $balance = $record->calculated_balance ?? 0;
                        $formattedBalance = number_format(abs($balance), 0);
                        $balanceIcon = $balance < 0 ? '🔻' : '🔺';
                        $balanceColor = $balance < 0 ? 'text-red-600' : 'text-green-600';
                        $badge = $balance < 0 ? '<span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full font-semibold">DUE</span>' : '';

                        // WhatsApp Button
                        $whatsappButton = '';
                        if ($phone !== '-') {
                            $cleanPhone = $this->formatPhoneForWhatsApp($phone);
                            $message = rawurlencode($this->createWhatsAppMessage($name, $formattedBalance));
                            $whatsappButton = <<<HTML
<a href="https://wa.me/{$cleanPhone}?text={$message}" target="_blank"
   class="inline-flex items-center justify-center px-3 py-1.5 bg-[#25D366] text-white font-semibold rounded-lg shadow-sm hover:bg-[#128C7E] hover:shadow-md transition-all duration-200 text-xs mt-2">
    ⏰ Remind
</a>
HTML;
                        }

                        return <<<HTML
<div class="bg-white shadow-md hover:shadow-lg transition-shadow duration-200 rounded-xl p-4 flex flex-col md:flex-row md:justify-between items-start md:items-center gap-3 border-l-4 border-red-500">
    <div class="flex flex-col">
        <span class="font-bold text-sm text-gray-800 hover:underline cursor-pointer">👤 {$name}</span>
        <span class="text-gray-600 text-xs mt-0.5">📱 {$phone}</span>
    </div>
    <div class="flex flex-col items-start md:items-end mt-2 md:mt-0">
        <span class="{$balanceColor} font-bold text-sm flex items-center gap-1">
            {$balanceIcon} {$formattedBalance} ৳ {$badge}
        </span>
        {$whatsappButton}
    </div>
</div>
HTML;
                    })
                    ->html()
                    ->extraAttributes(['class' => 'space-y-3']),
            ])
            ->heading('📊 Top 10 Negative Balance Users')
            ->description('Users with outstanding dues • Click WhatsApp to send reminder')
            ->emptyStateHeading('🎉 All Clear!')
            ->emptyStateDescription('No negative balances found.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped(false)
            ->paginated(false);
    }

    private function createWhatsAppMessage(string $name, string $formattedBalance): string
    {
        $currentDate = now()->format('d/m/Y');

        return "🏢 *Visa Office Chapai International*\n\n"
            ."🔔 *BALANCE REMINDER NOTIFICATION*\n\n"
            ."Dear *{$name}*,\n\n"
            ."Your account has an outstanding balance:\n\n"
            ."💰 *Amount Due:* -{$formattedBalance}৳\n"
            ."⚠️ *Status:* Payment Required\n"
            ."📅 *Date:* {$currentDate}\n\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            ."💳 *PAYMENT OPTIONS:*\n"
            ."• Cash payment at our office\n"
            ."• Bank transfer\n"
            ."• Mobile banking (bKash, Nagad, Rocket)\n\n"
            ."🏛️ *OFFICE INFORMATION:*\n"
            ."Visa Office Chapai International\n"
            ."[Your Office Address]\n"
            ."[Office Phone Number]\n\n"
            ."━━━━━━━━━━━━━━━━━━━━\n"
            ."Please clear your dues at the earliest to avoid any inconvenience.\n\n"
            ."Thank you for your cooperation.\n\n"
            ."Best regards,\n"
            ."*Visa Office Chapai International*";
    }

    private function formatPhoneForWhatsApp(string $phone): string
    {
        $clean = preg_replace('/[^0-9]/', '', $phone);
        $clean = ltrim($clean, '+');

        if (strlen($clean) == 11 && substr($clean, 0, 2) == '01') {
            return '880'.substr($clean, 1);
        }

        if (strlen($clean) == 10 && substr($clean, 0, 1) == '1') {
            return '880'.$clean;
        }

        return $clean;
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
