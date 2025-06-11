
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">

            <h3 class="box-title"> Create Wallet Matrix </h3> 
            <a href="{{route('wallet-matrix.index')}}" class="btn btn-primary">{{'<< '}} Back </a>       
            </div>    
        </div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('wallet-matrix.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                        <label for="parameter" class="col-sm-2 control-label">Parameter</label>
                        <div class="col-sm-9" style="">
                            {!! Form::text('parameter', null, ['maxlength'=>'255','placeholder' => 'parameter', 'required'=>true, 'class'=>'form-control' ]) !!}  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="salesman_rate" class="col-sm-2 control-label">Salesman %</label>
                        <div class="col-sm-9" style="">
                            {!! Form::number('salesman_rate', null, ['maxlength'=>'255','placeholder' => '0', 'class'=>'form-control' ]) !!}  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="delivery_driver_rate" class="col-sm-2 control-label">Delivery Driver %</label>
                        <div class="col-sm-9">
                            {!! Form::number('delivery_driver_rate', null, ['maxlength'=>'255','placeholder' => '0', 'class'=>'form-control' ]) !!}  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="turn_boy_rate" class="col-sm-2 control-label">Turn Boy %</label>
                        <div class="col-sm-9">
                            {!! Form::number('turn_boy_rate', null, ['maxlength'=>'255','placeholder' => '0', 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="driver_grn_rate" class="col-sm-2 control-label">Driver GRN %</label>
                        <div class="col-sm-9">
                            {!! Form::number('driver_grn_rate', null, ['maxlength'=>'255','placeholder' => '0', 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                </div>              
            </div>  
            <div class="box-footer">
                <div class="d-flex justify-content-between align-items-center">
                    {{-- <div></div> --}}

                <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection



