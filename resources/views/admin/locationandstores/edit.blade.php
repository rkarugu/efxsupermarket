@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Edit Location and Store </h3>

                    <a href="{{ route("$model.index") }}" role="button" class="btn btn-primary"> << Back to Location and Stores </a>
                </div>
            </div>

            <div class="box-primary">
                <div class="session-message-container">
                    @include('message')
                </div>

                {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Location Code</label>
                        <div class="col-sm-10">
                            {!! Form::text('location_code', null, ['maxlength'=>'255','placeholder' => 'Location Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}
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
                            {!!Form::select('route_id', $route, null, ['class' => 'form-control mlselect','placeholder' => 'Please select'  ])!!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Block This</label>
                        <div class="col-sm-10">
                            {!! Form::checkbox('is_cost_centre', null) !!}
                        </div>
                    </div>
                </div>
                <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Is Physical Store?</label>
                            <div class="col-sm-10">
                                <input class="form-check-input" type="checkbox" id="is_physical_store" name="is_physical_store" value="1"
                                @if($row->is_physical_store == '1') checked @endif>
{{--                                {!! Form::checkbox('is_physical_store', 0) !!}--}}
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
                                @foreach($row->bin_locations as $cat)
                                <option value="{{$cat->id}}" selected>{{$cat->title}}</optiom>
                                @endforeach
                            </select> 
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
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
            $('.bin_locations').select2(
            {
                placeholder:'Select Sub Category',
                ajax: {
                    url: '{{route("uom.dropdown_search",['id'=>$row->id])}}',
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



