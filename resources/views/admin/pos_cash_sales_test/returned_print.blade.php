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

<?php $all_settings = getAllSettings();?>
<div class="invoice-box">
    <table  style="text-align: center;">
        <tbody>
            <tr class="top">
                <th colspan="2">
                    <h2 style="font-size:18px !important">{{ $all_settings['COMPANY_NAME']}}</h2>
                </th>
            </tr>
            <tr class="top">
                <td colspan="2" style="    text-align: center;">{{getAllSettings()['ADDRESS_2']}}</td>
            </tr>
            <tr class="top">
                <th colspan="1"  style="    text-align: left;">CREDIT NOTE : {{@$data->first()->return_grn}}</th>
                <th colspan="1"  style="    text-align: right;">Cashier : {{@$data->first()->returned_by->name}}</th>
            </tr>
            @if ($data->first() && $data->first()->print_count > 0)
                <tr class="top">
                    <th colspan="1"  style="    text-align: center;">REPRINT : {{@$data->first()->print_count}}</th>
                    <th colspan="1"  style="    text-align: center;">REPRINT : {{@$data->first()->print_count}}</th>
                </tr>
            @endif
		</tbody>        
    </table>

    <br>
    <table>
        <tbody>
            <tr class="heading">
                {{-- <th>Return GRN </th> --}}
                <th style="width: 10%">Cash Sale No.</th>
                {{-- <th style="width: 10%">Returned By</th> --}}
                <th style="width: 10%">Item</th>
                <th style="width: 40%">Description</th>
                <th style="width: 15%">Dated</th>
                <th style="width: 12%;">Quantity</th>
                <th style="width: 13%;text-align:right" >Total</th>
            </tr>
                @php
                    $gross_amount = 0;
                @endphp
                @foreach($data as $item)
                    <tr class="item">
                        <td>{{@$item->parent->sales_no}}</td>
                         <td>{{@$item->item->stock_id_code}}</td>
                         <td>{{@$item->item->description}}</td>
                         {{-- <td>{{@$item->getTransferLocation->toStoreDetail->location_name}}</td> --}}
                         <td>{{date('d/m/Y H:i',strtotime(@$item->return_date))}}</td>
                         <td>{{manageAmountFormat($item->return_quantity)}}</td>
                         <td>{{manageAmountFormat($item->return_quantity * $item->selling_price)}}</td>                       
                    </tr>
                    @php
                        $gross_amount += ($item->return_quantity * $item->selling_price);
                    @endphp
                @endforeach  
                <tr style="    border-top: 2px dashed #cecece;">
                    <td colspan="6"></td>
                </tr>
                <tr >
                    <td colspan="2">{{count($data)}} Lines</td>
                    <td colspan="2">Returned By: {{@$data->first()->returned_by->name}}</td>
                    <td style="text-align: right;" colspan="1"><h4>Grand total:</h4></td>
                    <td colspan="1">{{manageAmountFormat($gross_amount)}}</td>
                </tr>         
            </tbody>        
    </table>
    <table>
        <tbody>
                    
        </tbody>
    </table>


    @if(!empty($esd_details))

            <div style="width:100%; padding: 10px; text-align:left;"> 
                <div style="width:20%;  float: left;">
                    @if($esd_details->verify_url!="")

                        @if(isset($is_print))
                            {{ QrCode::size(70)->generate($esd_details->verify_url) }}
                        @else
                            <img src="data:image/png;base64, {!! base64_encode(QrCode::size(70)->generate($esd_details->verify_url)) !!} ">
                        @endif

                       
                        
                    @endif
                </div>
                <div style="width:80%; text-align:left;  float: left;">
                    CU Serial No : {{ $esd_details->cu_serial_number }}<br>
                    <p> CU Invoice Number : {{ $esd_details->cu_invoice_number }} </p>
                </div>
            </div>
        @endif
</div>   

</body>
</html>