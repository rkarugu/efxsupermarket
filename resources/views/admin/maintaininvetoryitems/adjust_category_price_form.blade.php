<div id="costable">
	<table class="table" style="width: 100%">
		<tr>
			<th>Standard Cost</th>
			<td>{{manageAmountFormat($item_row->standard_cost)}}</td>
			@if ($item_row->getTaxesOfItem)
				<th>Standard Cost with Vat</th>
				<td>
					{{manageAmountFormat($item_row->standard_cost + (($item_row->standard_cost*$item_row->getTaxesOfItem->tax_value)/100))}}
				</td>
			@endif
			<th>Selling Price Inc Vat</th>
			<td>
				{{manageAmountFormat($item_row->selling_price)}}
			</td>
		</tr>
	</table>
</div>
<?= Form::hidden('item_slug', $item_row->slug, ['id' => 'item-slug-hidden']); ?>
<?= Form::hidden('item_id', $item_row->id, ['id' => 'item-id-hidden']); ?>
@foreach($categories as $key=> $val)
<?= Form::hidden('category_id[]', $val->id, ['id' => 'item-id-hidden']); ?>
	<div class="row form-group">
	    <label for="inputEmail3" class="col-sm-2 control-label">Category</label>
	    <div class="col-sm-10">
	        {!!Form::text('category', $val->title, ['id'=>'current_qty_available', 'class' => 'form-control','readonly'=>true ])!!} 
	    </div>
	</div>
	<div class=" row form-group">
	    <label for="inputEmail3" class="col-sm-2 control-label">Price:</label>
	    <div class="col-sm-10">
	        {!!Form::text('category_price[]', App\Model\WaCategoryItemPrice::getitemcatprice($item_row->id,$val->id), ['id'=>'category_price', 'class' => 'form-control' ])!!} 
	    </div>
	</div>
@endforeach
