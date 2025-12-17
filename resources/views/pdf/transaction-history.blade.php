<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transaction History</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: center;
        }

        th {
            background-color: #f3f4f6;
            font-weight: bold;
        }

        .no-border td {
            border: none;
            padding: 3px;
            text-align: left;
        }

        .right {
            text-align: right;
        }

        /* ===== COLORS ===== */
        .debit {
            color: #dc2626; /* red */
            font-weight: bold;
        }

        .credit {
            color: #16a34a; /* green */
            font-weight: bold;
        }

        .negative-balance {
            background-color: #fee2e2;
            color: #b91c1c;
            font-weight: bold;
        }

        .positive-balance {
            color: #065f46;
            font-weight: bold;
        }

        .current-balance-negative {
            color: #b91c1c;
            font-weight: bold;
        }

        .current-balance-positive {
            color: #065f46;
            font-weight: bold;
        }
    </style>
</head>

<body>

    <!-- ================= HEADER ================= -->
    <table class="no-border">
        <tr>
            <td>
                <strong>{{ $settings->app_name ?? 'Visa Office' }}</strong><br>
                Transaction History
            </td>
            <td class="right">
                <strong>Report Date:</strong>
                {{ $reportDate ?? now()->format('d-F-Y') }}
            </td>
        </tr>
    </table>

    <hr>

    <!-- ================= USER INFO ================= -->
    @php
        $currentBalance = $entries[0]['balance'] ?? 0;
    @endphp

    <table class="no-border">
        <tr>
            <td><strong>User Name:</strong> {{ $user->name }}</td>
            <td><strong>Email:</strong> {{ $user->email ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $user->phone1 ?? '-' }}</td>
            <td><strong>Phone 2:</strong> {{ $user->phone2 ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Current Balance:</strong>
                <span class="{{ $currentBalance < 0 ? 'current-balance-negative' : 'current-balance-positive' }}">
                    {{ number_format($currentBalance, 2) }}
                </span>
            </td>
        </tr>
    </table>

    <!-- ================= TRANSACTIONS ================= -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Name</th>
                <th>Passport</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry['date']->format('d-F-Y') }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ $entry['name'] }}</td>
                    <td>{{ $entry['passport'] }}</td>
                    <td>{{ $entry['description'] }}</td>

                    <!-- Debit -->
                    <td class="debit">
                        {{ $entry['debit'] > 0 ? number_format($entry['debit'], 2) : '-' }}
                    </td>

                    <!-- Credit -->
                    <td class="credit">
                        {{ $entry['credit'] > 0 ? number_format($entry['credit'], 2) : '-' }}
                    </td>

                    <!-- Balance -->
                    <td class="{{ $entry['balance'] < 0 ? 'negative-balance' : 'positive-balance' }}">
                        {{ number_format($entry['balance'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- ================= OFFICE INFO ================= -->
    <table class="no-border" style="margin-top: 10px;">
        <tr>
            <td><strong>Office Phone:</strong> {{ $settings->office_phone ?? '-' }}</td>
            <td><strong>Office Phone 2:</strong> {{ $settings->office_phone2 ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Office Address:</strong> {{ $settings->office_address ?? '-' }}
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <strong>Office Email:</strong> {{ $settings->office_email ?? '-' }}
            </td>
        </tr>
    </table>

</body>
</html>
