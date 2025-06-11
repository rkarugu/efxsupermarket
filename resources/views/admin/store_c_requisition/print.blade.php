<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Cash Sales</title>
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
                    <th colspan="2">
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="2" style="text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
                </tr>
				<tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    <th colspan="2" style="width: 100%;text-align:center;font-size:18px !important">
                        STORE C REQUISITIONS
                    </th>
				</tr>
               
                <tr class="top">
                    <th style="text-align:left;vertical-align: top;width:67%">
                        <table class="table">
                                <th style="text-align:left">
                                    Requisition: {{$row->requisition_no}}
                                </th>
                                <tr style="text-align:left">
                                    <th>To Store : {{@$row->getRelatedToLocationAndStore->location_name}}</th>
                                </tr>
                                <tr style="text-align:left">
                                    <th>Manual Doc No. : {{@$row->manual_doc_no}}</th>
                                </tr>
                        </table>
                    </th>
                    <th   style="text-align:left;width:33%">
                        <table class="table">
                            <tr class="top">
                                <th  style="text-align:left;width:33%"> DATE :</th>
                                <th  style="text-align:right;width:67%">{{date('d-M-Y',strtotime($row->requisition_date))}} </th>
                            </tr>
                            <tr class="top">
                                <th style="text-align:left;width:33%">Employee :</th>
                                <th style="text-align:right;width:67%">{{@$row->getrelatedEmployee->name}}</th>
                            </tr>
                            <tr class="top">
                                <th style="text-align:left;width:33%">Branch :</th>
                                <th style="text-align:right;width:67%">{{@$row->getBranch->name}}</th>
                            </tr>
                            <tr class="top">
                                <th style="text-align:left;width:33%">Department :</th>
                                <th style="text-align:right;width:67%">{{@$row->getDepartment->department_name}}</th>
                            </tr>
                        </table>
                    </th>
                </tr>
             
               
            </tbody>        
        </table>
        <table>
            <tbody>
                <tr class="heading">
					<th style="width:10%">Selection</th>
					<th style="width:40%">Description</th>
					<th style="width:10%">Bal Stock</th>
					<th style="width:15%;" >Unit</th>
					<th style="width:10%">QTY</th>
					<th style="width:15%;text-align:left">Location</th>
				</tr>
                @php
                    $gross_amount = 0;
                @endphp
                @foreach ($row->getRelatedItem as $item)
                    <tr class="item">
						<td>
							{{@$item->getInventoryItemDetail->stock_id_code}}
						</td>
						<td>{{@$item->getInventoryItemDetail->title}}</td>
						<td>{{@$item->getInventoryItemDetail->getAllFromStockMovesC->where('wa_location_and_store_id',$item->store_location_id)->sum('qauntity')}}</td>
						<td>{{@$item->getInventoryItemDetail->pack_size->title}}</td>
						<td>{{$item->quantity}}</td>
						<td style="text-align:left">{{@$item->location->location_name}}</td>                                       
                    </tr>
                    @php                       
                        $gross_amount += ($item->quantity);
                    @endphp
                @endforeach
                </tbody>
        </table>
        <table>
            <tbody>
                
                <tr >
					<td colspan="3"></td>
				</tr>

                <tr >
					<td colspan="1">{{count($row->getRelatedItem)}} Lines</td>
					<td style="text-align: right;" colspan="1">Total Quantity:</td>
					<td  colspan="1">{{manageAmountFormat($gross_amount)}}</td>
				</tr>                
                <tr >
					<td colspan="3"></td>
				</tr>
                <tr >
					<td colspan="3"></td>
				</tr>
                <tr >
					<td colspan="3"></td>
				</tr>
                <tr >
					<td colspan="3" style="text-align: left;">

                        Sign:...............................................................................................................
                    </td>
				</tr>
            </tbody>
        </table>
    </div>   
</body>
</html>