<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Card</title>
    <style>
        
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
            margin: 0;
    padding: 0;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 11px;
            line-height: 15px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 11px;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 1px 2px 1px 2px;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item:last-child {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="3">
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="3" style="    text-align: center;">STOCK CARD STORE C</td>
                </tr>
                <tr class="top">
                    <th  style="width: 38%;text-align:left">
                        {{@$row->stock_id_code}} - {{@$row->title}}
                    </th>
                    <th  style="width: 32%;text-align:center">
                       {{@$currentLocation->location_name}}
                    </th>
                    <th  style="width: 30%;text-align:right">From: {{date('d-M-Y',strtotime($request->from))}} - To: {{date('d-M-Y',strtotime($request->to))}}</th>

                </tr>
            </tbody>        
        </table>
        <table>
            <thead>
                <tr class="heading">
                    <td width="12%">Date</td>
                    <td width="12%">Doc No</td>
                    <td width="40%">Reference</td>
                    {{-- <td width="10%">User</td> --}}
                    {{-- <td width="13%">Location</td> --}}
                    {{-- <td width="10%">Txn</td>                 --}}
                    <td style="text-align: right" width="12%">Qty In</td>
                    <td style="text-align: right" width="12%">Qty Out</td>
                    <td width="12%">New QOH</td>
                    {{-- <td width="12%">Type</td>                     --}}
				</tr>
            </thead>
            <tbody>
                @php
                    $qauntity_positive = $qauntity_negetive = $new_qoh = $first_qoh = 0;
                @endphp
                @if(isset($lists) && !empty($lists))
                    @foreach($lists as $list)                        
                        <tr class="item">
                            <td>{!! date('d/M/y',strtotime(@$list->created_at)) !!}</td>  
                            <td>{!! @$list->document_no !!}</td>
                            <td>{!! @$list->refrence !!}</td>
                            {{-- <td>{!! manageOrderidWithPad(@$list->id) !!}</td> --}}
                            {{-- <td>{!! ucfirst(@$list->getRelatedUser->name) !!}</td> --}}
                            {{-- <td>{!! isset($list->getLocationOfStore->location_name) ? ucfirst($list->getLocationOfStore->location_name) : '' !!}</td> --}}
                            <td style="text-align: right">{!! manageAmountFormat(($list->qauntity >= 0) ? +$list->qauntity : NULL) !!}</td>
                            <td style="text-align: right">{!! manageAmountFormat(($list->qauntity < 0) ? -$list->qauntity : NULL) !!}</td>
                            <td style="text-align: right">{!! manageAmountFormat(@$list->new_qoh) !!}</td>
                            {{-- <td>{!! getStockMoveType($list) !!}</td> --}}
                        </tr>
                        @php
                            $qauntity_positive += (($list->qauntity >= 0) ? abs($list->qauntity) : 0);
                            $qauntity_negetive += (($list->qauntity < 0) ? abs($list->qauntity) : 0);
                            $new_qoh = @$list->new_qoh;
                        @endphp
                    @endforeach
                    @php
                        $first_qoh = @$firstQoh->new_qoh;
                    @endphp
                @endif
                </tbody>
                <tfoot>
                    <tr class="item" style="">
                        <td colspan="2" style="border-top:2px solid #000;text-align: right"><b>QTY Bf</b></td>
                        <td style="border-top:2px solid #000;text-align: right">{{manageAmountFormat(abs(@$first_qoh))}}</td>
                        <td style="border-top:2px solid #000;text-align: right">{!! manageAmountFormat(abs($qauntity_positive)) !!}</td>
                        <td style="border-top:2px solid #000;text-align: right">{!! manageAmountFormat(abs($qauntity_negetive)) !!}</td>
                        <td style="border-top:2px solid #000;text-align: right">{!! manageAmountFormat(abs($new_qoh)) !!}</td>
                    </tr>
                </tfoot>
        </table>
        
    </div>   
</body>
</html>