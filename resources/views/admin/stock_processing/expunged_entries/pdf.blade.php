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
                <th colspan="3"  style="    text-align: center;">Stock Expunged Entries</th>
            </tr>
		</tbody>        
    </table>

    <br>
    <table>
        <tbody>
            <tr class="heading">
                <th style="text-align: left;">Debtor Name</th>
                <th style="text-align: left;">Code</th>
                <th style="text-align: left;">Description</th>
                <th style="text-align: left;">Bin</th>
                <th style="text-align: center;">Variation</th>
            </tr>
            @foreach($stocks as $list)
                <tr class="item">
                    <td style="text-align: left;">{!! date('Y-m-d',strtotime($list->created_at)) !!}</td>
                    <td style="text-align: left;">{!! $list->getInventoryItemDetail->stock_id_code !!}</td>
                    <td style="text-align: left;">{!! $list->getInventoryItemDetail->description !!}</td>
                    <td style="text-align: left;">{!! $list->getUomDetail->title !!}</td>
                    <td style="text-align: center;">{!! number_format($list->variation) !!}</td>
                </tr>
            @endforeach        
            </tbody>        
    </table>

</div>   

</body>
</html>