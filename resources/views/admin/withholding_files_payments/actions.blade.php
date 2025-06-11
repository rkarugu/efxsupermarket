@if (can('approve', 'withholding-tax-payments') && $voucher->isPending())
    <a href="#" data-toggle="vouchers" data-target="#approve{{ $voucher->id }}" data-action="approve">
        <span class="span-action" data-toggle="tooltip" title="Approve" style="cursor: pointer;">
            <i class="fa fa-check-circle"></i>
        </span>
    </a>
    <form id="approve{{ $voucher->id }}" action="{{ route('withholding-tax-payments.approve', $voucher->id) }}"
        style="display: none" method="post">
        @csrf()
    </form>
@endif
@if ((can('edit', 'withholding-tax-payments') && $voucher->isPending()) || auth()->user()->isAdministrator())
    <a href="{{ route('withholding-tax-payments.edit', $voucher->id) }}">
        <span class="span-action" data-toggle="tooltip" title="Edit Voucher" style="margin-left: 10px">
            <i class="fa fa-edit"></i>
        </span>
    </a>
@endif
@if ((can('delete', 'withholding-tax-payments') && $voucher->isPending()) || auth()->user()->isAdministrator())
    <a href="#" data-toggle="vouchers" data-target="#delete{{ $voucher->id }}" data-action="delete">
        <span class="span-action" data-toggle="tooltip" title="Delete Voucher" style="margin-left: 5px">
            <i class="fa fa-trash"></i>
        </span>
    </a>
    <form id="delete{{ $voucher->id }}" action="{{ route('withholding-tax-payments.destroy', $voucher->id) }}"
        style="display: none" method="post">
        @method('DELETE')
        @csrf()
    </form>
@endif
<a href="{{ route('withholding-tax-payments.print', $voucher->id) }}" target="_blank">
    <span class="span-action" data-toggle="tooltip" title="Print Voucher" style="cursor: pointer; margin-left: 10px">
        <i class="fa fa-print"></i>
    </span>
</a>
