<html>
<title>Print</title>

<head>
	<style type="text/css">
	body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            
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
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box *{
            font-size: 12px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }

        .invoice-box table tr td:last-child {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 40px;
            line-height: 40px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item:last-child td,.invoice-box table tr.heading th {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
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

<?php $all_settings = getAllSettings();?>
<div class="invoice-box">
    <table  style="text-align: center;">
        <tbody>
            <tr class="top">
                <th colspan="3">
                    <h2 style="font-size:18px !important">{{ $all_settings['COMPANY_NAME']}}</h2>
                </th>
            </tr>
            <tr class="top">
                <td colspan="3" style="    text-align: center;">{{$all_settings['ADDRESS_2']}}</td>
            </tr>
            <tr class="top">
                <th colspan="3"  style="    text-align: center;">Stock Return (Excess)</th>
            </tr>
            <tr class="top">
                <th colspan="1"  style="    text-align: left;">From : {{ @$startDate }}</th>
                <th colspan="1"  style="    text-align: center;">To : {{ @$endDate }}</th>
                <th colspan="1"  style="    text-align: right;">Branch : {{ @$branch }}</th>
            </tr>
		</tbody>        
    </table>

    <br>
    <table>
        <tbody>
            <tr class="heading">
                <th style="width: 3%;">#</th>
                <th style="text-align: left;">Document No</th>
                <th style="text-align: left;">Date</th>
                <th style="text-align: left;">Debtor Name</th>
                <th style="text-align: left;">Store</th>
                <th style="text-align: right;">Total</th>
            </tr>
                @php
                    $gross_amount = 0;
                @endphp
                @foreach($debtors as $list)
                    <tr class="item">
                        <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                        <td style="text-align: left;">{!! $list->document_no !!}</td>
                        <td style="text-align: left;">{!! date('Y-m-d',strtotime($list->created_at)) !!}</td>
                        <td style="text-align: left;">{!! $list->name !!}</td>
                        <td style="text-align: left;">{!! $list->location_name !!}</td>
                        <td style="text-align: right;">{{ manageAmountFormat($list->total) }}</td>
                        @php
                            $gross_amount += $list->total;
                        @endphp
                    </tr>
                @endforeach        
            </tbody>        
    </table>
    <table>
        <tbody>
            <tr style="    border-top: 2px dashed #cecece;">
                <td colspan="3"></td>
            </tr>
            <tr >
                <td colspan="1">{{count($debtors)}} Lines</td>
                <td style="text-align: right;" colspan="1">Total:</td>
                <td colspan="1">{{manageAmountFormat($gross_amount)}}</td>
            </tr>           
        </tbody>
    </table>
</div>   

</body>
</html>