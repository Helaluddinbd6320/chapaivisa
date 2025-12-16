<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }
        th {
            background: #f3f4f6;
        }
        .footer {
            margin-top: 20px;
            font-size: 11px;
        }
    </style>
</head>
<body>

<h2>Complete Financial Transaction History</h2>

<p>
    <strong>Name:</strong> {{ $user->name }} <br>
    <strong>Phone:</strong> {{ $user->phone1 }}
</p>

<table>
    <thead>
        <tr>
            <th>Date</th>
            <th>Type</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ledger as $row)
            <tr>
                <td>{{ $row['date'] }}</td>
                <td>{{ $row['type'] }}</td>
                <td>{{ $row['description'] }}</td>
                <td>{{ $row['debit'] ? number_format($row['debit'], 0).' ৳' : '-' }}</td>
                <td>{{ $row['credit'] ? number_format($row['credit'], 0).' ৳' : '-' }}</td>
                <td>{{ number_format($row['balance'], 0) }} ৳</td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- Footer from Settings --}}
<div class="footer">
    <strong>{{ $settings['app_name'] }}</strong><br>

    @if($settings['office_address'])
        {{ $settings['office_address'] }}<br>
    @endif

    @if($settings['office_phone'])
        Phone: {{ $settings['office_phone'] }}
    @endif

    @if($settings['office_phone2'])
        , {{ $settings['office_phone2'] }}
    @endif
    <br>

    @if($settings['office_email'])
        Email: {{ $settings['office_email'] }}
    @endif
</div>

</body>
</html>
