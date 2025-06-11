@extends('layouts.admin.admin')

@section('content')
    <?php
    $logged_user_info = getLoggeduserProfile();
    $my_permissions = $logged_user_info->permissions;
    ?>
    <!-- Main content -->
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Sales Per Supplier Per Route Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'sales-per-supplier-per-route', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label for="supplier_id">Select Supplier:</label>

                        {!! Form::select('supplier_id', getSuppliers(), request()->supplier_id, [
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Select Supplier',
                        ]) !!}
                    </div>
                    <div class="col-md-3 form-group">
                        <label for="route_id">Select Route:</label>

                        {!! Form::select('route_id', $customers, request()->route_id, [
                            'class' => 'form-control mlselect',
                            'placeholder' => 'Select Route',
                        ]) !!}
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="from">Sale Period From</label>
                        <input type="date" class="form-control" name="from" value="{{ request()->from }}">
                    </div>
                    <div class="col-md-2 form-group">
                        <label for="to">Sale Period To</label>
                        <input type="date" class="form-control" name="to" value="{{ request()->to }}">
                    </div>


                    <div class="col-md-2 form-group">
                        <div style="margin-top: 24px;">
                            <button type="submit" class="btn btn-success" name="manage-request"
                                value="filter">Filter</button>
                            <input type="submit" value="Excel" name="type" class="btn btn-success">
                            <a class="btn btn-success " href="{!! route('sales-per-supplier-per-route') !!}">Clear </a>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Stock Id Code</th>
                                <th>Title</th>
                                <th>Price</th>
                                <th>Sold Quantity</th>
                                <th>Total Sales</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalSales = 0;
                            @endphp
                            @foreach ($data as $item)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ $item->stock_id_code }}</td>
                                    <td>{{ $item->item_name }}</td>
                                    <td style="text-align: right;">{{ manageAmountFormat($item->price) }}</td>
                                    <td style="text-align: center;">{{ (int) $item->qty }}</td>
                                    <td style="text-align: right;">{{ manageAmountFormat($item->gross_sales) }}</td>


                                </tr>
                                @php
                                    $totalSales += $item->gross_sales;
                                @endphp
                            @endforeach


                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5">Total</th>
                                <th style="text-align: right;">{{ manageAmountFormat($totalSales) }}</th>

                            </tr>

                        </tfoot>

                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
