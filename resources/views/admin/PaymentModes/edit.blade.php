
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.update',['id'=>$row->id]) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Payment Mode</label>
                    <div class="col-sm-10">
                        {!! Form::text('mode', $row->mode, ['maxlength'=>'255','placeholder' => 'Payment Mode', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
                  <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                      {!! Form::textarea('description',$row->description,['rows' => 4, 'cols' => 40,'maxlength'=>'255','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}
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

