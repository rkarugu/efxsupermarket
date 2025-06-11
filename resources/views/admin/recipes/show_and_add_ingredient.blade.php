
@extends('layouts.admin.admin')

@section('content')
<?php
    $lists = $data->getAssociateIngredient;
    
?>

<?php
    if(isset($permission[$pmodule.'___ingredient-add']) || $permission == 'superadmin'){
?>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <h3 class="box-title"> {!! $title !!} </h3>
            @include('message')
        </div>
        
        
        <div>
            <div class="box-body">
                {!! Form::open(['route' => ['admin.recipes.recipe-ingredient-save', $data->slug], 'class'=>'validate form-horizontal', 'id'=>'add-update-recipe-form']) !!}
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
                            {!!Form::select('wa_inventory_item_id',$inventory_items_list, null, ['required'=>true, 'placeholder'=>'Please Select', 'class' => 'form-control mlselec6t','id'=>'wa_inventory_item_id'])!!} 
                        </div>
                    </div>

                    

                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Number of Portion:</label>
                        <div class="col-md-9">
                            <?= Form::number('number_of_portion', 1, ['required'=>true, 'class'=>'form-control', 'id'=>'number_of_portion']) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Material Cost:</label>
                        <div class="col-md-9">
                            <?= Form::number('material_cost', null, ['class'=>'form-control','id'=>'material_cost','readonly'=>true]) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Weight/Portion:</label>
                        <div class="col-md-9">
                            <?= Form::number('weight_portion', null, ['class'=>'form-control', 'id'=>'weight_portion', 'readonly'=>true]) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Weight:</label>
                        <div class="col-md-9">
                            <?= Form::number('weight', null, ['required'=>true, 'class'=>'form-control', 'id'=>'weight']) ?>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-3 control-label">Cost:</label>
                        <div class="col-md-9">
                            <?= Form::number('cost', null, ['class'=>'form-control', 'id'=>'cost', 'readonly'=>true]) ?>
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
    <?php } ?>






<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="10%">S.No.</th>
                            <th width="10%"  >Ingredient</th>
                            <th width="10%"  >Unit</th>
                            <th width="10%"  >Material cost</th>
                            <th width="10%"  >Weight</th>
                            <th width="10%"  >Portion</th>
                            <th width="10%"  >Cost</th>
                            <th  width="20%" class="noneedtoshort" >Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                    </thead>
                    <tbody>
                        @if(isset($lists) && !empty($lists))
                        <?php $b = 1; ?>
                        @foreach($lists as $list)
                        <tr>
                            <td>{!! $b !!}</td>
                            <td>{!! isset($list->getAssociateItemDetail->title) ? $list->getAssociateItemDetail->title : '' !!}</td>
                            <td>
                                <?php
                                echo isset($list->getAssociateItemDetail->getUnitOfMeausureDetail->title) ? $list->getAssociateItemDetail->getUnitOfMeausureDetail->title : ''
                                ?>
                            </td>
                            <td>{!! $list->material_cost !!}</td>
                            <td>{!! $list->weight !!}</td>
                            <td>{!! $list->number_of_portion !!}</td>
                            <td>{!! $list->cost !!}</td>
                            
                            
                            <td class = "action_crud">
                                
                                @if(isset($permission[$pmodule.'___ingredient-edit']) || $permission == 'superadmin')
                                    <span>
                                        <a title="Edit" href="<?= route('admin.recipes.recipe-ingredient-edit', $list->id) ?>">
                                            <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                        </a> 
                                    </span>
                                @endif


                                @if(isset($permission[$pmodule.'___ingredient-delete']) || $permission == 'superadmin')

                                    <span>
                                        <form title="Trash" action="{{ URL::route('admin.recipes.recipe-ingredient-delete', $list->id) }}" method="POST">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button  style="float:left"><i class="fa fa-trash" aria-hidden="true" style="font-size: 16px;"></i>
                                            </button>
                                        </form>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        <?php $b++; ?>
                        @endforeach
                        @endif

                    </tbody>
                    <tfoot>
                        <tr style="font-weight: bold;">
                            <td></td>
                             <td></td>
                              <td></td>
                               <td></td>
                                <td></td>
                                <td>Total</td>
                               <td>{{ manageAmountFormat($lists->sum('cost'))}}</td>
                                <td></td>

                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>


</section>

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
    
    $('document').ready(function(){
       manageCosts(); 
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