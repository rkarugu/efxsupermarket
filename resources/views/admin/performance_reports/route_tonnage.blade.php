@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{{ $items ->first()->route_name }} Tonnage Performance Report</h3>
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('salesman-performance-report') }}" method="GET">
                        <div class="row">

                            <div class="col-md-3 form-group">
                                <label for="">From</label>
                                <input readonly type="date" name="start" id="start" class="form-control" value="{{request()->start ? request()->start : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">To</label>
                                <input readonly type="date" name="end" id="end" class="form-control" value="{{request()->end ? request()->end : \Carbon\Carbon::now()->toDateString() }}" >
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <button type="submit" name="filter" value="Filter" class="btn btn-success"><i class="fas fa-filter"></i> Filter</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Cartons</h3>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="carton_table">
                    <thead>
                    <tr>
                        <th>Stock ID Code</th>
                        <th>Title</th>
                        <th>Quantity</th>
                        <th>Current Selling Price</th>
                        <th>Sales Total</th>
                        <th>Net Weight (KG)</th>
                        <th>Total Weight (Tons)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $cartons_total = 0;
                        $total_amount_sold_cartons = 0;


                    @endphp

                    @foreach($items as $item)
                        @if($item->category === 'Cartons')
                            <tr>
                                <td>{{ $item->stock_id_code }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->item_count }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($item->selling_price) }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($item->total_amount_sold) }}</td>
                                <td>{{ $item->net_weight }}</td>
                                <td style="text-align: right">{{ number_format($item->total_weight, 4) }}</td>
                            </tr>
                            @php
                                $cartons_total += $item->total_weight;
                                $total_amount_sold_cartons += $item->total_amount_sold;

                            @endphp
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>

                        <td colspan="4" style="text-align: left"><strong>Total</strong></td>
                        <td style="text-align: right"><strong>{{ manageAmountFormat($total_amount_sold_cartons) }}</strong></td>
                        <td></td>
                        <td style="text-align: right"><strong>{{ manageAmountFormat($cartons_total)  }}</strong></td>
                        

                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Dozens</h3>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="dozen_table">
                    <thead>
                    <tr>
                        <th>Stock ID Code</th>
                        <th>Title</th>
                        <th>Quantity</th>
                        <th>Current Selling Price</th>
                        <th>Sales Total</th>
                        <th>Net Weight (KG)</th>
                        <th>Total Weight (Tons)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                     $dozens_total = 0;
                     $total_amount_sold = 0;
                    @endphp
                    @foreach($items as $item)
                        @if($item->category === 'Dozens')
                            <tr>
                                <td>{{ $item->stock_id_code }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->item_count }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($item->selling_price) }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($item->total_amount_sold) }}</td>
                                <td>{{ $item->net_weight }}</td>
                                <td style="text-align: right">{{ number_format($item->total_weight, 4) }}</td>
                            </tr>
                            @php
                                $dozens_total += $item->total_weight;
                                $total_amount_sold += $item->total_amount_sold;
                            @endphp
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                     <tr>

                        <td colspan="4" style="text-align: left"><strong>Total</strong></td>
                        <td style="text-align: right"><strong>{{ manageAmountFormat($total_amount_sold) }}</strong></td>
                        <td></td>
                        <td style="text-align: right"><strong>{{ manageAmountFormat($dozens_total)  }}</strong></td>

                    </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Bulk Items</h3>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="bulk_table">
                    <thead>
                    <tr>
                        <th>Stock ID Code</th>
                        <th>Title</th>
                        <th>Quantity</th>
                        <th>Current Selling Price</th>
                        <th>Sales Total</th>
                        <th>Net Weight (KG)</th>
                       <th>Total Weight (Tons)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $bulk_total = 0;
                        $total_amount_sold = 0;

                    @endphp
                    @foreach($items as $item)
                        @if($item->category === 'Bulk')
                            <tr>
                                <td>{{ $item->stock_id_code }}</td>
                                <td>{{ $item->title }}</td>
                                <td>{{ $item->item_count }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($item->selling_price) }}</td>
                                <td style="text-align: right">{{ manageAmountFormat($item->total_amount_sold) }}</td>
                                <td>{{ $item->net_weight }}</td>
                                <td style="text-align: right">{{ number_format($item->total_weight, 4) }}</td>
                            </tr>
                            @php
                                $bulk_total += $item->total_weight;
                                $total_amount_sold += $item->total_amount_sold;

                            @endphp
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>

                        <td colspan="4" style="text-align: left"><strong>Total</strong></td>
                        <td style="text-align: right"><strong>{{ manageAmountFormat($total_amount_sold) }}</strong></td>
                        <td></td>
                        <td style="text-align: right"><strong>{{ manageAmountFormat($bulk_total)  }}</strong></td>

                    </tr>
                    </tfoot>
                </table>
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
        $(document).ready(function() {
            $(document).ready(function() {
                $('#carton_table').DataTable({
                    // Optional configurations
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "lengthMenu": [5, 10, 25, 50, 100]
                });
            });
            $('#dozen_table').DataTable({
                // Optional configurations
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });
        $(document).ready(function() {
            $('#bulk_table').DataTable({
                // Optional configurations
                "paging": true,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "lengthMenu": [5, 10, 25, 50, 100]
            });
        });

        $(function() {
            $('body').addClass('sidebar-collapse');
            $(".mlselec6t").select2();

        });
    </script>
@endsection
