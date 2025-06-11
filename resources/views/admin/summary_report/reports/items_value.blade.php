@extends('layouts.report')

@section('title', 'INVENTORY VALUATION REPORT')

@section('content')
<table class="table table-hover table-bordered">
    <thead>
        <tr>
            <th>Item Code</th>
            <th>Item Description</th>
            <th>QOH</th>
            <th>Cost</th>
            <th>Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr>
                <td>{{ $item->stock_id_code }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->qoh }}</td>
                <td class="text-right">{{ manageAmountFormat($item->standard_cost) }}</td>
                <td class="text-right">{{ manageAmountFormat($item->total) }}</td>
            </tr>
        @endforeach
        <tr>
            <th class="text-right" colspan="4">Total: </td>
            <th class="text-right">{{ manageAmountFormat($items->sum('total')) }}</td>
        </tr>
    </tbody>
</table>
@endsection
