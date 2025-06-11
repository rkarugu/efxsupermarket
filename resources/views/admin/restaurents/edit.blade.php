@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $title }} </h3>

                    <a href="{{ route("$model.index") }}" class="btn btn-outline-primary" role="button"> << Back to Branch List </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                        <div class="col-sm-10">
                            {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Branch Code</label>
                        <div class="col-sm-10">
                            {!! Form::text('branch_code', null, ['maxlength'=>'255','placeholder' => 'Branch Code', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Company Name</label>
                        <div class="col-sm-10">
                            {!! Form::select('wa_company_preference_id', getCompanyDropdownFromPreferences(),null, ['maxlength'=>'255','placeholder' => 'Please Select', 'required'=>true, 'class'=>'form-control' ,'id'=>'selector_selects2']) !!}
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Opening Time</label>
                        <div class="col-sm-10">
                            {!! Form::text('opening_time', null, ['maxlength'=>'255','placeholder' => 'Opening Time', 'required'=>true, 'class'=>'form-control timepicker','id'=>'timepicker1']) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Closing Time</label>
                        <div class="col-sm-10">
                            {!! Form::text('closing_time', null, ['maxlength'=>'255','placeholder' => 'Closing Time', 'required'=>true, 'class'=>'form-control timepicker']) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Location</label>
                        <div class="col-sm-10">
                            {!! Form::text('location', null, ['maxlength'=>'255','placeholder' => 'Location', 'required'=>true, 'class'=>'form-control google_location','id'=>'search_location']) !!}
                            {!! Form::hidden('latitude', null, ['class'=>'form-control', 'id'=>'latitude']) !!}
                            {!! Form::hidden('longitude', null, ['class'=>'form-control', 'id'=>'longitude']) !!}
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Telephone</label>
                        <div class="col-sm-10">
                            {!! Form::text('telephone', null, ['maxlength'=>'255','placeholder' => 'Telephone Number', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Mpesa till</label>
                        <div class="col-sm-10">
                            {!! Form::text('mpesa_till', null, ['maxlength'=>'255','placeholder' => 'Mpesa Till', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
                {{-- Bank Details --}}
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Equity Account</label>
                        <div class="col-sm-10">
                            {!! Form::number('equity_account', null, ['maxlength'=>'255', 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
                  <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Equity Paybill</label>
                        <div class="col-sm-10">
                            {!! Form::number('equity_paybill', null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
                  <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Equity Payment Method</label>
                        <div class="col-sm-10">
                            <select class="form-control mlselect" name="equity_payment_method_id" id="equity_payment_method_id" required >
                                <option value="" disabled>Select Payment Method</option>
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->id}}" @if ($paymentMethod->id == $row->equity_payment_method_id)
                                        selected
                                        
                                    @endif>{{ $paymentMethod->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB MPESA Account</label>
                        <div class="col-sm-10">
                            {!! Form::number('kcb_account', null, ['maxlength'=>'255', 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>  <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB MPESA Paybill</label>
                        <div class="col-sm-10">
                            {!! Form::number('kcb_mpesa_paybill', null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB MPESA Payment Method</label>
                        <div class="col-sm-10">
                            <select class="form-control mlselect" name="mpesa_payment_method_id" id="mpesa_payment_method_id" required>
                                <option value="" disabled>Select Payment Method</option>
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->id}}"  @if ($paymentMethod->id == $row->mpesa_payment_method_id)
                                        selected
                                        
                                    @endif>{{ $paymentMethod->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB Vooma Account</label>
                        <div class="col-sm-10">
                            {!! Form::number('vooma_account', null, ['maxlength'=>'255', 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>  <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB Vooma Paybill</label>
                        <div class="col-sm-10">
                            {!! Form::number('kcb_vooma_paybill', null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">KCB Vooma Payment Method</label>
                        <div class="col-sm-10">
                            <select class="form-control mlselect" name="kcb_payment_method_id" id="kcb_payment_method_id" required>
                                <option value="" disabled>Select Payment Method</option>
                                @foreach($paymentMethods as $paymentMethod)
                                    <option value="{{$paymentMethod->id}}"  @if ($paymentMethod->id == $row->kcb_payment_method_id)
                                        selected
                                        
                                    @endif >{{ $paymentMethod->title }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {{-- End  Bank Details --}}


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Vat No</label>
                        <div class="col-sm-10">
                            {!! Form::text('vat', null, ['maxlength'=>'255','placeholder' => 'Vat No', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Pin No</label>
                        <div class="col-sm-10">
                            {!! Form::text('pin', null, ['maxlength'=>'255','placeholder' => 'Pin No', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>


                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Website Url</label>
                        <div class="col-sm-10">
                            {!! Form::text('website_url', null, ['maxlength'=>'255','placeholder' => 'Website', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Email</label>
                        <div class="col-sm-10">
                            {!! Form::email('email', null, ['maxlength'=>'255','placeholder' => 'Email', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>


                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                            <div class="col-sm-10">
                                <input type="file" name="image_update" title="Please select image" accept="image/*">

                                <img width="100px" height="100px;" src="{{ asset('uploads/restaurants/thumb/'.$row->image) }}">
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Floor Image</label>
                            <div class="col-sm-10">
                                <input type="file" name="floor_image_update" title="Please select image" accept="image/*">

                                <img width="100px" height="100px;" src="{{ asset('uploads/restaurants/thumb/'.$row->floor_image) }}">
                            </div>
                        </div>
                    </div>


                    <div class="box-footer">
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/jquery.timepicker.css')}}" rel="stylesheet"/>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="http://maps.googleapis.com/maps/api/js?libraries=places&key={{ $googleMapsApiKey }}"></script>

    <script src="{{asset('assets/admin/jquery.timepicker.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>





    <script type="text/javascript">
        $("#search_location").change(function () {
            $("#latitude").val('');
            $("#longitude").val('');

        });
        $(function () {
            $('.timepicker').timepicker({'timeFormat': 'H:i'});
            $(".mlselect").select2();

        });


        function initialize() {
            var input = document.getElementById('search_location');
            var options = {};

            var autocomplete = new google.maps.places.Autocomplete(input, options);
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                $("#latitude").val(lat);
                $("#longitude").val(lng);
            });
        }

        google.maps.event.addDomListener(window, 'load', initialize);
        $(function () {

            $("#selector_selects2").select2();
        });
    </script>

@endsection


