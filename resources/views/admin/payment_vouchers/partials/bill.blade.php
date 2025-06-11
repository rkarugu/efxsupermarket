<tr>
    <td class="details-control text-center">       
    </td>
    <td>{{ $voucherItem->payable->created_at->format('d//m/Y') }}</td>
    <td></td>
    <td>{{ $voucherItem->payable->id }}</td>
    <td>{{ $voucherItem->payable->cu_invoice_number }}</td>
    <td></td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->payable->amount) }}
    </td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->payable->withholding_amount) }}
    </td>
    <td class="text-right">
    </td>
    <td class="text-right">
        {{ manageAmountFormat($voucherItem->amount) }}
    </td>
    <td class="text-center">
        <div class="form-group" style="margin: 0">
            @if ($voucher->confirmed_by)
                <input type="checkbox" class="items" disabled checked name="items[]"
                    data-toggle="tooltip"
                    title="Confirmed By: {{ $voucher->confirmedBy->name }} at {{ $voucher->confirmed_at->format('d/m/Y H:i') }}"
                    value="{{ $voucherItem->amount }}">
            @endif
            @if (is_null($voucher->confirmed_by) || is_null($voucher->confirmation_approval_by))
                <input type="checkbox" class="items" name="items__{{ $voucherItem->id }}"
                    data-rule-required="true" value="{{ $voucherItem->amount }}">
            @endif
        </div>
    </td>
</tr>