<table>
    <tr>
        <td colspan="8"><strong>{{ getAllSettings()['COMPANY_NAME'] }}</strong></td>
    </tr>
    <tr>
        <td colspan="8"><strong>@isset($status) {{ Illuminate\Support\Str::upper($status) }} @endisset BANK PAYMENTS REPORT</strong></td>
    </tr>
    <tr>
        <td colspan="8"></td>
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
@include('admin.bank_files.partials.bank_payments')