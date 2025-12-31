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
            margin-top: 5px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
        }

        th {
            background: #f3f4f6;
            font-weight: bold;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: bottom;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .subtitle {
            font-size: 14px;
            margin-top: 2px;
        }

        .line {
            border-top: 2px solid #000;
            margin-top: 5px;
            margin-bottom: 5px;
        }

        .info-table td {
            border: none;
            text-align: left;
            padding: 3px;
        }

        .debit {
            color: #dc2626;
            font-weight: bold;
            font-size: 14px;
        }

        .credit {
            color: #16a34a;
            font-weight: bold;
            font-size: 14px;
        }

        .positive-balance {
            color: #16a34a;
            font-weight: bold;
            font-size: 15px;
        }

        .negative-balance {
            color: #dc2626;
            font-weight: bold;
            font-size: 15px;
            background-color: #fee2e2;
        }

        .col-debit {
            width: 100px;
        }

        .col-credit {
            width: 100px;
        }

        .col-balance {
            width: 130px;
        }

        .footer td {
            border: none;
            text-align: left;
            padding: 3px;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <table class="header-table">
        <tr>
            <td style="text-align: left;">
                <div class="title">{{ $settings->app_name ?? 'Visa Office Chapai International' }}</div>
                <div class="subtitle">Transaction History</div>
            </td>
            <td style="text-align: right;">
                <div class="subtitle"><strong>Report Date:</strong> {{ $reportDate }}</div>
            </td>
        </tr>
    </table>

    <div class="line"></div>

    {{-- User Info --}}
    @php
        $currentBalance = $entries[0]['balance'] ?? 0;
    @endphp

    <table class="info-table">
        <tr>
            <td><strong>User Name:</strong> {{ $user->name }}</td>
            <td><strong>Email:</strong> {{ $user->email ?? '-' }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $user->phone1 ?? '-' }}</td>
            <td>
                <strong>Current Balance:</strong>
                <span class="{{ $currentBalance < 0 ? 'negative-balance' : 'positive-balance' }}">
                    {{ number_format($currentBalance, 0) }}
                </span>
            </td>
        </tr>
    </table>

    {{-- Transactions --}}
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Name</th>
                <th>Passport</th>
                <th>Description</th>
                <th class="col-debit">Debit</th>
                <th class="col-credit">Credit</th>
                <th class="col-balance">Balance</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ $entry['name'] }}</td>
                    <td>{{ $entry['passport'] }}</td>
                    <td>{{ $entry['description'] }}</td>

                    <td class="debit col-debit">
                        {{ $entry['debit'] > 0 ? number_format($entry['debit'], 0) : '-' }}
                    </td>

                    <td class="credit col-credit">
                        {{ $entry['credit'] > 0 ? number_format($entry['credit'], 0) : '-' }}
                    </td>

                    <td class="col-balance {{ $entry['balance'] < 0 ? 'negative-balance' : 'positive-balance' }}">
                        {{ number_format($entry['balance'], 0) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Office Info --}}
    <table class="footer">
        <tr>
            <td><strong>Office Phone:</strong> {{ $settings->office_phone ?? '-' }}</td>
            <td><strong>Office Phone 2:</strong> {{ $settings->office_phone2 ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Office Address:</strong> {{ $settings->office_address ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Office Email:</strong> {{ $settings->office_email ?? '-' }}</td>
        </tr>
    </table>

</body>

</html>
