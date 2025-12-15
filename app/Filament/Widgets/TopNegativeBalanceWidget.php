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
                    ->formatStateUsing(function ($state, $record) {
                        // Format phone number and add WhatsApp button
                        $formattedPhone = $state ?: 'N/A';
                        $balance = $record->calculated_balance;
                        $formattedBalance = number_format(abs($balance), 0);

                        $whatsappUrl = $state ?
                            'https://wa.me/'.preg_replace('/[^0-9]/', '', $state).
                            '?text='.urlencode("Dear {$record->name}, your current balance is -{$formattedBalance}৳. Please clear your due as soon as possible. Thank you.") :
                            '#';

                        $whatsappBtn = $state ?
                            "<a href='{$whatsappUrl}' target='_blank' class='inline-flex items-center px-2 py-1 text-xs font-semibold text-white bg-green-600 rounded hover:bg-green-700'>
                                <svg class='w-3 h-3 mr-1' fill='currentColor' viewBox='0 0 24 24'>
                                    <path d='M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.67-1.612-.916-2.207-.242-.579-.487-.5-.67-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z'/>
                                    <path d='M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm6.958 17.963c-.188.545-.94.993-1.539 1.113-.409.081-.939.125-1.739-.133-.445-.145-1.004-.375-1.761-.775-.995-.523-1.828-1.197-2.558-2.058-2.357-2.387-3.156-5.183-2.987-6.99.076-.817.428-1.512.939-1.973.538-.487 1.213-.735 1.938-.735.272 0 .544.025.817.075.445.081.891.335 1.138.694.248.36.322.798.272 1.238-.074.644-.495 1.438-.57 1.587-.074.149-.074.248.05.372.124.124.272.272.396.421.124.149.248.298.347.421.099.124.149.272.025.446-.124.174-.223.298-.446.521-.223.223-.446.447-.669.644-.173.149-.347.322-.149.644.198.322.891 1.438 2.094 2.08.471.248.818.347 1.089.397.272.05.446.025.57-.05.124-.074.272-.223.347-.644.074-.421.272-.818.47-1.139.173-.298.421-.322.67-.223.248.099 1.564.792 1.836.938.272.149.446.223.52.347.075.124.075.694-.114 1.239z'/>
                                </svg>
                                Send
                            </a>" :
                            '';

                        return "<div class='flex flex-col gap-1'>
                            <span>{$formattedPhone}</span>
                            {$whatsappBtn}
                        </div>";
                    })
                    ->html(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),

                Tables\Columns\TextColumn::make('calculated_balance')
                    ->label('Balance')
                    ->formatStateUsing(fn ($state) => number_format($state, 0).' ৳'
                    )
                    ->color(fn ($state) => $state < 0 ? 'danger' : 'success')
                    ->description(function ($record) {
                        // Additional balance breakdown
                        $deposits = $record->accounts
                            ->where('transaction_type', 'deposit')
                            ->sum('amount');
                        $visas = $record->visas->sum('visa_cost');
                        $withdrawals = $record->accounts
                            ->where('transaction_type', 'withdrawal')
                            ->sum('amount');
                        $refunds = $record->accounts
                            ->where('transaction_type', 'refund')
                            ->sum('amount');

                        return 'D: '.number_format($deposits).
                               ' | V: -'.number_format($visas).
                               ' | W: -'.number_format($withdrawals).
                               ' | R: -'.number_format($refunds);
                    }, position: 'above')
                    ->tooltip('D=Deposits, V=Visa, W=Withdrawal, R=Refund'),

                // WhatsApp Action Column
                Tables\Columns\ActionColumn::make('actions')
                    ->label('Message')
                    ->actions([
                        Tables\Actions\Action::make('whatsapp')
                            ->label('WhatsApp')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->color('success')
                            ->url(function ($record) {
                                if (! $record->phone1) {
                                    return null;
                                }

                                $phone = preg_replace('/[^0-9]/', '', $record->phone1);
                                $balance = $record->calculated_balance;
                                $formattedBalance = number_format(abs($balance), 0);

                                $message = "Dear {$record->name}, your current balance is -{$formattedBalance}৳. ";
                                $message .= 'Please clear your due as soon as possible. Thank you.';

                                return "https://wa.me/{$phone}?text=".urlencode($message);
                            })
                            ->openUrlInNewTab()
                            ->visible(fn ($record) => ! empty($record->phone1))
                            ->tooltip('Send WhatsApp message about balance'),
                    ])
                    ->width('100px'),
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
