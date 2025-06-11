@extends('layouts.admin.admin')

@section('content')
    <form action="{{ route('maintain-customers.route_customer_store', $customer->id) }}" method="post" class="submitMe validate">
        {{ csrf_field() }}
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <h4>{{ $title }}</h4>
                    <hr>

                    @include('message')

                    <div class="col-md-12 no-padding-h table-responsive">
                        <div>
                            <input type="hidden" name="customer_id" value="{{ $customer->id }}">
                            <input type="hidden" name="route_id" id="route_id" value="{{ $customer->route_id }}"
                        </div>
                        <div class="form-group">
                            <label for="">Customer Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                   placeholder="Enter Name" aria-describedby="helpId" required>
                        </div>
                        <div class="form-group">
                            <label for="">Phone No</label>
                            <input type="text" name="phone_no" id="phone_no" class="form-control"
                                   placeholder="Enter Phone No" aria-describedby="helpId" required>
                        </div>
                        <div class="form-group">
                            <label for="">Business Name</label>
                            <input type="text" name="business_name" id="business_name" class="form-control"
                                   placeholder="Enter Business Name" aria-describedby="helpId" required>
                        </div>

                        <div class="form-group">
                            <label for="">KRA PIN</label>
                            <input type="text" name="kra_pin" id="kra_pin" class="form-control" placeholder="KRA PIN">
                        </div>

                        <div class="form-group">
                            <label for="">Town</label>
                            <input type="text" name="town" id="town" class="form-control"
                                   placeholder="Enter Town" aria-describedby="helpId">
                        </div>
                        <div class="form-group">
                            <label for="">Contact Person</label>
                            <input value="{{ $customer->contact_person }}" type="text" name="contact_person" id="contact_person" class="form-control"
                                   placeholder="Enter Contact Person" aria-describedby="helpId">
                        </div>

{{--                        <div class="form-group">--}}
{{--                            <label for="is_credit_customer" class="control-label">Is Credit Customer</label>--}}
{{--                            <input type="hidden" name="is_credit_customer" value="0">--}}
{{--                            {!! Form::checkbox('is_credit_customer', 1, false, ['id' => 'is_credit_customer', 'class' => 'form-check']) !!}--}}
{{--                        </div>--}}
{{--                        <div class="form-group xyz" style="display:none;">--}}
{{--                            <label for="inputEmail3" class="control-label">Credit Limit</label>--}}
{{--                            {!! Form::number('credit_limit', null, [--}}
{{--                                     'min' => '0',--}}
{{--                                     'placeholder' => 'Credit Limit',--}}
{{--                                     'class' => 'form-control',--}}
{{--                                 ]) !!}--}}
{{--                        </div>--}}
{{--                        <div class="form- xyz" style="display:none;">--}}
{{--                            <label for="inputEmail3" class="control-label">Return Limit</label>--}}
{{--                            {!! Form::number('return_limit', null, [--}}
{{--                                    'min' => '0',--}}
{{--                                    'placeholder' => 'Return Limit',--}}
{{--                                    'class' => 'form-control',--}}
{{--                                ]) !!}--}}
{{--                        </div>--}}
{{--                        <div class="form-group xyz" style="display:none;">--}}
{{--                            <label for="inputEmail3" class="control-label">Payment Terms</label>--}}
{{--                            {!! Form::select('payment_term_id', paymentTermsList(), null, [--}}
{{--                                'placeholder' => 'Please Select',--}}
{{--                                'class' => 'form-control',--}}
{{--                                'required' => true,--}}
{{--                            ]) !!}--}}

{{--                        </div>--}}



                        <div class="form-group">
                            <label for="inputEmail3" class="">Select Center</label>
                            <div>
                                <select name="center_id" id="center_id" class="form-control">
                                    <option value="">Select Center</option>
                                    @foreach ($centers as $center)
                                        <option @if ($center->id == $customer->center_id) selected @endif  value="{{ $center->id }}">{{ $center->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="location_name" class="control-label"> Shop Location </label>
                            <input type="text" name="location_name" id="location_name" placeholder="Search location"
                                   class="form-control" required value="{{ old('location_name') }}">
                        </div>

                        <div class="form-group">
                            <label for="lat" class="control-label"> Latitude </label>
                            <input type="text" name="lat" id="lat" placeholder="Latitude" class="form-control"
                                   required value="{{ old('lat') }}">
                        </div>

                        <div class="form-group">
                            <label for="lng" class="control-label"> Longitude </label>
                            <input type="text" name="lng" id="lng" placeholder="Longitude" class="form-control"
                                   required value="{{ old('lng') }}">
                        </div>

                        <button type="submit" class="btn btn-sm btn-danger">Save</button>
                    </div>
                </div>
            </div>
        </section>
    </form>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 60px !important;
        }

        .align_float_right {
            text-align: right;
        }

        .textData table tr:hover {
            background: #000 !important;
            color: white !important;
            cursor: pointer !important;
        }


        /* ALL LOADERS */

        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('uniquepagescript')
    <div id="loader-on" class="loder"
         style="
position: fixed;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
        <div class="loader " id="loader-1"></div>
    </div>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    {{--    <script  src="https://maps.googleapis.com/maps/api/js?libraries=maps,places&key={{ $google_maps_api_key }}"></script>--}}
    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key={{ $google_maps_api_key }}"></script>

    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#is_credit_customer').change(function() {
                if ($(this).is(':checked')) {
                    $('.xyz').show();
                } else {
                    $('.xyz').hide();
                }
            });
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
