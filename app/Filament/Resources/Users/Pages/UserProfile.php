<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;

class UserProfile extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): HtmlString
    {
        $user = $this->record;
        $phone = $user->phone1 ?? 'No phone';
        $email = $user->email ?? 'No email';
        $photoUrl = $this->getUserPhoto($user);

        $html = <<<HTML
        <div class="flex items-center space-x-4">
            <div class="shrink-0">
                <img src="{$photoUrl}" class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-800 shadow-sm">
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {$user->name}
                </p>
                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">Phone: {$phone}</span>
                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">Email: {$email}</span>
            </div>
        </div>
        HTML;

        return new HtmlString($html);
    }

    private function getUserPhoto($user): string
    {
        if ($user->photo && filter_var($user->photo, FILTER_VALIDATE_URL)) {
            return $user->photo;
        }

        if ($user->photo && strpos($user->photo, 'users/') === 0) {
            return asset('storage/'.$user->photo);
        }

        $colors = ['7F9CF5', '48BB78', 'ED8936', '9F7AEA', 'F56565', '38B2AC', 'ECC94B', '4299E1', '0BC5EA', 'ED64A6'];
        $colorIndex = $user->id % count($colors);
        $color = $colors[$colorIndex];

        return 'https://ui-avatars.com/api/?name='.urlencode($user->name).
               '&color=FFFFFF&background='.$color.'&bold=true';
    }

    protected function resolveRecord(string|int $key): Model
    {
        $user = static::getResource()::getModel()::with([
            'visas' => fn ($q) => $q->orderByDesc('updated_at'),
            'accounts' => fn ($q) => $q->orderByDesc('updated_at'),
        ])->findOrFail($key);

        $entries = [];

        foreach ($user->visas as $visa) {
            $entries[] = [
                'date' => $visa->updated_at->format('Y-m-d H:i:s'),
                'type' => 'Visa',
                'description' => $visa->visa_condition,
                'debit' => $visa->visa_cost,
                'credit' => 0,
            ];
        }

        foreach ($user->accounts as $acc) {
            $desc = match ($acc->transaction_type) {
                'deposit' => 'Deposit',
                'withdrawal' => 'Withdrawal',
                'refund' => 'Refund',
                default => $acc->transaction_type,
            };

            if ($acc->transaction_type === 'deposit') {
                $debit = 0;
                $credit = $acc->amount;
            } else {
                $debit = $acc->amount;
                $credit = 0;
            }

            $entries[] = [
                'date' => $acc->updated_at->format('Y-m-d H:i:s'),
                'type' => 'Account',
                'description' => $desc,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        // ✅ Debit & Credit = 0 উপরে, বাকি updated_at descending
        usort($entries, function ($a, $b) {
            $aZero = $a['debit'] == 0 && $a['credit'] == 0;
            $bZero = $b['debit'] == 0 && $b['credit'] == 0;

            if ($aZero && ! $bZero) {
                return -1;
            }
            if (! $aZero && $bZero) {
                return 1;
            }

            return strtotime($b['date']) <=> strtotime($a['date']);
        });

        // ✅ Balance নিচ থেকে ওপরে হিসাব
        $balance = 0;
        for ($i = count($entries) - 1; $i >= 0; $i--) {
            $entry = &$entries[$i];
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }

        $user->ledgerEntries = $entries;

        return $user;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('User Details')
                ->tabs([
                    Tabs\Tab::make('Financial Ledger')
                        ->icon('heroicon-o-currency-dollar')
                        ->badge(fn ($record) => isset($record->ledgerEntries) ? count($record->ledgerEntries) : 0)
                        ->schema([
                            Section::make('Transaction History')
                                ->description('Complete financial transaction history')
                                ->schema([
                                    Repeater::make('ledgerEntries')
                                        ->schema([
                                            TextInput::make('date')
                                                ->formatStateUsing(function ($state) {
                                                    $date = \Carbon\Carbon::parse($state);
                                                    $now = \Carbon\Carbon::now();
                                                    $diff = $date->diff($now);

                                                    return "{$diff->y} বছর {$diff->m} মাস {$diff->d} দিন";
                                                })
                                                ->label('Date')->disabled(),
                                            TextInput::make('type')->label('Type')->disabled(),
                                            TextInput::make('description')->label('Description')->disabled(),
                                            TextInput::make('debit')
                                                ->label('Debit')
                                                ->disabled()
                                                ->formatStateUsing(fn ($state) => $state ? '৳'.number_format($state, 2) : ''),
                                            TextInput::make('credit')
                                                ->label('Credit')
                                                ->disabled()
                                                ->formatStateUsing(fn ($state) => $state ? '৳'.number_format($state, 2) : ''),
                                            TextInput::make('balance')
                                                ->label('Balance')
                                                ->disabled()
                                                ->formatStateUsing(fn ($state) => '৳'.number_format($state, 2)),
                                        ])
                                        ->columns(6)
                                        ->columnSpanFull()
                                        ->disabled(),
                                ]),
                        ]),

                    Tabs\Tab::make('Visas')
                        ->icon('heroicon-o-document-text')
                        ->badge(fn ($record) => $record->visas ? $record->visas->count() : 0)
                        ->schema([
                            Section::make('Visa Applications')
                                ->schema([
                                    Repeater::make('visas')
                                        ->relationship('visas')
                                        ->schema([
                                            TextInput::make('name')->label('Name')->disabled(),
                                            TextInput::make('passport')->label('Passport')->disabled(),
                                            TextInput::make('visa_condition')->label('Visa Type')->disabled(),
                                            TextInput::make('visa_cost')->label('Cost')->prefix('৳')->disabled(),
                                            TextInput::make('created_at_display')
                                                ->label('Applied On')
                                                // ->formatStateUsing(fn ($record) => $record->created_at ? $record->created_at->format('d M Y') : 'N/A')
                                                ->formatStateUsing(function ($state) {
                                                    $date = \Carbon\Carbon::parse($state);
                                                    $now = \Carbon\Carbon::now();
                                                    $diff = $date->diff($now);

                                                    return "{$diff->y} বছর {$diff->m} মাস {$diff->d} দিন";
                                                })
                                                ->disabled(),
                                        ])
                                        ->columns(5)
                                        ->columnSpanFull()
                                        ->disabled(),
                                ]),
                        ]),

                    Tabs\Tab::make('Accounts')
                        ->icon('heroicon-o-banknotes')
                        ->badge(fn ($record) => $record->accounts ? $record->accounts->count() : 0)
                        ->schema([
                            Section::make('Account Transactions')
                                ->schema([
                                    Repeater::make('accounts')
                                        ->relationship('accounts')
                                        ->schema([
                                            TextInput::make('transaction_type')->label('Type')->disabled(),
                                            TextInput::make('amount')->label('Amount')->prefix('৳')->disabled(),

                                            TextInput::make('created_at_display')
                                                ->label('Date')
                                                // ->formatStateUsing(fn ($record) => $record->created_at ? $record->created_at->format('d M Y') : 'N/A')
                                                ->formatStateUsing(function ($state) {
                                                    $date = \Carbon\Carbon::parse($state);
                                                    $now = \Carbon\Carbon::now();
                                                    $diff = $date->diff($now);

                                                    return "{$diff->y} বছর {$diff->m} মাস {$diff->d} দিন";
                                                })
                                                ->disabled(),
                                        ])
                                        ->columns(3)
                                        ->columnSpanFull()
                                        ->disabled(),
                                ]),
                        ]),
                ])
                ->columnSpanFull()
                ->persistTabInQueryString(),
        ]);
    }
}
