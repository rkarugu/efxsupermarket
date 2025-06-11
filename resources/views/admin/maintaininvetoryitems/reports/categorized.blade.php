@extends('layouts.report')

@section('title', $title)

@section('content')
    @include('admin.maintaininvetoryitems.partials.categorized_stock_list')
@endsection
