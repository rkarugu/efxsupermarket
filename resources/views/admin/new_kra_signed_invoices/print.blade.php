<html>
<title>Print</title>

<head>
	<style type="text/css">
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

        .invoice-box table tr.item td {
            /* border-bottom: 1px solid #eee; */
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

<?php $all_settings = getAllSettings(); 
    $getLoggeduserProfile = getLoggeduserProfile();
    ?>
<div class="invoice-box">
    <table  style="text-align: center;">
        <tbody>
            <tr class="top">
                <th colspan="2">
                    <h2 style="font-size:18px !important">{{ $all_settings['COMPANY_NAME']}}</h2>
                </th>
            </tr>
            <tr class="top">
                <td colspan="2" style="    text-align: center;">
                        <br>
                        {{getAllSettings()['ADDRESS_2']}}                    <br>
                    Email: {{getAllSettings()['EMAILS']}}
                    <br>
                    {{getAllSettings()['ADDRESS_2']}}</td>
            </tr>
            
            <tr class="top">
                <!-- <th  colspan="1">
                    VAT NO:
                </th> -->
                <th colspan="1"  style="    text-align: left;">
                    PIN NO: {{getAllSettings()['PIN_NO']}}
                </th>
                <th colspan="1"  style="    text-align: right;font-size: 14px;">Invoice NO: {!! $list->transfer_no!!}</th>
            </tr>
            
            <tr class="top">

                <th colspan="1"  style="    text-align: left;">Customer PIN : {!! $list->customer_pin!!}</th>
                @php
                    $shift_id = \App\Model\WaShift::where('id',@$list->shift_id)->first();
                @endphp
                {{--<th colspan="1"  style="    text-align: right;">ShiftId: {!! @$shift_id->shift_id !!}</th>
                --}}
            </tr> 
            {{-- @if ($list->print_count > 1)
                <tr class="top">
                    <th  colspan="2" style="width: 100%;text-align:center">
                        <div style="display: flex">
                            <div style="width: 50%;text-align:center">
                                <h2 style="font-size:18px !important">REPRINT {{$list->print_count-1}}</h2>
                            </div>
                            <div style="width: 50%;text-align:center">
                                <h2 style="font-size:18px !important">REPRINT {{$list->print_count-1}}</h2>
                            </div>
                        </div>    
                    </th>
                </tr>
            @endif --}}
		</tbody>        
    </table>
	<table >
        <tbody>
            {{-- <tr class="top">
                @php
                $customer = $list->get_customer;
                @endphp
                <th  colspan="1" style="text-align: left;">ACC NO: {{$list->customer}} | {{@$customer->telephone}} | {{@$customer->address}}</th>
               
                <th  colspan="1" style="text-align:right">DATE: {!! date('Y-m-d',strtotime($list->transfer_date))!!}</th>
            </tr> --}}
			<tr class="top">
                <th  colspan="2" style="text-align: left;">Name: {{$list->name}}</th>
            </tr>
        </tbody>        
    </table>
    <br>
    <table>
        <tbody>
            <tr class="heading">
                <td style="width: 10%;">Code</td>
                <td style="width: 31%;">Description</td>
                <td style="width: 6%;">Qty</td>
                <td style="width: 12%;">Price</td>
                <td style="width: 12%;">Amount</td>
                <td style="width: 10%;">Disc</td>
                <td style="width: 8%;">Vat%</td>
                <td style="width: 11%;">Total</td>
            </tr>
            @php
                $TONNAGE = 0;
                $gross_amount = 0;
            @endphp
            @foreach($list->getRelatedItem as $item)
                <tr class="item">
                    <td>{{@$item->getInventoryItemDetail->stock_id_code}}</td>
                    <td>{{@$item->getInventoryItemDetail->description}}</td>
                    <td style="">{{floor($item->quantity)}}</td>
                    <td>{{manageAmountFormat($item->selling_price)}}</td>
                    <td>{{manageAmountFormat($item->quantity*$item->selling_price)}}</td>
                    <td>{{manageAmountFormat($item->discount_amount)}}</td>
                    <td>{{$item->vat_rate}}</td>
                    <td>{{manageAmountFormat($item->total_cost_with_vat)}}</td>
                </tr>

                @php
                    $gross_amount += (($item->quantity*$item->selling_price)-$item->discount_amount);

                    $TONNAGE = ($item->getInventoryItemDetail->net_weight ?? 1) * $item->quantity;
                @endphp
            @endforeach
        
            </tbody>        
    </table>
    <table>
        <tbody>
            <tr style="    border-top: 2px dashed #cecece;">
                <td colspan="5"></td>
            </tr>

            <tr >
                <td colspan="3">{{count($list->getRelatedItem)}} Lines</td>
                <td style="text-align: right;" colspan="1">Gross Amount:</td>
                <td >{{manageAmountFormat($gross_amount)}}</td>
            </tr>
            <tr >
                <td colspan="2">Prepared by: {{@$list->user->name}}</td>
                {{-- <td>Time: {{date('H:i A',strtotime($list->created_at))}}</td> --}}
                <td colspan="1">Delivered By: ___________</td>
                <td style="text-align: right;" colspan="1">Discount:</td>
                <td>{{manageAmountFormat($list->getRelatedItem->sum('discount_amount') ?? 0.00)}}</td>
            </tr>
            <tr >
                <td colspan="3"></td>
                <td style="text-align: right;" colspan="1">Net Amount:</td>
                <td >{{manageAmountFormat($gross_amount - ($list->getRelatedItem->sum('vat_amount') ?? 0.00))}}</td>
            </tr>
            <tr >
                <td colspan="3"></td>
                <td style="text-align: right;" colspan="1">V.A.T:</td>
                <td >{{manageAmountFormat($list->getRelatedItem->sum('vat_amount') ?? 0.00)}}</td>
            </tr>
            <tr >
                <td colspan="2">Received By: ______________</td>
                <td colspan="1">Sign: ______________</td>
                <td colspan="1"></td>
                <td colspan="1" style="text-align: center;">
                    <hr style="border: 1px dashed #7b7b7b;">
                </td>
            </tr>
            <tr >
                <td colspan="1"></td>
                <td colspan="1">RUBBER STAMP</td>
                <td colspan="1"></td>
                <td colspan="1" style="text-align: right;" >Total: 
                    <hr style="border: 2px dashed #979797;">
                </td>
                <td colspan="1">
                    {{manageAmountFormat($gross_amount)}}
                    <hr style="border: 2px dashed #979797;">
                </td>
            </tr>
            <tr >
                <td colspan="3">Amount in Words
                    <br>
                    {{strtoupper(getCurrencyInWords($gross_amount))}}
                </td>
                @php
                        $invoices = \App\Model\WaInventoryLocationTransfer::where('shift_id',@$list->shift_id)->pluck('id')->toArray();
                        $invoicesItems = \App\Model\WaInventoryLocationTransferItem::whereIn('wa_inventory_location_transfer_id',$invoices)->sum('total_cost_with_vat');
                @endphp     
                {{-- <td colspan="1">A/C Balance : {{manageAmountFormat($invoicesItems)}}</td> --}}
                <td colspan="2">Change: {{$list->change}}</td>
            </tr>
            <tr >
					<td colspan="5"></td>
				</tr>
                <tr >
					<td colspan="5"></td>
				</tr>
                <tr >
					<td colspan="5"></td>
				</tr>
                @if ($getLoggeduserProfile->upload_data == 0)
                <tr >
					<td colspan="5" style="text-align:center">{{($list->upload_data)}}</td>
					
				</tr>
                @endif
        </tbody>
    </table>

     @if(!empty($esd_details))

        <div style="width:100%; padding: 10px; text-align:left;"> 
            <div style="width:10%;  float: left;">
                @if($esd_details->verify_url!="")
                    @if(isset($is_pdf))
                        <img width="50" src="data:image/png;base64, {!! base64_encode(QrCode::size(70)->generate($esd_details->verify_url)) !!} ">
                    @else
                        {!! QrCode::size(70)->color(224, 224, 224)->backgroundColor(0, 0, 0)->generate($esd_details->verify_url) !!}
                    @endif

                    
                    
                @endif
            </div>
            <div style="width:90%; text-align:left;  float: left;">
                CU Serial No : {{ $esd_details->cu_serial_number }}<br>
                <p> CU Invoice Number : {{ $esd_details->cu_invoice_number }} </p>
            </div>
        </div>
    @endif
</div>   

</body>
</html>