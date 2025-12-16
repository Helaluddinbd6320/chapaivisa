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
                            ".($state < 0 ? '<span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">DUE</span>' : '').'
                        </div>';
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                // WhatsApp Column with Text Only
                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('Action')
                    ->getStateUsing(function ($record) {
                        $phone = $record->phone1 ?? null;
                        $balance = $record->calculated_balance ?? 0;
                        $name = $record->name ?? '';
                        $formattedBalance = number_format(abs($balance), 0);

                        if (! $phone) {
                            return '
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium bg-gray-100 text-gray-500">
                                No Phone
                            </span>';
                        }

                        // Create WhatsApp message
                        $message = $this->createBalanceReminderMessage($name, $formattedBalance);

                        // Generate WhatsApp URL
                        $whatsappUrl = $this->getWhatsAppUrl($phone, $message);

                        // Simple text-only button
                        return '
<a href="'.htmlspecialchars($whatsappUrl, ENT_QUOTES, 'UTF-8').'" 
   target="_blank"
   title="Send WhatsApp Reminder"
   class="inline-flex items-center gap-2 px-4 py-2
          text-sm font-semibold text-white
          bg-[#25D366] hover:bg-[#128C7E]
          rounded-full transition-all duration-200
          shadow-md hover:shadow-lg
          focus:outline-none focus:ring-2 focus:ring-green-400">

    <!-- WhatsApp SVG Icon (100% supported) -->
    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967
        -.273-.099-.471-.148-.67.15-.197.297-.767.966-.94
        1.164-.173.199-.347.223-.644.075-.297-.15-1.255
        -.463-2.39-1.475-.883-.788-1.48-1.761-1.653
        -2.059-.173-.297-.018-.458.13-.606.134-.133.298
        -.347.446-.52.149-.174.198-.298.298-.497.099
        -.198.05-.371-.025-.52-.075-.149-.67-1.612-.916
        -2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371
        -.01-.57-.01-.198 0-.52.074-.792.372-.272.297
        -1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213
        3.074.149.198 2.096 3.2 5.077 4.487.709.306
        1.262.489 1.694.625.712.227 1.36.195 1.871
        .118.571-.085 1.758-.719 2.006-1.413.248-.694
        .248-1.289.173-1.413-.074-.124-.272-.198-.57
        -.347z"/>
    </svg>

    <span>WhatsApp Remind</span>
</a>';
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
            ->paginated(false);
    }

    /**
     * Create WhatsApp message with proper format
     */
    private function createBalanceReminderMessage(string $name, string $formattedBalance): string
    {
        $currentDate = now()->format('d/m/Y');

        $message = "🏢 *Visa Office Chapai International*\n\n";
        $message .= "🔔 *BALANCE REMINDER NOTIFICATION*\n\n";
        $message .= "Dear *{$name}*,\n\n";
        $message .= "Your account has an outstanding balance:\n\n";
        $message .= "💰 *Amount Due:* -{$formattedBalance}৳\n";
        $message .= "⚠️ *Status:* Payment Required\n";
        $message .= "📅 *Date:* {$currentDate}\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💳 *PAYMENT OPTIONS:*\n";
        $message .= "• Cash payment at our office\n";
        $message .= "• Bank transfer\n";
        $message .= "• Mobile banking (bKash, Nagad, Rocket)\n\n";
        $message .= "🏛️ *OFFICE INFORMATION:*\n";
        $message .= "Visa Office Chapai International\n";
        $message .= "[Your Office Address]\n";
        $message .= "[Office Phone Number]\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "Please clear your dues at the earliest to avoid any inconvenience.\n\n";
        $message .= "Thank you for your cooperation.\n\n";
        $message .= "Best regards,\n";
        $message .= '*Visa Office Chapai International*';

        return $message;
    }

    /**
     * Generate WhatsApp URL with proper encoding
     */
    private function getWhatsAppUrl(string $phone, string $message): string
    {
        // Clean phone number
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);

        // Ensure UTF-8 encoding for emojis
        if (! mb_check_encoding($message, 'UTF-8')) {
            $message = mb_convert_encoding($message, 'UTF-8');
        }

        // Proper URL encoding
        $encodedMessage = rawurlencode($message);

        // Fix any encoding issues for special characters
        $encodedMessage = str_replace(
            ['%0A', '%20', '%2A', '%5F', '%7E'],
            ['%0A', '%20', '*', '_', '~'],
            $encodedMessage
        );

        return "https://wa.me/{$cleanPhone}?text={$encodedMessage}";
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
