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
            {{--
            <tr class="top">
                <td colspan="3" style="    text-align: center;">P.O. BOX 910 MOLO.TEL 0736776306, 0718743015, 0723958887, 0708203710, 0705242835</td>
            </tr>
            --}}
            <tr class="top">
                <th colspan="3"  style="    text-align: center;">TYRE INVENTORIES</th>
            </tr>
            {{--
                <tr class="top">
                    <th colspan="1"  style="    text-align: left;">From : {{date('d/m/y',strtotime(@$request['start-date']))}}</th>
                    <th colspan="1"  style="    text-align: center;">To : {{date('d/m/y',strtotime(@$request['end-date']))}}</th>
                   
                </tr>
            --}}
		</tbody>        
    </table>

    <br>
    <table class="table-bordered" style="border: 1px solid #ccc; padding: 10px;">
        <tbody>
            <tr class="heading">
                <th style="text-align: left; border: 1px solid #ccc; padding: 10px;">ID</th>
                {{--<th style="text-align: left; border: 1px solid #ccc; padding: 10px;">Stock ID Code</th>--}}
                <th style="text-align: left; border: 1px solid #ccc; padding: 10px;">Title</th>
                {{--<th style="text-align: left;">Description</th>--}}
                <th style="text-align: left; border: 1px solid #ccc; padding: 10px;">Tyre Size</th>
                <th style="text-align: right; border: 1px solid #ccc; padding: 10px;"> New Tire in Store</th>
                <th style="text-align: right; border: 1px solid #ccc; padding: 10px;">In Motor Vehicle</th>
                <th style="text-align: right; border: 1px solid #ccc; padding: 10px;">Retread Tire in Stock</th>
                <th style="text-align: right; border: 1px solid #ccc; padding: 10px;">Tyres in Retread</th>
                <th style="text-align: right; border: 1px solid #ccc; padding: 10px;">Damaged Tyres</th>
            </tr>
                @php
                    $total_new_tyre_in_stock_count = 0;
                    $total_in_motor_vehicle_count = 0;
                    $total_retread_tyre_in_stock_count = 0;
                    $total_tyres_in_retread_count = 0;
                    $total_damaged_tyre_count = 0;
                @endphp
                @foreach($data as $key => $list)
                    <tr class="item" >
                        <td style="text-align: left; border: 1px solid #ccc; padding: 10px;">{!! ++$key !!}</td>
                        {{--<td style="text-align: left; border: 1px solid #ccc; padding: 10px;">{!! $list->stock_id_code !!}</td>--}}
                        <td style="text-align: left; border: 1px solid #ccc; padding: 10px;">{!! $list->title !!}</td>
                        {{--<td style="text-align: left; border: 1px solid #ccc; padding: 10px;">{!! @$list->description !!}</td>--}}
                        <td style="text-align: left; border: 1px solid #ccc; padding: 10px;">{{$list->tyre_size}}</td>
                        <td style="text-align: right; border: 1px solid #ccc; padding: 10px;">{{$list->new_tyre_in_stock_count}}</td>
                        <td style="text-align: right; border: 1px solid #ccc; padding: 10px;">{{$list->in_motor_vehicle_count}}</td>
                        <td style="text-align: right; border: 1px solid #ccc; padding: 10px;">{{$list->retread_tyre_in_stock_count}}</td>
                        <td style="text-align: right; border: 1px solid #ccc; padding: 10px;">{{$list->tyres_in_retread_count}}</td>
                        <td style="text-align: right; border: 1px solid #ccc; padding: 10px;">{{$list->damaged_tyre_count}}</td>
                        
                        @php
                            $total_new_tyre_in_stock_count += $list->new_tyre_in_stock_count;
                            $total_in_motor_vehicle_count += $list->in_motor_vehicle_count;
                            $total_retread_tyre_in_stock_count += $list->retread_tyre_in_stock_count;
                            $total_tyres_in_retread_count += $list->tyres_in_retread_count;
                            $total_damaged_tyre_count += $list->damaged_tyre_count;
                        @endphp
                    </tr>
                @endforeach        
            </tbody>        
    </table>
    <table>
        <tbody>
            <tr style="    border-top: 2px dashed #cecece;">
                <td colspan="10"></td>
            </tr>
            <tr >
                <td colspan="4">{{count($data)}} Lines</td>
                <td  style=" width: 300px; text-align: right;" colspan="1">Total:</td>
                <td colspan="1"> {{manageAmountFormat($total_new_tyre_in_stock_count)}}</td>
                <td colspan="1"> {{manageAmountFormat($total_in_motor_vehicle_count)}}</td>
                <td colspan="1"> {{manageAmountFormat($total_retread_tyre_in_stock_count)}}</td>
                <td colspan="1"> {{manageAmountFormat($total_tyres_in_retread_count)}}</td>
                <td colspan="1"> {{manageAmountFormat($total_damaged_tyre_count)}}</td>
            </tr>           
        </tbody>
    </table>
</div>   

</body>
</html>