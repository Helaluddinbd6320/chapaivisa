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
                    ->label('Name')
                    ->searchable()
                    ->url(fn ($record): ?string => $record->id
                                ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl([
                                    'record' => $record->id,
                                ])
                                : null
                    )
                    ->color('primary')
                    ->tooltip('View user profile'),

                Tables\Columns\TextColumn::make('phone1')
                    ->label('Phone')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => $state ?: 'N/A'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance')
                    ->formatStateUsing(function ($state, $record) {
                        $formattedBalance = number_format(abs($state), 0);
                        $isNegative = $state < 0;
                        
                        // Show WhatsApp button for negative balance with phone number
                        if ($isNegative && !empty($record->phone1)) {
                            $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                            
                            // WhatsApp message template
                            $message = "Dear {$record->name},\n\n";
                            $message .= "Your current balance is: -{$formattedBalance}৳\n";
                            $message .= "Please clear your due payment as soon as possible.\n\n";
                            $message .= "Thank you,\n";
                            $message .= "Visa Office Chapai International";
                            
                            $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                            
                            return "<div class='flex items-center gap-2'>
                                <span class='font-bold text-red-600'>-{$formattedBalance} ৳</span>
                                <a href='{$whatsappUrl}' 
                                   target='_blank' 
                                   class='inline-flex items-center justify-center w-8 h-8 text-green-600 bg-green-50 rounded-full hover:bg-green-100 hover:text-green-700 transition-colors'
                                   title='Send WhatsApp reminder'>
                                    <svg class='w-4 h-4' fill='currentColor' viewBox='0 0 24 24'>
                                        <path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z'/>
                                    </svg>
                                </a>
                            </div>";
                        }
                        
                        // Show only balance without button
                        return $isNegative ? 
                            "<span class='font-bold text-red-600'>-{$formattedBalance} ৳</span>" : 
                            "<span class='font-bold text-green-600'>{$formattedBalance} ৳</span>";
                    })
                    ->html()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
                    
                // WhatsApp column as action
                Tables\Columns\IconColumn::make('whatsapp')
                    ->label('Message')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(function ($record) {
                        if (!$record->phone1 || $record->calculated_balance >= 0) {
                            return null;
                        }
                        
                        $phone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $balance = $record->calculated_balance;
                        $formattedBalance = number_format(abs($balance), 0);
                        
                        $message = "Dear {$record->name},\n\n";
                        $message .= "📊 *Balance Reminder*\n";
                        $message .= "——————\n";
                        $message .= "💰 *Due Amount:* -{$formattedBalance}৳\n";
                        $message .= "——————\n";
                        $message .= "Please make the payment at your earliest convenience.\n\n";
                        $message .= "Thank you,\n";
                        $message .= "*Visa Office Chapai International*";
                        
                        return "https://wa.me/{$phone}?text=" . urlencode($message);
                    })
                    ->openUrlInNewTab()
                    ->tooltip('Send WhatsApp reminder')
                    ->visible(fn ($record) => !empty($record->phone1) && $record->calculated_balance < 0),
            ])
            ->heading('Top 10 Negative Balance Users')
            ->emptyStateHeading('No negative balance found')
            ->emptyStateDescription('All users have positive balance.')
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