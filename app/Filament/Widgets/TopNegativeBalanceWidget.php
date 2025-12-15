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
                    ->selectRaw($this->getBalanceSubquery() . ' as calculated_balance')
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
                            " . ($state < 0 ? '<span class="text-xs bg-red-100 text-red-800 px-2 py-0.5 rounded-full">DUE</span>' : '') . "
                        </div>";
                    })
                    ->html()
                    ->badge()
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('whatsapp_action')
                    ->label('📲 Send Reminder')
                    ->formatStateUsing(function ($record) {
                        if (empty($record->phone1)) {
                            return '<div class="text-center py-1">
                                <span class="text-gray-400 text-xs">No phone</span>
                            </div>';
                        }
                        
                        if ($record->calculated_balance >= 0) {
                            return '<div class="text-center py-1">
                                <span class="text-green-500 text-xs font-medium">✅ Cleared</span>
                            </div>';
                        }
                        
                        $formattedBalance = number_format(abs($record->calculated_balance), 0);
                        
                        $html = <<<HTML
<div class="flex flex-col items-center gap-1 py-1">
    <a href="#" 
       class="whatsapp-btn inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow hover:shadow-lg transform hover:-translate-y-0.5 gap-2 w-full max-w-[160px]"
       data-phone="{$record->phone1}"
       data-name="{$record->name}"
       data-balance="{$formattedBalance}"
       title="Send WhatsApp reminder for -{$formattedBalance}৳">
        
        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
        </svg>
        <span>Send Reminder</span>
    </a>
    <span class="text-xs text-red-600 font-medium">-{$formattedBalance}৳ Due</span>
</div>
HTML;
                        return $html;
                    })
                    ->html()
                    ->alignCenter()
                    ->extraAttributes(['class' => 'min-w-[180px]'])
            ])
            ->heading('📊 Top 10 Negative Balance Users')
            ->description('Users with outstanding dues • Click WhatsApp to send reminder')
            ->emptyStateHeading('🎉 All Clear!')
            ->emptyStateDescription('No negative balances found.')
            ->emptyStateIcon('heroicon-o-check-circle')
            ->striped()
            ->paginated(false);
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        // WhatsApp JavaScript ফুটারে যোগ করুন
        return view('filament.widgets.whatsapp-script');
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