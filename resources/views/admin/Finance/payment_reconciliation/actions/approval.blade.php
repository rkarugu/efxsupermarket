@if (!$payment->isProcessing())
    <a onclick="approveAll({{ $payment->id }})" role="button" title="Approve All"><i class="fa fa-solid fa-check"></i></a>
@endif
<a title="Excel" href="{{ route('payment-reconciliation.approval.excel', $payment->id) }}" style="margin-left:5px;">
    <i aria-hidden="true" class="fa fa-file-excel"></i>
</a>
