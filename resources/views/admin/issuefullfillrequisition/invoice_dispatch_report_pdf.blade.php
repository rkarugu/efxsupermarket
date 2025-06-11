<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Summary</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
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
            font-size: 12px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
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

        .invoice-box table tr.item td:last-child {
            border-bottom: 1px solid #eee;
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
        .item.bg-grey{
            
        }
        .horizontal_dotted_line {
            text-align: left !important;
        }
    </style>
</head>
<body>

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2">
                        <h2>Dispatch Summary</h2>
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="1">
                        <b>Salesman : {{@$salesman->name}}</b>  
                    </th>
                    <th colspan="1">
                        <b>Shifts : </b>
                        {{count($allshifts)>0 ? implode(',',$allshifts) : NULL}}
                    </th>
                </tr>
            </tbody>        
        </table>
        <br>
        <table>
            <tbody>
                <tr class="heading">
                    <th style="width:30%;text-align:left">Particulars</th>
                    <th style="width:20%;text-align:left">Inv Date</th>
                    <th style="width:20%;text-align:left">Batch No</th>
                    <th style="width:15%;text-align:left">Quantity</th>
                    <th style="width:15%;text-align:right">Total Quantity</th>
                </tr>
                <tr style="    border-top: 2px dashed #cecece;">
					<td colspan="5"></td>
				</tr>
                @php
                    $i = 0;
                @endphp
                @foreach ($inventory as $inventory_item)
                    @php
                        $al = $items->where('wa_inventory_item_id',$inventory_item->id);
                        $quantity = 0;
                    @endphp
                    @if(count($al)>0)
                        <tr class="item @if($i%2==0) bg-grey @endif">
                            <td>{{@$inventory_item->title}}</td>
                            <td colspan="4"></td>
                        </tr>
                        @foreach($al as $item)
                            <tr class="item @if($i%2==0) bg-grey @endif">
                                <td></td>
                                <td>{{date('d/m/Y',strtotime(@$item->getTransferLocation->transfer_date))}}</td>
                                <td>{{@$item->getTransferLocation->transfer_no}}</td>
                                <td>{{manageAmountFormat($item->qty)}}</td>
                                <td></td>
                            </tr>
                            @php
                                $quantity += $item->qty;
                            @endphp
                        @endforeach
                        <tr class="item @if($i%2==0) bg-grey @endif">
                            <td colspan="4"></td>
                            <td>{{manageAmountFormat($quantity)}}</td>
                        </tr>
                        @php
                            $i++;
                        @endphp
                    @endif
                @endforeach
             
            </tbody>
        </table>
        <br>
        <div class="horizontal_dotted_line"><h4>Store Keeper...............................................................................................................................................................................</h4></div>
        
        <div class="horizontal_dotted_line"><h4>Sales Person...............................................................................................................................................................................</h4></div>
    </div>   
</body>
</html>