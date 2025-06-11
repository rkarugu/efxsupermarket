
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Term Code</label>
                    <div class="col-sm-10">
                        {!! Form::text('term_code', null, ['maxlength'=>'255','placeholder' => 'Term Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Term Description</label>
                    <div class="col-sm-10">
                        {!! Form::text('term_description', null, ['maxlength'=>'255','placeholder' => 'Term Description', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Due After A Given No. Of Days</label>
                    <div class="col-sm-10">
                          {!! Form::checkbox('due_after_given_month', null) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Days (Or Day In Following Month)</label>
                    <div class="col-sm-10">
                          {!! Form::number('days_in_following_months', null, ['min'=>'0','placeholder' => '', 'required'=>true, 'class'=>'form-control']) !!}  
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


