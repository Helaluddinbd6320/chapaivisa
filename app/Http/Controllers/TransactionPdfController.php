<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class TransactionPdfController extends Controller
{
    public function download(User $user)
    {
        $entries = [];

        // Visas → Debit
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

        // Accounts → Credit / Debit
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

        // Sort by date ascending
        usort($entries, fn ($a, $b) => $a['date'] <=> $b['date']);

        // Calculate balance
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }

        // Reverse for newest first
        $entries = array_reverse($entries);

        $settings = app('settings');

        $pdf = app('dompdf.wrapper')->loadView(
            'pdf.transaction-history',
            compact('user', 'entries', 'settings')
        );

        return $pdf->stream('transaction-history-'.$user->id.'.pdf');
    }
}
