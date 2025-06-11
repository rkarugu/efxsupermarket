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

                <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model . '.store') }}"
                      enctype="multipart/form-data">
                    {{ csrf_field() }}

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Customer Code</label>
                            <div class="col-sm-10">
                                {!! Form::text('customer_code', getCodeWithNumberSeries('CUSTOMERS'), [
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
                            <label for="inputEmail3" class="col-sm-2 control-label">Phone Number</label>
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
                            <label for="inputEmail3" class="col-sm-2 control-label">Email Address</label>
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
                            <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                            <div class="col-sm-10">
                                <select name="route_id" id="route_id" class="form-control" required>
                                    <option value="" selected disabled>Select Route</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                                    @endforeach
                                </select>

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
                            <label for="equity_payment_method_id" class="col-sm-2 control-label">Equity Payment Channel</label>
                            <div class="col-sm-10">
                                <select name="equity_payment_method_id" id="equity_payment_method_id" class="form-control" required>
                                    <option value="" selected disabled>Select channel</option>
                                    @foreach ($paymentMethods as $method)
                                        <option value="{{ $method->id }}">{{ $method->title }}</option>
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
                            <label for="is_credit_customer" class="col-sm-2 control-label">Is Invoice Customer</label>
                            <div class="col-sm-10">
                                {!! Form::checkbox('is_invoice_customer', 1, false, ['id' => 'is_invoice_customer', 'class' => 'form-check']) !!}
                            </div>

                        </div>
                    </div>

                    <div class="box-body xyz" style="display:none;">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">KRA PIN</label>
                            <div class="col-sm-10">
                                {!! Form::text('kra_pin', null, [
                                    'placeholder' => 'KRA Pin',
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="is_dependent" class="col-sm-2 control-label">Is Dependent Customer</label>
                            <div class="col-sm-10">
                                {!! Form::checkbox('is_dependent', 1, false, ['id' => 'is_dependent', 'class' => 'form-check']) !!}
                            </div>

                        </div>
                        <div class="dependent" style="display:none;">
                            <div class="form-group dependent">
                                <label for="inputEmail3" class="col-sm-2 control-label">Delivery Route</label>
                                <div class="col-sm-10">
                                    {!! Form::select('delivery_route_id', $delivery_routes, null, [
                                        'placeholder' => 'Please Select',
                                        'id'=>'delivery_route_id',
                                        'class' => 'form-control',
                                        'required' => true,
                                    ]) !!}

                                </div>
                            </div>
                            <div class="form-group dependent">
                                <label for="inputEmail3" class="col-sm-2 control-label">Select Center</label>
                                <div class="col-sm-10">
                                    <select name="center_id" id="center_id" class="form-control">
                                        <option value="">Select Center</option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-group dependent">
                                <label for="location_name" class="col-sm-2 control-label"> Shop Location </label>
                                <div class="col-sm-10">
                                    <input type="text" name="location_name" id="location_name" placeholder="Search location"
                                           class="form-control" required value="{{ old('location_name') }}">
                                </div>

                            </div>
                            <div class="form-group dependent">
                                <label for="lat" class="col-sm-2 control-label"> Latitude </label>
                                <div class="col-sm-10">
                                    <input type="text" name="lat" id="lat" placeholder="Latitude" class="form-control"
                                           required value="{{ old('lat') }}">
                                </div>

                            </div>
                            <div class="form-group dependent">
                                <label for="lng" class="col-sm-2 control-label"> Longitude </label>
                                <div class="col-sm-10">
                                    <input type="text" name="lng" id="lng" placeholder="Longitude" class="form-control"
                                           required value="{{ old('lng') }}">
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
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
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{config('app.google_maps_api_key') }}"></script>

    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $(function () {
            $("#route_id").select2();
            $("#delivery_route_id").select2();
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#is_invoice_customer').change(function() {
                if ($(this).is(':checked')) {
                    $('.xyz').show();
                } else {
                    $('.xyz').hide();
                }
            });
            $('#is_dependent').change(function() {
                if ($(this).is(':checked')) {
                    $('.dependent').show();
                } else {
                    $('.dependent').hide();
                }
            });
        });
    </script>

    <script>

        $(document).ready(function () {
            $('#delivery_route_id').change(function () {
                var selectedOption = $(this).val();
                $.ajax({
                    url: '/api/webRouteDeliveryCentres',
                    method: 'GET',
                    data: {
                        route_id: selectedOption
                    },
                    success: function (response) {

                        var select = $('#center_id');
                        select.empty();

                        $.each(response.centres, function (key, value) {
                            select.append($('<option></option>')
                                .attr('value', value
                                    .id)
                                .text(value.name)
                            );
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

    <script type="text/javascript">
        // Searchable selects
        $("#center_id").select2();

        function initializeRouteLocationSearch() {
            let StartingLocationInput = document.getElementById('location_name');
            let locationoptions = {};

            let autocomplete = new google.maps.places.Autocomplete(StartingLocationInput, locationoptions);

            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                let location_place = autocomplete.getPlace();
                let location_lat = location_place.geometry.location.lat();
                let location_lng = location_place.geometry.location.lng();
                $("#lat").val(location_lat);
                $("#lng").val(location_lng);
            });
        }



        google.maps.event.addDomListener(window, 'load', initializeRouteLocationSearch);

    </script>
@endsection
