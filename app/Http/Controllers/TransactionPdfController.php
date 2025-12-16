<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting; // Setting Model
use Illuminate\Http\Request;

class TransactionPdfController extends Controller
{
    public function download(User $user)
    {
        $entries = [];

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

        usort($entries, fn($a, $b) => $a['date'] <=> $b['date']);

        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }
        $entries = array_reverse($entries);

        $settings = Setting::first(); // বা app('settings') যদি Filament ব্যবহার হয়

        $pdf = app('dompdf.wrapper')->loadView(
            'pdf.transaction-history',
            compact('user', 'entries', 'settings')
        );

        return $pdf->stream('transaction-history-'.$user->id.'.pdf');
    }
}
