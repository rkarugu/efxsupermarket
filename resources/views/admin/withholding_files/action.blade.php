<a href="{{ route('withholding-files.download', $file) }}" class="row-action" data-toggle="tooltip" title="Download"
    target="_blank">
    <i class="fa fa-download"></i>
</a>
@if (can('delete', 'withholding-files'))
    <x-actions.delete-record class="row-action"
        action="{{ route('withholding-files.destroy', $file->id) }}" />
@endif
@if ($file->payment)
    <a href="{{ route('withholding-tax-payments.print', $file->payment) }}" class="row-action" data-toggle="tooltip"
        title="Payment Voucher" target="_blank">
        <i class="fa fa-file-pdf"></i>
    </a>
@else
    <a href="{{ route('withholding-tax-payments.create', ['file' => $file->id]) }}" class="row-action"
        data-toggle="tooltip" title="Create Payment Voucher">
        <i class="fa fa-file-invoice"></i>
    </a>
@endif
