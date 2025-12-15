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
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        
                        return "<div class='flex flex-col'>
                            <span>{$state}</span>
                        </div>";
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance & Action')
                    ->formatStateUsing(function ($state, $record) {
                        $formattedBalance = number_format(abs($state), 0);
                        $isNegative = $state < 0;
                        $hasPhone = !empty($record->phone1);
                        
                        if ($isNegative && $hasPhone) {
                            $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                            
                            // WhatsApp message - আপনি চাইলে এডিট করতে পারেন
                            $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.";
                            $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                            
                            return <<<HTML
<div class="flex items-center gap-3">
    <span class="font-bold text-red-600 text-sm">-{$formattedBalance} ৳</span>
    <a href="{$whatsappUrl}" 
       target="_blank" 
       class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded-lg hover:bg-green-700 transition-colors shadow-sm hover:shadow-md gap-2"
       title="Send WhatsApp reminder">
        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
        Send WhatsApp
    </a>
</div>
HTML;
                        }
                        
                        // No WhatsApp button - only show balance
                        return $isNegative ? 
                            "<span class='font-bold text-red-600'>-{$formattedBalance} ৳</span>" : 
                            "<span class='font-bold text-green-600'>{$formattedBalance} ৳</span>";
                    })
                    ->html()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),
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