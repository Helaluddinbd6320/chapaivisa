<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;

class TransactionPdfController extends Controller
{
    public function download(User $user)
    {
        $entries = [];

        // Visa Transactions
        foreach ($user->visas as $visa) {
            $entries[] = [
                'date' => $visa->updated_at,
                'type' => 'Visa',
                'description' => $visa->visa_condition,
                'name' => $visa->name ?? 'N/A',
                'passport' => $visa->passport ?? 'N/A',
                'debit' => $visa->visa_cost,
                'credit' => 0,
            ];
        }

        // Account Transactions
        foreach ($user->accounts as $acc) {
            $entries[] = [
                'date' => $acc->updated_at,
                'type' => 'Account',
                'description' => ucfirst($acc->transaction_type),
                'name' => '—',
                'passport' => '—',
                'debit' => $acc->transaction_type === 'deposit' ? 0 : $acc->amount,
                'credit' => $acc->transaction_type === 'deposit' ? $acc->amount : 0,
            ];
        }

        // ✅ Debit & Credit = 0 entries উপরে, বাকি updated_at descending
        usort($entries, function($a, $b) {
            $aZero = $a['debit'] == 0 && $a['credit'] == 0;
            $bZero = $b['debit'] == 0 && $b['credit'] == 0;

            if ($aZero && !$bZero) return -1;
            if (!$aZero && $bZero) return 1;

            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        // ✅ Balance নিচ থেকে ওপরে হিসাব
        $balance = 0;
        for ($i = count($entries) - 1; $i >= 0; $i--) {
            $entry = &$entries[$i];
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }
        unset($entry);

        // Office settings
        $settings = Setting::first();

        // Date format for file name: 20-December-2025
        $date = now()->format('d-F-Y');

        // Safe user name for file
        $userName = Str::slug($user->name ?? 'user');

        // File name
        $fileName = "{$userName}-transaction-history-{$date}.pdf";

        // Generate PDF
        $pdf = app('dompdf.wrapper')
            ->loadView('pdf.transaction-history', [
                'user' => $user,
                'entries' => $entries,
                'settings' => $settings,
                'reportDate' => $date,
            ])
            ->setPaper('A4', 'portrait');

        return $pdf->download($fileName);
    }
}
