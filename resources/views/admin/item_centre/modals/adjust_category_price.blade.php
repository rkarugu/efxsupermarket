<div class="modal " id="manage-category-model" role="dialog" tabindex="-1" aria-hidden="true" role="dialog"
    aria-labelledby="myModalLabel1">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            {!! Form::open(['route' => 'maintain-items.manage-category-price', 'class' => 'validate form-horizontal']) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel1">
                    Adjust Item Category Price : <span id="moretitle"></span>
                </h4>
            </div>
            <div class="modal-body">

                <div class="box-body">
                    <div id="costable">
                        <table class="table" style="width: 100%">
                            <tr>
                                <th>Standard Cost</th>
                                <td>{{ manageAmountFormat($item->standard_cost) }}</td>
                                @if ($item->getTaxesOfItem)
                                    <th>Standard Cost with Vat</th>
                                    <td>
                                        {{ manageAmountFormat($item->standard_cost + ($item->standard_cost * $item->getTaxesOfItem->tax_value) / 100) }}
                                    </td>
                                @endif
                                <th>Selling Price Inc Vat</th>
                                <td>
                                    {{ manageAmountFormat($item->selling_price) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                    <?= Form::hidden('item_slug', $item->slug, ['id' => 'item-slug-hidden']) ?>
                    <?= Form::hidden('item_id', $item->id, ['id' => 'item-id-hidden']) ?>
                    @foreach ($categories as $key => $val)
                        <?= Form::hidden('category_id[]', $val->id, ['id' => 'item-id-hidden']) ?>
                        <div class="row form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Category</label>
                            <div class="col-sm-10">
                                {!! Form::text('category', $val->title, [
                                    'id' => 'current_qty_available',
                                    'class' => 'form-control',
                                    'readonly' => true,
                                ]) !!}
                            </div>
                        </div>
                        <div class=" row form-group">
                            <label for="inputEmail3" class="col-sm-2 control-label">Price:</label>
                            <div class="col-sm-10">
                                {!! Form::text('category_price[]', App\Model\WaCategoryItemPrice::getitemcatprice($item->id, $val->id), [
                                    'id' => 'category_price',
                                    'class' => 'form-control',
                                ]) !!}
                            </div>
                        </div>
                    @endforeach
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
