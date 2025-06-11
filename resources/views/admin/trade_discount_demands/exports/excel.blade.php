<table>
    <tr>
        <td colspan="11"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="11"><strong>TRADE DISCOUNT DEMANDS REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="11"></td>
    </tr>
    @if ($from && $to)
        <tr>
            <td><strong>PERIOD:</strong></td>
            <td colspan="11"><strong>{{ $from }} - {{ $to }}</strong></td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>
            <th><strong>Demand No</strong></th>
            <th><strong>Supplier</strong></th>
            <th><strong>Reference</strong></th>
            <th><strong>CU Invoice No.</strong></th>
            <th><strong>Note Date</strong></th>
            <th><strong>Memo</strong></th>
            <th><strong>Processed</strong></th>
            <th><strong>Date Processed</strong></th>
            <th><strong>Credit Note No.</strong></th>
            <th><strong>Amount</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($demands as $demand)
            <tr>
                <td>{{ $demand->demand_no }}</td>
                <td>{{ $demand->supplier_name }}</td>
                <td>{{ $demand->supplier_reference }}</td>
                <td>{{ $demand->cu_invoice_number }}</td>
                <td>{{ $demand->note_date }}</td>
                <td>{{ $demand->memo }}</td>
                <td>{{ $demand->processed }}</td>
                <td>{{ $demand->processed_at }}</td>
                <td>{{ $demand->credit_note_no }}</td>
                <td style="text-align: right">{{ number_format($demand->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="9" style="text-align: right"><strong>Total</strong></td>
            <td style="text-align: right"><strong>{{ number_format($demands->sum('amount'), 2) }}</strong></td>
        </tr>
    </tfoot>
</table>
