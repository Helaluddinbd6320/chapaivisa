<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TransactionPdfController extends Controller
{
    public function download(User $user)
    {
        $authUser = Auth::user();

        // শুধুমাত্র নিজের ডাটা অথবা admin/manager দেখতে পারবে
        if ($authUser->id !== $user->id && ! $authUser->hasAnyRole(['super_admin', 'admin', 'manager'])) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access.');
        }

        $entries = [];

        // Visa condition mapping
        $visaTypes = [
            'only_visa' => 'Only Visa',
            'visa_processing' => 'Visa + Processing',
            'only_processing' => 'Only Processing',
            'full_package' => 'Full Package',
        ];

        // Visa Transactions
        foreach ($user->visas as $visa) {
            $entries[] = [
                'date' => $visa->updated_at,
                'type' => 'Visa',
                'description' => $visaTypes[$visa->visa_condition] ?? $visa->visa_condition,
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

        // Debit & Credit = 0 entries উপরে, বাকি updated_at descending
        usort($entries, function($a, $b) {
            $aZero = $a['debit'] == 0 && $a['credit'] == 0;
            $bZero = $b['debit'] == 0 && $b['credit'] == 0;

            if ($aZero && !$bZero) return -1;
            if (!$aZero && $bZero) return 1;

            return $b['date']->timestamp <=> $a['date']->timestamp;
        });

        // Balance নিচ থেকে ওপরে হিসাব
        $balance = 0;
        for ($i = count($entries) - 1; $i >= 0; $i--) {
            $entry = &$entries[$i];
            $balance += $entry['credit'] - $entry['debit'];
            $entry['balance'] = $balance;
        }
        unset($entry);

        // Office settings
        $settings = Setting::first();

        $date = now()->format('d-F-Y');
        $userName = Str::slug($user->name ?? 'user');
        $fileName = "{$userName}-transaction-history-{$date}.pdf";

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
