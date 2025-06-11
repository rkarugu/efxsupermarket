<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-size: 11px;
        font-family: times new roman;
    }

    tr {
        border-bottom: 7px solid #fff;
    }

    td,
    th {
        text-align: left;
        padding: 2px 2px;
    }
</style>
<div style="width: 100%;padding-bottom: 30px;margin: auto;" class="clearfix" id="div_content">
    <h2>Statement of Account</h2>

    <table class="table" style="width: 50%;
float: right;">
        <tr>
            <td style="text-align: right;">
                <?php $cn = 0; ?>
                <?php $all_settings = getAllSettings(); ?>
                {{ $all_settings['COMPANY_NAME'] }}</b></span><br>
                {{ $all_settings['ADDRESS_1'] }}<br>
                {{ $all_settings['ADDRESS_2'] }}<br>
                {{ $all_settings['ADDRESS_3'] }}<br>
                Tel: {{ $all_settings['PHONE_NUMBER'] }}<br>
                {{ $all_settings['EMAILS'] }}<br>
                {{ $all_settings['WEBSITE'] }}<br>
                Pin No: {{ $all_settings['PIN_NO'] }}<br><br>
            </td>
        </tr>

        <tr>
            <td style="text-align: right;">
                <b>Opening Balance : {{ manageAmountFormat($getOpeningBlance) }}</b><br>
            </td>
        </tr>
    </table>


    <table class="table" style="width: 50%; float: left; text-align: left;">
        <tr>
            <td style="font-weight:700;font-size:13px">
                A/C CODE: {{ @$supplier->customerDetail->customer_code }}
            </td>
        </tr>
        <tr>
            <td style="font-weight:700;font-size:13px">
                A/C NAME: {{ ucfirst(@$supplier->customerDetail->customer_name) }}
            </td>
        </tr>
        <tr>
            <td>
                DATE: {{ date('d/m/Y') }} TIME: {{ date('h:i') }}
            </td>
        </tr>
        <tr>
            <td>
                From : {{ $date1 }} | To : {{ $date2 }}
            </td>
        </tr>

    </table>
    <br><br>
    <br><br>
    <br>
    <br><br>
    <br>
    <table class="table" style="width: 100%; float: none;">
        <thead>
            <tr style="background-color: #ddd;">
                <th style="text-align: left; width:9%">Date
                </th>
                <th style="text-align: left; width:7%">Type
                </th>
                <th style="text-align: left; width:10%">Document
                </th>
                <th style="text-align: left; width:31%">Name/Reference
                </th>
                <th style="text-align: right; width:11%">Debit
                </th>
                <th style="text-align: right; width:11%">Credit
                </th>
                <th style="text-align: right; width:10%">Trans Bal
                </th>
                <th style="text-align: right; width:13%">Balance
                </th>

            </tr>
        </thead>
        <?php
        $total_amount = [];
        $nvtotal_amount = [];
        $pvtotal_amount = [];
        $opBal = $getOpeningBlance;
        ?>
        <tbody>
            @foreach ($lists as $list)
                @php
                    $balance = $opBal + (float) $list->amount;
                    $opBal = $balance;
                @endphp
                <tr style="border-bottom: 1px solid black !important;">
                    <td style="text-align: left;">{{ \Carbon\Carbon::parse($list->created_at)->toDateString() }}</td>
                    <td style="text-align: left;">{!! isset($number_series_list[$list->type_number]) ? $number_series_list[$list->type_number] : '' !!}</td>
                    <td style="text-align: left;">{{ $list->document_no ? $list->document_no : '-' }}</td>
                    {{-- <td style="text-align: left;">{{ ($list->invoice_customer_name ? $list->invoice_customer_name.'/' : NULL).$list->reference}}</td> --}}
                    <td style="text-align: left;">
                        {{ $list->invoice_customer_name ? $list->invoice_customer_name : $list->reference }}</td>


                    <td style="text-align: right;">{{ $list->amount > 0 ? manageAmountFormat($list->amount) : '' }}
                    </td>
                    <td style="text-align: right;">{{ $list->amount < 0 ? manageAmountFormat($list->amount) : '' }}
                    </td>
                    <td style="text-align: right;">{{ manageAmountFormat($list->amount) }}</td>

                    <td style="text-align: right;">{{ manageAmountFormat($balance) }}</td>
                </tr>
                <?php $total_amount[] = $list->amount;
                $nvtotal_amount[] = $list->amount < 0 ? $list->amount : 0;
                $pvtotal_amount[] = $list->amount > 0 ? $list->amount : 0;
                ?>
            @endforeach
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;"><b>B/F :
                        {{ manageAmountFormat($getOpeningBlance) }}</b></td>

                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                    <b>{{ manageAmountFormat(array_sum($pvtotal_amount)) }}</b></td>
                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                    <b>{{ manageAmountFormat(array_sum($nvtotal_amount)) }}</b></td>
                <th style="text-align: right;">Closing Bal</th>
                <td style="text-align: right; border-top: 1px solid #000;border-bottom: 1px solid #000;">
                    <b>{{ manageAmountFormat($opBal) }}</b></td>
            </tr>
        </tbody>


    </table>



</div>
