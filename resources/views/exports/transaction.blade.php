@extends('layouts.export')

@section('content')
<div style="width: 95%; margin: 0 auto;">
    <div style="width: 10%; float:left; margin-right: 20px;">
        <img src="{{ public_path('assets/images/logo.png') }}" width="100%"  alt="">
    </div>
    <div style="width: 50%; float: left;">
        <h1>Transactions</h1>
    </div>
</div>

<table style="position: relative; top: 50px;">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Reference</th>
            <th>User</th>
            <th>Type</th>
            <th>Amount</th>
            <th>Description</th>
            <th>Method</th>
            <th>Status</th>
            <th>Date Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($transactions as $key => $transaction)
            <tr>
                <td data-column="S/N">{{ $key + 1 }}</td>
                <td data-column="Reference">{{ $transaction->reference }}</td>
                <td data-column="User">{{ $transaction->user->name }}</td>
                <td data-column="Type">{{ $transaction->type }}</td>
                <td data-column="Amount">{{ number_format($transaction->amount, 2) }}</td>
                <td data-column="Description">{{ $transaction->description }}</td>
                <td data-column="Method">{{ $transaction->method }}</td>
                <td data-column="Status">{{ $transaction->status }}</td>
                <td data-column="Date Created">{{ $transaction->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection