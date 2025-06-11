<div class="modal " id="manage-recipe-model" role="dialog" tabindex="-1"  aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => 'admin.recipes.add','class'=>'validate form-horizontal', 'id'=>'add-update-recipe-form']) !!}
            <div class="modal-header">
                <button type="button" class="close" 
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Add New Recipe
                </h4>
            </div>
            <div class="modal-body">
                <div class="box-body">

                    {!! Form::hidden('id', null, ['id'=>'recipe_id']) !!}
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-4 control-label">Recipe No:</label>
                        <div class="col-md-8">
                            {!! Form::text('recipe_number', getCodeWithNumberSeries('RECIPE'), ['id'=>'recipe_recipe_number', 'maxlength'=>'255','placeholder' => 'Stock ID Code', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-4 control-label">Recipe Name:</label>
                        <div class="col-md-8">
                            {!! Form::text('title', null, ['id'=>'recipe_title', 'maxlength'=>'255','placeholder' => 'Recipe Name', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                    
                  
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-4 control-label">Major Group:</label>
                        <div class="col-md-8">
                            {!! Form::select('major_group_id', $major_group_list,null, ['id'=>'recipe_major_group_id', 'maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-4 control-label">Base unit:</label>
                        <div class="col-md-8">
                            {!! Form::select('unit_of_mesaurement_id', $unit_of_measure_list,null, ['id'=>'recipe_unit_of_mesaurement_id', 'maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="inputEmail3" class="col-md-4 control-label">Store Location:</label>
                        <div class="col-md-8">
                            {!! Form::select('wa_location_and_store_id', $location_list,null, ['id'=>'recipe_wa_location_and_store_id', 'maxlength'=>'255','placeholder' => 'Please select', 'required'=>true, 'class'=>'form-control']) !!}  
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <?= Form::submit('Submit', ['class' => 'btn btn-primary']) ?>

                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>

            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div> 

