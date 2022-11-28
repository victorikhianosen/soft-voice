<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimates</title>

    <style>
        table {
            width: 95%;
            border-collapse: collapse;
            margin: 50px auto;
        }

        /* Zebra striping */
        tr:nth-of-type(odd) {
            background: #eee;
        }

        th {
            background: #3498db;
            color: white;
            font-weight: bold;
        }

        td,
        th {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: left;
            font-size: 18px;
        }


    </style>

</head>

<body>

    <div style="width: 95%; margin: 0 auto;">
        <div style="width: 10%; float:left; margin-right: 20px;">
            <img src="{{ public_path('assets/images/logo.png') }}" width="100%"  alt="">
        </div>
        <div style="width: 50%; float: left;">
            <h1>Estimates</h1>
        </div>
    </div>

    <table style="position: relative; top: 50px;">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Estimate ID</th>
                <th>Customer</th>
                <th>Amount</th>
                <th>Date Created</th>
                <th>Due Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($estimates as $key => $estimate)
                <tr>
                    <td data-column="S/N">{{ $key + 1 }}</td>
                    <td data-column="Invoice ID">{{ $estimate->estimate_id }}</td>
                    <td data-column="Customer">{{ $estimate->customer->name }}</td>
                    <td data-column="Amount">{{ $estimate->amount }}</td>
                    <td data-column="Date Created">{{ $estimate->date }}</td>
                    <td data-column="Due Date">{{ $estimate->due_date }}</td>
                    <td data-column="Status">{{ $estimate->status }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>