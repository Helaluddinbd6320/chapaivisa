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

    protected static string $view = 'filament.widgets.user-ledger-widget';

    public $ledgerEntries = [];
    public $shouldShowWidget = false;
    public $totalDebit = 0;
    public $totalCredit = 0;
    public $currentBalance = 0;
    public $startingBalance = 0;

    public function mount(): void
    {
        $user = Auth::user();
        $this->shouldShowWidget = ! $user->hasAnyRole(['super_admin', 'admin', 'manager']);

        if (! $this->shouldShowWidget) {
            return;
        }

        $entries = [];

        try {
            // Visa ডাটা লোড করুন - পুরাতন থেকে নতুন
            $visas = Visa::where('user_id', $user->id)
                        ->whereNotNull('visa_cost')
                        ->where('visa_cost', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

            foreach ($visas as $visa) {
                $entries[] = [
                    'date' => $visa->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $visa->created_at->timestamp,
                    'display_date' => $visa->created_at->format('d M, Y'),
                    'type' => 'Visa',
                    'description' => $this->getVisaDescription($visa),
                    'debit' => (float) $visa->visa_cost,
                    'credit' => 0,
                    'color' => 'danger',
                    'icon' => 'heroicon-m-document-text',
                    'raw_date' => $visa->created_at,
                ];
            }

            // Account ডাটা লোড করুন - পুরাতন থেকে নতুন
            $accounts = Account::where('user_id', $user->id)
                            ->whereNotNull('amount')
                            ->where('amount', '>', 0)
                            ->orderBy('created_at', 'asc')
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
                    'date' => $acc->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $acc->created_at->timestamp,
                    'display_date' => $acc->created_at->format('d M, Y'),
                    'type' => 'Account',
                    'description' => $desc,
                    'debit' => $debit,
                    'credit' => $credit,
                    'color' => $color,
                    'icon' => $icon,
                    'status' => $acc->status ?? 'pending',
                    'raw_date' => $acc->created_at,
                ];
            }

            // তারিখের উপর ভিত্তি করে পুরাতন থেকে নতুন ক্রমে সর্ট
            usort($entries, fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);

            // ✅ সঠিক ব্যালেন্স ক্যালকুলেশন - নিচ থেকে ওপরে (পুরুন থেকে নতুন)
            $balance = 0;
            $totalDebit = 0;
            $totalCredit = 0;
            $runningBalances = [];
            
            // প্রথমে সব ট্রানজেকশন প্রসেস করুন (পুরাতন থেকে নতুন)
            foreach ($entries as $entry) {
                $totalDebit += $entry['debit'];
                $totalCredit += $entry['credit'];
                $balance += $entry['credit'] - $entry['debit'];
                // প্রতিটি এন্ট্রির জন্য ব্যালেন্স সেভ করুন
                $entry['balance'] = $balance;
                $entry['balance_color'] = $balance >= 0 ? 'success' : 'danger';
                $runningBalances[] = $entry;
            }
            
            // প্রথম ব্যালেন্স (সর্বপ্রথম লেনদেনের পরের ব্যালেন্স)
            $this->startingBalance = count($runningBalances) > 0 ? $runningBalances[0]['balance'] : 0;
            
            // শেষ ব্যালেন্স (সর্বশেষ লেনদেনের পরের ব্যালেন্স)
            $this->currentBalance = $balance;
            
            // এন্ট্রিগুলো রিভার্স করুন যাতে নতুন গুলো উপরে দেখায়
            $reversedEntries = array_reverse($runningBalances);
            
            // রিভার্স করার পর ব্যালেন্সগুলো সঠিক রাখুন
            foreach ($reversedEntries as &$entry) {
                $entry['is_first'] = false;
                $entry['is_last'] = false;
            }
            
            // প্রথম এবং শেষ এন্ট্রি চিহ্নিত করুন
            if (count($reversedEntries) > 0) {
                $reversedEntries[0]['is_first'] = true; // সর্বশেষ লেনদেন
                $reversedEntries[count($reversedEntries)-1]['is_last'] = true; // প্রথম লেনদেন
            }

            $this->ledgerEntries = $reversedEntries;
            $this->totalDebit = $totalDebit;
            $this->totalCredit = $totalCredit;

        } catch (\Exception $e) {
            \Log::error('Ledger Widget Error: ' . $e->getMessage());
            $this->ledgerEntries = [];
        }
    }

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
            $description .= " #" . $account->transaction_id;
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