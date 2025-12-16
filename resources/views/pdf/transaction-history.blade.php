<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaction History</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 5px;
            font-size: 12px;
            text-align: center;
        }

        th {
            background: #f5f5f5;
        }

        .header,
        .footer {
            width: 100%;
            margin-bottom: 10px;
        }

        .header td,
        .footer td {
            border: none;
            text-align: left;
            padding: 2px;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>

<body>

    <!-- Title -->
    <div class="title">
        {{ $settings->app_name ?? 'Visa Office' }}<br>
        Transaction History
    </div>

    <!-- User Info -->
    <table class="header">
        <tr>
            <td><strong>User Name:</strong> {{ $user->name }}</td>
            <td><strong>Email:</strong> {{ $user->email }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $user->phone1 ?? '-' }}</td>
            <td><strong>Phone 2:</strong> {{ $user->phone2 ?? '-' }}</td>
        </tr>
    </table>

    <!-- Transaction Table -->
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Name</th>
                <th>Passport</th>
                <th>Description</th>
                <th>Debit </th>
                <th>Credit </th>
                <th>Balance </th>
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
                    <td>{{ number_format($entry['debit'], 2) }}</td>
                    <td>{{ number_format($entry['credit'], 2) }}</td>
                    <td>{{ number_format($entry['balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Office Info -->
    <table class="footer">
        <tr>
            <td><strong>Office Phone:</strong> {{ $settings->office_phone ?? '-' }}</td>
            <td><strong>Boss:</strong> {{ $settings->office_phone2 ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Address:</strong> {{ $settings->office_address ?? '-' }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Email:</strong> {{ $settings->office_email ?? '-' }}</td>
        </tr>
    </table>

</body>

</html>
