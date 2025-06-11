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
                    <label for="inputEmail3" class="col-sm-2 control-label">From</label>
                    <div class="col-sm-10">
                        {!! Form::text('from', $row->from, ['maxlength'=>'255','placeholder' => 'From', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">To</label>
                    <div class="col-sm-10">
                        {!! Form::text('to', $row->to, ['maxlength'=>'255','placeholder' => 'To', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Rate</label>
                    <div class="col-sm-10">
                        {!! Form::text('rate', $row->rate, ['maxlength'=>'255','placeholder' => 'Rate', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
                   <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Amount</label>
                    <div class="col-sm-10">
                        {!! Form::text('amount', $row->amount, ['maxlength'=>'255','placeholder' => 'Amount', 'required'=>true, 'class'=>'form-control']) !!}  
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



    