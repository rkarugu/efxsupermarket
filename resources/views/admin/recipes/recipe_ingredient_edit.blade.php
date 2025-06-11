
@extends('layouts.admin.admin')

@section('content')
<?php
    $lists = $data->getAssociateIngredient;
    
?>



<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <h3 class="box-title"> {!! $title !!} </h3>
        </div>
        @include('message')
        <div>
            <div class="box-body">
                {!! Form::open(['route' => ['admin.recipes.recipe-ingredient-update', $ingredient->id], 'class'=>'validate form-horizontal', 'id'=>'add-update-recipe-form']) !!}
                <div class = "row">

                    {!! Form::hidden('wa_recipe_id', $data->id) !!}
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Recipe Name:</label>
                        <div class="col-md-9">
                            {!! Form::text('recipe_number', $data->title, ['maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Recipe No:</label>
                        <div class="col-md-9">
                            {!! Form::text('recipe_number', $data->recipe_number, ['maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                        </div>
                    </div>



                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Stock Item:</label>
                        <div class="col-md-9">
                            {!!Form::select('wa_inventory_item_id',$inventory_items_list, $ingredient->wa_inventory_item_id, ['required'=>true, 'placeholder'=>'Please Select', 'class' => 'form-control mlselec6t','id'=>'wa_inventory_item_id'])!!} 
                        </div>
                    </div>

                    

                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Number of Portion:</label>
                        <div class="col-md-9">
                            <?= Form::number('number_of_portion', $ingredient->number_of_portion, ['required'=>true, 'class'=>'form-control', 'id'=>'number_of_portion']) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Material Cost:</label>
                        <div class="col-md-9">
                            <?= Form::number('material_cost', $ingredient->material_cost, ['class'=>'form-control','id'=>'material_cost','readonly'=>true]) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Weight/Portion:</label>
                        <div class="col-md-9">
                            <?= Form::number('weight_portion', $ingredient->weight_portion, ['class'=>'form-control', 'id'=>'weight_portion', 'readonly'=>true]) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Weight:</label>
                        <div class="col-md-9">
                            <?= Form::number('weight', $ingredient->weight, ['required'=>true, 'class'=>'form-control', 'id'=>'weight']) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Cost:</label>
                        <div class="col-md-9">
                            <?= Form::number('cost', $ingredient->cost, ['class'=>'form-control', 'id'=>'cost', 'readonly'=>true]) ?>
                        </div>
                    </div>
                    
                    
                    

                </div>
                <div class="modal-footer">
                    <?= Form::submit('Submit', ['class' => 'btn btn-primary']) ?>
                </div>
                {!! Form::close() !!}


            </div>

        </div>

</section>







<!-- Main content -->


@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 
<style type="text/css">
   .select2{
    width: 100% !important;
   }
   #note{
    height: 80px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    var inventory_items_list_data = <?php echo json_encode($inventory_items_list_data) ?>;
    $(function () {
        $(".mlselec6t").select2();
    });
    
    $('#weight, #wa_inventory_item_id, #number_of_portion').bind("change keyup input",function() { 
        manageCosts();
    });
    
    
    function manageCosts(){
        inventory_id = $('#wa_inventory_item_id').val();
        if(inventory_id){
            standard_cost = inventory_items_list_data[inventory_id]['standard_cost'];
            number_of_portion = $('#number_of_portion').val();
            $('#material_cost').val(standard_cost);
            number_of_portion = $('#number_of_portion').val();
            weight_portion = standard_cost/number_of_portion;
            $('#weight_portion').val(weight_portion);
            weight = $('#weight').val();
            cost = standard_cost * weight;
            $('#cost').val(cost);
        }
    }

</script>
@endsection