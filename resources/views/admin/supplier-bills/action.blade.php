@if (is_null($bill->payment_voucher_id))
    @if (can('edit', 'supplier-bills'))
        <span class="row-action">
            <a href="{{ route('supplier-bills.edit', $bill) }}" data-toggle="tooltip" title="Edit Bill"
                style="margin-left: 5px">
                <i class="fa fa-edit"></i>
            </a>
        </span>
    @endif
    @if (can('delete', 'supplier-bills'))
        <x-actions.delete-record identifier="bill{{ $bill->id }}"
            action="{{ route('supplier-bills.destroy', $bill->id) }}" />
    @endif
@elseif($bill->payment_voucher_id && auth()->user()->isAdministrator())
    <a href="{{ route('credit-debit-bills.edit', $bill) }}" data-toggle="tooltip" title="Edit bill"
        style="margin-left: 5px">
        <i class="fa fa-edit"></i>
    </a>
@endif
