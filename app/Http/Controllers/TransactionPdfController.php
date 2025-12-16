<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\AppSettings;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionPdfController extends Controller
{
    public function download(User $user)
    {
        $entries = [];

        // -------------------------
        // Visas → Debit
        // -------------------------
        foreach ($user->visas as $visa) {
            $entries[] = [
                'date' => $visa->created_at->format('Y-m-d'),
                'datetime' => $visa->created_at,
                'type' => 'Visa',
                'description' => $visa->visa_condition,
                'debit' => (float) $visa->visa_cost,
                'credit' => 0,
            ];
        }

        // -------------------------
        // Accounts → Debit / Credit
        // -------------------------
        foreach ($user->accounts as $acc) {
            $description = match ($acc->transaction_type) {
                'deposit' => 'Deposit',
                'withdrawal' => 'Withdrawal',
                'refund' => 'Refund',
                default => ucfirst($acc->transaction_type),
            };

            if ($acc->transaction_type === 'deposit') {
                $debit = 0;
                $credit = (float) $acc->amount;
            } else {
                $debit = (float) $acc->amount;
                $credit = 0;
            }

            $entries[] = [
                'date' => $acc->created_at->format('Y-m-d'),
                'datetime' => $acc->created_at,
                'type' => 'Account',
                'description' => $description,
                'debit' => $debit,
                'credit' => $credit,
            ];
        }

        // -------------------------
        // Sort old → new
        // -------------------------
        usort($entries, fn ($a, $b) => $a['datetime'] <=> $b['datetime']);

        // -------------------------
        // Running balance
        // -------------------------
        $balance = 0;
        foreach ($entries as &$entry) {
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }

        // New → old
        $entries = array_reverse($entries);

        // -------------------------
        // Settings info
        // -------------------------
        $settings = AppSettings::info();

        $pdf = Pdf::loadView('pdf.transaction-history', [
            'user'     => $user,
            'ledger'   => $entries,
            'settings' => $settings,
        ]);

        return $pdf->stream(
            'transaction-history-'.$user->id.'.pdf'
        );
    }
}
