<table>
    <tbody>
        <tr>
            <td colspan="9"></td>
        </tr>
        @foreach ($file->items as $item)
            @foreach ($item->voucher->cheques as $cheque)
                <tr>
                    <td>{{ now()->format('d/m/Y') }}</td>
                    <td>{{ now()->format('d/m/Y') }}</td>
                    <td>{{ $cheque->number }}</td>
                    <td>{{ $item->voucher->supplier->name }}</td>
                    <td>{{ $item->voucher->supplier->bank_account_no }}</td>
                    <td>{{ $item->voucher->supplier->bank_name }}</td>
                    <td>{{ $item->voucher->supplier->bank_swift }}</td>
                    <td>{{ $item->voucher->supplier->bank_branch }}</td>
                    <td>{{ $cheque->amount }}</td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
