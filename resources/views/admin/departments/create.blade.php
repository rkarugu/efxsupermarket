
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> {{ $title }} </h3>

                <a href="{{ route("$model.index") }}" class="btn btn-outline-primary" role="button"> << Back to Department List </a>
            </div>
        </div>

        <div class="box-body">
            @include('message')
            <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Department Name</label>
                        <div class="col-sm-10">
                            {!! Form::text('department_name', null, ['maxlength'=>'255','placeholder' => 'Department Name', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Department Code</label>
                        <div class="col-sm-10">
                            {!! Form::text('department_code', null, ['maxlength'=>'255','placeholder' => 'Department Code', 'required'=>true, 'class'=>'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                        <div class="col-sm-10">
                            {!!Form::select('restaurant_id', getBranchesDropdown(), null, ['class' => 'form-control','required'=>true,'placeholder' => 'Please select branch','id'=>'selector_selects2'  ])!!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Internal Requisitions Authorization</label>
                        <div class="col-sm-10">
                            {!!Form::select('authorization_user_id[]', getAuthorizerEmployee(), [], ['class' => 'form-control select2','required'=>false,'placeholder' => 'Please select branch','multiple'=>'multiple'  ])!!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">External Requisitions Authorization</label>
                        <div class="col-sm-10">
                            {!!Form::select('external_authorization_user_id[]', getExternalAuthorizerEmployee(), [], ['class' => 'form-control select2','required'=>false,'placeholder' => 'Please select branch','multiple'=>'multiple'  ])!!}
                        </div>
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Purchase Order Requisitions Authorization</label>
                        <div class="col-sm-10">
                            {!!Form::select('purchase_order_authorization_user_id[]', getPurchaseAuthorizerEmployee(), [], ['class' => 'form-control select2','required'=>false,'placeholder' => 'Please select branch','multiple'=>'multiple'  ])!!}
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
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $('.select2').select2();
    $("#selector_selects2").select2();
});
</script>

@endsection

