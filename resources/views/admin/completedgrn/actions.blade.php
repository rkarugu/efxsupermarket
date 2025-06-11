@if (!$grn->returns_to_print_count)
    <span class="row-action">
        <a href="javascript:void(0);" data-url="{{ route('completed-grn.printToPdf', $grn->grn_number) }}"
            data-toggle="document" data-title="{{ $grn->grn_number }}" title="Export GRN" id="generate-download-grn-pdf">
            <i aria-hidden="true" class="fa fa-file-pdf" style="font-size: 20px;"></i>
        </a>
    </span>
    <span class="row-action">
        <a title="Received Note" data-toggle="tooltip" href="{{ route('completed-grn.printNote', $grn->grn_number) }}"
            target="_blank">
            <i aria-hidden="true" class="fa fa-file-text" style="font-size: 20px;"></i>
        </a>
    </span>
@endif
@if ($grn->returns->count())
    <span class="row-action">
        <a title="Print GRN Returns" data-toggle="tooltip"
            href="{{ route('return-to-supplier.from-grn.print', $grn->returns->first()->return_number) }}"
            target="_blank">
            <i aria-hidden="true" class="fa fa-file-export" style="font-size: 20px;"></i>
        </a>
    </span>
@endif
@if (isset($grn->lpo->documents) || (isset($grn->rlpo->documents) && $grn->rlpo->documents != '[]'))
    @php $documents = $grn->lpo?->documents; @endphp
    @php $documents_r = $grn->rlpo?->documents; @endphp
    <span class="row-action">
        <a data-toggle="modal" title="View Documents" href='#modal-id{{ $grn->id }}'>
            <i class="fa fa-file" style="font-size: 20px;"></i>
        </a>
        <div class="modal fade" id="modal-id{{ $grn->id }}">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title text-left">View Documents</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-hover">
                            @forelse (json_decode(isset($documents) ? $documents : $documents_r, true) as $key => $val)
                                <tr>
                                    <th>
                                        {{ strtoupper(str_replace('_', ' ', $key)) }}
                                    </th>
                                    <td>
                                        <a href="#" data-title="{{ strtoupper(str_replace('_', ' ', $key)) }}"
                                            data-toggle="documents"
                                            data-url="{{ str_contains($val, 'http') ? $val : asset('uploads/purchases_docs/' . @$val) }}">
                                            View <i class="fa fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2">No Files</td>
                                </tr>
                            @endforelse
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </span>
@endif
@if (!$grn->documents_sent && can('confirm', 'confirmed-receive-purchase-order'))
    @php
        $documents = json_decode($grn->lpo?->documents);
        $documents_r = json_decode($grn->rlpo?->documents);

        $link = $documents->supplier_invoice ?? ($documents_r->supplier_invoice ?? '');
    @endphp

    <span class="row-action">
        <a href="#" data-toggle="invoice"
            data-url="{{ str_contains($link, 'http') ? $link : asset('uploads/purchases_docs/' . $link) }}"
            data-title="Supplier Invoice" data-action="{{ route('grns.send-documents', $grn->grn_number) }}">
            <i aria-hidden="true" class="fa fa-envelope" style="font-size: 20px;"></i>
        </a>
    </span>
@endif
