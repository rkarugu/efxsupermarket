@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Assign Vehicle Modal -->
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
                            {!! Form::select('selected_vehicle_id', getAvailableVehicles(), null, [
                                'placeholder' => 'Select Vehicle',
                                'class' => 'form-control mlselect',
                                'required' => true,
                                'id' => 'selected-vehicle-id',
                            ]) !!}
                        </div>

                        <div class="form-group">
                            <label class="control-label"> Dispatcher </label>
                            {!! Form::select('selected_store_keeper_id', getAllStoreKeepers(), null, [
                                'placeholder' => 'Select dispatcher',
                                'class' => 'form-control mlselect',
                                'required' => true,
                                'id' => 'selected-dispatcher',
                            ]) !!}
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
                <h3 class="box-title"> Dispatch & Close Loading Sheet </h3>
            </div>

            <div class="box-body">
                <div style="height: 50px ! important;">
                    <div class="session-message-container">
                        @include('message')
                    </div>

                    <div class="filters">
                        {!! Form::open(['route' => 'delivery-loading-sheets.generate-for-dispatch', 'method' => 'POST']) !!}
                        {{ csrf_field() }}

                        <div class="col-md-3 form-group">
                            {!! Form::select('salesman_id', getAllsalesmanList(), null, [
                                'placeholder' => 'Select Salesman',
                                'class' => 'form-control mlselect getshiftdata',
                                'required' => true,
                            ]) !!}
                        </div>

                        <div class="col-md-3 form-group">
                            {!! Form::select('shift_id', getLoadingShiftList(), null, [
                                'placeholder' => 'Select Shift',
                                'class' => 'form-control  mlselect shiftList',
                                'required' => true,
                            ]) !!}
                        </div>

                        <div class="col-md-3 form-group">
                            {!! Form::select('store_id', getAllStores(), null, [
                                'placeholder' => 'Select Store',
                                'class' => 'form-control  mlselect',
                                'required' => true,
                            ]) !!}
                        </div>

                        <div class="col-md-1 form-group">
                            <button type="submit" class="btn btn-primary" name="manage-request"
                                value="pdf">Submit</button>
                        </div>

                        {{ Form::close() }}
                    </div>
                </div>

                <hr>

                @if (!empty($invoiceItems))
                    <div class="box-header with-border">
                        <h3 class="box-title"> Loading Sheet Particulars </h3>
                    </div>

                    <div class="particulars">
                        {!! Form::open([
                            'route' => 'confirm-invoice.dispatch_and_close_loading_sheet_post',
                            'method' => 'POST',
                            'id' => 'dispatch-form',
                        ]) !!}
                        {{ csrf_field() }}

                        <table class="table">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Particular</th>
                                    <th scope="col">Total Quantity</th>
                                    <th scope="col"> Quantity Dispatched</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($invoiceItems as $index => $item)
                                    <tr>
                                        <th scope="row"> {{ $index + 1 }} </th>
                                        <td>{{ $item->getInventoryItemDetail->title }}</td>
                                        <td>
                                            <span>{{ manageAmountFormat($item->total_quantity) }}</span>
                                            <input type="hidden" value="{{ $item->total_quantity }}"
                                                name="requested_quantities[]">
                                        </td>
                                        <td>
                                            <input data-total-qty="{{ $item->total_quantity }}"
                                                value="{{ $item->total_quantity }}" name="loaded_quantities[]"
                                                type="text" class="form-control entered_qty">
                                            <input type="hidden" value="{{ $item->wa_inventory_item_id }}"
                                                name="inventory_item_ids[]">
                                            <input type="hidden" value="{{ $item->store_location_id }}"
                                                name="store_location_ids[]">
                                        </td>
                                    </tr>
                                @endforeach

                                {{--                        @php --}}
                                {{--                            $i = 0; --}}
                                {{--                        @endphp --}}
                                {{--                        @foreach ($inventory as $inventory_item) --}}
                                {{--                            @php --}}
                                {{--                                $al = $items->where('wa_inventory_item_id',$inventory_item->id); --}}
                                {{--                                $quantity = 0; --}}
                                {{--                            @endphp --}}
                                {{--                            @if (count($al) > 0) --}}

                                {{--                                @foreach ($al as $item) --}}

                                {{--                                    @php --}}
                                {{--                                        $quantity += $item->qty; --}}
                                {{--                                    @endphp --}}
                                {{--                                @endforeach --}}
                                {{--                                <tr class="item @if ($i % 2 == 0) bg-grey @endif"> --}}
                                {{--                                    <td colspan="1">{{@$inventory_item->title}}</td> --}}
                                {{--                                    <td>{{manageAmountFormat($quantity)}}</td> --}}
                                {{--                                    <td><input data-total-qty={{$quantity}} name="store_qty_loaded[]" type="text" class="form-control entered_qty"></td> --}}
                                {{--                                </tr> --}}

                                {{--                                <input type="hidden" name="item_id[]" value="{{@$inventory_item->id}}"> --}}
                                {{--                                <input type="hidden" name="item_total_qty[]" value="{{@$quantity}}"> --}}


                                {{--                                @php --}}
                                {{--                                    $i++; --}}
                                {{--                                @endphp --}}
                                {{--                            @endif --}}
                                {{--                        @endforeach --}}

                            </tbody>
                        </table>

                        <input type="hidden" name="shift_id" value="{{ $shiftId }}">
                        <input type="hidden" name="vehicle_id" id="vehicle-id">
                        <input type="hidden" name="user_id" id="user-id">

                        {{--                        <div class="form-group"> --}}
                        {{--                            <input type="submit" value="Dispatch & Close" class="btn btn-primary"> --}}
                        {{--                        </div> --}}

                        {{ Form::close() }}
                    </div>

                    <button class="btn btn-primary" data-toggle="modal" data-target="#assign-vehicle-modal"
                        data-backdrop="static"> Dispatch & Close</button>
                @endif
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
        $(function() {
            $(".mlselect").select2();
        });

        $(document).ready(function() {
            $(".getshiftdata").change(function() {
                var salesmanId = $(this).val();
                $.ajax({
                    url: "{{ route('sales-and-receivables-reports.getShiftBySalesman') }}",
                    dataType: "JSON",
                    data: {
                        '_token': "{{ csrf_token() }}",
                        salesman_id: salesmanId,
                        'shift_summary': '1'
                    },
                    success: function(result) {
                        $('.shiftList').html('');
                        $.each(result, function(key, val) {
                            $('.shiftList').append('<option value="' + key + '">' +
                                val + '</option>');
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

        $('.entered_qty').on('keyup', function() {
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
