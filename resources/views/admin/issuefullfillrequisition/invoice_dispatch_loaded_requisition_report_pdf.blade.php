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
                        <h2>STORE C STOCKS REQUISTION</h2>
                    </th>
                </tr>
                <tr class="top">
                    <th colspan="1">
                        <b>Salesman : {{@$data->first()->getSalesMan->name}}</b>  
                    </th>

                    <th colspan="1">
                        <b>Store : {{@$data->first()->getStoreLocation->location_name}}</b>  
                    </th>
                    <th colspan="1">
                        <?php 
                        if(isset($data->first()->getShift)){ ?>
                            <b>Shifts : {{@$data->first()->getShift->shift_id}}</b>
                        <?php }else{ ?>
                        <b>Document No.:{{@$data->first()->document_no}}</b>
                        <?php } ?>
                    </th>
                </tr>
            </tbody>        
        </table>
        <br>
        <table>
            <tbody>
                <tr class="heading">
                    <th  style="width:55%;text-align:left">Particulars</th>
                    <th style="width:15%;text-align:right">Total Qty</th>
                    <th style="width:15%;text-align:right">Dispatched Qty</th>
                    <th style="width:15%;text-align:right">Balance Qty</th>
                </tr>
                <tr style="    border-top: 2px dashed #cecece;">
          <td colspan="5"></td>
        </tr>
                @php
                    $i = 0;
                    $total = 0;
                @endphp
                @foreach ($data as $inventory_item)
                @php 
                  $total +=$inventory_item->total_qty;
                @endphp
                        <tr class="item @if($i%2==0) bg-grey @endif">
                            <td style="width:40%;text-align:left">{{@$inventory_item->getInventoryItem->title}}</td>
                            <td style="width:20%;text-align:right">{{@$inventory_item->total_qty}}</td>                            
                            <td style="width:20%;text-align:right">{{@$inventory_item->qty_loaded}}</td>                            
                            <td style="width:20%;text-align:right">{{@$inventory_item->balance_qty}}</td>                            
                        </tr>
                        
                           
                    
                @endforeach
                  <tr class="item @if($i%2==0) bg-grey @endif">
                            <td  colspan = "4" style="text-align:right">Total: {{$total}}</td>                           
                  </tr>
                
             
            </tbody>
        </table>
        <br>

        <div class="horizontal_dotted_line"><h4>Generated By user: {{getLoggeduserProfile()->name}}</h4></div>
        <div class="horizontal_dotted_line"><h4>Date & Timestamp: {{@$data[0]->created_at}}</h4></div>
        <div class="horizontal_dotted_line"><h4>Printed Time: {{date('Y-m-d')}}</h4></div>

        <div class="horizontal_dotted_line"><h4>Signature.....................................................................................................................................................................</h4></div>
        
        <div class="horizontal_dotted_line"><h4>Offical Stamp...............................................................................................................................................................................</h4></div>
    </div>   
</body>
</html>