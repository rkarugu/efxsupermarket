
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
                        {!!Form::select('parent_id', $getParentList, $row->getRelativeData->parent_id, ['placeholder'=>'Select Sub Major group ', 'class' => 'form-control','required'=>true,'title'=>'Please sub major group '  ])!!} 
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
                        {!!Form::select('tax_manager_ids[]', $all_taxes, $row->getManyRelativeTaxes->pluck('tax_manager_id'), ['data-placeholder'=>'Select tax', 'class' => 'form-control select2','multiple'=>'multiple' ])!!} 

                    </div>
                </div>
            </div>
            
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type = "file" name = "image_update" title = "Please select image"  accept="image/*">
                        <img width="100px" height="100px;"src="{{ asset('uploads/menu_item_groups/thumb/'.$row->image) }}">
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

