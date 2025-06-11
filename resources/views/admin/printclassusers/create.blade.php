
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
            </div>
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Restaurant</label>
                    <div class="col-sm-10">
                        {!!Form::select('restaurant_id', $restroList, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control','required'=>true,'title'=>'Please select Branch'  ])!!}
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Print Class</label>
                    <div class="col-sm-10">
                        {!!Form::select('print_class_id', $printClassList, null, ['placeholder'=>'Select Print Class ', 'class' => 'form-control','required'=>true,'title'=>'Please select role'  ])!!}
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-10">
                        {!! Form::text('username', null, ['maxlength'=>'255','placeholder' => 'Username', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Password</label>
                    <div class="col-sm-10">
                        <input type = "password" name="password" placeholder="Password" maxlength="30" class="form-control">
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Can Cancle Item ?</label>
                    <div class="col-sm-1" style="padding-top: 5px;">
                        {!! Form::checkbox('can_cancle_item', null) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body" style="display:none">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Can Print Bill ?</label>
                    <div class="col-sm-1" style="padding-top: 5px;">
                        {!! Form::checkbox('can_print_bill', null) !!}  
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


