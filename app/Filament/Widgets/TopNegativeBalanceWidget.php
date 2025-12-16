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
                    ->label('User')
                    ->searchable()
                    ->url(fn ($record): ?string => $record->id
                                ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl([
                                    'record' => $record->id,
                                ])
                                : null
                    )
                    ->formatStateUsing(function ($state, $record) {
                        $initials = $this->getInitials($record->name);
                        return '<div class="flex items-center gap-3">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-sm">
                                <span class="text-xs font-bold text-white">'.$initials.'</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-semibold text-gray-900 dark:text-gray-100 text-sm">'.$state.'</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">ID: '.$record->id.'</span>
                            </div>
                        </div>';
                    })
                    ->html()
                    ->size('sm'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('Contact')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        if (!$state) {
                            return '<div class="flex items-center gap-2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="text-sm">No phone</span>
                            </div>';
                        }
                        
                        return '<div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span class="font-medium text-gray-900 dark:text-gray-100 text-sm">'.$state.'</span>
                            </div>
                            <button onclick="navigator.clipboard.writeText(\''.$state.'\')" class="text-xs text-primary-600 hover:text-primary-700 dark:text-primary-400 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Copy
                            </button>
                        </div>';
                    })
                    ->html()
                    ->size('sm'),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $isNegative = $state < 0;
                        
                        return '<div class="flex items-center justify-between">
                            <div class="flex flex-col">
                                <span class="text-lg font-bold '.($isNegative ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400').'">
                                    '.($isNegative ? '- ' : '+ ').$formattedBalance.' ৳
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    Outstanding '.($isNegative ? 'Due' : 'Credit').'
                                </span>
                            </div>
                            <div class="flex-shrink-0">
                                '.($isNegative ? '
                                <div class="px-3 py-1 rounded-full bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <span class="text-xs font-semibold text-red-700 dark:text-red-300 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        DUE
                                    </span>
                                </div>
                                ' : '
                                <div class="px-3 py-1 rounded-full bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                                    <span class="text-xs font-semibold text-green-700 dark:text-green-300 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        CLEAR
                                    </span>
                                </div>
                                ').'
                            </div>
                        </div>';
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('Actions')
                    ->getStateUsing(function ($record) {
                        $phone = $record->phone1 ?? null;
                        $balance = $record->calculated_balance ?? 0;
                        $name = $record->name ?? '';
                        $formattedBalance = number_format(abs($balance), 0);

                        if (! $phone) {
                            return '<button disabled class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 dark:bg-gray-800 rounded-lg cursor-not-allowed gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.893-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                                </svg>
                                No Phone
                            </button>';
                        }

                        $cleanPhone = $this->formatPhoneForWhatsApp($phone);
                        $message = $this->createWhatsAppMessage($name, $formattedBalance);
                        $encodedMessage = rawurlencode($message);

                        return '<div x-data="{ isHover: false, isSending: false }" class="space-y-2">
                            <a href="https://wa.me/'.$cleanPhone.'?text='.$encodedMessage.'"
                               target="_blank"
                               @mouseenter="isHover = true"
                               @mouseleave="isHover = false"
                               @click="isSending = true; setTimeout(() => isSending = false, 2000)"
                               :class="isHover ? \'bg-[#128C7E] shadow-lg transform -translate-y-0.5 border-[#128C7E]\' : \'bg-[#25D366] shadow-md border-[#25D366]\'"
                               class="inline-flex items-center justify-center w-full px-4 py-2.5 text-sm font-semibold text-white rounded-lg border transition-all duration-200 cursor-pointer gap-2">
                                <svg x-show="!isSending" class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.893-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                                </svg>
                                <svg x-show="isSending" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                <span x-text="isSending ? \'Sending...\' : \'Send Reminder\'"></span>
                            </a>
                            
                            <div class="flex gap-2">
                                <button onclick="navigator.clipboard.writeText(`'.$this->createWhatsAppMessage($name, $formattedBalance, true).'`)"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    Copy Text
                                </button>
                                
                                <button onclick="navigator.clipboard.writeText(`'.$cleanPhone.'`)"
                                        class="flex-1 inline-flex items-center justify-center px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition-colors gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z" />
                                    </svg>
                                    Copy Number
                                </button>
                            </div>
                        </div>';
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-48']),
            ])
            ->heading('⚠️ Outstanding Dues')
            ->description('Top 10 users with negative balances. Send payment reminders via WhatsApp.')
            ->emptyStateHeading('🎉 All Clear!')
            ->emptyStateDescription('No users with negative balances found.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->emptyStateActions([
                Tables\Actions\Action::make('refresh')
                    ->label('Refresh')
                    ->button()
                    ->icon('heroicon-o-arrow-path')
                    ->action(fn () => $this->refreshTableData()),
            ])
            ->striped()
            ->deferLoading()
            ->paginated(false)
            ->headerActions([
                Tables\Actions\Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->action(fn () => $this->exportData()),
            ])
            ->recordUrl(null)
            ->recordAction(null);
    }

    /**
     * Create WhatsApp message
     */
    private function createWhatsAppMessage(string $name, string $formattedBalance, bool $plainText = false): string
    {
        $currentDate = now()->format('d/m/Y');
        $time = now()->format('h:i A');

        if ($plainText) {
            $message = "🏢 Visa Office Chapai International\n\n";
            $message .= "🔔 BALANCE REMINDER NOTIFICATION\n\n";
            $message .= "Dear {$name},\n\n";
            $message .= "Your account has an outstanding balance:\n\n";
            $message .= "💰 Amount Due: -{$formattedBalance}৳\n";
            $message .= "⚠️ Status: Payment Required\n";
            $message .= "📅 Date: {$currentDate}\n";
            $message .= "⏰ Time: {$time}\n\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "💳 PAYMENT OPTIONS:\n";
            $message .= "• Cash payment at our office\n";
            $message .= "• Bank transfer\n";
            $message .= "• Mobile banking (bKash, Nagad, Rocket)\n\n";
            $message .= "🏛️ OFFICE INFORMATION:\n";
            $message .= "Visa Office Chapai International\n";
            $message .= "[Your Office Address]\n";
            $message .= "[Office Phone Number]\n\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "Please clear your dues at the earliest to avoid any inconvenience.\n\n";
            $message .= "Thank you for your cooperation.\n\n";
            $message .= "Best regards,\n";
            $message .= "Visa Office Chapai International";
        } else {
            $message = "🏢 *Visa Office Chapai International*\n\n";
            $message .= "🔔 *BALANCE REMINDER NOTIFICATION*\n\n";
            $message .= "Dear *{$name}*,\n\n";
            $message .= "Your account has an outstanding balance:\n\n";
            $message .= "💰 *Amount Due:* -{$formattedBalance}৳\n";
            $message .= "⚠️ *Status:* Payment Required\n";
            $message .= "📅 *Date:* {$currentDate}\n";
            $message .= "⏰ *Time:* {$time}\n\n";
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
        }

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

    /**
     * Get initials from name
     */
    private function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        $initials = '';
        
        if (count($words) >= 2) {
            $initials = strtoupper(substr($words[0], 0, 1) . substr(end($words), 0, 1));
        } else {
            $initials = strtoupper(substr($name, 0, 2));
        }
        
        return $initials;
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

    /**
     * Refresh table data
     */
    private function refreshTableData(): void
    {
        $this->refresh();
    }

    /**
     * Export data
     */
    private function exportData(): void
    {
        // Export functionality
    }
}