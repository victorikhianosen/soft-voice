@extends('layouts.export')

@section('content')
<div style="width: 95%; margin: 0 auto;">
    <div style="width: 10%; float:left; margin-right: 20px;">
        <img src="{{ public_path('assets/images/logo.png') }}" width="100%"  alt="">
    </div>
    <div style="width: 50%; float: left;">
        <h1>Customers</h1>
    </div>
</div>

<table style="position: relative; top: 50px;">
    <thead>
        <tr>
            <th>S/N</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Category</th>
            <th>Website</th>
            <th>Country</th>
            <th>State</th>
            <th>Date Created</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $key => $customer)
            <tr>
                <td data-column="S/N">{{ $key + 1 }}</td>
                <td data-column="Name">{{ $customer->name }}</td>
                <td data-column="Email">{{ $customer->email }}</td>
                <td data-column="Phone">{{ $customer->phone }}</td>
                <td data-column="Address">{{ $customer->address }}</td>
                <td data-column="Category">{{ $customer->category }}</td>
                <td data-column="Website">{{ $customer->website }}</td>
                <td data-column="Country">{{ $customer->country }}</td>
                <td data-column="State">{{ $customer->state }}</td>
                <td data-column="Date Created">{{ $customer->created_at }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection