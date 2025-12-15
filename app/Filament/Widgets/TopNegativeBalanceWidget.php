<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Actions\Action as ActionsAction;
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
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('Phone')
                    ->searchable()
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $state);
                        
                        return <<<HTML
<div class="flex flex-col gap-1">
    <span class="font-medium text-gray-700">{$state}</span>
    <a href="tel:{$cleanPhone}" 
       class="text-xs text-blue-600 hover:text-blue-800 hover:underline">
        📞 Tap to call
    </a>
</div>
HTML;
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->icon('heroicon-o-envelope')
                    ->iconColor('gray'),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        $isNegative = $state < 0;
                        
                        $colorClass = $isNegative ? 'text-red-600' : 'text-green-600';
                        $icon = $isNegative ? '⬇️' : '⬆️';
                        
                        return <<<HTML
<div class="flex items-center gap-2">
    <span class="text-lg {$colorClass} font-bold">{$icon} {$formattedBalance} ৳</span>
    {!! $isNegative ? '<span class="px-2 py-0.5 text-xs font-semibold bg-red-100 text-red-800 rounded-full">DUE</span>' : '' !!}
</div>
HTML;
                    })
                    ->html()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->description(function ($record) {
                        // Show balance breakdown
                        $deposits = $record->accounts
                            ->where('transaction_type', 'deposit')
                            ->sum('amount');
                        $visas = $record->visas->sum('visa_cost');
                        
                        return 'Deposits: ৳' . number_format($deposits) . ' | Visa: -৳' . number_format($visas);
                    }),

                // WhatsApp Action Column
                Tables\Columns\TextColumn::make('whatsapp')
                    ->label('Send Reminder')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return <<<HTML
<div class="text-center py-2">
    <span class="text-xs text-gray-400 italic">No action needed</span>
</div>
HTML;
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        // Professional WhatsApp message template
                        $message = <<<MSG
*🏢 Visa Office Chapai International*
*📋 Balance Reminder Notification*

Dear {$record->name},

We hope this message finds you well.

**📊 Account Summary:**
• Current Balance: *-{$formattedBalance}৳*
• Status: *Payment Due*

**🔔 Important Notice:**
Please clear your outstanding balance at your earliest convenience to avoid any service interruptions.

**💳 Payment Methods:**
- Cash Payment at Office
- Bank Transfer
- Mobile Banking

**📞 Contact Us:**
If you have any questions or need assistance, please feel free to contact us.

Thank you for your prompt attention to this matter.

Best regards,
*Visa Office Chapai International*
*Official WhatsApp*
MSG;
                        
                        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                        
                        return <<<HTML
<div class="flex flex-col items-center gap-2 py-1">
    <a href="{$whatsappUrl}" 
       target="_blank" 
       class="inline-flex items-center justify-center w-full px-4 py-2.5 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 gap-2"
       title="Send professional WhatsApp reminder">
        
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
            </svg>
            <span>WhatsApp</span>
        </div>
    </a>
    
    <div class="text-xs text-gray-500 text-center">
        <div class="flex items-center justify-center gap-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Professional reminder
        </div>
    </div>
</div>
HTML;
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'w-40']), // Column width
            ])
            ->heading('📋 Top 10 Negative Balance Users')
            ->description('Users with outstanding dues - Send WhatsApp reminders')
            ->emptyStateHeading('🎉 No negative balance found!')
            ->emptyStateDescription('All users have positive balance or cleared dues.')
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