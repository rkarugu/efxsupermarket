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

        .invoice-box table tr.item td {
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
        .horizontal_dotted_line {
            text-align: left !important;
        }

        .pagenum:before {
            content: counter(page);
        }


        #content {
            display: table;
        }

        #pageFooter {
            display: table-footer-group;
        }

        #pageFooter:after {
            counter-increment: page;
            content: counter(page);
        }
    </style>


</head>




<body id="content">
    
    <div class="invoice-box">

        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="2">&nbsp;</th>
                    <th colspan="1">
                        Date & Time: {{date('Y-m-d H:i:s A')}}
                    </th>
                </tr>

                <tr class="top">
                    <th colspan="3">
                        <h2>Delivery Loading Sheet</h2>
                    </th>
                </tr>

                

                <tr class="top">
                    <th colspan="1">
                        <b>Salesman : {{@$salesman->name}}</b>  
                    </th>
                    <th colspan="1">
                        <b>Store : {{@$storeLocation->location_name}}</b>  
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
                    <th style="text-align:left">Particulars</th>
                    <th style="text-align:right">Total Quantity</th>
                    <th style="text-align:right">Tonnage</th>
                    <th style="text-align:right">Total Tonnage</th>
                </tr>
                @php
                    $totalInvoices = [];
                    $tonnage = 0;
                    $totalAmount = 0;
                @endphp

                
                <tr>
                    <td colspan="4"><span class="pageFooter"></span></td>
                </tr>
                @foreach ($inventory as $inventory_item)
                    @php
                        $al = $items->where('wa_inventory_item_id',$inventory_item->id);
                        $quantity = 0;
                    @endphp
                    @if(count($al)>0)
                        
                        
                        <tr class="item ">
                            <td>{{@$inventory_item->title}}</td>
                            <td style="text-align:right">{{manageAmountFormat(@$al->sum('qty'))}}</td>
                            <td style="text-align:right">{{manageAmountFormat(@$inventory_item->gross_weight)}}</td>
                            <td style="text-align:right">{{manageAmountFormat(@$al->sum('qty') * @$inventory_item->gross_weight)}}</td>
                        </tr>    
                        @php
                            $tonnage += (@$al->sum('qty') * @$inventory_item->gross_weight);
                            foreach($al as $a){
                                $totalInvoices[] = $a->wa_inventory_location_transfer_id;
                            }
                            $totalAmount += @$al->sum('finalAmount');
                        @endphp
                    @endif
                @endforeach
                <tr class="item ">
                    <th style="text-align:right" colspan = "3">Tonnage: </th>
                    <th style="text-align: right">{{manageAmountFormat($tonnage)}}</th>
                </tr>  
                <tr class="item ">
                    @php
                    $totalInvoices = array_unique($totalInvoices);
                        $totalAmount = \App\Model\WaInventoryLocationTransferItem::
                        whereIn('wa_inventory_location_transfer_id',$totalInvoices)
                                    ->sum(DB::RAW('(quantity - return_quantity)*(selling_price+category_price+commission)'));
                    @endphp 
                    <th style="text-align:left" colspan = "4">Total Invoices:  {{count(($totalInvoices))}}</th>
                </tr>  
                <tr class="item ">
                    <th style="text-align:left" colspan = "4">Total Value of Goods: {{manageAmountFormat($totalAmount)}}</th>
                </tr>  
            </tbody>
        </table>

        <script type="text/php">
        if ( isset($pdf) ) {
            $font = null;
            $x = 30;
            $y = 45;
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = null;
            $size = 14;
            $color = array(0,0,0);
            $word_space = 0.0;  //  default
            $char_space = 0.0;  //  default
            $angle = 0.0; 
            $pdf->page_text($x, $y, $text, $font, $size, $color,$word_space, $char_space, $angle);
        }
    </script>

        <br>
        <div class="horizontal_dotted_line"><h4>Store Keeper...............................................................................................................................................................................</h4></div>
        
        <div class="horizontal_dotted_line"><h4>Sales Person...............................................................................................................................................................................</h4></div>
    </div>   
</body>
</html>



   
