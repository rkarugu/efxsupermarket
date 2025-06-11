
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Currency</label>
                    <div class="col-sm-10">
                         {!!Form::select('ISO4217', getCurrencyDropdown(), null, ['class' => 'form-control','required'=>true,'placeholder' => 'Please select currency'  ])!!} 
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Country</label>
                    <div class="col-sm-10">
                        {!! Form::text('country', null, ['maxlength'=>'255','placeholder' => 'Country Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

               

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Decimal places</label>
                    <div class="col-sm-10">
                         {!!Form::select('decimal_places', ['0'=>'0','1'=>'1','2'=>'2','3'=>'3'], 2, ['class' => 'form-control','required'=>true  ])!!} 
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Show In Webshop</label>
                    <div class="col-sm-10">
                         {!!Form::select('show_in_webshop', ['0'=>'No','1'=>'Yes'], null, ['class' => 'form-control','required'=>true  ])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Exchange Rate</label>
                    <div class="col-sm-10">
                        {!! Form::number('exchange_rate', null, ['min'=>'0', 'required'=>true, 'class'=>'form-control']) !!}  
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


