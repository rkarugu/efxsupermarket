
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        {{-- <div class="box-header with-border">
            <h3 class="box-title"> {{$inventoryItem->title}} | Create Discount Band </h3>    
            <a href="{{ route('maintain-items.index') }}" class="btn btn-success">Back</a>

        </div> --}}

        <div class="d-flex justify-content-between align-items-center" style="padding-left: 5px;padding-right: 5px">
            <h3 class="box-title"> {{$inventoryItem->title}} | Create Discount Band </h3>    
            {{-- <a href="{{ route('maintain-items.index') }}" class="btn btn-success">Back</a> --}}
        </div>

         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route('discount-bands.store', $inventoryItem->id) }}" enctype = "multipart/form-data">
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">From Quantity</label>
                    <div class="col-sm-9" style="">
                        {!! Form::number('from_quantity', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">To Quantity</label>
                    <div class="col-sm-9">
                        {!! Form::number('to_quantity', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control' ]) !!}  
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Discount Amount</label>
                    <div class="col-sm-9">
                        {!! Form::number('discount_amount', null, ['maxlength'=>'255','placeholder' => '0', 'required'=>true, 'class'=>'form-control']) !!}  
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



