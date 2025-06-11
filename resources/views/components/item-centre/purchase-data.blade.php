<div style="padding: 10px">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th width="3%">S.No.</th>
                <th>Supplier</th>
                <th>Price</th>
                <th>Cost Per Our Unit</th>
                <th>Currency</th>
                <th>Effective From</th>
                <th>Preferred</th>
                <th class="noneedtoshort">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($item_suppliers as $list)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $list->supplier?->supplier_code }}</td>
                    <td>{{ $list->price }}</td>
                    <td>{{ $list->our_unit_of_measure }}</td>
                    <td>{{ $list->currency }}</td>
                    <td>{{ $list->price_effective_from }}</td>
                    <td>{{ $list->preferred_supplier }}</td>
                    <td style="display:flex">
                        {!! buttonHtmlCustom('edit', route('maintain-items.purchaseDataEdit', ['stockid' => encrypt($inventoryItem->id), 'itemid' => encrypt($list->id)])) !!}
                        {!! buttonHtmlCustom('delete', route('maintain-items.purchaseDataDelete', ['stockid' => encrypt($inventoryItem->id), 'itemid' => encrypt($list->id)])) !!}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($suppliers->count())
        <form action="{{ route('maintain-items.purchaseDataAdd', ['stockid' => $inventoryItem->id]) }}" method="get">
            <input type="hidden" name="stockid" value="{{ $inventoryItem->id }}">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th width="3%">S.No.</th>
                        <th>Supplier Code</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($suppliers as $supplier)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td><input type="submit" name="supplier_code" value="{{ $supplier->supplier_code }}"
                                    class="btn btn-primary btn-sm"></td>
                            <td>{{ $supplier->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </form>
    @else
        <center><b>No Supplier found</b></center>
    @endif
</div>
