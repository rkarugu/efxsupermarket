<div class="col-md-12">
    <table class="table table-hover table-inverse table-bordered">
        <thead>
            <tr>
                <th>Ln</th>
                <th>Code</th>
                <th>Product Description</th>
                <th>Unit</th>
                <th>Qty</th>
                <th>Return</th>
                <th>Sell Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @if (count($data->items)>0)
                @foreach ($data->items->where('is_dispatched',0)->where('store_location_id',$getLoggeduserProfile->wa_location_and_store_id) as $key => $item)
                    <tr>
                        <td>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="item_id[{{$item->id}}]" value="{{$item->id}}" >
                                {{$key+1}}
                              </label>
                            </div>
                        </td>
                        <td>{{@$item->item->stock_id_code}}</td>
                        <td>{{@$item->item->description}}</td>
                        <td>{{@$item->item->pack_size->title}}</td>
                        @php
                            $quantity = $item->qty-@$item->dispatch_details->sum('dispatch_quantity');
                        @endphp
                        <td>
                            <div class="form-group">
                              <input type="number" name="item_qty[{{$item->id}}]" onkeyup="getTotal(this);" onchange="getTotal(this);" data-max="{{$quantity}}" max="{{$quantity}}" class="item_qty form-control" value="{{$quantity}}" aria-describedby="helpId">
                            </div>
                        </td>
                        <td>0.00</td>
                        <td> <span class="selling_price">{{$item->selling_price}}</span></td>
                        <td class="total">{{$item->selling_price*$quantity}}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>
<div class="col-md-12">
    <button type="submit" class="btn btn-danger">Dispatch</button>
</div>