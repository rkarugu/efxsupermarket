
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
            <h3 class="box-title"> {!! $title !!} </h3>
            <div>
                <a href="{{route('petty-cash-types.index')}}" class="btn btn-success">Back</a>
            </div>
            </div>
        </div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => ['petty-cash-types.update', $row->id],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
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
                    <label for="inputEmail3" class="col-sm-2 control-label">GL Account</label>
                    <div class="col-sm-10">
                        {!! Form::select('gl_account', getChartOfAccountsDropdown(),$row->wa_chart_of_accounts_id, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select']) !!}  
                    </div>
                </div>
            </div>

              {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Output tax GL Account</label>
                    <div class="col-sm-10">
                        {!! Form::select('output_tax_gl_account', getChartOfAccountsDropdown(),null, ['maxlength'=>'255', 'required'=>true, 'class'=>'form-control mlselect','placeholder'=>'Please select']) !!}  
                    </div>
                </div>
            </div> --}}

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


