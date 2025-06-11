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
                    <h3 class="box-title">Reported Missing Items</h3>
                   
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('reported-missing-items.index') }}" method="GET">
                        <div class="row">
                            
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t" >
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{request()->branch ==  $branch->id ? 'selected' : ''}}>{{ $branch->location_name }}</option>
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
                                <a href="{{route('reported-missing-items.index')}}" class="btn btn-success"><i class="fas fa-eraser"></i> Clear</a>
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
                                <th>Reported By</th>
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
                        @php
                            $total = 0;
                        @endphp
                        <tbody>
                            @foreach ($missingItems as $item)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$item->created_at}}</td>
                                    <td>{{$item->name}}</td>
                                    <td>{{$item->stock_id_code}}</td>
                                    <td>{{$item->title}}</td>
                                    <td>{{$item->last_purchase_date}}</td>
                                    <td>{{$item->last_sale_date}}</td>
                                    <td>{{$item->supplier}}</td>
                                    <td>{{$item->procurement_users}}</td>
                                    <td style="text-align: center;">{{$item->as_at_quantity}}</td>
                                    <td style="text-align: center;">{{$item->quantity}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat(($item->quantity ?? 0) * $item->selling_price)}}</td>
                                </tr>
                                @php
                                    $total += ($item->quantity ?? 0) * $item->selling_price;
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="11">Total</th>
                                <th  style="text-align: right;">{{manageAmountFormat($total)}}</th>
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
