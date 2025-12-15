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
                    ->size('sm'),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('💰 Balance')
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        if ($state < 0) {
                            return "<div class='flex items-center gap-2'>
                                <span class='text-red-600 font-bold'>-{$formattedBalance} ৳</span>
                                <span class='text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full font-medium'>DUE</span>
                            </div>";
                        }
                        return "<span class='text-green-600 font-bold'>{$formattedBalance} ৳</span>";
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                // WhatsApp Button - Beautiful Design
                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('📲 Send Reminder')
                    ->formatStateUsing(function ($record) {
                        if (empty($record->phone1)) {
                            return '<span class="text-gray-400 text-sm">-</span>';
                        }
                        
                        if ($record->calculated_balance >= 0) {
                            return '<span class="text-green-500 text-sm font-medium">✅ Cleared</span>';
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        // Professional WhatsApp message
                        $message = "🌟 *Visa Office Chapai International* 🌟\n" .
                                   "━━━━━━━━━━━━━━━━━━━━\n" .
                                   "📋 *BALANCE REMINDER NOTIFICATION*\n\n" .
                                   "Dear *{$record->name}*,\n\n" .
                                   "Your account has an outstanding balance:\n\n" .
                                   "💰 *Amount Due:* -{$formattedBalance}৳\n" .
                                   "📊 *Status:* Payment Required\n" .
                                   "📅 *Date:* Today\n\n" .
                                   "━━━━━━━━━━━━━━━━━━━━\n" .
                                   "💳 *PAYMENT OPTIONS:*\n" .
                                   "• Cash payment at our office\n" .
                                   "• Bank transfer\n" .
                                   "• Mobile banking (bKash, Nagad, Rocket)\n\n" .
                                   "🏢 *OFFICE INFORMATION:*\n" .
                                   "Visa Office Chapai International\n" .
                                   "[Your Office Address]\n" .
                                   "[Office Phone Number]\n\n" .
                                   "━━━━━━━━━━━━━━━━━━━━\n" .
                                   "Please clear your dues at the earliest to avoid any inconvenience.\n\n" .
                                   "Thank you for your cooperation.\n\n" .
                                   "Best regards,\n" .
                                   "*Visa Office Chapai International*";
                        
                        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                        
                        // Using concatenated string to avoid indentation issues
                        $html = '<div class="flex flex-col items-center gap-1">';
                        $html .= '<a href="' . $whatsappUrl . '" ';
                        $html .= 'target="_blank" ';
                        $html .= 'class="whatsapp-btn inline-flex items-center justify-center px-4 py-2.5 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 gap-2 w-full max-w-[160px] group" ';
                        $html .= 'title="Send WhatsApp reminder">';
                        $html .= '<div class="flex items-center gap-2">';
                        $html .= '<div class="relative">';
                        $html .= '<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">';
                        $html .= '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>';
                        $html .= '</svg>';
                        $html .= '<div class="absolute -top-1 -right-1 w-2 h-2 bg-green-300 rounded-full animate-ping"></div>';
                        $html .= '</div>';
                        $html .= '<span>Send WhatsApp</span>';
                        $html .= '</div>';
                        $html .= '</a>';
                        $html .= '<div class="text-xs text-gray-600 font-medium bg-amber-50 px-2 py-0.5 rounded-full">';
                        $html .= 'Due: -' . $formattedBalance . '৳';
                        $html .= '</div>';
                        $html .= '</div>';
                        
                        return $html;
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'min-w-[180px]']),
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