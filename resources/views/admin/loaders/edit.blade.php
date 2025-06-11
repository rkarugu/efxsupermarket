@extends('layouts.admin.admin')

@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Update Loader </h3>

                <a href="{!! route('loaders.index')!!}" class="btn btn-primary"> << Back Loaders </a>
            </div>
        </div>

         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('loaders.update', $loader->id) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            @method('PATCH')
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', $loader->name, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                   
                </div>
               
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                    <div class="col-sm-10">
                        {!!Form::select('restaurant_id', getRestaurants(), $loader->restaurant_id, ['placeholder'=>'Select Branch ', 'class' => 'form-control','required'=>true,'title'=>'Please select Branch','id'=>'branch'  ])!!}
                    </div>

                   
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Phone Number</label>
                    <div class="col-sm-10">
                        {!! Form::text('phone_number', $loader->phone_number, ['maxlength'=>'255','placeholder' => 'Phone Number', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                    
                </div>
            </div>

            <div class="box-boddy">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">ID Number</label>
                    <div class="col-sm-10">
                        {!! Form::text('id_number', $loader->id_number, ['maxlength'=>'255','placeholder' => 'Id Number', 'required'=>true, 'class'=>'form-control']) !!}
                    </div>
                </div>
            </div>
   
            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
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


<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script type="text/javascript">
//on document ready
$(document).ready(function() {
$('#branch').select2();
});
</script>


</script>
    
@endsection