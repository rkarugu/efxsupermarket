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
                    <h3 class="box-title">Promotion Items Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'items-with-promotions-reports', 'method' => 'get']) !!}
                <div class="row">
                    <div class="col-md-4 form-group">
                        <select name="item" id="item" class="mlselect">
                            <option value="" selected disabled>Select Item</option>
                            @foreach ($inventoryItems as $item)
                                <option value="{{ $item->id }}" {{ $item->id == request()->item ? 'selected' : '' }}>
                                    {{ $item->stock_id_code . ' - ' . $item->title }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-4 form-group">
                        <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter</button>
                        <input type="submit" value="Download" name="type" class="btn btn-success">
                        <a class="btn btn-success ml-12" href="{!! route('items-with-promotions-reports') !!}">Clear </a>
                    </div>
                </div>

                {!! Form::close() !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable_50">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Item Code</th>
                                <th>Title</th>
                                <th>Pack Size</th>
                                <th>Price</th>
                                <th>Sale Quantity</th>
                                <th>Promo Item</th>
                                <th>Pack Size</th>
                                <th>Price</th>
                                <th>Promo Quantity</th>
                                <th>Created By</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($data as $row)
                                <tr>
                                    <th>{{ $loop->index + 1 }}</th>
                                    <td>{{ \Carbon\Carbon::parse($row->start_date)->toDateString() }}</td>
                                    <td>{{ \Carbon\Carbon::parse($row->end_date)->toDateString() }}</td>
                                    <td>{{ $row->parent_stock_id_code }}</td>
                                    <td>{{ $row->parent_title }}</td>
                                    <td>{{ $row->parent_pack_size }}</td>
                                    <td style="text-align: right;">{{ manageAmountFormat($row->parent_price) }}</td>
                                    <td style="text-align: center;">{{ $row->sale_quantity }}</td>
                                    <td>{{ $row->promotion_item_title }}</td>
                                    <td>{{ $row->promotion_pack_size }}</td>
                                    <td style="text-align: right;">{{ manageAmountFormat($row->promotion_item_price) }}
                                    </td>
                                    <td style="text-align: center;">{{ $row->promotion_quantity }}</td>
                                    <td>{{ $row->user_name }}</td>

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
