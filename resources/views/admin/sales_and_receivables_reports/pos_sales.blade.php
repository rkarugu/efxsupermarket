@extends('layouts.admin.admin')

@section('content')
   
            <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <div>
                        <h3 class="box-title"> {{$branchDetails->name}} POS SALES </h3>
                    
                        <h4>Period : {{\Carbon\Carbon::parse($startDate)->toDateString() . '-' . \Carbon\Carbon::parse($endDate)->toDateString()}}</h4>

                    </div>
                    <a href="{{route('pos-cash-sales.overview')}}" class="btn btn-success">Back</a>
                 
                </div>
            </div>

            <div class="box-body">

                @include('message')
                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Date</th>
                            <th>Cashier</th>
                            <th>Customer</th>
                            <th>Sale No.</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($posSales as $sale)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{\Carbon\Carbon::parse($sale->paid_at)->toDateString()}}</td>
                                    <td>{{$sale->user?->name}}</td>
                                    <td>{{$sale->customer}}</td>
                                    <td>{{$sale->sales_no}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat($sale->items->sum('total'))}}</td>
                                    
                                </tr>
                                @php
                                    $total += $sale->items->sum('total');
                                @endphp
                                
                            @endforeach
                        </tbody>
                        <tfoot>
                           <tr>
                                <th colspan="5">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($total)}}</th>
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
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>



    <script type="text/javascript">
        $(document).ready(function () {
            $('.download-link').on('click', function (event) {
                event.preventDefault();
                var shiftId = $(this).data('shift-id');
                $('#confirmDownloadBtn').attr('href', "{{ url('admin/salesman-shifts') }}/" + shiftId + "/loading-sheet");
                $('#confirmDownloadModal').modal('show');
            });

            //close modal
            $('#confirmDownloadBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmDownloadModal').modal('hide');
            });

            //shift reopen
            $('.shift-reopen').on('click', function (event) {
                event.preventDefault();
                var shiftId = $(this).data('shift-id');
                $('#confirmShiftReopenBtn').attr('href', "{{ url('admin/salesman-shifts') }}/" + shiftId + "/reopen-from-back-end");
                $('#confirmShiftReopenModal').modal('show');
            });

            //close modal
            $('#confirmShiftReopenBtn').on('click', function () {
                var downloadLink = $(this).attr('href');
                $('#confirmShiftReopenModal').modal('hide');

            });
            $('#branch').change(function() {
                    var branchId = $(this).val();
                    var url = $(this).data('url');
        
                    $.ajax({
                        url: url,
                        type: 'GET',
                        data: { branch_id: branchId },
                        success: function(data) {
                            console.log(data);
                            $('#route').empty();
                            $('#route').append('<option value="" selected disabled>Select Route</option>');
        
                            $.each(data.routes, function(key, value) {
                                $('#route').append('<option value="' + value.id + '">' + value.route_name + '</option>');
                            });
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                        }
                    });
                });
        });
    </script>
@endsection
