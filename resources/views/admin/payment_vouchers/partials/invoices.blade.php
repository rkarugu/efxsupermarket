<tr>
    <td class="details-control text-center">
        <i class="fa fa-plus-circle" style="cursor: pointer; font-size: 16px;" aria-hidden="true"></i>
    </td>
    <td>{{ $voucherItem->invoice?->supplier_invoice_date->format('d/m/Y') }}</td>
    <td>{{ $voucherItem->payable->purchaseOrder?->purchase_no }}</td>
    <td>{{ $voucherItem->payable->suppreference }}</td>
    <td>{{ $voucherItem->payable->cu_invoice_number }}</td>
    <td>{{ $voucherItem->invoice?->grn_number }}</td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->invoice?->amount) }}
    </td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->payable->withholding_amount) }}
    </td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->payable->notes->sum('amount')) }}
    </td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->amount) }}
    </td>
    <td class="text-center">
        <div class="form-group" style="margin: 0">
            @if ($voucher->confirmed_by)
                <input type="checkbox" class="items" disabled checked name="items[]" data-toggle="tooltip"
                    title="Confirmed By: {{ $voucher->confirmedBy->name }} at {{ $voucher->confirmed_at->format('d/m/Y H:i') }}"
                    value="{{ $voucherItem->amount }}">
            @endif
            @if (is_null($voucher->confirmed_by) || is_null($voucher->confirmation_approval_by))
                <input type="checkbox" class="items" name="items__{{ $voucherItem->id }}" data-rule-required="true"
                    value="{{ $voucherItem->amount }}">
            @endif
        </div>
    </td>
