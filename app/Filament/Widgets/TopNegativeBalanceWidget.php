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
                    ->formatStateUsing(function ($state) {
                        $formattedBalance = number_format(abs($state), 0);
                        return $state < 0 ? 
                            "<span style='color: #dc2626; font-weight: bold;'>-{$formattedBalance} ৳</span>" : 
                            "<span style='color: #059669; font-weight: bold;'>{$formattedBalance} ৳</span>";
                    })
                    ->html()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success'),

                // WhatsApp Column with inline styles
                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('Send Reminder')
                    ->formatStateUsing(function ($state, $record) {
                        if (empty($record->phone1) || $record->calculated_balance >= 0) {
                            return '<span style="color: #9ca3af; font-style: italic;">-</span>';
                        }
                        
                        $cleanPhone = preg_replace('/[^0-9]/', '', $record->phone1);
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        // WhatsApp message
                        $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.";
                        $whatsappUrl = "https://wa.me/{$cleanPhone}?text=" . urlencode($message);
                        
                        return <<<HTML
<div style="display: flex; justify-content: center;">
    <a href="{$whatsappUrl}" 
       target="_blank" 
       style="display: inline-flex; align-items: center; padding: 6px 12px; background-color: #22c55e; color: white; font-weight: 500; border-radius: 6px; text-decoration: none; gap: 6px; font-size: 13px;"
       title="Send WhatsApp reminder">
        <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
        WhatsApp
    </a>
</div>
HTML;
                    })
                    ->html()
                    ->alignCenter(),
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