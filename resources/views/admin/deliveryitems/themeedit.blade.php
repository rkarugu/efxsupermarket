
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.themeupdate', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-10">
                        {!! Form::text('name', null, ['maxlength'=>'255','placeholder' => 'Name', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>
            
            
             {!! Form::hidden('price', '0') !!} 

             

            

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Offer</label>
                    <div class="col-sm-10">
                        {!!Form::select('category_id', $familyGroups, $row->getItemCategoryRelation->category_id, ['placeholder'=>'Select menu item group', 'class' => 'form-control','required'=>true  ])!!} 
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Print Class</label>
                    <div class="col-sm-10">
                        {!!Form::select('print_class_ids[]', $printclasses, $row->getManyRelativePrintClasses->pluck('print_class_id'), ['data-placeholder'=>'Select print group', 'class' => 'form-control select2','multiple'=>'multiple' ,'required'=>true])!!} 
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Condiments Group</label>
                    <div class="col-sm-10">
                        {!!Form::select('condiment_group_ids[]', $getCondimentGroupsList,  $row->getManyRelativeCondimentsGroup->pluck('condiment_group_id'), ['data-placeholder'=>'Select condiments group', 'class' => 'form-control select2','multiple'=>'multiple' ])!!} 

                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">PLU Number</label>
                    <div class="col-sm-10">
                        {!!Form::select('plu_number', $pluNumberList, null, ['placeholder'=>'Select plu number', 'class' => 'form-control select2'  ])!!} 
                    </div>
                </div>
            </div>
            
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Image</label>
                    <div class="col-sm-10">
                        <input type = "file" name = "image_update" title = "Please select image"  accept="image/*">
                        <?php
                        $image_url = $row->image?'uploads/menu_items/thumb/'.$row->image:'uploads/item_none.png';
                        ?>
                        <img width="100px" height="100px;"src="{{ asset($image_url) }}">
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
    $('.select2').select2();
});
</script>

@endsection


