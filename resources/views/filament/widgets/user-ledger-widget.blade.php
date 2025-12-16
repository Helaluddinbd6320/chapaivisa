@php
use Illuminate\Support\Facades\Auth;
use App\Models\Visa;
use App\Models\Account;

$user = Auth::user();
$shouldShowWidget = !$user->hasAnyRole(['super_admin', 'admin', 'manager']);

if (!$shouldShowWidget) {
    return;
}

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
    $entries[] = [
        'date' => $visa->created_at->format('Y-m-d H:i:s'),
        'timestamp' => $visa->created_at->timestamp,
        'display_date' => $visa->created_at->format('d M, Y'),
        'type' => 'Visa',
        'description' => $visa->name ? "Visa - {$visa->name}" : "Visa",
        'debit' => (float) $visa->visa_cost,
        'credit' => 0,
        'raw_date' => $visa->created_at,
    ];
}

// Account ডাটা
$accounts = Account::where('user_id', $user->id)
                ->whereNotNull('amount')
                ->where('amount', '>', 0)
                ->orderBy('created_at', 'asc')
                ->get();

foreach ($accounts as $account) {
    $type = match ($account->transaction_type) {
        'deposit' => 'Deposit',
        'withdrawal' => 'Withdrawal',
        'refund' => 'Refund',
        default => 'Transaction',
    };
    
    $description = $type;
    if ($account->transaction_id) {
        $description .= " #{$account->transaction_id}";
    }
    
    if ($account->transaction_type === 'deposit') {
        $debit = 0;
        $credit = (float) $account->amount;
    } else {
        $debit = (float) $account->amount;
        $credit = 0;
    }
    
    $entries[] = [
        'date' => $account->created_at->format('Y-m-d H:i:s'),
        'timestamp' => $account->created_at->timestamp,
        'display_date' => $account->created_at->format('d M, Y'),
        'type' => 'Account',
        'description' => $description,
        'debit' => $debit,
        'credit' => $credit,
        'raw_date' => $account->created_at,
    ];
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
$ledgerEntries = array_reverse($runningBalances);
$startingBalance = count($runningBalances) > 0 ? $runningBalances[0]['balance'] : 0;
$currentBalance = $balance;

if (count($ledgerEntries) > 0) {
    $ledgerEntries[0]['is_first'] = true;
    $ledgerEntries[count($ledgerEntries)-1]['is_last'] = true;
}
@endphp

@if($shouldShowWidget && count($ledgerEntries) > 0)
<div class="fi-wi-user-ledger-widget">
    <!-- আপনার HTML কনটেন্ট এখানে -->
    <div class="section-1-full-width">
        <div class="section-1-header">
            <div class="ledger-title-section">
                <div class="ledger-icon">
                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div class="ledger-title">
                    <h2 class="ledger-heading">My Ledger</h2>
                    <p class="ledger-subtitle">
                        {{ count($ledgerEntries) }} transactions • {{ now()->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <div class="stats-cards-row">
                <div class="stat-card net-balance-card">
                    <div class="stat-card-content">
                        <div class="stat-card-text">
                            <p class="stat-label">Current Balance</p>
                            <p class="stat-value {{ $currentBalance >= 0 ? 'positive' : 'negative' }}">
                                {{ number_format($currentBalance, 0) }} ৳
                            </p>
                        </div>
                        <div class="stat-card-icon">
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card credit-card">
                    <div class="stat-card-content">
                        <div class="stat-card-text">
                            <p class="stat-label">Total Credit</p>
                            <p class="stat-value credit">
                                {{ number_format($totalCredit, 0) }} ৳
                            </p>
                        </div>
                        <div class="stat-card-icon">
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="stat-card debit-card">
                    <div class="stat-card-content">
                        <div class="stat-card-text">
                            <p class="stat-label">Total Debit</p>
                            <p class="stat-value debit">
                                {{ number_format($totalDebit, 0) }} ৳
                            </p>
                        </div>
                        <div class="stat-card-icon">
                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction History Section -->
    <div class="section-2-full-width transaction-section">
        <div class="transaction-header">
            <h3>Transaction History</h3>
            <span class="transaction-count">{{ count($ledgerEntries) }} entries</span>
        </div>

        <div class="transaction-table-container">
            <div class="transaction-table-scroll">
                <table class="transaction-table">
                    <thead>
                        <tr>
                            <th class="date-col">Date</th>
                            <th class="type-col">Type</th>
                            <th class="desc-col">Description</th>
                            <th class="amount-col">Amount</th>
                            <th class="balance-col">Balance</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ledgerEntries as $row)
                            @php
                                $date = $row['date'] ?? now();
                                $carbonDate = \Carbon\Carbon::parse($date);
                                $balance = $row['balance'] ?? 0;
                                $balanceClass = $balance >= 0 ? 'balance-positive' : 'balance-negative';
                            @endphp
                            <tr class="transaction-row {{ $row['is_first'] ?? false ? 'first-row' : '' }}">
                                <td class="date-cell">
                                    <span class="date-main">{{ $carbonDate->format('M d') }}</span>
                                    <span class="date-year">{{ $carbonDate->format('Y') }}</span>
                                    @if($row['is_first'] ?? false)
                                        <span class="date-badge latest-badge">Latest</span>
                                    @endif
                                </td>
                                <td class="type-cell">
                                    <div class="type-content">
                                        <span class="type-icon {{ $row['type'] === 'Visa' ? 'visa-icon' : 'account-icon' }}">
                                            @if($row['type'] === 'Visa')
                                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @else
                                                <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                            @endif
                                        </span>
                                        <span class="type-text">{{ $row['type'] }}</span>
                                    </div>
                                </td>
                                <td class="desc-cell">
                                    <div class="desc-content" title="{{ $row['description'] ?? '' }}">
                                        {{ $row['description'] ?? '' }}
                                    </div>
                                </td>
                                <td class="amount-cell">
                                    @if(($row['debit'] ?? 0) > 0)
                                        <div class="amount-badge debit-badge">
                                            -{{ number_format($row['debit'], 0) }} ৳
                                        </div>
                                    @elseif(($row['credit'] ?? 0) > 0)
                                        <div class="amount-badge credit-badge">
                                            +{{ number_format($row['credit'], 0) }} ৳
                                        </div>
                                    @endif
                                </td>
                                <td class="balance-cell">
                                    <span class="balance-amount {{ $balanceClass }}">
                                        {{ number_format($balance, 0) }} ৳
                                        @if($row['is_first'] ?? false)
                                            <span class="balance-indicator current-indicator">Current</span>
                                        @endif
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* আপনার CSS এখানে */
</style>
@endif