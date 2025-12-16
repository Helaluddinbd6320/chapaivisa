<x-filament::widget>
    @if ($this->shouldShowWidget)
        <div class="fi-wi-user-ledger-widget">
            <!-- SECTION 1: My Ledger + Stats Cards -->
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
                            @if (count($this->ledgerEntries) > 0)
                                <p class="ledger-subtitle">
                                    {{ count($this->ledgerEntries) }} transactions • Updated: {{ now()->format('M d, Y h:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if (!empty($this->ledgerEntries))
                        <div class="stats-cards-row">
                            <div class="stat-card net-balance-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-text">
                                        <p class="stat-label">Current Balance</p>
                                        <p class="stat-value {{ $this->currentBalance >= 0 ? 'positive' : 'negative' }}">
                                            {{ number_format($this->currentBalance, 0) }} ৳
                                        </p>
                                    </div>
                                    <div class="stat-card-icon">
                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
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
                                            {{ number_format($this->totalCredit, 0) }} ৳
                                        </p>
                                    </div>
                                    <div class="stat-card-icon">
                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
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
                                            {{ number_format($this->totalDebit, 0) }} ৳
                                        </p>
                                    </div>
                                    <div class="stat-card-icon">
                                        <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if (empty($this->ledgerEntries))
                <div class="section-2-full-width empty-state">
                    <div class="empty-icon">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.801 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.801 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                        </svg>
                    </div>
                    <h3>No transactions yet</h3>
                    <p>Your transaction history will appear here once you have transactions.</p>
                </div>
            @else
                <div class="section-2-full-width transaction-section">
                    <div class="transaction-header">
                        <h3>Transaction History (Newest First)</h3>
                        <span class="transaction-count">{{ count($this->ledgerEntries) }} entries</span>
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
                                    @foreach ($this->ledgerEntries as $index => $row)
                                        @php
                                            $date = $row['date'] ?? now();
                                            $carbonDate = \Carbon\Carbon::parse($date);
                                            $balance = $row['balance'] ?? 0;
                                            $balanceClass = $balance >= 0 ? 'balance-positive' : 'balance-negative';
                                        @endphp
                                        <tr class="transaction-row {{ $row['is_first'] ? 'first-row' : '' }}">
                                            <td class="date-cell">
                                                <span class="date-main">{{ $carbonDate->format('M d') }}</span>
                                                <span class="date-year">{{ $carbonDate->format('Y') }}</span>
                                                @if($row['is_first'])
                                                    <span class="date-badge latest-badge">Latest</span>
                                                @endif
                                            </td>
                                            <td class="type-cell">
                                                <div class="type-content">
                                                    <span class="type-icon {{ $row['type'] === 'Visa' ? 'visa-icon' : 'account-icon' }}">
                                                        @if ($row['type'] === 'Visa')
                                                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        @else
                                                            <svg class="h-2.5 w-2.5" fill="none" viewBox="0 0 24 24"
                                                                stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
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
                                                @if (($row['debit'] ?? 0) > 0)
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
                                                    @if($row['is_first'])
                                                        <span class="balance-indicator current-indicator">Current</span>
                                                    @endif
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if (count($this->ledgerEntries) > 6)
                            <div class="scroll-indicator">
                                <svg class="h-2 w-2 animate-pulse" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                Scroll for more
                            </div>
                        @endif
                    </div>
                    
                    <!-- Balance Timeline -->
                    @if(count($this->ledgerEntries) > 1 && isset($this->ledgerEntries[count($this->ledgerEntries)-1]))
                        @php
                            $firstEntry = $this->ledgerEntries[count($this->ledgerEntries)-1];
                            $firstBalance = $firstEntry['balance'] ?? 0;
                        @endphp
                        <div class="balance-timeline mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-between text-sm">
                                <div class="text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Starting Balance:</span>
                                    <span class="ml-2 {{ $firstBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($firstBalance, 0) }} ৳
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">({{ $firstEntry['display_date'] ?? '' }})</span>
                                </div>
                                <div class="text-gray-500">→</div>
                                <div class="text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Current Balance:</span>
                                    <span class="ml-2 {{ $this->currentBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($this->currentBalance, 0) }} ৳
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">(Today)</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <style>
            /* আপনার আগের CSS কোড এখানে যোগ করুন */
            /* শুধু নিচের নতুন স্টাইলগুলো যোগ করুন */

            .first-row {
                background: linear-gradient(to right, #f0f9ff, #e0f2fe);
                border-left: 3px solid #3b82f6;
            }

            .date-badge {
                position: absolute;
                top: -8px;
                right: -5px;
                font-size: 0.6rem;
                font-weight: 700;
                padding: 1px 4px;
                border-radius: 3px;
                background: #3b82f6;
                color: white;
            }

            .latest-badge {
                background: #059669;
            }

            .balance-indicator {
                position: absolute;
                top: -8px;
                right: -5px;
                font-size: 0.6rem;
                font-weight: 700;
                padding: 1px 4px;
                border-radius: 3px;
            }

            .current-indicator {
                background: #3b82f6;
                color: white;
            }

            .balance-timeline {
                border: 1px solid #e5e7eb;
            }

            .date-cell {
                position: relative;
            }

            .balance-amount {
                position: relative;
            }
        </style>
    @endif
</x-filament::widget>