<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserLedgerWidget extends Widget
{

    protected static ?string $heading = 'My Ledger';
    protected static ?int $sort = 2; // প্রথমে দেখাবে

    // Widget size - compact
    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.user-ledger-widget'; // এটিকে non-static রাখুন

    public $ledgerEntries = [];

    public $shouldShowWidget = false;

    // শুধুমাত্র normal user-দের জন্য render হবে
    public function shouldRender(): bool
    {
        $user = Auth::user();

        return ! $user->hasAnyRole(['super_admin', 'admin', 'manager']);
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->shouldShowWidget = ! $user->hasAnyRole(['super_admin', 'admin', 'manager']);

        if (! $this->shouldShowWidget) {
            return;
        }

        $entries = [];

        // Visas → Debit
        foreach ($user->visas as $visa) {
            $entries[] = [
                'date' => $visa->created_at->format('Y-m-d'),
                'type' => 'Visa',
                'description' => $visa->visa_condition,
                'debit' => $visa->visa_cost,
                'credit' => 0,
            ];
        }

        // Accounts → Debit / Credit
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

        // Running balance
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }

        $this->ledgerEntries = $entries;
    }
}