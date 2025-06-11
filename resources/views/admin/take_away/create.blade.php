
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Title', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Branch</label>
                    <div class="col-sm-10">
                        {!!Form::select('restaurant_id', $restroList, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control','required'=>true,'title'=>'Please select Branch'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Url</label>
                    <div class="col-sm-10">
                        {!! Form::url('url', null, ['maxlength'=>'255','placeholder' => 'Url', 'required'=>true, 'class'=>'form-control']) !!}  
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


