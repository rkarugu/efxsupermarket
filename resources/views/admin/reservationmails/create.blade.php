
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
                    <label for="inputEmail3" class="col-sm-3 control-label">Phone Number(+254)</label>
                    <div class="col-sm-9">
                        {!! Form::text('phone_number', null, ['maxlength'=>'20','placeholder' => 'Phone number', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>

                 <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Email</label>
                    <div class="col-sm-9">
                        {!! Form::email('email', null, ['maxlength'=>'255','placeholder' => 'Email', 'required'=>true, 'class'=>'form-control']) !!}  
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


