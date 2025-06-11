
@extends('layouts.admin.admin')

@section('content')


<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="box box-primary">
        <div class="box-header with-border no-padding-h-b">
            <div align = "right">
            <?php if (isset($permission[$pmodule . '___add']) || $permission == 'superadmin') { ?>
                <button type="button" class="btn btn-success" data-toggle="modal" onclick="openAddRecipePopup();">Add New Recipe</button>
            <?php } ?>
            
            </div>
            <br/>
            @include('message')
            <div class="col-md-12 no-padding-h">
                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                        <tr>
                            <th width="5%">S.No.</th>
                            <th width="15%"  >Recipe No</th>
                            <th width="15%"  >Name</th>
                            <th width="10%"  >Number of Ingredient</th>
                            <th width="10%"  >COS</th>
                            <th width="15%"  >Base Units</th>
                            <th width="15%"  >Date</th>
                            <th  width="15%" class="noneedtoshort" >Action</th>

                            <!--th style = "width:10%" class = "noneedtoshort">Date</th-->

                        </tr>
                    </thead>
                    <tbody>
                        <?php if(isset($lists) && !empty($lists)) { ?>
                        <?php $b = 1; ?>
                        <?php foreach($lists as $list) { ?>
                        <?php
                        $ingredient_rows =  $list->getAssociateIngredient;
                        $number_of_ingredient = count($ingredient_rows);
                        $cos = 0;
                        foreach($ingredient_rows as $key_ingredient => $row_ingredient ){
                            $cos += $row_ingredient->cost;
                        }
                        ?>
                        <tr>
                            <td>{!! $b !!}</td>

                            <td>{!! $list->recipe_number !!}</td>
                            <td>{!! $list->title !!}</td>
                            <td>{!! $number_of_ingredient !!}</td>
                            <td>{!! $cos !!}</td>
                            <td>{!! @$list->getUnitOfMeausureDetail->title !!}</td>
                            <td><?= date('Y-m-d', strtotime($list->created_at)) ?></td>
                            
                            
                            <td class = "action_crud">
                                
                                {{-- @if(isset($permission[$pmodule.'___ingredient-view']) || $permission == 'superadmin') --}}
                                <span>
                                    <a title="View" href="{{ route($model.'.show', $list->slug) }}" >
                                        <i class="fa fa-eye" aria-hidden="true"></i>
                                    </a>
                                </span>
                                {{-- @endif --}}
                                
                                {{-- @if(isset($permission[$pmodule.'___edit']) || $permission == 'superadmin') --}}
                                    <span>
                                        <a title="Edit" href="javascript:void(0);" onclick='openManageRecipeEditPopup("<?= $list->id ?>")'>
                                            <img src="{!! asset('assets/admin/images/edit.png') !!}" alt="">
                                        </a>
                                    </span>
                                {{-- @endif --}}


                                {{-- @if(isset($permission[$pmodule.'___delete']) || $permission == 'superadmin') --}}

                                    <span>
                                        <form title="Trash" action="{{ URL::route($model.'.destroy', $list->slug) }}" method="POST">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button  style="float:left"><i class="fa fa-trash" aria-hidden="true" style="font-size: 16px;"></i>
                                            </button>
                                        </form>
                                    </span>
                                {{-- @endif --}}
                            </td>
                        </tr>
                        <?php
                        $b++;
                        } } ?>
                        

                    </tbody>
                </table>
            </div>
        </div>
    </div>


</section>

@include('admin.recipes.add_new_recipe_popup')

<script>

function openAddRecipePopup(){
    $('#add-update-recipe-form').trigger("reset");
    $('#recipe_id').val(null);
    $('#manage-recipe-model .modal-title').text('Add New Recipe');
    $('#manage-recipe-model').modal('show');
}
    
    
function openManageRecipeEditPopup(recipe_id){
    
    jQuery.ajax({
        url: '{{route('admin.recipes.editForm')}}',
        type: 'POST',
        dataType:'Json',
        data:{recipe_id:recipe_id},
        headers: {
        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) 
        {
            $('#recipe_id').val(response['id']);
            $('#recipe_recipe_number').val(response['recipe_number']);
            $('#recipe_title').val(response['title']);
            $('#recipe_major_group_id').val(response['major_group_id']);
            $('#recipe_unit_of_mesaurement_id').val(response['unit_of_mesaurement_id']);
            $('#recipe_wa_location_and_store_id').val(response['wa_location_and_store_id']);
            $('#manage-recipe-model .modal-title').text('Edit Recipe');
            $('#manage-recipe-model').modal('show');
        }
    });
}

</script>


@endsection
