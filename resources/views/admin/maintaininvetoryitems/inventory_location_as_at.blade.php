@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Location Stock As At Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">

                <form action="" method="get" role="form">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ $start_date }}">
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Category</label>
                                <select name="category" class="form-control mtselect">
                                    <option value="" selected>Show All</option>
                                    @foreach ($categories as $key => $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == request()->category ? 'selected' : '' }}>
                                            {{ $category->category_description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="">Supplier</label>
                                <select name="supplier" class="form-control mtselect">
                                    <option value="" selected>Show All</option>
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" @selected($supplier->id == request()->supplier)>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="categorize">
                                    Categorize
                                    <input type="checkbox" id="categorize" name="categorize" value="1"
                                        {{ request()->categorize ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control mtselect">
                                    <option value="quantity" {{ request()->type == 'quantity' ? 'selected' : '' }}>Quantity
                                    </option>
                                    <option value="values" {{ request()->type == 'values' ? 'selected' : '' }}>Values
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label for="can_order">Can Order</label>
                                <select name="can_order" id="can_order" class="form-control mtselect">
                                    <option value="" selected>Show All</option>
                                    <option value="1" {{ request()->can_order === '1' ? 'selected' : '' }}>Full Packs
                                    </option>
                                    <option value="0" {{ request()->can_order === '0' ? 'selected' : '' }}>Inner Packs
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="submit" class="btn btn-primary" name="print" value="1">Print PDF</button>
                </form>

                @include('message')
                <div class="col-md-12 no-padding-h">
                    @isset($categorized)
                        @include('admin.maintaininvetoryitems.partials.categorized_stock_list')
                    @else
                        @include('admin.maintaininvetoryitems.partials.stock_list')
                    @endisset
                </div>
                <div class="text-right">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function(e) {
            $('.mtselect').select2();
        });
    </script>
@endsection
