
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Period No</label>
                    <div class="col-sm-10">
                        {!! Form::text('period_no', null, ['maxlength'=>'255','placeholder' => 'Period No', 'required'=>true, 'class'=>'form-control numberwithhifun']) !!}  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Start Date</label>
                    <div class="col-sm-10">
                          {!! Form::text('start_date', null, ['maxlength'=>'255','placeholder' => 'Start Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">End Date</label>
                    <div class="col-sm-10">
                           {!! Form::text('end_date', null, ['maxlength'=>'255','placeholder' => 'End Date', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!} 
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Is Active Period ?</label>
                    <div class="col-sm-10">
                           {!! Form::checkbox('is_current_period', null) !!}  
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
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection



