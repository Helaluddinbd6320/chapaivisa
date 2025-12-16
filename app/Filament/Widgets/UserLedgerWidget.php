<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Visa;
use App\Models\Account;

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

        try {
            // ✅ Visa ডাটা লোড করুন - user_id এর উপর ভিত্তি করে
            $visas = Visa::where('user_id', $user->id)
                        ->whereNotNull('visa_cost')
                        ->where('visa_cost', '>', 0)
                        ->latest()
                        ->get();

            foreach ($visas as $visa) {
                $entries[] = [
                    'date' => $visa->created_at->format('Y-m-d H:i'),
                    'display_date' => $visa->created_at->format('d M, Y'),
                    'type' => 'Visa',
                    'description' => $this->getVisaDescription($visa),
                    'debit' => (float) $visa->visa_cost,
                    'credit' => 0,
                    'color' => 'danger',
                    'icon' => 'heroicon-m-document-text',
                ];
            }

            // ✅ Account ডাটা লোড করুন - user_id এর উপর ভিত্তি করে
            $accounts = Account::where('user_id', $user->id)
                            ->whereNotNull('amount')
                            ->where('amount', '>', 0)
                            ->latest()
                            ->get();

            foreach ($accounts as $acc) {
                $desc = $this->getAccountDescription($acc);
                
                if ($acc->transaction_type === 'deposit') {
                    $debit = 0;
                    $credit = (float) $acc->amount;
                    $color = 'success';
                    $icon = 'heroicon-m-arrow-down-circle';
                } else {
                    $debit = (float) $acc->amount;
                    $credit = 0;
                    $color = 'danger';
                    $icon = 'heroicon-m-arrow-up-circle';
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

        } catch (\Exception $e) {
            \Log::error('Ledger Widget Error: ' . $e->getMessage());
            $this->ledgerEntries = [];
        }
    }

    /**
     * Visa-র জন্য বর্ণনা তৈরি
     */
    private function getVisaDescription($visa): string
    {
        $description = "Visa";
        
        if (!empty($visa->name)) {
            $description .= " - " . $visa->name;
        }
        
        if (!empty($visa->passport)) {
            $description .= " (Passport: " . $visa->passport . ")";
        }
        
        if (!empty($visa->visa_condition)) {
            $condition = match ($visa->visa_condition) {
                'only_visa' => 'Only Visa',
                'visa_processing' => 'Visa + Processing',
                'only_processing' => 'Only Processing',
                'full_package' => 'Full Package',
                default => $visa->visa_condition,
            };
            $description .= " - " . $condition;
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
        
        if (!empty($account->transaction_id)) {
            $description .= " #" . substr($account->transaction_id, 0, 8);
        }
        
        if (!empty($account->payment_method)) {
            $method = match ($account->payment_method) {
                'cash' => 'Cash',
                'bank' => 'Bank',
                'mobile_banking' => 'Mobile Banking',
                'card' => 'Card',
                default => ucfirst($account->payment_method),
            };
            $description .= " via " . $method;
        }
        
        if (!empty($account->status) && $account->status !== 'verified') {
            $description .= " [" . ucfirst($account->status) . "]";
        }

        return $description;
    }
}