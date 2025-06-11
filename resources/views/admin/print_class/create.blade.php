
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <br/>
                <br/>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">IP</label>
                    <div class="col-sm-10">
                        {!! Form::text('ip', null, ['maxlength'=>'255','placeholder' => 'IP', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <br/>
                <br/>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Port</label>
                    <div class="col-sm-10">
                        {!! Form::text('port', null, ['maxlength'=>'255','placeholder' => 'Port', 'required'=>true, 'class'=>'form-control']) !!}  
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

@endsection

@section('uniquepagescript')


@endsection


