<?= Form::hidden('item_slug', $item_row->slug, ['id' => 'item-slug-hidden']); ?>
<?= Form::hidden('item_id', $item_row->id, ['id' => 'item-id-hidden']); ?>

<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Item Adjustment Code:</label>
    <div class="col-sm-10">
        {!!Form::text('item_adjustment_code', getCodeWithNumberSeries('ITEM ADJUSTMENT'), ['id'=>'item_adjustment_code', 'class' => 'form-control authorization_level','readonly'=>true ])!!} 
    </div>
</div>

<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Stock Code:</label>
    <div class="col-sm-10">
        {!!Form::text('stock_id_code', $item_row->stock_id_code, ['id'=>'stock_id_code_input', 'class' => 'form-control authorization_level','readonly'=>true ])!!} 
    </div>
</div>

<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Item Description:</label>
    <div class="col-sm-10">
        {!!Form::text('description', $item_row->description, ['id'=>'description', 'class' => 'form-control authorization_level','readonly'=>true ])!!} 
    </div>
</div>

<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">UOM:</label>
    <div class="col-sm-10">
        {!!Form::text('uom', $item_row->getUnitOfMeausureDetail->title, ['id'=>'uom', 'class' => 'form-control authorization_level','readonly'=>true ])!!} 
    </div>
</div>
<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost:</label>
    <div class="col-sm-10">
        {!!Form::number('standard_cost', $item_row->standard_cost, ['id'=>'standard_cost', 'class' => 'form-control authorization_level','readonly'=>true ])!!} 
    </div>
</div>

<div class="row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Adjustment To Store Location:</label>
    <div class="col-sm-10">
        {!!Form::select('wa_location_and_store_id', $locations, null, ['id'=>'location-input','class' => 'form-control authorization_level','required'=>true,'placeholder' => 'Please select', 'onchange'=>'getAndUpdateItemAvailableQuantity(this)' ])!!} 
    </div>
</div>
<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Current Qty Available:</label>
    <div class="col-sm-10">
        {!!Form::text('current_qty_available', null, ['id'=>'current_qty_available', 'class' => 'form-control authorization_level','readonly'=>true ])!!} 
    </div>
</div>

<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Comments On Why:</label>
    <div class="col-sm-10">
        {!!Form::textArea('comments', null, ['id'=>'current_qty_available', 'class' => 'form-control authorization_level', 'rows'=>4])!!} 
    </div>
</div>





<div class=" row form-group">
    <label for="inputEmail3" class="col-sm-2 control-label">Adjustment Quantity:</label>
    <div class="col-sm-10">
        {!!Form::number('adjustment_quantity', null, ['id'=>'quantity-input', 'class' => 'form-control authorization_level','required'=>true, 'placeholder' => 'Quantity' ])!!} 
    </div>
</div>



