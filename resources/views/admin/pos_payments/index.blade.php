@extends('layouts.admin.admin')

@section('content')
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Pos Payment Allocations</h3>
                    <div>
                        <a href="{{route('manually-allocate-pos-payments')}}" class="btn btn-success btn-sm"><i class="fas fa-link"></i>   Allocate Payments</a>
                    </div>

                </div>
            </div>

            <div class="box-body">
                {!! Form::open(['route' => 'cashier-management.pos-payments-consumption', 'method' => 'get']) !!}
                <div class="row">

                    <div class="col-md-2 form-group">
                        <input type="date" name="start_date" id="from" class="form-control" value="{{ request()->get('start_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <input type="date" name="end_date" id="to" class="form-control" value="{{ request()->get('end_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div class="col-md-2 form-group">
                        <select name="payment_method" id="payment_method" class="form-control mlselect">
                            <option value="">--Select Channel--</option>
                            @foreach ($paymentMethods as $channel)
                                <option value="{{$channel->id }}" {{request()->payment_method == $channel->id ? "selected" : ""}}>{{ $channel->title }}</option>
                            @endforeach
                        </select>

                    </div>

                    @if($permission =='superadmin')
                        <div class="col-md-2 form-group">
                            <select name="branch" id="branch" class="form-control mlselect">
                                <option value="">--Select Branch--</option>
                                @foreach ($branches as $branch)
                                    <option value="{{$branch->id }}" {{request()->branch == $branch->id ? "selected" : ""}}>{{ $branch->name }}</option>
                                @endforeach
                            </select>

                        </div>
                    @endif

                    <div class="col-md-2 form-group">
                        <select name="trans_type" id="trans_type" class="form-control mlselect">
                            <option value="">--Select Type--</option>
                            <option value="all" @if (request()->trans_type == 'all')
                                selected
                            @endif>All</option>
                            <option value="utilised" @if (request()->trans_type == 'utilised')
                                selected
                            @endif>Utilised</option>
                            <option value="unutilised"  @if (request()->trans_type == 'unutilised')
                                selected
                            @endif>Unutilised</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <select name="cashier" id="cashier" class="form-control mlselect">
                            <option value="">--Select Cashier--</option>
                            @foreach ($cashiers as $cashier)
                                <option value="{{$cashier->id }}" {{request()->cashier == $cashier->id ? "selected" : ""}}>{{ $cashier->name }}</option>
                            @endforeach
                        </select>

                    </div>

                    <div class="col-md-3 form-group">
                        {{-- <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter">Filter</button>
                        <input type="submit" class="btn btn-success btn-sm" name="intent" value="Download"> --}}
                        <button type="submit" class="btn btn-success btn-sm" name="manage-request" value="filter">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <button type="submit" class="btn btn-success btn-sm" name="intent" value="Download">
                            <i class="fas fa-file-excel"></i> Download
                        </button>
                
                        <a class="btn btn-success btn-sm" href="{!! route('cashier-management.pos-payments-consumption') !!}">Clear </a>

                    </div>
                </div>

                {!! Form::close(); !!}

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Paid At</th>
                            <th>Channel</th>
{{--                            <th>Receipt No.</th>--}}
                            <th>Reference</th>
                            <th>Paid By</th>
                            <th>Customer</th>
                            <th>Sale No.</th>
                            <th>Cashier</th>
                            <th>Allocated At</th>
                            <th>Payment Amount</th>
                            <th>Sale Amount</th>
                            {{-- <th>Action</th> --}}
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $total = $saleTotal = 0;
                            @endphp
                            @foreach ($payments as $payment)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$payment->created_at}}</td>
                                    <td>{{$payment->channel}}</td>
{{--                                    <td>{{$payment->receipt_no}}</td>--}}
                                    <td>{{$payment->reference}}</td>
                                    <td>{{$payment->paid_by}}</td>
                                    <td>{{$payment->customer_name}}</td>
                                    <td>{{$payment->sales_no}}</td>
                                    <td>{{$payment->cashier}}</td>
                                    <td>{{$payment->allocated_at}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat($payment->payment_amount)}}</td>
                                    <td style="text-align: right;">{{manageAmountFormat($payment->sale_amount)}}</td>
                                    {{-- <td></td> --}}

                                </tr>
                            @php
                                $total += $payment->payment_amount;
                                $saleTotal += $payment->sale_amount;
                            @endphp                               
                            @endforeach


                        </tbody>
                        
                        <tfoot>
                            <tr>
                                <th colspan="9">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($total)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($saleTotal)}}</th>
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
            $('body').addClass('sidebar-collapse');
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
