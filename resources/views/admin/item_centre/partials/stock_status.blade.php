<div style="padding: 10px">
    <form action="{{ route('maintain-items.update-stock-status') }}" method="post" class="submitMe">
        @csrf

        <input type="hidden" name="inventory_id" value="{{ $item->id }}">
        <input type="hidden" name="stockIdCode" value="{{ $item->stock_id_code }}">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="1%">S.No.</th>
                    <th width="10%">Store Location</th>
                    <th width="10%">Quantity On Hand</th>
                    <th width="10%">Max Stock</th>
                    <th width="10%">Re-Order Level</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($locations as $location)
                
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ ucfirst($location->location_name) }}</td>
                        <td>{{ $isAdmin || $hasPermission || $location->id == $authuserlocation ? $location->qoh : '#####' }}</td>
                        <td>
                            <input type="text" class="form-control" name="max_stock[{{ $location->id }}]"
                                value="{{ $isAdmin || $hasPermission || $location->id == $authuserlocation ? $location->max_stock : '#####' }}" 
                                {{-- @if (!$isAdmin && !$hasPermission && $location->id != $authuserlocation && (!can('edit-max-stock', 'maintain-items'))) readonly @endif> --}}
                                @if (!$isAdmin && !$hasPermission && $location->id != $authuserlocation) readonly @endif>
                        </td>
                        <td>
                            <input type="text" class="form-control" name="re_order_level[{{ $location->id }}]"
                                value="{{ $isAdmin || $hasPermission || $location->id == $authuserlocation ? $location->re_order_level : '#####' }}" 
                                {{-- @if (!$isAdmin && !$hasPermission && $location->id != $authuserlocation && (!can('edit-reorder-level', 'maintain-items'))) readonly @endif> --}}
                                @if (!$isAdmin && !$hasPermission && $location->id != $authuserlocation) readonly @endif>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <button type="submit" class="btn btn-primary">Update Stock Details</button>
    </form>
</div>
