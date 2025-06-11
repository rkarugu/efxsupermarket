@extends('layouts.report')

@section('title', 'INVENTORY VALUATION REPORT')

@section('content')
    <table class="table table-hover table-bordered">
        <thead>
            <tr>
                <th class="text-left">Category</th>
                <th class="text-right">Value</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($categories as $category)
                <tr>
                    <td>{{ $category->category_description }}</td>
                    <td class="text-right">{{ manageAmountFormat($category->total) }}</td>
                </tr>
            @endforeach
            <tr>
                <th class="text-right">Total: </td>
                <th class="text-right">{{ manageAmountFormat($categories->sum('total')) }}</td>
            </tr>
        </tbody>
    </table>
@endsection
