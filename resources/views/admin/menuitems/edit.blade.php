
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
                        {!! Form::text('price', null, ['maxlength'=>'10','placeholder' => 'Price', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Recipe Cost</label>
                    <div class="col-sm-10">
                        {!! Form::text('recipe_cost', null, ['maxlength'=>'10','min'=>'1','placeholder' => 'Recipe Cost', 'required'=>true, 'class'=>'form-control recipe_cost', 'readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Family Group</label>
                    <div class="col-sm-10">
                        {!!Form::select('category_id', $familyGroups, @$row->getItemCategoryRelation->category_id, ['placeholder'=>'Select family group', 'class' => 'form-control select2','required'=>true  ])!!} 
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
                        {!!Form::select('print_class_ids[]', $printclasses, $row->getManyRelativePrintClasses->pluck('print_class_id'), ['data-placeholder'=>'Select print group', 'class' => 'form-control select2','multiple'=>'multiple','required'=>true ])!!} 
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
                    <label for="inputEmail3" class="col-sm-2 control-label">Tax</label>
                    <div class="col-sm-10">
                        {!!Form::select('tax_manager_ids[]', $all_taxes, $row->getManyRelativeTaxes->pluck('tax_manager_id'), ['data-placeholder'=>'Select tax', 'class' => 'form-control select2','multiple'=>'multiple' ])!!} 

                    </div>
                </div>
            </div>
  
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Recipe Name</label>
                    <div class="col-sm-10">
                        {!!Form::select('wa_recipe_id', $recipe_list, null, ['placeholder'=>'Select Recipe', 'class' => 'form-control select2 autofillrecipeAmount', 'required'=>true  ])!!} 
                    </div>
                </div>
            </div>



             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Show on Menu?</label>
                    <div class="col-sm-10">
                        {!! Form::checkbox('is_available_in_stock', null) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Show To Customer?</label>
                    <div class="col-sm-10">
                        {!! Form::checkbox('show_to_customer', null) !!}  
                    </div>
                </div>
            </div>

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Show To Waiter?</label>
                    <div class="col-sm-10">
                        {!! Form::checkbox('show_to_waiter', null) !!}  
                    </div>
                </div>
            </div>
            
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Check Stock before Sale</label>
                    <div class="col-sm-10">
                        {!! Form::checkbox('check_stock_before_sale', null) !!}  
                    </div>
                </div>
            </div>
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">Recipe Mandatory?</label>
                    <div class="col-sm-10">
                        {!! Form::checkbox('recipe_mandatory', null) !!}  
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
$(document).on('change','.autofillrecipeAmount',function(){
    var recipeId = $(this).val();
    // 
    var token = "{{csrf_token()}}";
    var myKeyVals = { _token:token, wa_recipe_id : recipeId }



var saveData = $.ajax({
      type: 'POST',
      url: "{{route('admin.autofillrecieptAmnt')}}",
      data: myKeyVals,
      dataType: "json",
      success: function(resultData) {
	      var costing = resultData.data.cost;
          $('.recipe_cost').val(costing.toFixed(2));
        
          //console.log('response +++ '+JSON.stringify(resultData.data.cost)); 
         // alert("Save Complete")
           }
});
saveData.error(function() { alert("Something went wrong"); });
});
</script>

@endsection

