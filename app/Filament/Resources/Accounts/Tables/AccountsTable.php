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
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;

class AccountsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ✅ নতুন ট্রানজ্যাকশন ওপরে দেখাবে
            ->defaultSort('created_at', 'desc')
            
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();

                if (! $user->hasAnyRole(['super_admin', 'admin', 'manager'])) {
                    $query->where('user_id', $user->id);
                }

                return $query;
            })

            ->columns([
                // ✅ Created at - নতুন ডাটা উপরে দেখাবে
                TextColumn::make('created_at')
                    ->label('Created')
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'N/A';
                        
                        $date = \Carbon\Carbon::parse($state);
                        $now = \Carbon\Carbon::now();

                        $diff = $date->diff($now);

                        return "{$diff->y} বছর {$diff->m} মাস {$diff->d} দিন";
                    })
                    ->description(fn ($record) => $record ? $record->created_at->format('d M, Y h:i A') : '')
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Agent')
                    ->searchable()
                    ->url(fn ($record): string => $record && $record->user && $record->user_id
                            ? UserProfile::getUrl(['record' => $record->user_id])
                            : '#'
                    )
                    ->color('primary')
                    ->openUrlInNewTab(false)
                    ->tooltip('View Agent Profile')
                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin', 'manager'])),

                TextColumn::make('transaction_id')
                    ->label('Transaction ID')
                    ->searchable()
                    ->copyable()
                    ->disabledClick()
                    ->weight('medium'),

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
                        'deposit' => '💰 Deposit',
                        'withdrawal' => '💸 Withdrawal',
                        'refund' => '🔄 Refund',
                        default => $state ?? 'N/A',
                    })
                    ->disabledClick(),

                TextColumn::make('amount')
                    ->label('Amount')
                    ->money('BDT')
                    ->color(fn ($record) => $record && $record->transaction_type === 'deposit' ? 'success' : 'danger')
                    ->sortable()
                    ->disabledClick()
                    ->weight('bold')
                    ->description(fn ($record) => $record ? "({$record->transaction_type})" : ''),

                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'cash' => 'warning',
                        'bank' => 'primary',
                        'mobile_banking' => 'success',
                        'card' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cash' => '💵 Cash',
                        'bank' => '🏦 Bank',
                        'mobile_banking' => '📱 Mobile',
                        'card' => '💳 Card',
                        default => $state ?? 'N/A',
                    })
                    ->disabledClick(),

                ImageColumn::make('receipt_image')
                    ->disk('public')
                    ->label('Receipt')
                    ->url(fn ($record) => $record && $record->receipt_image ? Storage::url($record->receipt_image) : null)
                    ->disabledClick(fn ($record) => !($record && $record->receipt_image))
                    ->openUrlInNewTab()
                    ->tooltip('View Receipt'),

                TextColumn::make('bank_name')
                    ->label('Bank')
                    ->disabledClick()
                    ->searchable()
                    ->visible(fn ($record) => $record && $record->payment_method === 'bank')
                    ->icon('heroicon-m-building-library'),

                TextColumn::make('account_number')
                    ->label('Account No.')
                    ->disabledClick()
                    ->searchable()
                    ->visible(fn ($record) => $record && $record->payment_method === 'bank')
                    ->icon('heroicon-m-credit-card'),

                TextColumn::make('mobile_banking_provider')
                    ->label('Provider')
                    ->disabledClick()
                    ->searchable()
                    ->visible(fn ($record) => $record && $record->payment_method === 'mobile_banking')
                    ->icon('heroicon-m-device-phone-mobile'),

                TextColumn::make('mobile_number')
                    ->label('Mobile No.')
                    ->disabledClick()
                    ->searchable()
                    ->visible(fn ($record) => $record && $record->payment_method === 'mobile_banking')
                    ->icon('heroicon-m-phone'),

                TextColumn::make('payment_date')
                    ->label('Payment Date')
                    ->disabledClick()
                    ->date('d M, Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                TextColumn::make('receipt_number')
                    ->label('Receipt No.')
                    ->disabledClick()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-document-text'),

                TextColumn::make('reference_number')
                    ->label('Reference No.')
                    ->disabledClick()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-hashtag'),

                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'verified',
                        'danger' => 'cancelled',
                        'gray' => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => '⏳ Pending',
                        'verified' => '✅ Verified',
                        'cancelled' => '❌ Cancelled',
                        'rejected' => '🚫 Rejected',
                        default => $state ? ucfirst($state) : 'N/A',
                    })
                    ->disabledClick(),

                TextColumn::make('verifier.name')
                    ->label('Verified By')
                    ->disabledClick()
                    ->placeholder('Not verified')
                    ->icon('heroicon-m-check-circle')
                    ->color('success'),

                TextColumn::make('verified_at')
                    ->label('Verified At')
                    ->dateTime('d M, Y h:i A')
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),

                // ✅ নিচের কলামগুলো hidden হিসেবে রাখা হলো
                TextColumn::make('notes')
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->dateTime('d M, Y h:i A')
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('deleted_at')
                    ->dateTime('d M, Y h:i A')
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            // ✅ Filters
            ->filters([
                TrashedFilter::make(),
                
                Filter::make('recent')
                    ->label('Recent (Last 7 Days)')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
                
                SelectFilter::make('transaction_type')
                    ->label('Transaction Type')
                    ->options([
                        'deposit' => 'Deposit',
                        'withdrawal' => 'Withdrawal',
                        'refund' => 'Refund',
                    ]),
                
                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'bank' => 'Bank',
                        'mobile_banking' => 'Mobile Banking',
                        'card' => 'Card',
                    ]),
                
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'verified' => 'Verified',
                        'cancelled' => 'Cancelled',
                        'rejected' => 'Rejected',
                    ]),
                
                Filter::make('pending_verification')
                    ->label('Pending Verification')
                    ->query(fn (Builder $query) => $query->where('status', 'pending')),
            ])
            
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-m-eye'),
                
                EditAction::make()
                    ->icon('heroicon-m-pencil'),
                
                ForceDeleteAction::make()
                    ->icon('heroicon-m-trash')
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
            ])
            
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-m-trash'),
                ]),
            ]);
    }
}