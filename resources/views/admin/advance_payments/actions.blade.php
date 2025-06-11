@if ($advance->payment)   
    <a href="{{ route('payment-vouchers.print_pdf', $advance->payment->voucher) }}" style="margin-left:5px"
        data-toggle="tooltip" title="Payment Voucher" target="_blank">
        <i class="fa fa-file-pdf-o"></i>
    </a>
@else
    <a href="{{ route('maintain-suppliers.payment_vouchers.create', ['code' => $advance->supplier->supplier_code, 'type' => 'advance']) }}"
        data-toggle="tooltip" title="Create Voucher" target="_blank">
        <i class="fa fa-file-text-o"></i>
    </a>
    <a href="#" data-url="{{ route('advance-payments.destroy', $advance) }}" class="text-danger destroy"
        data-toggle="tooltip" title="Delete Advance" style="margin-left:5px">
        <i class="fa fa-trash-o"></i>
    </a>
@endif
