<x-filament::widget>
    @if ($this->shouldShowWidget)
        <div class="fi-wi-user-ledger-widget">
            <!-- SECTION 1: My Ledger + Stats Cards (Full Width) -->
            <div class="section-1-full-width">
                <div class="section-1-header">
                    <!-- My Ledger Title -->
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

                    <!-- Compact Stats Cards -->
                    @if (!empty($this->ledgerEntries))
                        @php
                            $totalDebit = $this->totalDebit;
                            $totalCredit = $this->totalCredit;
                            $netBalance = $this->currentBalance; // সর্বশেষ ব্যালেন্স
                            $startingBalance = $this->startingBalance; // প্রারম্ভিক ব্যালেন্স
                        @endphp

                        <div class="stats-cards-row">
                            <!-- Current Balance -->
                            <div class="stat-card net-balance-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-text">
                                        <p class="stat-label">Current Balance</p>
                                        <p class="stat-value {{ $netBalance >= 0 ? 'positive' : 'negative' }}">
                                            {{ number_format($netBalance, 0) }} ৳
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

                            <!-- Total Income -->
                            <div class="stat-card credit-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-text">
                                        <p class="stat-label">Total Income</p>
                                        <p class="stat-value credit">
                                            {{ number_format($totalCredit, 0) }} ৳
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

                            <!-- Total Expense -->
                            <div class="stat-card debit-card">
                                <div class="stat-card-content">
                                    <div class="stat-card-text">
                                        <p class="stat-label">Total Expense</p>
                                        <p class="stat-value debit">
                                            {{ number_format($totalDebit, 0) }} ৳
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

            <!-- SECTION 2: Transaction History (Full Width) -->
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
                                            $isFirst = $index === 0;
                                            $isLast = $index === count($this->ledgerEntries) - 1;
                                        @endphp
                                        <tr class="transaction-row {{ $isFirst ? 'first-row' : '' }} {{ $isLast ? 'last-row' : '' }}">
                                            <td class="date-cell">
                                                <span class="date-main">{{ $carbonDate->format('M d') }}</span>
                                                <span class="date-year">{{ $carbonDate->format('Y') }}</span>
                                                @if($isFirst)
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
                                                        <svg class="h-2 w-2 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                        </svg>
                                                        -{{ number_format($row['debit'], 0) }} ৳
                                                    </div>
                                                @elseif(($row['credit'] ?? 0) > 0)
                                                    <div class="amount-badge credit-badge">
                                                        <svg class="h-2 w-2 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                        </svg>
                                                        +{{ number_format($row['credit'], 0) }} ৳
                                                    </div>
                                                @else
                                                    <div class="amount-badge zero-badge">
                                                        0 ৳
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="balance-cell">
                                                @php
                                                    $balance = $row['balance'] ?? 0;
                                                    $balanceClass = $balance >= 0 ? 'balance-positive' : 'balance-negative';
                                                @endphp
                                                <span class="balance-amount {{ $balanceClass }}">
                                                    {{ number_format($balance, 0) }} ৳
                                                    @if($isFirst)
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
                    @if(count($this->ledgerEntries) > 1)
                        <div class="balance-timeline mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center justify-between text-sm">
                                <div class="text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Starting Balance:</span>
                                    <span class="ml-2 {{ $startingBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($startingBalance, 0) }} ৳
                                    </span>
                                </div>
                                <div class="text-gray-500">→</div>
                                <div class="text-gray-600 dark:text-gray-400">
                                    <span class="font-medium">Current Balance:</span>
                                    <span class="ml-2 {{ $netBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($netBalance, 0) }} ৳
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <style>
            /* ===== BASE STYLES ===== */
            .fi-wi-user-ledger-widget {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            /* ===== SECTION 1: FULL WIDTH ===== */
            .section-1-full-width {
                width: 100%;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                padding: 1rem;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            .section-1-header {
                width: 100%;
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            @media (min-width: 768px) {
                .section-1-header {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                }
            }

            /* My Ledger Title */
            .ledger-title-section {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                flex: 1;
                min-width: 0;
            }

            .ledger-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 36px;
                height: 36px;
                background: linear-gradient(135deg, #3b82f6, #1d4ed8);
                border-radius: 8px;
                color: white;
                flex-shrink: 0;
                box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
            }

            .ledger-title {
                min-width: 0;
            }

            .ledger-heading {
                font-size: 1.25rem;
                font-weight: 700;
                color: #111827;
                line-height: 1.2;
            }

            .ledger-subtitle {
                font-size: 0.8125rem;
                color: #6b7280;
                margin-top: 0.25rem;
                white-space: nowrap;
            }

            /* Stats Cards Row */
            .stats-cards-row {
                display: flex;
                gap: 0.75rem;
                flex: 2;
                min-width: 0;
            }

            @media (max-width: 767px) {
                .stats-cards-row {
                    overflow-x: auto;
                    padding-bottom: 4px;
                    margin-top: 0.5rem;
                }

                .stats-cards-row::-webkit-scrollbar {
                    display: none;
                }
            }

            .stat-card {
                flex: 1;
                min-width: 140px;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                padding: 0.75rem;
                transition: all 0.2s ease;
                box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            }

            .stat-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
                border-color: #d1d5db;
            }

            .stat-card-content {
                display: flex;
                align-items: center;
                justify-content: space-between;
                height: 100%;
            }

            .stat-card-text {
                min-width: 0;
            }

            .stat-label {
                font-size: 0.75rem;
                font-weight: 600;
                color: #6b7280;
                margin-bottom: 0.25rem;
                text-transform: uppercase;
                letter-spacing: 0.025em;
            }

            .stat-value {
                font-size: 1rem;
                font-weight: 800;
                line-height: 1.2;
            }

            .stat-value.positive {
                color: #059669;
            }

            .stat-value.negative {
                color: #dc2626;
            }

            .stat-value.credit {
                color: #1d4ed8;
            }

            .stat-value.debit {
                color: #b91c1c;
            }

            .stat-card-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 28px;
                height: 28px;
                background: #f8fafc;
                border-radius: 6px;
                flex-shrink: 0;
                margin-left: 0.5rem;
                border: 1px solid #e2e8f0;
            }

            .net-balance-card .stat-card-icon {
                color: #3b82f6;
                background: #eff6ff;
                border-color: #dbeafe;
            }

            .credit-card .stat-card-icon {
                color: #1d4ed8;
                background: #eff6ff;
                border-color: #dbeafe;
            }

            .debit-card .stat-card-icon {
                color: #b91c1c;
                background: #fef2f2;
                border-color: #fee2e2;
            }

            /* ===== SECTION 2: FULL WIDTH ===== */
            .section-2-full-width {
                width: 100%;
                background: white;
                border: 1px solid #e5e7eb;
                border-radius: 10px;
                overflow: hidden;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            }

            /* Empty State */
            .empty-state {
                padding: 3rem 1rem;
                text-align: center;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                min-height: 200px;
            }

            .empty-icon {
                width: 56px;
                height: 56px;
                background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin-bottom: 1.25rem;
                color: #9ca3af;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            }

            .empty-state h3 {
                font-size: 1rem;
                font-weight: 600;
                color: #1f2937;
                margin-bottom: 0.5rem;
            }

            .empty-state p {
                font-size: 0.875rem;
                color: #6b7280;
                max-width: 320px;
                line-height: 1.5;
                margin: 0 auto;
            }

            /* Transaction Section */
            .transaction-section {
                padding: 1.25rem;
            }

            .transaction-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 1rem;
                padding-bottom: 0.75rem;
                border-bottom: 1px solid #f3f4f6;
            }

            .transaction-header h3 {
                font-size: 1rem;
                font-weight: 700;
                color: #111827;
            }

            .transaction-count {
                font-size: 0.8125rem;
                font-weight: 500;
                color: #6b7280;
                background: #f3f4f6;
                padding: 0.25rem 0.75rem;
                border-radius: 16px;
            }

            /* Table Container */
            .transaction-table-container {
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                overflow: hidden;
                background: white;
                position: relative;
            }

            .transaction-table-scroll {
                max-height: 300px;
                overflow-y: auto;
            }

            /* Smooth Scrollbar */
            .transaction-table-scroll {
                scrollbar-width: thin;
                scrollbar-color: #cbd5e1 #f8fafc;
            }

            .transaction-table-scroll::-webkit-scrollbar {
                width: 8px;
            }

            .transaction-table-scroll::-webkit-scrollbar-track {
                background: #f8fafc;
                border-radius: 4px;
            }

            .transaction-table-scroll::-webkit-scrollbar-thumb {
                background-color: #cbd5e1;
                border-radius: 4px;
                border: 2px solid #f8fafc;
            }

            .transaction-table-scroll::-webkit-scrollbar-thumb:hover {
                background-color: #94a3b8;
            }

            /* Table Styles */
            .transaction-table {
                width: 100%;
                font-size: 0.875rem;
                border-collapse: collapse;
                min-width: 600px;
            }

            .transaction-table thead {
                background: linear-gradient(to right, #f8fafc, #f1f5f9);
                position: sticky;
                top: 0;
                z-index: 10;
            }

            .transaction-table th {
                padding: 0.875rem 1rem;
                text-align: left;
                font-weight: 700;
                color: #4b5563;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                font-size: 0.75rem;
                border-bottom: 2px solid #e5e7eb;
                white-space: nowrap;
            }

            .transaction-table tbody tr {
                border-bottom: 1px solid #f3f4f6;
                transition: all 0.15s ease;
            }

            .transaction-table tbody tr:last-child {
                border-bottom: 0;
            }

            .transaction-table tbody tr:hover {
                background-color: #f9fafb;
                transform: translateX(2px);
            }

            .first-row {
                background: linear-gradient(to right, #f0f9ff, #e0f2fe);
                border-left: 3px solid #3b82f6;
            }

            .last-row {
                border-bottom: 2px solid #e5e7eb;
            }

            .transaction-table td {
                padding: 0.875rem 1rem;
                vertical-align: middle;
            }

            /* Column Widths */
            .date-col {
                width: 12%;
            }

            .type-col {
                width: 15%;
            }

            .desc-col {
                width: 38%;
            }

            .amount-col {
                width: 20%;
            }

            .balance-col {
                width: 15%;
            }

            /* Cell Styles */
            .date-cell {
                white-space: nowrap;
                position: relative;
            }

            .date-main {
                display: block;
                font-size: 0.875rem;
                font-weight: 600;
                color: #1f2937;
            }

            .date-year {
                display: block;
                font-size: 0.75rem;
                color: #6b7280;
                margin-top: 0.125rem;
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

            .type-content {
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .type-icon {
                display: flex;
                align-items: center;
                justify-content: center;
                width: 20px;
                height: 20px;
                flex-shrink: 0;
            }

            .visa-icon {
                color: #3b82f6;
            }

            .account-icon {
                color: #059669;
            }

            .type-text {
                font-weight: 600;
                color: #374151;
            }

            .desc-content {
                color: #4b5563;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                cursor: help;
                padding-right: 0.5rem;
            }

            /* AMOUNT BADGE STYLES */
            .amount-badge {
                display: inline-flex;
                align-items: center;
                padding: 0.375rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8125rem;
                font-weight: 700;
                white-space: nowrap;
                transition: all 0.2s ease;
            }

            .debit-badge {
                background: linear-gradient(135deg, #fee2e2, #fecaca);
                color: #b91c1c;
                border: 1px solid #fca5a5;
            }

            .debit-badge:hover {
                background: linear-gradient(135deg, #fecaca, #fca5a5);
                transform: translateY(-1px);
            }

            .credit-badge {
                background: linear-gradient(135deg, #dcfce7, #bbf7d0);
                color: #166534;
                border: 1px solid #86efac;
            }

            .credit-badge:hover {
                background: linear-gradient(135deg, #bbf7d0, #86efac);
                transform: translateY(-1px);
            }

            .zero-badge {
                background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
                color: #6b7280;
                border: 1px solid #d1d5db;
            }

            .balance-amount {
                font-weight: 800;
                font-size: 0.875rem;
                white-space: nowrap;
                padding: 0.25rem 0.5rem;
                border-radius: 6px;
                display: inline-block;
                min-width: 80px;
                text-align: right;
                position: relative;
            }

            .balance-positive {
                color: #059669;
                background: #f0fdf4;
            }

            .balance-negative {
                color: #dc2626;
                background: #fef2f2;
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

            .scroll-indicator {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                padding: 0.75rem;
                background: linear-gradient(to right, #f8fafc, #f1f5f9);
                border-top: 1px solid #e5e7eb;
                font-size: 0.8125rem;
                font-weight: 500;
                color: #6b7280;
            }

            /* Balance Timeline */
            .balance-timeline {
                border: 1px solid #e5e7eb;
            }

            /* ===== RESPONSIVE ADJUSTMENTS ===== */
            @media (max-width: 1024px) {
                .stat-card {
                    min-width: 130px;
                }

                .transaction-table {
                    min-width: 800px;
                }
            }

            @media (max-width: 768px) {
                .section-1-full-width,
                .section-2-full-width {
                    padding: 0.875rem;
                }

                .ledger-heading {
                    font-size: 1.125rem;
                }

                .stat-card {
                    min-width: 120px;
                    padding: 0.625rem;
                }

                .stat-value {
                    font-size: 0.9375rem;
                }

                .transaction-section {
                    padding: 1rem;
                }

                .transaction-table-scroll {
                    max-height: 280px;
                }

                /* Smaller amount badges on mobile */
                .amount-badge {
                    padding: 0.25rem 0.5rem;
                    font-size: 0.75rem;
                }
            }

            @media (max-width: 640px) {
                .fi-wi-user-ledger-widget {
                    gap: 0.75rem;
                }

                .section-1-full-width,
                .section-2-full-width {
                    padding: 0.75rem;
                    border-radius: 8px;
                }

                .ledger-icon {
                    width: 32px;
                    height: 32px;
                }

                .ledger-heading {
                    font-size: 1rem;
                }

                .ledger-subtitle {
                    font-size: 0.75rem;
                }

                .stat-card {
                    min-width: 110px;
                    padding: 0.5rem;
                }

                .stat-label {
                    font-size: 0.7rem;
                }

                .stat-value {
                    font-size: 0.875rem;
                }

                .stat-card-icon {
                    width: 24px;
                    height: 24px;
                }

                .transaction-table-scroll {
                    max-height: 250px;
                }

                .transaction-table th,
                .transaction-table td {
                    padding: 0.75rem 0.875rem;
                }

                /* Even smaller amount badges on small mobile */
                .amount-badge {
                    padding: 0.125rem 0.375rem;
                    font-size: 0.7rem;
                }
            }

            @media (max-width: 480px) {
                .section-1-header {
                    gap: 0.75rem;
                }

                .stats-cards-row {
                    gap: 0.5rem;
                }

                .stat-card {
                    min-width: 100px;
                }

                .transaction-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 0.5rem;
                }

                .transaction-count {
                    align-self: flex-start;
                }

                /* Compact amount badges for very small screens */
                .amount-badge {
                    padding: 0.125rem 0.25rem;
                    font-size: 0.65rem;
                }
            }
        </style>
    @endif
</x-filament::widget>