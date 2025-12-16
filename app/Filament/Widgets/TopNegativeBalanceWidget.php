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
                // Customer Info Column
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
                        ->color('gray-900')
                        ->weight('bold')
                        ->size('base')
                        ->icon('heroicon-o-user-circle')
                        ->iconColor('primary'),
                    
                    Tables\Columns\TextColumn::make('phone1')
                        ->label('Contact')
                        ->searchable()
                        ->formatStateUsing(function ($state) {
                            return $state 
                                ? "<div class='flex items-center gap-2'>
                                        <span class='text-blue-500'>📱</span>
                                        <span class='font-medium text-gray-700'>{$state}</span>
                                   </div>"
                                : "<span class='text-gray-400 text-sm flex items-center gap-1'><span>📵</span> No phone</span>";
                        })
                        ->html()
                        ->size('sm'),
                ])->space(1),

                // Balance Column
                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance Status')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $amount = abs($state);
                        
                        // Color based on amount
                        $colorClass = match(true) {
                            $amount > 100000 => 'bg-red-100 text-red-800 border-red-200',
                            $amount > 50000 => 'bg-orange-100 text-orange-800 border-orange-200',
                            $amount > 20000 => 'bg-amber-100 text-amber-800 border-amber-200',
                            default => 'bg-yellow-100 text-yellow-800 border-yellow-200'
                        };
                        
                        // Icon based on amount
                        $icon = match(true) {
                            $amount > 100000 => '🔥',
                            $amount > 50000 => '⚠️',
                            $amount > 20000 => '📉',
                            default => '📌'
                        };
                        
                        return "
                        <div class='flex flex-col gap-2'>
                            <div class='flex items-center justify-between'>
                                <span class='text-xs font-medium text-gray-500'>Outstanding</span>
                                <span class='text-xs font-semibold px-2 py-0.5 rounded-full {$colorClass} border'>
                                    {$icon} Due
                                </span>
                            </div>
                            <div class='flex items-center justify-between'>
                                <span class='text-2xl font-bold text-red-600'>৳{$formattedBalance}</span>
                                <div class='flex items-center'>
                                    " . str_repeat('<span class="text-red-400 text-xs">●</span>', min(5, ceil($amount / 20000))) . "
                                </div>
                            </div>
                        </div>";
                    })
                    ->html()
                    ->alignCenter()
                    ->sortable(),

                // Action Column
                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('Quick Actions')
                    ->getStateUsing(function ($record) {
                        $phone = $record->phone1 ?? null;
                        $balance = $record->calculated_balance ?? 0;
                        $name = $record->name ?? '';
                        $formattedBalance = number_format(abs($balance), 0);

                        if (!$phone) {
                            return '
                            <div class="flex flex-col items-center justify-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="p-2 bg-gray-100 rounded-full mb-2">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                                <span class="text-xs text-gray-500 font-medium">No Contact</span>
                                <span class="text-xs text-gray-400 mt-1">Add phone to send reminders</span>
                            </div>';
                        }

                        $cleanPhone = $this->formatPhoneForWhatsApp($phone);
                        $message = $this->createWhatsAppMessage($name, $formattedBalance);
                        $encodedMessage = rawurlencode($message);
                        
                        return '
                        <div class="space-y-3">
                            <!-- Main WhatsApp Button -->
                            <a href="https://wa.me/'.$cleanPhone.'?text='.$encodedMessage.'"
                               target="_blank"
                               class="group relative w-full flex items-center justify-center gap-3 px-4 py-3 bg-gradient-to-r from-green-500 via-green-500 to-green-600 text-white font-bold rounded-xl shadow-lg hover:shadow-xl hover:shadow-green-200 transition-all duration-300 transform hover:-translate-y-1 active:scale-95">
                                <span class="absolute inset-0 bg-gradient-to-r from-green-600 to-green-700 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>
                                <span class="relative flex items-center gap-2">
                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.76.982.998-3.675-.236-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.9 6.994c-.004 5.45-4.438 9.88-9.888 9.88m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.333.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.304-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.333 11.893-11.893 0-3.18-1.24-6.162-3.495-8.411"/>
                                    </svg>
                                    <span class="text-sm">Send WhatsApp Reminder</span>
                                </span>
                                <span class="relative ml-auto animate-pulse">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                </span>
                            </a>
                            
                            <!-- Quick Action Row -->
                            <div class="flex gap-2">
                                <!-- SMS Button -->
                                <a href="sms:'.$phone.'?body='.rawurlencode("Dear {$name}, your outstanding balance is ৳{$formattedBalance}. Please clear your dues at your earliest convenience.").'"
                                   class="flex-1 flex flex-col items-center justify-center p-2 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-lg transition-colors group/sms">
                                    <div class="p-1.5 bg-blue-100 rounded-lg mb-1 group-hover/sms:bg-blue-200 transition-colors">
                                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-blue-700">SMS</span>
                                </a>
                                
                                <!-- Call Button -->
                                <a href="tel:'.$phone.'"
                                   class="flex-1 flex flex-col items-center justify-center p-2 bg-purple-50 hover:bg-purple-100 border border-purple-200 rounded-lg transition-colors group/call">
                                    <div class="p-1.5 bg-purple-100 rounded-lg mb-1 group-hover/call:bg-purple-200 transition-colors">
                                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-purple-700">Call</span>
                                </a>
                                
                                <!-- Copy Button -->
                                <button onclick="navigator.clipboard.writeText(\''.$phone.'\'); this.innerHTML=\'<div class=\\\'p-1.5 bg-green-100 rounded-lg mb-1\\\'><svg class=\\\'w-4 h-4 text-green-600\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M5 13l4 4L19 7\\\'></path></svg></div><span class=\\\'text-xs font-medium text-green-700\\\'>Copied!</span>\'; setTimeout(()=>{this.innerHTML=\'<div class=\\\'p-1.5 bg-gray-100 rounded-lg mb-1\\\'><svg class=\\\'w-4 h-4 text-gray-600\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\\\'></path></svg></div><span class=\\\'text-xs font-medium text-gray-700\\\'>Copy</span>\'}, 2000)"
                                        class="flex-1 flex flex-col items-center justify-center p-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-lg transition-colors group/copy">
                                    <div class="p-1.5 bg-gray-100 rounded-lg mb-1 group-hover/copy:bg-gray-200 transition-colors">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </div>
                                    <span class="text-xs font-medium text-gray-700">Copy</span>
                                </button>
                            </div>
                        </div>';
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-64']),
            ])
            ->heading(
                '<div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <span class="p-2 bg-red-50 rounded-lg">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </span>
                            Top Negative Balances
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">Customers with outstanding dues requiring attention</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-gradient-to-r from-red-50 to-red-100 text-red-700 border border-red-200">
                            🔴 Immediate Attention
                        </span>
                    </div>
                </div>'
            )
            ->description(null)
            ->emptyState(function () {
                return '
                <div class="py-16 text-center">
                    <div class="mx-auto w-20 h-20 mb-4 flex items-center justify-center rounded-full bg-green-50">
                        <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">All Clear! 🎉</h3>
                    <p class="text-gray-600 max-w-sm mx-auto mb-4">
                        No negative balances found. All customers are up to date with their payments.
                    </p>
                    <div class="px-4 py-2 bg-green-50 text-green-700 rounded-lg border border-green-200 inline-block">
                        <span class="text-sm font-medium">Great job managing accounts!</span>
                    </div>
                </div>';
            })
            ->striped()
            ->paginated(false)
            ->deferLoading()
            ->extraAttributes([
                'class' => 'rounded-xl border border-gray-200 shadow-sm bg-white'
            ]);
    }

    /**
     * Create WhatsApp message
     */
    private function createWhatsAppMessage(string $name, string $formattedBalance): string
    {
        $currentDate = now()->format('d/m/Y');
        $dueDate = now()->addDays(7)->format('d F, Y');

        $message = "🏢 *Visa Office Chapai International*\n\n";
        $message .= "🔔 *BALANCE REMINDER NOTIFICATION*\n\n";
        $message .= "Dear *{$name}*,\n\n";
        $message .= "Your account has an outstanding balance:\n\n";
        $message .= "💰 *Amount Due:* -{$formattedBalance}৳\n";
        $message .= "⚠️ *Status:* Payment Required\n";
        $message .= "📅 *Date:* {$currentDate}\n";
        $message .= "⏰ *Suggested Payment Date:* {$dueDate}\n\n";
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