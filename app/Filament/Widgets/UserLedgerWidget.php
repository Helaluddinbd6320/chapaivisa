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
                'datetime' => $visa->created_at,
                'type' => 'Visa',
                'description' => $visa->visa_condition,
                'name' => $visa->name ?? 'N/A',
                'passport' => $visa->passport ?? 'N/A',
                'debit' => $visa->visa_cost,
                'credit' => 0,
                'id' => $visa->id,
                'model_type' => 'visa',
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
                'datetime' => $acc->created_at,
                'type' => 'Account',
                'description' => $desc,
                'name' => '—',
                'passport' => '—',
                'debit' => $debit,
                'credit' => $credit,
                'id' => $acc->id,
                'model_type' => 'account',
            ];
        }

        // প্রথমে তারিখ ও সময় অনুসারে পুরনো থেকে নতুন (ascending) সাজানো
        usort($entries, function ($a, $b) {
            return $a['datetime'] <=> $b['datetime'];
        });

        // পুরনো থেকে নতুন করে ব্যালেন্স কালকুলেট
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }

        // এখন উল্টো করে সাজানো (নতুন থেকে পুরনো)
        $entries = array_reverse($entries);

        $this->ledgerEntries = $entries;
    }
}