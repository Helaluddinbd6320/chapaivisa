<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->size(40)
                    ->disk('public')
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?name='.urlencode($record->name).
                               '&bold=true';
                    })

                    ->disabledClick()
                    ->url(fn ($record) => $record->photo ? Storage::url($record->photo) : null)
                    ->disabledClick(fn ($record) => ! $record->photo) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),

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
                            <span class='{$colorClass} font-bold'>{$icon} -{$formattedBalance} ৳</span>
                            ".($state < 0 ? '<span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">DUE</span>' : '').'
                        </div>';
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                // WhatsApp Column - Alpine.js Version
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

                        // Format phone for WhatsApp
                        $cleanPhone = $this->formatPhoneForWhatsApp($phone);

                        // Create button with Alpine.js
                        $message = $this->createWhatsAppMessage($name, $formattedBalance);
                        $encodedMessage = rawurlencode($message);

                        return '
                        <div x-data="{ isHover: false }">
                            <a href="https://wa.me/'.$cleanPhone.'?text='.$encodedMessage.'"
                               target="_blank"
                               @mouseenter="isHover = true"
                               @mouseleave="isHover = false"
                               :class="isHover ? \'bg-[#128C7E] shadow-md transform -translate-y-0.5\' : \'bg-[#25D366] shadow-sm\'"
                               class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-all duration-200 cursor-pointer">
                                ⏰ WhatsApp Remind
                            </a>
                        </div>';
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
     * Create WhatsApp message
     */
    private function createWhatsAppMessage(string $name, string $formattedBalance): string
    {
        $currentDate = now()->format('d/m/Y');
        
        // Get settings data
        $settings = app('settings');
        $appName = $settings->get('app_name', 'Visa Office Chapai International');
        $officeAddress = $settings->get('office_address', '[Your Office Address]');
        $officePhone = $settings->get('office_phone', '[Office Phone Number]');
        $officePhone2 = $settings->get('office_phone2');
        $whatsappNumber = $settings->get('whatsapp_number');
        $currencySymbol = $settings->get('currency_symbol', '৳');

        $message = "🏢 *{$appName}*\n\n";
        $message .= "🔔 *BALANCE REMINDER NOTIFICATION*\n\n";
        $message .= "Dear *{$name}*,\n\n";
        $message .= "Your account has an outstanding balance:\n\n";
        $message .= "💰 *Amount Due:* -{$formattedBalance}{$currencySymbol}\n";
        $message .= "⚠️ *Status:* Payment Required\n";
        $message .= "📅 *Date:* {$currentDate}\n\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "💳 *PAYMENT OPTIONS:*\n";
        $message .= "• Cash payment at our office\n";
        $message .= "• Bank transfer\n";
        $message .= "• Mobile banking (bKash, Nagad, Rocket)\n\n";
        $message .= "🏛️ *OFFICE INFORMATION:*\n";
        $message .= "{$appName}\n";
        $message .= "{$officeAddress}\n";
        $message .= "Phone: {$officePhone}\n";
        
        if ($officePhone2) {
            $message .= "Alt. Phone: {$officePhone2}\n";
        }
        
        if ($whatsappNumber) {
            $message .= "WhatsApp: {$whatsappNumber}\n";
        }
        
        $message .= "\n━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "Please clear your dues at the earliest to avoid any inconvenience.\n\n";
        $message .= "Thank you for your cooperation.\n\n";
        $message .= "Best regards,\n";
        $message .= "*{$appName}*";

        return $message;
    }

    /**
     * Format phone number for WhatsApp
     */
    private function formatPhoneForWhatsApp(string $phone): string
    {
        // Remove all non-numeric characters
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading plus
        $clean = ltrim($clean, '+');

        // For Bangladeshi numbers
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