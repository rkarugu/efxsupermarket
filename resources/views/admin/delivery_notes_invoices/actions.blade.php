<span class="row-action">
    <a href="javascript:void(0);" data-url="{{ route('completed-grn.printToPdf', $grn->grn_number) }}"
        data-toggle="document" data-title="{{ $grn->grn_number }}" title="Export GRN" id="generate-download-grn-pdf">
        <i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
    </a>
</span>
<span class="row-action">
    <a href="{{ route('delivery-notes-invoices.create', ['grn' => $grn->grn_number]) }}" data-toggle="toggle"
        title="Post Delivery Invoice">
        <i class="fa fa-eye"></i>
    </a>
</span>
