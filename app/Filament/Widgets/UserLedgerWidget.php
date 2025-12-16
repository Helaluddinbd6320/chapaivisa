<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\Auth;
use App\Models\Visa;
use App\Models\Account;

class UserLedgerWidget
{
    public static function make()
    {
        $user = Auth::user();
        
        // শুধুমাত্র নরমাল ইউজারদের জন্য
        if ($user->hasAnyRole(['super_admin', 'admin', 'manager'])) {
            return null;
        }

        // ডাটা প্রস্তুত করুন
        $data = self::prepareLedgerData($user);
        
        // ভিউ রিটার্ন করুন
        return view('filament.widgets.user-ledger-widget', $data);
    }

    private static function prepareLedgerData($user): array
    {
        $entries = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $balance = 0;

        // Visa ডাটা
        $visas = Visa::where('user_id', $user->id)
                    ->whereNotNull('visa_cost')
                    ->where('visa_cost', '>', 0)
                    ->orderBy('created_at', 'asc')
                    ->get();

        foreach ($visas as $visa) {
            $entries[] = self::createVisaEntry($visa);
        }

        // Account ডাটা
        $accounts = Account::where('user_id', $user->id)
                        ->whereNotNull('amount')
                        ->where('amount', '>', 0)
                        ->orderBy('created_at', 'asc')
                        ->get();

        foreach ($accounts as $account) {
            $entries[] = self::createAccountEntry($account);
        }

        // সর্ট করুন
        usort($entries, fn ($a, $b) => $a['timestamp'] <=> $b['timestamp']);

        // ব্যালেন্স ক্যালকুলেশন
        $runningBalances = [];
        foreach ($entries as $entry) {
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];
            $balance += $entry['credit'] - $entry['debit'];
            
            $entry['balance'] = $balance;
            $runningBalances[] = $entry;
        }

        // নতুন থেকে পুরাতন
        $reversedEntries = array_reverse($runningBalances);
        
        if (count($reversedEntries) > 0) {
            $reversedEntries[0]['is_first'] = true;
            $reversedEntries[count($reversedEntries)-1]['is_last'] = true;
        }

        return [
            'ledgerEntries' => $reversedEntries,
            'shouldShowWidget' => true,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'currentBalance' => $balance,
            'startingBalance' => count($runningBalances) > 0 ? $runningBalances[0]['balance'] : 0,
        ];
    }

    private static function createVisaEntry($visa): array
    {
        return [
            'date' => $visa->created_at->format('Y-m-d H:i:s'),
            'timestamp' => $visa->created_at->timestamp,
            'display_date' => $visa->created_at->format('d M, Y'),
            'type' => 'Visa',
            'description' => self::getVisaDescription($visa),
            'debit' => (float) $visa->visa_cost,
            'credit' => 0,
            'raw_date' => $visa->created_at,
        ];
    }

    private static function createAccountEntry($account): array
    {
        if ($account->transaction_type === 'deposit') {
            $debit = 0;
            $credit = (float) $account->amount;
        } else {
            $debit = (float) $account->amount;
            $credit = 0;
        }

        return [
            'date' => $account->created_at->format('Y-m-d H:i:s'),
            'timestamp' => $account->created_at->timestamp,
            'display_date' => $account->created_at->format('d M, Y'),
            'type' => 'Account',
            'description' => self::getAccountDescription($account),
            'debit' => $debit,
            'credit' => $credit,
            'status' => $account->status ?? 'pending',
            'raw_date' => $account->created_at,
        ];
    }

    private static function getVisaDescription($visa): string
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

    private static function getAccountDescription($account): string
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
        
        return $description;
    }
}