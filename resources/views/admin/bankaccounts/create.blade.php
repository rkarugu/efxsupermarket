
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data">
            {{ csrf_field() }}

            <?php 
            $attr = 2;
            $column = 10;
            ?>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Bank Account Gl Code</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('bank_account_gl_code_id', getChartOfAccountsDropdown(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Bank Account Name</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('account_name', null, ['maxlength'=>'255','placeholder' => 'Bank Account Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Bank Account Code</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('account_code', null, ['maxlength'=>'255','placeholder' => 'Bank Account Code', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Bank Account Number</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('account_number', null, ['maxlength'=>'255','placeholder' => 'Bank Account Number', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>


            


                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Bank Address</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::text('bank_address', null, ['maxlength'=>'255','placeholder' => 'Bank Address', 'required'=>true, 'class'=>'form-control','id'=>'search_location']) !!}  
                    </div>
                </div>
            </div>

              


              


               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-{{ $attr }} control-label">Currency Of Account</label>
                    <div class="col-sm-{{ $column }}">
                        {!! Form::select('currency', getCompanyPreferencesCurrency(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select'      ]) !!}  
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
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')


<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>






<script type="text/javascript">
    $(function () {
   
    $(".mlselect").select2();
});
</script>


@endsection

