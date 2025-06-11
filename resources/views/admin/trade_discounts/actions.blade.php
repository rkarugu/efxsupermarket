@if (can('view', 'trade-discounts') && $discount->items->count())
    <a href="{{ route('trade-discounts.show', $discount) }}" target="_blank" title="View Details" data-toggle="tooltip">
        <i class="fa fa-eye"></i>
    </a>
@endif
@if (can('approve', 'trade-discounts') && is_null($discount->demand_no))
    <a href="#" title="Approve Discount" style="margin-left: 10px" class="edit-discount" data-toggle="tooltip"
        data-discount-action="{{ route('trade-discounts.update', $discount) }}" data-discount="{{ $discount }}">
        <i class="fa fa-edit"></i>
    </a>
@endif
@if (can('delete', 'trade-discounts') && is_null($discount->demand_no))
    <a href="#" data-toggle="discounts" data-target="#delete{{ $discount->id }}" style="margin-left: 10px">
        <span class="span-action" data-toggle="tooltip" title="Delete Discount">
            <i class="fa fa-trash"></i>
        </span>
    </a>
    <form id="delete{{ $discount->id }}" action="{{ route('trade-discounts.destroy', $discount) }}"
        style="display: none" method="post">
        @csrf()
        @method('DELETE')
    </form>
@endif
