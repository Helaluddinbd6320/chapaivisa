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

    /**
     * Render user profile title with compact horizontal layout
     * Phone first, Email after, Balance at the end
     */
    public function getTitle(): HtmlString
    {
        $user = $this->record;
        $phone = $user->phone1 ? $user->phone1 : 'No phone';
        $email = $user->email ? $user->email : 'No email';
        $balance = $this->calculateBalance($user);
        $photoUrl = $this->getUserPhoto($user);

        $html = <<<HTML
        <div class="flex items-center space-x-4">
            <!-- Avatar -->
            <div class="shrink-0">
                <img src="{$photoUrl}" 
                     
                     class="w-10 h-10 rounded-full border-2 border-white dark:border-gray-800 shadow-sm">
            </div>

            <!-- User Info Horizontal -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6">
                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                    {$user->name}
                </p>

                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    Phone: {$phone}
                </span>

                <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                    Email: {$email}
                </span>

                
            </div>
        </div>
        HTML;

        return new HtmlString($html);
    }

    /**
     * Calculate current user balance from accounts
     */
    private function calculateBalance($user): string
    {
        $balance = 0;

        if ($user->accounts) {
            foreach ($user->accounts as $acc) {
                if ($acc->transaction_type === 'deposit') {
                    $balance += $acc->amount;
                } else { // withdrawal / refund
                    $balance -= $acc->amount;
                }
            }
        }

        return number_format($balance, 2);
    }

    /**
     * Get user photo URL or default avatar
     */
    private function getUserPhoto($user): string
    {
        if ($user->photo && filter_var($user->photo, FILTER_VALIDATE_URL)) {
            return $user->photo;
        }

        if ($user->photo && strpos($user->photo, 'users/') === 0) {
            return asset('storage/'.$user->photo);
        }

        // Default avatar
        $colors = ['7F9CF5', '48BB78', 'ED8936', '9F7AEA', 'F56565', '38B2AC', 'ECC94B', '4299E1', '0BC5EA', 'ED64A6'];
        $colorIndex = $user->id % count($colors);
        $color = $colors[$colorIndex];

        return 'https://ui-avatars.com/api/?name='.urlencode($user->name).
               '&color=FFFFFF&background='.$color.'&bold=true';
    }

    /**
     * Eager load visas and account entries, combine into ledger entries
     */
    protected function resolveRecord(string|int $key): Model
    {
        $user = static::getResource()::getModel()::with(['visas', 'accounts'])
            ->findOrFail($key);

        $entries = [];

        // Visa entries
        foreach ($user->visas as $visa) {
            $entries[] = [
                'date' => $visa->created_at->format('Y-m-d'),
                'type' => 'Visa',
                'description' => $visa->visa_condition,
                'debit' => $visa->visa_cost,
                'credit' => 0,
            ];
        }

        // Account entries
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
                'date' => $acc->created_at->format('Y-m-d'),
                'type' => 'Account',
                'description' => $desc,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        // Sort by date ascending
        usort($entries, fn ($a, $b) => strtotime($a['date']) <=> strtotime($b['date']));

        // Calculate running balance
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }

        $user->ledgerEntries = $entries;

        return $user;
    }

    /**
     * Form schema for displaying ledger
     */
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
                                            TextInput::make('date')->label('Date')->disabled(),
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
                                                ->formatStateUsing(fn ($record) => $record->created_at ? $record->created_at->format('d M Y') : 'N/A')
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
                                                ->formatStateUsing(fn ($record) => $record->created_at ? $record->created_at->format('d M Y') : 'N/A')
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
