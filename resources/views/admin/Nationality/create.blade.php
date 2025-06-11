
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Nationality</label>
                    <div class="col-sm-10">
                        {!! Form::text('nationality', null, ['maxlength'=>'255','placeholder' => 'Nationality', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                   <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                      {!! Form::textarea('description',null,['rows' => 4, 'cols' => 40,'maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}
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



    