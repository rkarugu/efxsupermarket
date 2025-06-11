
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Price</label>
                    <div class="col-sm-10">
                        {!! Form::number('price', null, ['maxlength'=>'255','placeholder' => 'Price', 'required'=>true, 'class'=>'form-control','min'=>'1']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Max Selection Limit</label>
                    <div class="col-sm-10">
                        {!! Form::number('max_selection_limit', null, ['maxlength'=>'255','placeholder' => 'Selection Limit', 'required'=>true, 'class'=>'form-control','min'=>'1']) !!}  
                    </div>
                </div>
            </div>
           
           


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Sub Major Group name</label>
                    <div class="col-sm-10">
                        {!!Form::select('parent_id', $getParentList, null, ['placeholder'=>'Select Sub Major group ', 'class' => 'form-control','required'=>true,'title'=>'Please sub major group '  ])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Description</label>
                    <div class="col-sm-10">
                        {!! Form::textarea('description', null, ['maxlength'=>'1000','placeholder' => 'Description', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Tax</label>
                    <div class="col-sm-10">
                        {!!Form::select('tax_manager_ids[]', $all_taxes, null, ['data-placeholder'=>'Select tax', 'class' => 'form-control select2','multiple'=>'multiple' ])!!} 

                    </div>
                </div>
            </div>
           
           
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type = "file" name = "image" title = "Please select image" required accept="image/*" >
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
<link href="{{asset('assets/admin/jquery.timepicker.css')}}" rel="stylesheet" />
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
@endsection

@section('uniquepagescript')




 <script src="{{asset('assets/admin/jquery.timepicker.js')}}"></script>

<script type="text/javascript">
  
 $(function () {
   $('.timepicker').timepicker({ 'timeFormat': 'H:i' });
});

</script>

<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(function () {
    $('.select2').select2();
});
</script>


@endsection


