<table class="table table-bordered">
    @foreach ($accounts as $account)
        <thead>
            <tr>
                <th colspan="8"><strong>{{ $account->account_name }}</strong></th>
            </tr>
            <tr>
                <th><strong>Supplier</strong></th>
                <th><strong>REF#</strong></th>
                <th><strong>Voucher No.</strong></th>
                <th><strong>Voucher Date</strong></th>
                <th><strong>Pay Date</strong></th>
                <th><strong>Payment Mode</strong></th>
                <th><strong>Bank File</strong></th>
                <th style="text-align: right;"><strong>Amount</strong></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments->where('account_id', $account->id) as $payment)
                <tr>
                    <td>{{ $payment->supplier_name }}</td>
                    <td>{{ $payment->id }}</td>
                    <td>{{ $payment->number }}</td>
                    <td>{{ $payment->created_at }}</td>
                    <td>{{ $payment->bank_file_date }}</td>
                    <td>{{ $payment->payment_mode }}</td>
                    <td>{{ $payment->bank_file_no }}</td>
                    <td style="text-align: right;">{{ manageAmountFormat($payment->amount) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center">No records found</td>
                </tr>
            @endforelse
            <tr>
                <th colspan="7" style="text-align: right;">Total</th>
                <th style="text-align: right">
                    <strong>{{ manageAmountFormat($payments->where('account_id', $account->id)->sum('amount')) }}</strong>
                </th>
            </tr>
        </tbody>
    @endforeach
    <tfoot>
        <tr>
            <th colspan="7" style="text-align: right;"><strong>Grand Total</strong></th>
            <th style="text-align: right;"><strong>{{ manageAmountFormat($payments->sum('amount')) }}</strong></th>
        </tr>
    </tfoot>
</table>
