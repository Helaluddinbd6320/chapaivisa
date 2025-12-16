<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserLedgerWidget extends Widget
{
    protected static ?string $heading = 'My Ledger';
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.widgets.user-ledger-widget';

    public $ledgerEntries = [];
    public $shouldShowWidget = false;
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $currentBalance = 0;

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

        // ✅ Visas → Debit (সর্বশেষ তথ্য উপরে)
        foreach ($user->visas()->latest()->get() as $visa) {
            $entries[] = [
                'date' => $visa->created_at->format('Y-m-d H:i'),
                'display_date' => $visa->created_at->format('d M, Y'),
                'type' => 'Visa',
                'description' => $this->getVisaDescription($visa),
                'debit' => $visa->visa_cost ?? 0,
                'credit' => 0,
                'color' => 'danger',
                'icon' => 'heroicon-o-document-text',
            ];
        }

        // ✅ Accounts → Debit / Credit (সর্বশেষ তথ্য উপরে)
        foreach ($user->accounts()->latest()->get() as $acc) {
            $desc = $this->getAccountDescription($acc);
            
            if ($acc->transaction_type === 'deposit') {
                $debit = 0;
                $credit = $acc->amount ?? 0;
                $color = 'success';
                $icon = 'heroicon-o-arrow-down-circle';
            } else {
                $debit = $acc->amount ?? 0;
                $credit = 0;
                $color = 'danger';
                $icon = 'heroicon-o-arrow-up-circle';
            }

            $entries[] = [
                'date' => $acc->created_at->format('Y-m-d H:i'),
                'display_date' => $acc->created_at->format('d M, Y'),
                'type' => 'Account',
                'description' => $desc,
                'debit' => $debit,
                'credit' => $credit,
                'color' => $color,
                'icon' => $icon,
                'status' => $acc->status ?? 'pending',
            ];
        }

        // ✅ তারিখের উপর ভিত্তি করে ডিসেন্ডিং অর্ডারে সর্ট (নতুন ডাটা উপরে)
        usort($entries, fn ($a, $b) => strtotime($b['date']) <=> strtotime($a['date']));

        // ✅ Running balance এবং টোটাল ক্যালকুলেশন
        $balance = 0;
        $totalDebit = 0;
        $totalCredit = 0;
        
        foreach ($entries as &$entry) {
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
            $entry['balance_color'] = $balance >= 0 ? 'success' : 'danger';
        }

        $this->ledgerEntries = $entries;
        $this->totalDebit = $totalDebit;
        $this->totalCredit = $totalCredit;
        $this->currentBalance = $balance;
    }

    /**
     * Visa-র জন্য বর্ণনা তৈরি
     */
    private function getVisaDescription($visa): string
    {
        $description = "Visa #" . ($visa->id ?? 'N/A');
        
        if ($visa->passport ?? false) {
            $description .= " - Passport: " . $visa->passport;
        }
        
        if ($visa->visa_condition ?? false) {
            $description .= " ({$visa->visa_condition})";
        }
        
        return $description;
    }

    /**
     * Account-র জন্য বর্ণনা তৈরি
     */
    private function getAccountDescription($account): string
    {
        $type = match ($account->transaction_type ?? '') {
            'deposit' => 'Deposit',
            'withdrawal' => 'Withdrawal',
            'refund' => 'Refund',
            default => 'Transaction',
        };

        $description = "{$type}";
        
        if ($account->transaction_id ?? false) {
            $description .= " #" . $account->transaction_id;
        }
        
        if ($account->payment_method ?? false) {
            $description .= " via " . ucfirst($account->payment_method);
        }
        
        if ($account->receipt_number ?? false) {
            $description .= " (Receipt: {$account->receipt_number})";
        }

        return $description;
    }

    /**
     * টেবিলের জন্য কলাম ডেফিনিশন
     */
    public function getTableColumns(): array
    {
        return [
            [
                'name' => 'date',
                'label' => 'Date',
                'sortable' => true,
            ],
            [
                'name' => 'type',
                'label' => 'Type',
                'badge' => true,
                'colors' => [
                    'Visa' => 'warning',
                    'Account' => 'info',
                ],
            ],
            [
                'name' => 'description',
                'label' => 'Description',
            ],
            [
                'name' => 'debit',
                'label' => 'Debit (৳)',
                'format' => 'currency',
                'color' => 'danger',
            ],
            [
                'name' => 'credit',
                'label' => 'Credit (৳)',
                'format' => 'currency',
                'color' => 'success',
            ],
            [
                'name' => 'balance',
                'label' => 'Balance (৳)',
                'format' => 'currency',
                'color' => fn($record) => $record['balance'] >= 0 ? 'success' : 'danger',
            ],
        ];
    }

    /**
     * সামারি তথ্য
     */
    public function getSummary(): array
    {
        return [
            'total_debit' => [
                'label' => 'Total Debit',
                'value' => $this->totalDebit,
                'color' => 'danger',
                'icon' => 'heroicon-o-arrow-up-circle',
            ],
            'total_credit' => [
                'label' => 'Total Credit',
                'value' => $this->totalCredit,
                'color' => 'success',
                'icon' => 'heroicon-o-arrow-down-circle',
            ],
            'current_balance' => [
                'label' => 'Current Balance',
                'value' => $this->currentBalance,
                'color' => $this->currentBalance >= 0 ? 'success' : 'danger',
                'icon' => $this->currentBalance >= 0 ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle',
            ],
        ];
    }
}