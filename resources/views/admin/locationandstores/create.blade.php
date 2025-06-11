@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Add Location and Store </h3>

                    <a href="{{ route("$model.index") }}" role="button" class="btn btn-primary"> << Back to Location and Stores </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form class="validate form-horizontal" role="form" method="POST" action="{{ route($model.'.store') }}" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Location Code</label>
                            <div class="col-sm-10">
                                {!! Form::text('location_code', null, ['maxlength'=>'255','placeholder' => 'Location Code', 'required'=>true, 'class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Location Name</label>
                            <div class="col-sm-10">
                                {!! Form::text('location_name', null, ['maxlength'=>'255','placeholder' => 'Location Name', 'required'=>true, 'class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    {{-- <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Credit Limit</label>
                            <div class="col-sm-10">
                                {!! Form::number('credit_limit', 0.00, ['maxlength'=>'255','placeholder' => 'Credit Limit', 'required'=>true, 'class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div> --}}


                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                            <div class="col-sm-10">
                                {!!Form::select('wa_branch_id', $restroList, null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Route</label>
                            <div class="col-sm-10">
                                {!!Form::select('route_id', $route, null, ['class' => 'form-control mlselect','required'=>true,'placeholder' => 'Please select'  ])!!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Block This</label>
                            <div class="col-sm-10">
                                {!! Form::checkbox('is_cost_centre', 1) !!}
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Is Physical Store?</label>
                            <div class="col-sm-10">
                                <input class="form-check-input" type="checkbox" id="is_physical_store" name="is_physical_store" value="1">
{{--                                {!! Form::checkbox('is_physical_store', null, ['class' => 'form-control']) !!}--}}
                            </div>
                        </div>
                    </div>


                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Account No</label>
                            <div class="col-sm-10">
                                {!! Form::text('account_no', null, ['maxlength'=>'255','placeholder' => 'Account No', 'required'=>false, 'class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Biller No</label>
                            <div class="col-sm-10">
                                {!! Form::text('biller_no', null, ['maxlength'=>'255','placeholder' => 'Biller No', 'required'=>false, 'class'=>'form-control']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Bin Location</label>
                            <div class="col-sm-10">
                                <select name='bin_locations[]' class='form-control bin_locations' multiple="multiple">
                                  
                                </select> 
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
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
            $('.bin_locations').select2(
            {
                placeholder:'Select Bin Location',
                ajax: {
                    url: '{{route("uom.dropdown_search")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.title};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        });
    </script>

@endsection



