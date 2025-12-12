<?php

namespace App\Filament\Resources\Users\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Photo')
                    ->circular()
                    ->size(40)
                    ->disk('public')
                    ->defaultImageUrl(function ($record) {
                        return 'https://ui-avatars.com/api/?name='.urlencode($record->name).
                               '&color=FFFFFF&background='.(self::getAvatarColor($record->id)).
                               '&bold=true';
                    })

                    ->disabledClick()
                    ->url(fn ($record) => $record->photo ? Storage::url($record->photo) : null)
                    ->disabledClick(fn ($record) => ! $record->photo) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),

                TextColumn::make('name')
                    ->searchable()
                    ->label('Name')
                    ->url(fn ($record): string => $record->id
                        ? \App\Filament\Resources\Users\Pages\UserProfile::getUrl(['record' => $record->id])
                        : '#'
                    )
                    ->color('primary')
                    ->weight('semibold')
                    ->description(fn ($record) => $record->reference ? 'Ref: '.$record->reference : null)
                    ->tooltip('View user profile'),

                // Ledger-style current balance with color badges
                TextColumn::make('current_balance')
                    ->label('Balance')
                    ->disabledClick()
                    ->formatStateUsing(function ($record) {
                        $debit = $record->visas->sum('visa_cost');

                        $credit = $record->accounts->sum(function ($acc) {
                            return $acc->transaction_type === 'deposit' ? $acc->amount : 0;
                        });

                        $withdrawals = $record->accounts->sum(function ($acc) {
                            return in_array($acc->transaction_type, ['withdrawal', 'refund']) ? $acc->amount : 0;
                        });

                        return $credit - ($debit + $withdrawals);
                    })
                    ->money('BDT', locale: 'bn')
                    ->prefix('৳')
                    ->color(function ($state) {
                        return $state >= 0 ? 'success' : 'danger';
                    })
                    ->icon(function ($state) {
                        return $state >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down';
                    })
                    ->iconPosition('after')
                    ->badge(),

                TextColumn::make('email')
                    ->label('Email')
                    ->disabledClick()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->iconPosition('before'),

                TextColumn::make('phone1')
                    ->label('Phone')
                    ->disabledClick()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->iconPosition('before')
                    ->placeholder('Not set'),
                TextColumn::make('phone2')
                    ->label('Phone')
                    ->disabledClick()
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone')
                    ->iconPosition('before')
                    ->placeholder('Not set'),

                TagsColumn::make('roles.name')
                    ->label('Roles')
                    ->searchable()
                    ->disabledClick()
                    ->separator(',')
                    ->limit(2)
                    ->colors([
                        'primary' => 'super_admin',
                        'success' => 'admin',
                        'warning' => 'manager',
                        'info' => 'user',
                    ]),

                TextColumn::make('address')
                    ->label('Address')
                    ->disabledClick()
                    ->searchable()
                    ->limit(30)
                    ->tooltip(function ($state) {
                        return $state;
                    })
                    ->placeholder('Not set')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('email_verified_at')
                    ->label('Verified')
                    ->dateTime('d M Y, h:i A')
                    ->disabledClick()
                    ->badge()
                    ->color(fn ($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn ($state) => $state ? 'Verified' : 'Not Verified')
                    ->icon(fn ($state) => $state ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('d M Y')
                    ->disabledClick()
                    ->icon('heroicon-o-calendar')
                    ->iconPosition('before')
                    ->toggleable(),

                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime('d M Y, h:i A')
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->label('Filter by Role'),

                SelectFilter::make('email_verified')
                    ->label('Email Verification')
                    ->options([
                        'verified' => 'Verified',
                        'unverified' => 'Not Verified',
                    ])
                    ->query(function ($query, $data) {
                        if ($data['value'] === 'verified') {
                            return $query->whereNotNull('email_verified_at');
                        } elseif ($data['value'] === 'unverified') {
                            return $query->whereNull('email_verified_at');
                        }

                        return $query;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),
                EditAction::make()
                    ->icon('heroicon-o-pencil')
                    ->color('warning'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation(),
                ]),
            ])
            ->defaultSort('name', 'asc')
            ->reorderable('name')
            ->paginated([10, 25, 50, 100])
            ->striped()
            ->searchable()
            ->selectable();
    }

    /**
     * Generate consistent avatar color based on user ID
     */
    private static function getAvatarColor($userId): string
    {
        $colors = [
            '7F9CF5', // Blue
            '48BB78', // Green
            'ED8936', // Orange
            '9F7AEA', // Purple
            'F56565', // Red
            '38B2AC', // Teal
            'ECC94B', // Yellow
            '4299E1', // Light Blue
            '0BC5EA', // Cyan
            'ED64A6', // Pink
        ];

        $index = $userId % count($colors);

        return $colors[$index];
    }
}
