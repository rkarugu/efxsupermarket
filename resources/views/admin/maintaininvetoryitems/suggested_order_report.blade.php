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
                    <h3 class="box-title">Suggested Order Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            {{-- <div class="box-header with-border">
                <h3 class="box-title"> Suggested Order Report </h3>
            </div> --}}

            <div class="box-body">
                <form action="" method="get" role="form">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="">Store Location</label>
                            <select name="store" class="form-control" id="store"
                                @if (getLoggeduserProfile()->role_id != 1) disabled @endif>
                                <option value="" selected>Show All</option>
                                @foreach ($stores as $key => $store)
                                    <option value="{{ $store['id'] }}" @if (request()->store == $store['id'] ||
                                            (getLoggeduserProfile()->wa_location_and_store_id == $store['id'] && getLoggeduserProfile()->role_id != 1)) selected @endif>
                                        {{ $store['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="">Supplier </label>
                            <select name="supplier" class="form-control" id="supplier">
                                <option value="" selected>Show All</option>
                                @foreach ($suppliers as $key => $supplier)
                                    <option value="{{ $supplier['id'] }}" @if (request()->supplier == $supplier['id']) selected @endif>
                                        {{ $supplier['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Filter</button>
                    {{--                    <button type="submit" class="btn btn-primary" name="excel" value="1">Excel</button> --}}
                </form>

                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="create_datatable_50">
                        <thead>
                            <tr>

                                <th width="10%">Stock ID Code</th>
                                <th width="10%">Title</th>
                                <th width="10%">Location</th>
                                <th width="10%">Bin Location</th>
                                <th width="10%">Supplier</th>
                                <th width="10%">Max Stock</th>
                                <th width="10%">Re Order Level</th>
                                <th width="10%">Current Stock</th>
                                <th width="10%">Order Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->stock_id_code }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->location_name }}</td>
                                    <td>{{ @$item->bin_location }}</td>
                                    <td> {{ $item->supplier }} </td>
                                    <td>{{ $item->max_stock_f }}</td>
                                    <td>{{ $item->re_order_level }}</td>
                                    <td>{{ $item->qty_inhand }}</td>
                                    <td>{{ (float) $item->max_stock_f - (float) $item->qty_inhand }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#store").select2();
            $("#supplier").select2();
        });
    </script>
@endsection
