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
    protected static ?string $heading = '📊 Negative Balance Dashboard';
    protected static ?string $pollingInterval = '30s';

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
                // User Info Column
                Tables\Columns\Layout\Stack::make([
                    Tables\Columns\TextColumn::make('name')
                        ->label('Customer')
                        ->searchable()
                        ->url(fn ($record): ?string => $record->id
                                    ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl([
                                        'record' => $record->id,
                                    ])
                                    : null
                        )
                        ->color('gray')
                        ->weight('semibold')
                        ->size('lg')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('gray'),
                    
                    Tables\Columns\TextColumn::make('phone1')
                        ->label('Contact')
                        ->searchable()
                        ->formatStateUsing(function ($state) {
                            return $state 
                                ? "<div class='flex items-center gap-2'>
                                        <span class='text-gray-600'>📱</span>
                                        <span class='font-medium text-gray-900'>{$state}</span>
                                   </div>"
                                : "<span class='text-gray-400 text-sm'>No phone</span>";
                        })
                        ->html()
                        ->size('sm'),
                ])->space(1),

                // Balance Column with Visual Indicator
                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Outstanding Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $amount = abs($state);
                        
                        // Color intensity based on amount
                        $colorIntensity = match(true) {
                            $amount > 100000 => '600',
                            $amount > 50000 => '500',
                            $amount > 20000 => '400',
                            default => '300'
                        };
                        
                        // Visual indicator
                        $indicatorWidth = min(100, ($amount / 5000) * 10); // Scale indicator
                        
                        return "<div class='space-y-2'>
                            <div class='flex items-center justify-between'>
                                <span class='text-xs font-medium text-gray-500'>Due Amount</span>
                                <div class='flex items-center gap-2'>
                                    <span class='text-lg font-bold text-red-{$colorIntensity}'>
                                        ৳{$formattedBalance}
                                    </span>
                                    <span class='text-xs font-semibold px-2 py-0.5 rounded-full bg-red-100 text-red-700'>
                                        DUE
                                    </span>
                                </div>
                            </div>
                            <div class='w-full bg-gray-200 rounded-full h-2 overflow-hidden'>
                                <div class='bg-gradient-to-r from-red-{$colorIntensity} to-red-400 h-full rounded-full transition-all duration-500'
                                     style='width: {$indicatorWidth}%'></div>
                            </div>
                        </div>";
                    })
                    ->html()
                    ->alignCenter(),

                // Action Column with Enhanced WhatsApp
                Tables\Columns\TextColumn::make('actions')
                    ->label('Actions')
                    ->formatStateUsing(function ($record) {
                        $phone = $record->phone1 ?? null;
                        $balance = $record->calculated_balance ?? 0;
                        $name = $record->name ?? '';
                        
                        if (!$phone) {
                            return '
                            <div class="flex flex-col items-center justify-center h-full p-2">
                                <div class="p-3 bg-gray-100 rounded-full">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="mt-2 text-xs text-gray-500">No Phone</span>
                            </div>';
                        }

                        $cleanPhone = $this->formatPhoneForWhatsApp($phone);
                        $formattedBalance = number_format(abs($balance), 0);
                        $message = $this->createWhatsAppMessage($name, $formattedBalance);
                        $encodedMessage = rawurlencode($message);
                        
                        // Multiple action buttons
                        return '
                        <div class="flex flex-col gap-2" x-data="{ activeTab: \'whatsapp\' }">
                            <!-- Action Buttons -->
                            <div class="flex gap-1 bg-gray-50 p-1 rounded-lg">
                                <button @click="activeTab = \'whatsapp\'" 
                                        :class="activeTab === \'whatsapp\' ? \'bg-white shadow-sm text-green-600\' : \'text-gray-500\'"
                                        class="flex-1 px-3 py-2 text-xs font-medium rounded-md transition-all">
                                    WhatsApp
                                </button>
                                <button @click="activeTab = \'sms\'" 
                                        :class="activeTab === \'sms\' ? \'bg-white shadow-sm text-blue-600\' : \'text-gray-500\'"
                                        class="flex-1 px-3 py-2 text-xs font-medium rounded-md transition-all">
                                    SMS
                                </button>
                                <button @click="activeTab = \'call\'" 
                                        :class="activeTab === \'call\' ? \'bg-white shadow-sm text-purple-600\' : \'text-gray-500\'"
                                        class="flex-1 px-3 py-2 text-xs font-medium rounded-md transition-all">
                                    Call
                                </button>
                            </div>
                            
                            <!-- Action Content -->
                            <div x-show="activeTab === \'whatsapp\'" class="animate-fade-in">
                                <a href="https://wa.me/'.$cleanPhone.'?text='.$encodedMessage.'"
                                   target="_blank"
                                   class="group w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg shadow-sm hover:shadow-md hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.304-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.893-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                                    </svg>
                                    <span>Send Reminder</span>
                                </a>
                                <p class="mt-1 text-xs text-gray-500 text-center">Click to send WhatsApp message</p>
                            </div>
                            
                            <!-- SMS Tab Content -->
                            <div x-show="activeTab === \'sms\'" class="animate-fade-in">
                                <a href="sms:'.$phone.'?body='.rawurlencode("Dear {$name}, your outstanding balance is ৳{$formattedBalance}. Please clear your dues.").'"
                                   class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                    Send SMS
                                </a>
                            </div>
                            
                            <!-- Call Tab Content -->
                            <div x-show="activeTab === \'call\'" class="animate-fade-in">
                                <a href="tel:'.$phone.'"
                                   class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-purple-500 to-purple-600 text-white font-semibold rounded-lg shadow-sm hover:shadow-md transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    Make Call
                                </a>
                            </div>
                        </div>';
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-64']),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_negative')
                    ->label('Export Report')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->outlined()
                    ->action(fn () => $this->exportReport()),
            ])
            ->heading(function () {
                $negativeCount = User::query()
                    ->selectRaw($this->getBalanceSubquery().' as calculated_balance')
                    ->having('calculated_balance', '<', 0)
                    ->count();
                
                return "
                <div class='flex items-center justify-between'>
                    <div>
                        <h2 class='text-xl font-bold text-gray-900'>📊 Negative Balance Dashboard</h2>
                        <p class='text-sm text-gray-600 mt-1'>
                            <span class='font-semibold text-red-600'>{$negativeCount}</span> customers with outstanding dues
                        </p>
                    </div>
                    <div class='flex items-center gap-2'>
                        <span class='px-3 py-1 text-xs font-medium rounded-full bg-red-50 text-red-700 border border-red-200'>
                            ⚠️ Requires Attention
                        </span>
                        <span class='px-3 py-1 text-xs font-medium rounded-full bg-amber-50 text-amber-700 border border-amber-200'>
                            Total Due: ৳" . number_format($this->getTotalNegativeBalance(), 0) . "
                        </span>
                    </div>
                </div>";
            })
            ->description('Monitor and manage customers with outstanding balances')
            ->emptyState(function () {
                return "
                <div class='py-12 text-center'>
                    <div class='mx-auto w-16 h-16 mb-4 flex items-center justify-center rounded-full bg-green-100'>
                        <svg class='w-8 h-8 text-green-600' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
                            <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'/>
                        </svg>
                    </div>
                    <h3 class='text-lg font-semibold text-gray-900 mb-1'>All Clear! 🎉</h3>
                    <p class='text-gray-600 max-w-sm mx-auto'>
                        No negative balances found. All customers are up to date with their payments.
                    </p>
                </div>";
            })
            ->striped()
            ->paginated(false)
            ->extraAttributes([
                'class' => 'rounded-xl border border-gray-200 shadow-sm overflow-hidden'
            ])
            ->contentGrid([
                'md' => 1,
                'xl' => 1,
            ]);
    }

    /**
     * Create WhatsApp message
     */
    private function createWhatsAppMessage(string $name, string $formattedBalance): string
    {
        $currentDate = now()->format('d F, Y');
        $dueDate = now()->addDays(7)->format('d F, Y');

        $message = "🏢 *Visa Office Chapai International*\n\n";
        $message .= "🔔 *BALANCE REMINDER NOTIFICATION*\n\n";
        $message .= "Dear *{$name}*,\n\n";
        $message .= "We hope this message finds you well.\n\n";
        $message .= "📋 *ACCOUNT SUMMARY*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "• Outstanding Balance: *৳{$formattedBalance}*\n";
        $message .= "• Status: *Payment Required*\n";
        $message .= "• Due Date: *{$dueDate}*\n";
        $message .= "• Invoice Date: {$currentDate}\n\n";
        $message .= "💳 *PAYMENT METHODS*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "🏦 Bank Transfer\n";
        $message .= "📱 Mobile Banking (bKash/Nagad/Rocket)\n";
        $message .= "💵 Cash Payment\n\n";
        $message .= "📍 *OFFICE DETAILS*\n";
        $message .= "━━━━━━━━━━━━━━━━━━━━\n";
        $message .= "Visa Office Chapai International\n";
        $message .= "📞 [Office Contact]\n";
        $message .= "🕒 9:00 AM - 6:00 PM (Sat-Thu)\n\n";
        $message .= "Please clear your dues to avoid any service interruptions.\n\n";
        $message .= "Thank you for your cooperation!\n\n";
        $message .= "Best regards,\n";
        $message .= "*Visa Office Chapai International*";

        return $message;
    }

    /**
     * Format phone number for WhatsApp
     */
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

    /**
     * Get total negative balance
     */
    private function getTotalNegativeBalance(): float
    {
        return User::query()
            ->selectRaw('SUM((' . $this->getBalanceSubquery() . ')) as total_negative')
            ->havingRaw($this->getBalanceSubquery() . ' < 0')
            ->value('total_negative') ?? 0;
    }

    /**
     * Export report action
     */
    private function exportReport(): void
    {
        // Implement export functionality here
        // You can use Laravel Excel or simple CSV export
    }
}