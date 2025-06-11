<table style="width: 100%; margin-bottom: 20px">
    <tbody>
        <tr>
            <th colspan="2">
                <h2 style="text-align: left; margin:0">{!! strtoupper($all_settings['COMPANY_NAME']) !!}</h2>
            </th>
        </tr>
        <tr>
            <th>
                <h4 style="text-align: left; margin:0">TILL SUMMARY REPORT</h4>
            </th>
            <th>
                <h4 style="text-align: right; margin:0">{{ is_null($branch) ? '' : "BRANCH: $branch->name" }}
                </h4>
            </th>
        </tr>
        <tr>
            <th>
                <h4 style="text-align: left; margin:0">Input By: {{ $user->name }}</h4>
            </th>
            <th>
                <h4 style="text-align: right; margin:0">DATE FROM:
                    {{ date('d-M-Y', strtotime(request()->date)) }} |
                    DATE TO {{ date('d-M-Y', strtotime(request()->todate)) }} | TIME: {{ date('H:i A') }}</h4>
            </th>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr class="heading">
            <td>#</td>
            <td>ROUTE</td>
            <td style="text-align:right">CS</td>
            <td style="text-align:right">CSR</td>
            <td style="text-align:right">VCS</td>
            <td style="text-align:right">VCR</td>
            <td style="text-align:right">INV</td>
            <td style="text-align:right">SALES</td>
            <td style="text-align:right">PETTY</td>
            {{-- <td>VSR</td> --}}
            <td style="text-align:right">Eazzy</td>
            <td style="text-align:right">Vooma</td>
            <td style="text-align:right">USSD</td>
            <td style="text-align:right">Mpesa</td>
            <td style="text-align:right">NETCASH</td>
        </tr>
    </thead>
    <tbody>
        @php
            $sum_INV = 0;
            $cash_sales = $cash_sales_returns = $invoices = $invoices_return = $petty_cash = $customer_receipt =  $eazzy = $vooma= $ussd= $totalNetCash = 0;
        @endphp
        @foreach ($data as $item)
        
        {{-- Elimminate rows without any transaction --}}
        @php
            $total = ($item->cs ?? 0.0) + ($item->vcs ?? 0.0) - ($item->csr ?? 0.0) - ($item->returns ?? 0.0);
            $netcash = ($item->Eazzy ?? 0.0) + ($item->Mpesa ?? 0.0) + ($item->Vooma ?? 0.0) + ($item->Ussd ?? 0.0) - ($item->petty_cash ?? 0.0); 
            $showRow = ($item->cs != 0 || $item->csr != 0 || $item->vcs != 0 || $item->returns != 0 || $item->vcr != 0 || $item->inv_backend != 0 || $item->petty_cash != 0 || $item->Eazzy != 0 || $item->Vooma != 0 || $item->Ussd != 0 || $item->Mpesa != 0 || $netcash != 0);
        @endphp
        @if ($showRow)
            <tr >
                <td>{{$loop->index+1}}</td>
                <td style="margin-top: 0px !important; padding-top: 0px !important;">{{ ucwords(strtolower($item->name)) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->cs) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->csr) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->vcs) }}</td>
                {{-- <td style="text-align:right">{{ manageAmountFormat($item->vcr) }}</td> --}}
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->returns) }}</td>

                @php
                    $total = 0;
                    $sum_INV += $item->inv_backend - $item->inv_backend_return;
                    $total =
                        ($item->cs ?? 0.0) + ($item->vcs ?? 0.0) - ($item->csr ?? 0.0) - ($item->returns ?? 0.0);
                @endphp
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->inv_backend - $item->inv_backend_return) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($total) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->petty_cash) }}</td>
                {{-- <td>{{ manageAmountFormat(abs($item->customer_receipt)) }}</td> --}}
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->Eazzy) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->Vooma) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($item->Ussd) }}</td>
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">0.00</td>
                @php
                    $netcash = 0;
                    $netcash =
                        ($item->Eazzy ?? 0.0) +
                        ($item->Mpesa ?? 0.0) +
                        ($item->Vooma ?? 0.0) +
                        ($item->Ussd ?? 0.0) -
                        ($item->petty_cash ?? 0.0); 
                    $cash_sales += ($item->cs ?? 0.0);
                    $cash_sales_returns += $item->csr ?? 0.0;
                    $invoices += $item->vcs ?? 0.0;
                    $invoices_return += $item->returns ?? 0.0;
                    $petty_cash += $item->petty_cash ?? 0.0;
                    $customer_receipt += abs($item->customer_receipt ?? 0.0);
                    $eazzy += $item->Eazzy ?? 0.0;
                    $vooma += $item->Vooma?? 0.0;
                    $ussd += $item->Ussd?? 0.0;
                    $totalNetCash += $netcash ?? 0.0;
                @endphp
                <td style="text-align:right; margin-top: 0px !important; padding-top: 0px !important;">{{ manageAmountFormat($netcash) }}</td>
            </tr>
        @endif
        @endforeach
        <tr style="    border-top: 2px dashed #cecece;">
            <td colspan="14"></td>
        </tr>
        <tr>
            <th style="text-align:left" colspan="2">Total</th>
            <th style="text-align:right">{{ manageAmountFormat($cash_sales) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($cash_sales_returns) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($invoices) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($invoices_return) }}</th>
            @php
                $total1 = 0;
                $total1 =
                    ($cash_sales ?? 0.0) +
                    ($invoices ?? 0.0) -
                    ($cash_sales_returns ?? 0.0) -
                    ($invoices_return ?? 0.0);
            @endphp
            <th style="text-align:right">{{ manageAmountFormat($sum_INV) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($total1) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($petty_cash) }}</th>
            {{-- <th style="text-align:left">{{ manageAmountFormat(abs($customer_receipt)) }}</th> --}}
            <th style="text-align:right">{{ manageAmountFormat($eazzy) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($vooma) }}</th>
            <th style="text-align:right">{{ manageAmountFormat($ussd) }}</th>
            <th style="text-align:right">0.00</th>


            @php
                $netcash1 = 0;
                $netcash1 =
                    ($cash_sales ?? 0.0) -
                    ($cash_sales_returns ?? 0.0) -
                    ($petty_cash ?? 0.0) +
                    $customer_receipt;

                $sum_cashSales = $cash_sales ?? 0.0;
                $sum_CRS = $cash_sales_returns ?? 0.0;
                $sum_VCS = $invoices ?? 0.0;
                $sum_VCR = $invoices_return ?? 0.0;

                $sum_CRN = abs($customer_receipt);
            @endphp
            <th style="text-align:right">{{ manageAmountFormat($totalNetCash) }}</th>
        </tr>
        <tr>
            <td colspan="14" style="border-bottom: 2px dashed #cecece;"></td>
        </tr>
        <tr>
            <td colspan="3">
                <div class="horizontal_dotted_line">
                    Done By:
                </div>
            </td>
            <td colspan="1"></td>
            <td colspan="2">
                <div class="horizontal_dotted_line"> Checked By</div>
            </td>
            <td colspan="1"></td>
            <td colspan="2">
                <div class="horizontal_dotted_line"> Approved:</div>
            </td>
            <td colspan="1"></td>

        </tr>
    </tbody>
</table>