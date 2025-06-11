<table>
    <tr>
        <td colspan="9"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="9"><strong>@isset($status) {{ Illuminate\Support\Str::upper($status) }} @endisset PAYMENT VOUCHERS REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="9"></td>
    </tr>
    @if($supplier)
        <tr>
            <td><strong>SUPPLIER:</strong></td>
            <td colspan="8"><strong>{{ $supplier->supplier_code }} - {{ $supplier->name }}</strong></td>
        </tr>
    @endif
    @if($from && $to)
        <tr>
            <td><strong>PERIOD:</strong></td>
            <td colspan="8"><strong>{{ $from }} - {{ $to }}</strong></td>
        </tr>
    @endif
</table>
<table>
    <thead>
        <tr>
            <th><strong>ID</strong></th>
            <th><strong>Voucher No</strong></th>
            <th><strong>Date Created</strong></th>
            <th><strong>Supplier</strong></th>
            <th><strong>Account</strong></th>
            <th><strong>Payment Mode</strong></th>
            <th><strong>Bank File</strong></th>
            <th><strong>Date Paid</strong></th>
            <th style="text-align: right"><strong>Amount</strong></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher)
            <tr>
                <td style="width: 100px">{{ $voucher->id }}</td>
                <td>{{ $voucher->number }}</td>
                <td>{{ $voucher->created_at }}</td>
                <td>{{ $voucher->supplier_name }}</td>
                <td>{{ $voucher->account_name }}</td>
                <td>{{ $voucher->payment_mode }}</td>
                <td>{{ $voucher->bank_file_no }}</td>
                <td>{{ $voucher->bank_file_date }}</td>
                <td style="text-align: right">{{ number_format($voucher->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="8" style="text-align: right"><strong>Total</strong></td>
            <td style="text-align: right"><strong>{{ number_format($vouchers->sum('amount'), 2) }}</strong></td>
        </tr>
    </tfoot>
</table>
