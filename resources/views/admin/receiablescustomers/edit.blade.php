@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> {!! $title !!} </h3>

                    <a href="{{ route("$model.index") }}" role="button" class="btn btn-primary"> << Back to Customer Listing </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                {!! Form::model($row, [
                    'method' => 'PATCH',
                    'route' => [$model . '.update', $row->slug],
                    'class' => 'validate',
                    'enctype' => 'multipart/form-data',
                ]) !!}

                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Customer Code</label>
                        <div class="col-sm-10">
                            {!! Form::text('customer_code', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Customer Code',
                                'required' => true,
                                'class' => 'form-control',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Customer Name</label>
                        <div class="col-sm-10">
                            {!! Form::text('customer_name', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Customer Name',
                                'required' => true,
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Address</label>
                        <div class="col-sm-10">
                            {!! Form::text('address', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Address',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Telephone</label>
                        <div class="col-sm-10">
                            {!! Form::text('telephone', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Telephone',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            {!! Form::email('email', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Email',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Customer Since</label>
                        <div class="col-sm-10">


                            {!! Form::text('customer_since', null, [
                                'maxlength' => '255',
                                'placeholder' => 'Customer Since',
                                'class' => 'form-control datepicker',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Credit Limit</label>
                        <div class="col-sm-10">
                            {!! Form::number('credit_limit', null, [
                                'min' => '0',
                                'placeholder' => 'Credit Limit',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Return Limit</label>
                        <div class="col-sm-10">
                            {!! Form::number('return_limit', null, [
                                'min' => '0',
                                'placeholder' => 'Return Limit',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Equity Till</label>
                        <div class="col-sm-10">
                            {!! Form::number('equity_till', null, [
                                'min' => '0',
                                'placeholder' => 'Equity Till',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="equity_payment_method_id" class="col-sm-2 control-label">EQUITY Payment Channel</label>
                        <div class="col-sm-10">
                            <select name="equity_payment_method_id" id="equity_payment_method_id" class="form-control" required>
                                <option value="">Select channel</option>
                                @foreach ($paymentMethods as $method)
                                    <option @if ($row->equity_payment_method_id == $method->id) selected @endif value="{{ $method->id }}">
                                        {{ $method->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB Till</label>
                        <div class="col-sm-10">
                            {!! Form::number('kcb_till', null, [
                                'min' => '0',
                                'placeholder' => 'KCB Till',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="kcb_payment_method_id" class="col-sm-2 control-label">KCB Payment Channel</label>
                        <div class="col-sm-10">
                            <select name="kcb_payment_method_id" id="kcb_payment_method_id" class="form-control" required>
                                <option value="">Select channel</option>
                                @foreach ($paymentMethods as $method)
                                    <option @if ($row->kcb_payment_method_id == $method->id) selected @endif value="{{ $method->id }}">
                                        {{ $method->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                @php
                    $used_limit = \App\Model\WaDebtorTran::where('wa_customer_id', $row->id)->sum('amount');
                @endphp
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Current A/C Balance</label>
                        <div class="col-sm-10">
                            <span class="form-control">{{ manageAmountFormat($used_limit) }}</span>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Available Limit</label>
                        <div class="col-sm-10">
                            <span class="form-control">{{ manageAmountFormat($row->credit_limit - $used_limit) }}</span>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Payment Terms</label>
                        <div class="col-sm-10">
                            {!! Form::select('payment_term_id', paymentTermsList(), null, [
                                'placeholder' => 'Please Select',
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}

                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                        <div class="col-sm-10">
                            {{-- {!!Form::select('route_id',$route, null, ['placeholder'=>'Please Select', 'class' => 'form-control','required'=>true  ])!!} --}}

                            <select name="route_id" id="route_id" class="form-control" required>
                                <option value="">Select Route</option>
                                @foreach ($route as $ing)
                                    <option @if ($row->route_id == $ing->id) selected @endif value="{{ $ing->id }}">
                                        {{ $ing->route_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="is_credit_customer" class="col-sm-2 control-label">Is Invoice Customer</label>
                        <div class="col-sm-10">
                            {!! Form::checkbox('is_invoice_customer', 1, $row ->is_invoice_customer, ['id' => 'is_invoice_customer', 'class' => 'form-check']) !!}
                        </div>

                    </div>
                </div>

                <div class="box-body xyz">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Delivery Route</label>
                        <div class="col-sm-10">
                            {!! Form::select('delivery_route_id', $delivery_routes, $row->delivery_route_id, [
                                'placeholder' => 'Please Select',
                                'id'=>'delivery_route_id',
                                'class' => 'form-control',
                                'required' => true,
                            ]) !!}

                        </div>
                    </div>
                </div>
                <div class="box-body xyz">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KRA PIN</label>
                        <div class="col-sm-10">
                            {!! Form::text('kra_pin', null, [
                                'placeholder' => 'KRA Pin',
                                'class' => 'form-control',
                            ]) !!}
                        </div>
                    </div>
                </div>

{{--                @if($row->associatedRouteCustomer)--}}
{{--                    <div class="box-body">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="inputEmail3" class="col-sm-2 control-label">Select Center</label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <select name="center_id" id="center_id" class="form-control">--}}
{{--                                    <option value="">Select Center</option>--}}

{{--                                </select>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="box-body">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="location_name" class="col-sm-2 control-label"> Shop Location </label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" name="location_name" id="location_name" placeholder="Search location"--}}
{{--                                       class="form-control" required value="{{ $row->associatedRouteCustomer->location_name }}">--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="box-body">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="lat" class="col-sm-2 control-label"> Latitude </label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" name="lat" id="lat" placeholder="Latitude" class="form-control"--}}
{{--                                       required value="{{ $row->associatedRouteCustomer->lat }}">--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="box-body">--}}
{{--                        <div class="form-group">--}}
{{--                            <label for="lng" class="col-sm-2 control-label"> Longitude </label>--}}
{{--                            <div class="col-sm-10">--}}
{{--                                <input type="text" name="lng" id="lng" placeholder="Longitude" class="form-control"--}}
{{--                                       required value="{{ $row->associatedRouteCustomer->lng }}">--}}
{{--                            </div>--}}

{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}



                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Block This Customer</label>
                        <div class="col-sm-10">
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="is_blocked" id="is_blocked"
                                           value="1" {{ $row->is_blocked == 1 ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
                {{ Form::close() }}
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        $(function () {
            $("#route_id").select2();
        });
    </script>

    <script>
        $(document).ready(function() {

            if ($('#is_invoice_customer').is(':checked')) {
                $('.xyz').show();
            } else {
                $('.xyz').hide();
            }

            $('#is_invoice_customer').change(function() {
                if ($(this).is(':checked')) {
                    $('.xyz').show();
                } else {
                    $('.xyz').hide();
                }
            });
        });
    </script>


    <script>
        $(document).ready(function () {
            $('#route_id').change(function () {
                var selectedOption = $(this).val();
                $.ajax({
                    url: '/api/webRouteDeliveryCentres', // Replace with your API endpoint
                    method: 'POST',
                    data: {
                        route_id: selectedOption
                    },
                    success: function (response) {

                        var select = $('#center_id');
                        select.empty(); // Clear previous options

                        $.each(response.centres, function (key, value) {
                            select.append($('<option></option>')
                                .attr('value', value
                                    .id) // Assuming an ID field in your data
                                .text(value.name)
                            ); // Assuming a name field in your data
                        });

                    },
                    error: function (error) {
                        console.log(error);
                    }
                });
            });

        });


        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