</tr>
<tr style="display: none">
    <td colspan="10">
        <table class="table">
            <tr>
                <th></th>
                <th>Document Date</th>
                <th>Document No.</th>
                <th class="text-right">QTY</th>
                <th class="text-right">Tonnage</th>
                <th class="text-right">VAT</th>
                <th class="text-right">Amount</th>
            </tr>
            <tr>
                <td>
                    @php
                        $documents = json_decode($voucherItem->invoice?->lpo?->documents);
                        $documents_r = json_decode($voucherItem->invoice?->rlpo?->documents);

                        $link = $documents->supplier_invoice ?? ($documents_r->supplier_invoice ?? '');
                    @endphp
                    @if ($link)
                        <a href="#" data-toggle="document" data-title="Supplier Invoice"
                            data-url="{{ str_contains($link, 'http') ? $link : asset('uploads/purchases_docs/' . $link) }}">
                            Supplier Invoice ({{ $voucherItem->invoice->supplier_invoice_number }})
                        </a>
                    @else
                        Supplier Invoice ({{ $voucherItem->invoice->supplier_invoice_number }})
                    @endif
                </td>
                <td>{{ $voucherItem->invoice->supplier_invoice_date->format('d/m/Y') }}</td>
                <td>{{ $voucherItem->invoice->cu_invoice_number }}</td>
                <td class="text-right">
                    {{ number_format($voucherItem->invoice->items->sum('quantity')) }}
                </td>
                <td class="text-right">
                    @php
                        $weight = 0;
                        foreach ($voucherItem->invoice->items as $item) {
                            $weight +=
                                $item->quantity *
                                (is_null($item->inventoryItem) ? 0 : $item->inventoryItem->net_weight);
                        }
                    @endphp
                    {{ manageAmountFormat($weight) }}
                </td>
                <td class="text-right">
                    {{ manageAmountFormat($voucherItem->invoice->vat_amount) }}</td>
                <td class="text-right">
                    {{ manageAmountFormat($voucherItem->invoice->amount) }}
                </td>
            </tr>
            <tr>
                @php
                    $weight = 0;
                    $grns = \App\Model\WaGrn::where('grn_number', $voucherItem->invoice?->grn_number)->get();
                @endphp
                <td>
                    <a href="javascript:void(0);" data-toggle="document" data-title="{{ $grns->first()?->grn_number }}"
                        data-url="{{ route('completed-grn.printToPdf', $grns->first()?->grn_number) }}">
                        GRN ({{ $grns->first()?->grn_number }}) {{ $grns->first()?->supplier_invoice_no }}
                    </a>
                </td>
                <td>{{ Carbon\Carbon::parse($grns->first()?->delivery_date)->format('d/m/Y') }}</td>
                <td>{{ $grns->first()?->cu_invoice_number }}</td>
                <td>
                    <a href="#" data-toggle="modal" class="text-right" style="display: block"
                        data-target="#items{{ $voucherItem->id }}" target="_blank">
                        {{ number_format($grns->sum('item_quantity')) }}
                    </a>
                    <div class="modal fade" role="dialog" id="items{{ $voucherItem->id }}">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">
                                        {{ $grns->first()?->grn_number }} -
                                        {{ \Carbon\Carbon::parse($grns->first()?->delivery_date)->format('d/m/Y') }}
                                    </h4>
                                </div>
                                <div class="modal-body">
                                    <table class="table">
                                        <tr>
                                            <th>Item</th>
                                            <th>Code</th>
                                            <th class="text-right">QTY</th>
                                            <th class="text-right">Tonnage</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        @foreach ($grns as $grnItem)
                                            @if (json_decode($grnItem->invoice_info)->qty > 0)
                                                <tr>
                                                    <td>{{ $grnItem->item_code }}</td>
                                                    <td>{{ $grnItem->item_description }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format(json_decode($grnItem->invoice_info)->qty) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ manageAmountFormat((float) json_decode($grnItem->invoice_info)->qty * $grnItem->inventoryItem->net_weight) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('item-centre.show', $grnItem->inventoryItem->id) }}"
                                                            data-toggle="tooltip" title="view item" target="_blank">
                                                            <i class="fa fa-chevron-right"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <th colspan="5">
                                                <h4 style="margin: 0">Unfulfilled items
                                                </h4>
                                            </th>
                                        </tr>
                                        @foreach ($grns as $grnItem)
                                            @if ($grnItem->item_quantity == 0)
                                                <tr>
                                                    <td>{{ $grnItem->item_code }}</td>
                                                    <td>{{ $grnItem->item_description }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ number_format($grnItem->item_quantity) }}
                                                    </td>
                                                    <td class="text-right">
                                                        {{ manageAmountFormat($grnItem->item_quantity * $grnItem->inventoryItem->net_weight) }}
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="{{ route('item-centre.show', $grnItem->inventoryItem->id) }}"
                                                            data-toggle="tooltip" title="view item" target="_blank">
                                                            <i class="fa fa-chevron-right"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <th colspan="2" class="text-right">Total
                                            </th>
                                            <th class="text-right">
                                                {{ number_format($grns->sum('item_quantity')) }}</th>
                                            <th class="text-right">
                                                {{ manageAmountFormat($weight) }}</th>
                                            <td></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-right">
                    @foreach ($grns as $grnItem)
                        @php
                            $weight += round(
                                ($grnItem->getRelatedInventoryItem->getInventoryItemDetail->net_weight ?? 1) *
                                    $grnItem->item_quantity,
                                2,
                            );
                        @endphp
                    @endforeach
                    {{ manageAmountFormat($weight) }}
                </td>
                <td class="text-right">{{ manageAmountFormat($grns->sum('item_vat')) }}</td>
                <td class="text-right">{{ manageAmountFormat($grns->sum('item_total')) }}</td>
            </tr>
            <tr>
                <td>
                    <a href="#" data-toggle="document" data-title="Local Purchase Order" data-url="{{ route('purchase-orders.exportToPdf', ['slug' => $voucherItem->invoice->lpo->purchase_no]) }}">
                        LPO
                    </a>
                </td>
                <td>{{ Carbon\Carbon::parse($voucherItem->invoice->lpo->purchase_date)->format('d/m/Y') }}</td>
                <td>{{ $voucherItem->invoice->lpo->purchase_no }}</td>
                <td class="text-right">
                    {{ number_format($voucherItem->invoice->lpo->getRelatedItem->sum('quantity')) }}
                </td>
                <td class="text-right">
                    @php
                        $weightLpo = 0;
                        foreach ($voucherItem->invoice->lpo->getRelatedItem as $item) {
                            $weightLpo +=
                                $item->quantity *
                                (is_null($item->inventoryItem) ? 0 : $item->inventoryItem->net_weight);
                        }
                    @endphp
                    {{ manageAmountFormat($weightLpo) }}
                </td>
                <td class="text-right">
                    {{ manageAmountFormat($voucherItem->invoice->lpo->getRelatedItem->sum('vat_amount')) }}
                </td>
                <td class="text-right">
                    {{ manageAmountFormat($voucherItem->invoice->lpo->getRelatedItem->sum('total_cost_with_vat')) }}
                </td>
            </tr>
            @foreach ($voucherItem->payable->notes as $note)
                <tr>
                    <td>
                        @if ($note->file_name)
                            <a href="#" data-toggle="document" data-title="Credit Notes"
                                data-url="{{ url('/uploads/financial_notes/' . $note->file_name) }}">
                                Credit Note ({{ $note->note_no }})
                            </a>
                        @else
                            Credit Note ({{ $note->note_no }})
                        @endif
                    </td>
                    <td>{{ Carbon\Carbon::parse($note->note_date)->format('d/m/Y') }}</td>
                    <td>{{ $note->supplier_invoice_number }}</td>
                    <td>
                        <a href="#" data-toggle="modal" class="text-right" style="display: block"
                            data-target="#noteItems{{ $note->id }}" target="_blank">
                            Items ({{ number_format($note->items->count()) }})
                        </a>
                        <div class="modal fade" role="dialog" id="noteItems{{ $note->id }}">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                            aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">
                                            {{ $note->note_no }}
                                        </h4>
                                    </div>
                                    <div class="modal-body">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Account</th>
                                                    <th>Memo</th>
                                                    <th>VAT</th>
                                                    <th>W/H VAT</th>
                                                    <th>Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($note->items as $item)
                                                    <tr>
                                                        <td>{{ $item->account->account_name }}
                                                        </td>
                                                        <td>{{ $note->memo }}
                                                        </td>
                                                        <td class="text-right">
                                                            {{ manageAmountFormat($item->tax_amount) }}
                                                        </td>
                                                        <td class="text-right">
                                                            {{ manageAmountFormat($item->withholding_amount) }}
                                                        </td>
                                                        <td class="text-right">
                                                            {{ manageAmountFormat($item->amount + $item->tax_amount) }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="3" class="text-right">
                                                        Total</th>
                                                    <th class="text-right">
                                                        {{ manageAmountFormat($note->items->sum('withholding_amount')) }}
                                                    </th>
                                                    <th class="text-right">
                                                        {{ manageAmountFormat($note->items->sum('amount') + $note->items->sum('tax_amount')) }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td></td>
                    <td class="text-right">{{ manageAmountFormat($note->tax_amount) }}
                    </td>
                    <td class="text-right">{{ manageAmountFormat($note->amount) }}</td>
                </tr>
            @endforeach
        </table>
    </td>
    <td></td>
</tr>
