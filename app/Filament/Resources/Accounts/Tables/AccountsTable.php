<?php

namespace App\Filament\Resources\Accounts\Tables;

use App\Filament\Resources\Users\Pages\UserProfile;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();

                if (! $user->hasAnyRole(['super_admin', 'admin', 'manager'])) {
                    $query->where('user_id', $user->id);
                }

                return $query;
            })

            ->columns([
                TextColumn::make('created_at')
                    ->label('Created')
                    ->formatStateUsing(function ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        $now = \Carbon\Carbon::now();

                        $diff = $date->diff($now);

                        return "{$diff->y} বছর {$diff->m} মাস {$diff->d} দিন";
                    }),
                TextColumn::make('user.name')
                    ->label('Agent')
                    ->searchable()
                    ->url(fn ($record): string => $record->user && $record->user_id
                            ? UserProfile::getUrl(['record' => $record->user_id])
                            : '#'
                    )
                    ->color('primary')
                    ->openUrlInNewTab(false) // একই ট্যাবে খুলবে
                    ->tooltip('View Agent Profile')

                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin', 'manager'])),

                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->copyable()
                    ->disabledClick(),

                TextColumn::make('transaction_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'deposit' => 'success',
                        'withdrawal' => 'danger',
                        'refund' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'deposit' => 'Deposit',
                        'withdrawal' => 'Withdrawal',
                        'refund' => 'Refund',
                        default => $state,
                    })
                    ->disabledClick(),

                TextColumn::make('amount')
                    ->money('BDT')
                    ->color(fn ($record) => $record->transaction_type === 'deposit' ? 'success' : 'danger')
                    ->sortable()
                    ->disabledClick(),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color('gray')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'mobile_banking' => 'Mobile',
                        'card' => 'Card',
                        default => $state,
                    })
                    ->disabledClick(),

                ImageColumn::make('receipt_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->receipt_image ? Storage::url($record->receipt_image) : null)
                    ->disabledClick(fn ($record) => ! $record->receipt_image) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),

                TextColumn::make('bank_name')
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('account_number')
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('mobile_banking_provider')
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('mobile_number')
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('payment_date')
                    ->disabledClick()
                    ->label('Date')
                    ->date('d M, Y'),

                TextColumn::make('receipt_number')
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('reference_number')
                    ->disabledClick()
                    ->searchable(),

                TextColumn::make('status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'cancelled',
                    ])
                    ->disabledClick(),

                // IconColumn::make('is_verified')
                //     ->boolean(),

                TextColumn::make('verifier.name')
                    ->label('Verified By')
                    ->disabledClick()
                    ->placeholder('Not verified'),

                TextColumn::make('verified_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M, Y H:i')
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
