<table>
    <tr>
        <td colspan="12"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="12"><strong>TRADE DISCOUNTS REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="12"></td>
    </tr>
    @if ($from && $to)
        <tr>
            <td><strong>PERIOD:</strong></td>
            <td colspan="12"><strong>{{ $from }} - {{ $to }}</strong></td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>
            <th><strong>Ref</strong></th>
            <th><strong>Supplier</strong></th>
            <th><strong>Discount Type</strong></th>
            <th><strong>Invoice No.</strong></th>
            <th><strong>Invoice Date</strong></th>
            <th><strong>Demand No.</strong></th>
            <th><strong>Description</strong></th>
            <th><strong>Prepared By</strong></th>
            <th><strong>Approval</strong></th>
            <th><strong>Invoice Amount</th>
            <th><strong>Disc. Amount</strong></th>
            <th><strong>Approved Disc. Amount</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($discounts as $discount)
            <tr>
                <td>{{ $discount->id }}
                <td>{{ $discount->supplier_name }}
                <td>{{ $discount->discount_type }}
                <td>{{ $discount->supplier_invoice_number }}
                <td>{{ $discount->invoice_date }}
                <td>{{ $discount->demand_no }}
                <td>{{ $discount->description }}
                <td>{{ $discount->prepared_by }}
                <td>{{ $discount->status ? 'Yes' : 'No' }}
                <td style="text-align: right">{{ $discount->invoice_amount }}
                <td style="text-align: right">{{ number_format($discount->amount, 2) }}</td>
                <td style="text-align: right">{{ number_format($discount->approved_amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="10" style="text-align: right"><strong>Total</strong></td>
            <td style="text-align: right"><strong>{{ number_format($discounts->sum('amount'), 2) }}</strong></td>
            <td style="text-align: right"><strong>{{ number_format($discounts->sum('approved_amount'), 2) }}</strong>
            </td>
        </tr>
    </tfoot>
</table>
