<div style="padding:10px;">
    <div class="row">
        <div class="col-md-3">
            <label for="">Category</label>
            <p>{{ $item->category->category_description }}</p>
            <label for="">Sub Category</label>
            <p>{{ $item->sub_category->title }}</p>
            <label for="">Pack Size</label>
            <p>
                @if ($item->pack_size)
                    {{ $item->pack_size->title }}
                @else
                    -
                @endif
            </p>
        </div>
        <div class="col-md-3">
            <label for="">Prev Standard Cost</label>
            <p>{{ $item->prev_standard_cost }}</p>
            <label for="">Selling Price</label>
            <p>{{ $item->selling_price }}</p>
            <label for="">Percentage Margin</label>
            <p>{{ $item->percentage_margin }} %</p>
            <label for="">Tax</label>
            <p>
                @if ($item->getTaxesOfItem)
                    {{ $item->getTaxesOfItem->title }}
                @else
                    -
                @endif
            </p>
        </div>
        <div class="col-md-3">
            <label for="">Gross Weight</label>
            <p>{{ $item->gross_weight }}</p>
            <label for="">Net Weight</label>
            <p>{{ $item->net_weight }}</p>
        </div>
        <div class="col-md-3">
            <label for="">Status</label>
            <p>{{ $item->status == 1 ? 'Active' : 'Retired' }}</p>

            <label for="">Approval Status</label>
            <p>{{ $item->approval_status }}</p>

            <label for="">Restocking Method</label>
            <p>{{ $item->restocking_method }}</p>

            <label for="">Supplier(s)</label>
            <p>
                {{ implode(',', $item->suppliers->pluck('name')->toArray()) }}
            </p>
        </div>
    </div>
</div>
