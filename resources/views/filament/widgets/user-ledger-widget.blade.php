<x-filament::widget>
    @if ($this->shouldShowWidget)
        <div class="fi-wi-user-ledger-widget">
            <!-- SECTION 1: My Ledger + Stats Cards (Full Width) -->
            <div class="section-1-full-width">
                <!-- একই কোড আগের মতো আছে -->
            </div>

            <!-- SECTION 2: Transaction History (Full Width) -->
            @if (empty($this->ledgerEntries))
                <div class="section-2-full-width empty-state">
                    <!-- একই কোড আগের মতো আছে -->
                </div>
            @else
                <div class="section-2-full-width transaction-section">
                    <div class="transaction-header">
                        <h3>Transaction History</h3>
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
                                        <th class="name-col">Name</th>
                                        <th class="passport-col">Passport</th>
                                        <th class="amount-col">Amount</th>
                                        <th class="balance-col">Balance</th>
                                        <th class="action-col">View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($this->ledgerEntries as $row)
                                        @php
                                            // ✅ সঠিক রাউট নেম ব্যবহার করুন
                                            $viewUrl = '';
                                            if ($row['model_type'] === 'visa') {
                                                // Filament 2.x এর জন্য:
                                                // $viewUrl = route('filament.resources.visas.edit', $row['id']);
                                                // Filament 3.x এর জন্য:
                                                $viewUrl = route('filament.admin.resources.visas.edit', $row['id']);
                                            } elseif ($row['model_type'] === 'account') {
                                                // Filament 2.x এর জন্য:
                                                // $viewUrl = route('filament.resources.accounts.edit', $row['id']);
                                                // Filament 3.x এর জন্য:
                                                $viewUrl = route('filament.admin.resources.accounts.edit', $row['id']);
                                            }
                                        @endphp
                                        <tr class="transaction-row" data-view-url="{{ $viewUrl }}">
                                            <td class="date-cell">
                                                <span
                                                    class="date-main">{{ \Carbon\Carbon::parse($row['date'])->format('M d') }}</span>
                                                <span
                                                    class="date-year">{{ \Carbon\Carbon::parse($row['date'])->format('Y') }}</span>
                                            </td>
                                            <td class="type-cell">
                                                <div class="type-content">
                                                    <span
                                                        class="type-icon {{ $row['type'] === 'Visa' ? 'visa-icon' : 'account-icon' }}">
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
                                                <div class="desc-content" title="{{ $row['description'] }}">
                                                    {{ $row['description'] }}
                                                </div>
                                            </td>
                                            <td class="name-cell">
                                                <div class="name-content" title="{{ $row['name'] }}">
                                                    {{ Str::limit($row['name'], 10) }}
                                                </div>
                                            </td>
                                            <td class="passport-cell">
                                                <div class="passport-content" title="{{ $row['passport'] }}">
                                                    {{ $row['passport'] }}
                                                </div>
                                            </td>
                                            <td class="amount-cell">
                                                @if ($row['debit'] > 0)
                                                    <div class="amount-badge debit-badge">
                                                        -{{ number_format($row['debit'], 0) }} ৳
                                                    </div>
                                                @elseif($row['credit'] > 0)
                                                    <div class="amount-badge credit-badge">
                                                        +{{ number_format($row['credit'], 0) }} ৳
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="balance-cell">
                                                <span
                                                    class="balance-amount {{ $row['balance'] >= 0 ? 'balance-positive' : 'balance-negative' }}">
                                                    {{ number_format($row['balance'], 0) }} ৳
                                                </span>
                                            </td>
                                            <td class="action-cell">
                                                @if (!empty($viewUrl))
                                                    <a href="{{ $viewUrl }}" 
                                                       class="view-link"
                                                       title="View {{ $row['type'] }} Details">
                                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                    </a>
                                                @else
                                                    <span class="no-view" title="View not available">—</span>
                                                @endif
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
                </div>
            @endif
        </div>

        <style>
            /* একই স্টাইল আগের মতো, শুধু নতুন স্টাইল যোগ */

            /* No View Span */
            .no-view {
                display: inline-block;
                color: #9ca3af;
                font-size: 0.875rem;
                padding: 0.25rem 0.5rem;
            }

            /* Column Widths আপডেট */
            .date-col {
                width: 9%;
            }

            .type-col {
                width: 9%;
            }

            .desc-col {
                width: 22%;
            }

            .name-col {
                width: 11%;
            }

            .passport-col {
                width: 13%;
            }

            .amount-col {
                width: 13%;
            }

            .balance-col {
                width: 12%;
            }

            .action-col {
                width: 5%;
                text-align: center;
            }
        </style>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Passport number copy functionality
                document.querySelectorAll('.passport-content').forEach(function(element) {
                    element.addEventListener('click', function(e) {
                        e.stopPropagation(); // Prevent row click event
                        const passportNumber = this.getAttribute('title') || this.textContent.trim();
                        
                        if (passportNumber === '—' || passportNumber === 'N/A') {
                            return; // Don't copy if no passport
                        }
                        
                        // Copy to clipboard
                        navigator.clipboard.writeText(passportNumber).then(function() {
                            // Show copied notification
                            const originalText = element.textContent;
                            element.textContent = 'Copied!';
                            element.style.backgroundColor = '#dcfce7';
                            element.style.color = '#166534';
                            element.style.borderColor = '#86efac';
                            
                            // Revert after 1.5 seconds
                            setTimeout(function() {
                                element.textContent = originalText;
                                element.style.backgroundColor = '#f0f9ff';
                                element.style.color = '#3b82f6';
                                element.style.borderColor = '#e0f2fe';
                            }, 1500);
                        }).catch(function(err) {
                            console.error('Failed to copy: ', err);
                        });
                    });
                });

                // Row click to view functionality
                document.querySelectorAll('.transaction-row').forEach(function(row) {
                    row.addEventListener('click', function(e) {
                        // Don't trigger if clicking on passport, view link, or no-view span
                        if (e.target.closest('.passport-content') || 
                            e.target.closest('.view-link') || 
                            e.target.closest('.no-view')) {
                            return;
                        }
                        
                        const viewUrl = this.getAttribute('data-view-url');
                        if (viewUrl && viewUrl !== '') {
                            window.location.href = viewUrl;
                        }
                    });
                });

                // View link hover effects
                document.querySelectorAll('.view-link').forEach(function(link) {
                    link.addEventListener('mouseenter', function() {
                        this.closest('.transaction-row').style.backgroundColor = '#f0f9ff';
                    });
                    
                    link.addEventListener('mouseleave', function() {
                        this.closest('.transaction-row').style.backgroundColor = '';
                    });
                });
            });
        </script>
    @endif
</x-filament::widget>