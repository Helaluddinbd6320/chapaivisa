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
                'date' => $visa->created_at,
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
                'date' => $acc->created_at,
                'type' => 'Account',
                'description' => ucfirst($acc->transaction_type),
                'name' => '—',
                'passport' => '—',
                'debit' => $acc->transaction_type === 'deposit' ? 0 : $acc->amount,
                'credit' => $acc->transaction_type === 'deposit' ? $acc->amount : 0,
            ];
        }

        // Sort by date
        usort($entries, fn ($a, $b) => $a['date'] <=> $b['date']);

        // Running balance
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }
        unset($entry);

        // Latest first
        $entries = array_reverse($entries);

        // Office settings
        $settings = Setting::first();

        // Date format: 20-December-2025
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

        // ⬇️ Direct download (NO save)
        return $pdf->download($fileName);
    }
}
