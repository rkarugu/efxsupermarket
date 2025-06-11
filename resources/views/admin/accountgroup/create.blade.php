
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Group Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('group_name', null, ['maxlength'=>'255','placeholder' => 'Group Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

                <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Parent Group</label>
                    <div class="col-sm-10">
                         {!!Form::select('parent_id', getParentAccountGroupsDropdown(), null, ['class' => 'form-control','required'=>false,'placeholder' => 'Please select group','id'=>'selector_selects2'   ])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Section In Accounts</label>
                    <div class="col-sm-10">
                         {!!Form::select('wa_account_section_id', getAccountSectionDropdown(), null, ['class' => 'form-control','required'=>true,'placeholder' => 'Please select section','id'=>'selector_selects'  ])!!} 
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Profit And Loss</label>
                    <div class="col-sm-10">
                         {!!Form::select('profit_and_loss', ['Y'=>'Yes','N'=>'No'], null, ['class' => 'form-control','required'=>true  ])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sequence In TB</label>
                    <div class="col-sm-10">
                        {!! Form::text('sequence_in_tb', null, ['maxlength'=>'255','placeholder' => 'Sequence In TB', 'required'=>true, 'class'=>'form-control numberwithhifun']) !!}  
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
      $("#selector_selects").select2();
      $("#selector_selects2").select2();
     
});
</script>
@endsection


