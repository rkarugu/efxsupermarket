@extends('layouts.admin.admin')

@section('content')

    <section class="content">
        <div class="modal fade" id="assign-vehicle-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Assign Loading Sheet </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label class="control-label"> Vehicle </label>
                            {!!Form::select('selected_vehicle_id', getAvailableVehicles(), null, ['placeholder'=>'Select Vehicle', 'class' => 'form-control mlselect' ,'required'=> true, 'id' => 'selected-vehicle-id' ])!!}
                        </div>

                        <div class="form-group">
                            <label class="control-label"> Dispatcher </label>
                            {!!Form::select('selected_store_keeper_id', getAllStoreKeepers(), null, ['placeholder'=>'Select dispatcher', 'class' => 'form-control mlselect' ,'required'=> true, 'id' => 'selected-dispatcher' ])!!}
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                            <button type="button" class="btn btn-primary" onclick="assignVehicle();">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                <h3 class="box-title"> Assign Loading Sheet </h3>
            </div>

            <div class="box-body">
                <div style="height: 50px ! important;">
                    <div class="session-message-container">
                        @include('message')
                    </div>

                    <div class="filters">
                        {!! Form::open(['route' => 'confirm-invoice.assign', 'method'=>'POST']) !!}
                        {{ csrf_field() }}

                        <div class="col-md-3 form-group">
                            {!!Form::select('salesman_id', getAllsalesmanList(), null, ['placeholder'=>'Select Salesman', 'class' => 'form-control mlselect getshiftdata' ,'required'=>true ])!!}
                        </div>

                        <div class="col-md-3 form-group">
                            {!!Form::select('shift_id', getLoadingShiftList(), null, ['placeholder'=>'Select Shift', 'class' => 'form-control  mlselect shiftList', 'required'=>true  ])!!}
                        </div>

{{--                        <div class="col-md-3 form-group">--}}
{{--                            {!!Form::select('store_id', getAllStores(), null, ['placeholder'=>'Select Store', 'class' => 'form-control  mlselect', 'required'=>true  ])!!}--}}
{{--                        </div>--}}

                        <div class="col-md-1 form-group">
                            <button type="submit" class="btn btn-primary" name="manage-request" value="pdf">Submit</button>
                        </div>

                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $(".mlselect").select2();
        });

        $(document).ready(function () {
            $(".getshiftdata").change(function () {
                var salesmanId = $(this).val();
                $.ajax({
                    url: "{{route('sales-and-receivables-reports.getShiftBySalesman')}}",
                    dataType: "JSON",
                    data: {'_token': "{{csrf_token()}}", salesman_id: salesmanId, 'shift_summary': '1'},
                    success: function (result) {
                        $('.shiftList').html('');
                        $.each(result, function (key, val) {
                            $('.shiftList').append('<option value="' + key + '">' + val + '</option>');
                        });
//			$("#div1").html(result);
                    }
                });
            });
        });

        //         $(function () {
        //             $(".mlselec6t").select2({
        //                 closeOnSelect: true,
        //             });
        // //        $(".mlselec6t").select2();
        //         });

        $('.entered_qty').on('keyup', function () {
            var total_qty = $(this).data('total-qty');
            var entered_qty = $(this).val();
            if (entered_qty > total_qty) {
                alert('You cannot enter QTY More than ' + total_qty);
                $(this).val('');
            }
        });


    </script>

    <script>


        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });

    </script>

    <script type="text/javascript">
        function assignVehicle() {
            let selectedVehicleId = $("#selected-vehicle-id").val();
            if (!selectedVehicleId) {
                return alert('Please assign a vehicle to continue.');
            }

            let selectedDispatcherId = $("#selected-dispatcher").val();
            if (!selectedDispatcherId) {
                return alert('Please select a dispatcher to continue.');
            }

            $("#vehicle-id").val(selectedVehicleId)
            $("#user-id").val(selectedDispatcherId)

            $("#dispatch-form").submit();
        }
    </script>

@endsection