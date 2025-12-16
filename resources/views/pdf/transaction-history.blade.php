<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction History</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: center; }
        th { background-color: #f3f4f6; }
        td { vertical-align: middle; }
        .debit { color: red; }
        .credit { color: green; }
    </style>
</head>
<body>
    <h2 style="text-align:center;">{{ $settings->app_name ?? 'Visa Office' }}<br>Transaction History</h2>
    <p><strong>User:</strong> {{ $user->name }} | <strong>Email:</strong> {{ $user->email }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>NAME</th>
                <th>PASSPORT</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                <tr>
                    <td>{{ $entry['date']->format('d/m/Y') }}</td>
                    <td>{{ $entry['type'] }}</td>
                    <td>{{ $entry['description'] }}</td>
                    <td>{{ $entry['name'] }}</td>
                    <td>{{ $entry['passport'] }}</td>
                    <td class="debit">
                        {{ $entry['debit'] ? number_format($entry['debit'], 2) : '-' }}
                    </td>
                    <td class="credit">
                        {{ $entry['credit'] ? number_format($entry['credit'], 2) : '-' }}
                    </td>
                    <td>{{ number_format($entry['balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
