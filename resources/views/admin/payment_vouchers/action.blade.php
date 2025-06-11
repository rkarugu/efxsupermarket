<div class="text-center">
    @if ($voucher->isPending())
        @if (can('approve-voucher', 'payment-vouchers') &&
                !is_null($voucher->confirmed_by) &&
                !is_null($voucher->confirmation_approval_by))
            <a href="#" data-toggle="vouchers" data-target="#approve{{ $voucher->id }}" data-action="approve">
                <span class="span-action" data-toggle="tooltip" title="Approve" style="cursor: pointer;">
                    <i class="fa fa-check-circle"></i>
                </span>
            </a>
            <form id="approve{{ $voucher->id }}" action="{{ route('payment-vouchers.approve', $voucher->id) }}"
                style="display: none" method="post">
                @csrf()
            </form>
        @endif
        @if (
            (can('confirm-details', 'payment-vouchers') || can('approve-confirmation', 'payment-vouchers')) &&
                (is_null($voucher->confirmed_by) || is_null($voucher->confirmation_approval_by)))
            <a href="{{ route('payment-vouchers.show', $voucher->id) }}">
                <span data-toggle="tooltip" title="Voucher Details" style="cursor: pointer; margin-right:5px;">
                    <i class="fa fa-indent"></i>
                </span>
            </a>
        @endif
        @if (can('edit', 'payment-vouchers'))
            <a href="#" data-toggle="vouchers" data-target="#decline{{ $voucher->id }}" data-action="decline">
                <span class="span-action" data-toggle="tooltip" title="Decline"
                    style="cursor: pointer; margin-left: 10px">
                    <i class="fa fa-ban"></i>
                </span>
            </a>
            <form id="decline{{ $voucher->id }}" action="{{ route('payment-vouchers.decline', $voucher->id) }}"
                style="display: none" method="post">
                @csrf()
            </form>
            <a href="{{ route('payment-vouchers.edit', $voucher->number) }}">
                <span class="span-action" data-toggle="tooltip" title="Edit"
                    style="cursor: pointer; margin-left: 10px">
                    <i class="fa fa-edit"></i>
                </span>
            </a>
        @endif
    @elseif($voucher->isApproved())
        @if (can('edit', 'payment-vouchers'))
            <a href="#" data-toggle="vouchers" data-target="#decline{{ $voucher->id }}" data-action="decline">
                <span class="span-action" data-toggle="tooltip" title="Decline"
                    style="cursor: pointer; margin-left: 10px">
                    <i class="fa fa-ban"></i>
                </span>
            </a>
            <form id="decline{{ $voucher->id }}" action="{{ route('payment-vouchers.decline', $voucher->id) }}"
                style="display: none" method="post">
                @csrf()
            </form>
            <a href="{{ route('payment-vouchers.edit', $voucher->number) }}">
                <span class="span-action" data-toggle="tooltip" title="Edit"
                    style="cursor: pointer; margin-left: 10px">
                    <i class="fa fa-edit"></i>
                </span>
            </a>
        @endif
    @endif
    <a href="{{ route('payment-vouchers.print_pdf', $voucher->id) }}" target="_blank">
        <span class="span-action" data-toggle="tooltip" title="Print" style="cursor: pointer; margin-left: 10px">
            <i class="fa fa-print"></i>
        </span>
    </a>
</div>
