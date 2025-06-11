@extends('layouts.report')

@section('left-detail')
    @isset($supplier)
        <strong>Supplier: {{ $supplier->name }}</strong>
    @endisset
@endsection

@section('content')
    @include('admin.maintaininvetoryitems.partials.stock_list')
@endsection
