
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Edit Discount Band </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('discount-bands.update', $discountBand->id) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">From Quantity</label>
                    <div class="col-sm-9" >
                        {!! Form::number('from_quantity', $discountBand->from_quantity, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">To Quantity</label>
                    <div class="col-sm-9">
                        {!! Form::number('to_quantity', $discountBand->to_quantity, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount Amount</label>
                    <div class="col-sm-9">
                        {!! Form::number('discount_amount', $discountBand->discount_amount, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
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



