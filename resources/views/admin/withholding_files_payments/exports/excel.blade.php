<table>
    <tr>
        <td colspan="7"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="7"><strong>WITHHOLDING TAX PAYMENT VOUCHERS REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    @if ($from && $to)
        <tr>
            <td><strong>PERIOD:</strong></td>
            <td colspan="7"><strong>{{ $from }} - {{ $to }}</strong></td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>
            <th><strong>ID</strong></th>
            <th><strong>Voucher No</strong></th>
            <th><strong>Cheque No</strong></th>
            <th><strong>Memo</strong></th>
            <th><strong>Account</strong></th>
            <th><strong>Withholding File</strong></th>
            <th><strong>Date Paid</strong></th>
            <th><strong>Amount</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher)
            <tr>
                <td style="width: 100px">{{ $voucher->id }}</td>
                <td>{{ $voucher->number }}</td>
                <td>{{ $voucher->cheque_number }}</td>
                <td>{{ $voucher->memo }}</td>
                <td>{{ $voucher->account_name }}</td>
                <td>{{ $voucher->withholding_file_no }}</td>
                <td>{{ $voucher->payment_date }}</td>
                <td style="text-align: right">{{ number_format($voucher->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="7" style="text-align: right"><strong>Total</strong></td>
            <td style="text-align: right"><strong>{{ number_format($vouchers->sum('amount'), 2) }}</strong></td>
        </tr>
    </tfoot>
</table>
