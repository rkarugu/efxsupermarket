<div class="modal " id="manage-stock-model" role="dialog" aria-hidden="true" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => 'admin.opening-balances.stock-takes.add-stock-check-file','class'=>'validate form-horizontal']) !!}
            <div class="modal-header">
                <button type="button" class="close" 
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Add Stock check File 
                </h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Store Location</label>
                        <div class="col-sm-10">
                            {!!Form::select('wa_location_and_store_id', getStoreLocationDropdown(), null, ['id'=>'location-input','class' => 'form-control wa_location_and_store_id authorization_level store_location_id mlselec6t','required'=>true,])!!} 
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Bin Location</label>
                        <div class="col-sm-10">
                            {!!Form::select('wa_unit_of_measure_id[]', [], null, ['id'=>'location-input','class' => 'form-control wa_unit_of_measures_id wa_unit_of_measure_id authorization_level mlselec6t','required'=>true, 'multiple'=>true])!!} 
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Inventory Category</label>
                        <div class="col-sm-10">
                            {!! Form::select('wa_inventory_category_id[]', getInventoryCategoryList() ,null, ['class'=>'form-control wa_inventory_category_id mlselec6t', 'multiple'=>'multiple']) !!}  
                        </div>
                    </div>                    
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Action:</label>
                        <div class="col-sm-10">
                            <?php
                            //$action_arr = [1=>'Make a new stock check', 2=>'Add/Update Existing Stock'];
                            $action_arr = [2=>'Add/Update Existing Stock'];
                            ?>
                            {!!Form::select('action_add_or_update', $action_arr, 2, ['id'=>'location-input','class' => 'form-control authorization_level','required'=>true,'placeholder' => 'Please select'])!!} 
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Only print item with 0 quantities:</label>
                        <div class="col-sm-10">
                            <?= Form::checkbox('quantities_zero', 1) ?>
                        </div>
                    </div>            
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-success" value="Print and Process">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div> 
