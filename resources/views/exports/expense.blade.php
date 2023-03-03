@extends('layouts.export')

@section('content')
<div style="width: 95%; margin: 0 auto;">
    <div style="width: 10%; float:left; margin-right: 20px;">
        <img src="{{ public_path('assets/images/logo.png') }}" width="100%"  alt="">
    </div>
    <div style="width: 50%; float: left;">
        <h1>Expenses</h1>
    </div>
</div>

<table style="position: relative; top: 50px;">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Customer</th>
            <th>Amount</th>
            <th>Date</th>
            <th>Recurring</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($expenses as $key => $expense)
            <tr>
                <td data-column="S/N">{{ $key + 1 }}</td>
                <td data-column="Customer">{{ $expense->customer->name }}</td>
                <td data-column="Amount">{{ number_format($expense->amount) }}</td>
                <td data-column="Date">{{ $expense->date }}</td>
                <td data-column="Recurring">{{ $expense->recurring == 1 ? "Yes" : "No" }}</td>
                <td data-column="Status">{{ $expense->status }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection