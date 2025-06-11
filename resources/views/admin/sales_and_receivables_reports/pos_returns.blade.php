@extends('layouts.admin.admin')

@section('content')
   
            <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <div>
                        <h3 class="box-title"> {{$branchDetails->name}} POS RETURNS </h3>
                    
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
                            <th>Return No.</th>
                            <th>Sale No.</th>
                            <th>Store</th>
                            <th>Code</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Amount</th>
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = 0;
                            @endphp
                            @foreach ($posReturns as $return)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{\Carbon\Carbon::parse($return->updated_at)->toDateString()}}</td>
                                    <td>{{$return->cashier}}</td>
                                    <td>{{$return->customer}}</td>
                                    <td>{{$return->return_grn}}</td>
                                    <td>{{$return->sales_no}}</td>
                                    <td>{{$return->bin}}</td>
                                    <td>{{$return->stock_id_code}}</td>
                                    <td>{{$return->title}}</td>
                                    <td style="text-align: center;">{{$return->return_quantity}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat($return->selling_price)}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat($return->selling_price * $return->return_quantity)}}</td>
                                    
                                </tr>
                                @php
                                    $total += $return->return_quantity * $return->selling_price;
                                @endphp                       
                            @endforeach
                           
                        </tbody>
                        <tfoot>
                           <tr>
                                <th colspan="11">Total</th>
                                <th colspan="1" style="text-align: right;">{{manageAmountFormat($total)}}</th>

                             
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
