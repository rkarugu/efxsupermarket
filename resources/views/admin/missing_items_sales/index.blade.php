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
                    <h3 class="box-title">Missing  Items Sales</h3>
                   
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('missing-items-sales.index') }}" method="GET">
                        <div class="row">
                            
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t">
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach (getBranchesDropdown() as $key => $branch)
                                        <option value="{{ $key }}" {{request()->branch ==  $key ? 'selected' : ''}}>{{ $branch }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Choose From Date</label>
                                <input type="date" name="date" id="date" class="form-control" value="{{request()->date ? request()->date : \Carbon\Carbon::now()->toDateString()}}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Choose To Date</label>
                                <input type="date" name="todate" id="todate" class="form-control" value="{{request()->todate ? request()->todate : \Carbon\Carbon::now()->toDateString()}}">
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                                <button type="submit" name="intent" value="Excel" class="btn btn-success"><i class="fas fa-file-excel"></i> Excel</button>
                                <a href="{{route('missing-items-sales.index')}}" class="btn btn-success"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    <table  class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr >
                                <th>#</th>
                                <th>Date</th>
                                <th>Route</th>
                                <th>Invoice No</th>
                                <th>Stock Id Code</th>
                                <th>Item</th>
                                <th>Last Purchase Date</th>
                                <th>Last Sale Date</th>
                                <th>Supplier</th>
                                <th>Procurement Users</th>
                                <th>Qoh As At</th>
                                <th>Order Qty</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalAmount = 0;
                            @endphp
                            @foreach ($missingItems as $item)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$item->created_at}}</td>
                                    <td>{{$item->route}}</td>
                                    <td>{{$item->invoice_number}}</td>
                                    <td>{{$item->stock_id_code}}</td>
                                    <td>{{$item->item_name}}</td>
                                    <td>{{$item->last_purchase_date}}</td>
                                    <td>{{$item->last_sale_date}}</td>
                                    <td>{{$item->supplier}}</td>
                                    <td>{{$item->procurement_users}}</td>
                                    <td style="text-align: center;">{{$item->qoh_as_at}}</td>
                                    <td style="text-align: center;">{{$item->order_quantity}}</td>
                                    <td style="text-align: right;">{{ manageAmountFormat($item->order_quantity * $item->selling_price) }}</td>

                                </tr>
                                @php
                                    $totalAmount += ($item->order_quantity * $item->selling_price);
                                @endphp
                                
                            @endforeach

                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="12">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($totalAmount)}}</th>
                            </tr>

                        </tfoot>
                       
                    </table>
                </div>

            </div>
        </div>


    </section>
@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
       
        $(function() {
            $(".mlselec6t").select2();
        });
    </script>
@endsection
