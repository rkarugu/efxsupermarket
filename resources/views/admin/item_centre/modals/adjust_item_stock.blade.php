<div class="modal " id="manage-stock-model" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
    aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => 'maintain-items.manage-stock', 'class' => 'validate form-horizontal']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Adjust Item Stock 
                </h4>
            </div>
            <div class="modal-body">
                <div class="box-body">
                    <?= Form::hidden('item_slug', $item->slug, ['id' => 'item-slug-hidden']) ?>
                    <?= Form::hidden('item_id', $item->id, ['id' => 'item-id-hidden']) ?>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Adjustment Code:</label>
                        <div class="col-sm-10">
                            {!! Form::text('item_adjustment_code', getCodeWithNumberSeries('ITEM ADJUSTMENT'), [
                                'id' => 'item_adjustment_code',
                                'class' => 'form-control authorization_level',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Stock Code:</label>
                        <div class="col-sm-10">
                            {!! Form::text('stock_id_code', $item->stock_id_code, [
                                'id' => 'stock_id_code_input',
                                'class' => 'form-control authorization_level',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Item Description:</label>
                        <div class="col-sm-10">
                            {!! Form::text('description', $item->description, [
                                'id' => 'description',
                                'class' => 'form-control authorization_level',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">UOM:</label>
                        <div class="col-sm-10">
                            @if ($item->getUnitOfMeausureDetail)
                                {!! Form::text('uom', $item->getUnitOfMeausureDetail->title, [
                                    'id' => 'uom',
                                    'class' => 'form-control authorization_level',
                                    'readonly' => true,
                                ]) !!}
                            @else
                                {!! Form::text('uom', null, ['id' => 'uom', 'class' => 'form-control authorization_level', 'readonly' => true]) !!}
                            @endif
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Standard Cost:</label>
                        <div class="col-sm-10">
                            {!! Form::number('standard_cost', $item->standard_cost, [
                                'id' => 'standard_cost',
                                'class' => 'form-control authorization_level',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class="row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Adjustment To Store Location:</label>
                        <div class="col-sm-10">
                            {!! Form::select('wa_location_and_store_id', $getLocations, null, [
                                'id' => 'location-input',
                                'class' => 'form-control authorization_level',
                                'required' => true,
                                'placeholder' => 'Please select',
                                'onchange' => 'getAndUpdateItemAvailableQuantity(this)',
                            ]) !!}
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Current Qty Available:</label>
                        <div class="col-sm-10">
                            {!! Form::text('current_qty_available', null, [
                                'id' => 'current_qty_available',
                                'class' => 'form-control authorization_level',
                                'readonly' => true,
                            ]) !!}
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Comments On Why:</label>
                        <div class="col-sm-10">
                            {!! Form::textArea('comments', null, [
                                'id' => 'current_qty_available',
                                'class' => 'form-control authorization_level',
                                'rows' => 4,
                            ]) !!}
                        </div>
                    </div>
                    <div class=" row form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">Adjustment Quantity:</label>
                        <div class="col-sm-10">
                            {!! Form::number('adjustment_quantity', null, [
                                'id' => 'quantity-input',
                                'class' => 'form-control authorization_level',
                                'required' => true,
                                'placeholder' => 'Quantity',
                            ]) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="submit" class="btn btn-primary" value="Submit">
                <button type="button" class="btn btn-default" data-dismiss="modal">
                    Close
                </button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        function getAndUpdateItemAvailableQuantity(input_obj) {
            location_id = $(input_obj).val();
            if (location_id) {
                stock_id_code = $('#stock_id_code_input').val();
                jQuery.ajax({
                    url: '{{ route('maintain-items.get-available-quantity-ajax') }}',
                    type: 'POST',
                    dataType: "json",
                    data: {
                        location_id: location_id,
                        stock_id_code: stock_id_code
                    },
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#current_qty_available').val(response['available_quantity']);
                    }
                });
            } else {
                $('#current_qty_available').val(0);
            }

        }
    </script>
@endpush
