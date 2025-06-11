
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Title</label>
                    <div class="col-sm-10">
                        {!! Form::text('title', null, ['maxlength'=>'255','placeholder' => 'Title', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            
           
             
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tax Value (%)</label>
                    <div class="col-sm-10">
                        {!! Form::number('tax_value', null, ['maxlength'=>'255','placeholder' => 'Tax value', 'required'=>true, 'class'=>'form-control','min'=>'0']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Input tax GL Account</label>
                    <div class="col-sm-10">
                        {!! Form::select('input_tax_gl_account', getChartOfAccountsDropdown(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Output tax GL Account</label>
                    <div class="col-sm-10">
                        {!! Form::select('output_tax_gl_account', getChartOfAccountsDropdown(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select']) !!}  
                    </div>
                </div>
            </div>

             
             


            <div class="box-footer">
                <button type="submit" class="btn btn-primary">Update</button>
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


