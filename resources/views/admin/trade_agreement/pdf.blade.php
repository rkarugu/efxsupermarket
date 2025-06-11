<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trade Agreement</title>
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
            margin-bottom: 10px;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 10px;
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

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 35px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.heading td {
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .with_border td, .with_border th{
            border: 1px solid #ddd;
            padding-left: 2px;
            border-collapse: collapse
        }


        .invoice-box table tr.item td {
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
        }  
        .makeBackgroundGrey{
        background: #eee !important;
    }
    </style>
</head>
<body>
                @php $all_settings = getAllSettings(); @endphp

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="3">
                        <h2>{{$all_settings['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="3" style="    text-align: center;">Trade Agreement 
                    </td>
                </tr>
                <tr class="top">
                    <th colspan="1" style="text-align: left">Supplier: {{$trade->supplier->supplier_code}} / {{$trade->supplier->name}}</th>
                    <th colspan="1" style="text-align: center">Reference {{@$trade->reference}}</th>
                    <th colspan="1" style="text-align: right">Date: {{date('d M Y', strtotime($trade->date))}}</th>
                </tr>
            </tbody>        
        </table>

        <table  style="text-align: center;">
            <tbody>
                <tr >
                    <th colspan="1">
                        <h3 style="margin:5px;padding:0">Agreement Summary</h3>
                    </th>
                </tr>
                <tr >
                    <td style="text-align: left">
                        <ol>
                        @php
                            $summary = json_decode($trade->summary);
                        @endphp
                        @if($summary && count($summary)>0)
                            @foreach ($summary as $key => $item)
                            <li>{{$item}}</li>
                            @endforeach
                        @endif
                    </ol>
                    </td>
                </tr>
            </tbody>        
        </table>
        <table class="with_border" style="text-align: left !important;">
            <tbody>
                <tr >
                    <th colspan="4"  style="text-align: center !important;">
                        <h3  style="margin:5px;padding:0">Agreement Discounts</h3>
                    </th>
                </tr>
            </tbody>
        </table>
                @foreach($discounts as $k => $discount)
                <table class="with_border" style="text-align: left !important;">
                    <tbody>
                    @php
                        $other_options = (array)json_decode($discount->other_options);
                    @endphp
                    <tr>
                        <th style="text-align: left !important;" colspan="4">
                            <h3  style="margin:5px;padding:0">{{$k+1}}. {{$discount->discount_type}}</h3>
                        </th>
                    </tr>
                    
                    <tr>
                        <td colspan="4">
                            <b>Target: </b>{{$discount->discount_type == 'Invoice Discount' ? 'Invoice' : 'Product'}}<br>
                            <b>Application Stage: </b> {{@$discount_types[$discount->discount_type]['stage']}}<br>
                            @if($discount->discount_value_type && !in_array($discount->discount_type,['End month Discount']))
                            <p>Discount Type : {{$discount->discount_value_type}}</p>
                            @endif
                            @if(in_array($discount->discount_type,['Target discount on total value','No Goods Return Discount']))
                                <div >
                                    <b>Discount :</b> {{$discount->discount_value}}{{$discount->discount_value_type == 'Value' ? " KSH" : "%"}}
                                </div>
                            @endif
                            @if(in_array($discount->discount_type,['Base Discount','Quarterly Discount','Invoice Discount']))
                                <div >
                                    @php
                                    $discounts_value = [];
                                    @endphp
                                    @foreach ($other_options as $key => $option)
                                        @php
                                            $discounts_value[$option->discount][] = $option->stock_id;
                                        @endphp
                                    @endforeach

                                    @foreach($discounts_value as $key => $dis)
                                        <b>Discount {{$key}}{{$discount->discount_value_type == 'Value' ? " KSH" : " %"}}: </b> {{count($dis)}} Products, 
                                    @endforeach
                                </div>
                            @endif
                            @if(in_array($discount->discount_type,['End month Discount']))
                                <div >
                                    @php
                                    $discounts_value = [];
                                    @endphp
                                    @foreach ($other_options as $key => $option)
                                        @php
                                            $discounts_value[$option->discount.(@$option->type == 'Value' ? " KSH" : " %")][] = $option->stock_id;
                                        @endphp
                                    @endforeach

                                    @foreach($discounts_value as $key => $dis)
                                        <b>Discount {{$key}}: </b> {{count($dis)}} Products, 
                                    @endforeach
                                </div>
                            @endif

                            

                            @if (in_array($discount->discount_type,['Target discount on value','Purchase Quantity Offer']))
                            <div >
                                @php
                                $discounts_value = [];
                                @endphp
                                @foreach ($other_options as $key => $option)
                                    @php
                                        $discounts_value['Purchase Quantity Offer: '.$option->purchase_quantity.' and Free Stock: '.$option->free_stock][] = $option->stock_id;
                                    @endphp
                                @endforeach

                                @foreach($discounts_value as $key => $dis)
                                    <b>{{$key}}: </b> {{count($dis)}} Products,
                                @endforeach
                            </div>
                            @endif
                            @if($discount->discount_type == 'Transport rebate')
                                <div >
                                    <p >Invoices are routinely
                                        discounted at the end of each month.</p>
                                    <table class="table table-bordered table-hover selected-product-list">
                                        <thead>
                                            <tr>
                                                <th style="text-align: left !important;">Location</th>
                                                <th style="text-align: left !important;">Per Unit</th>
                                                <th style="text-align: left !important;">% of Invoice Amount</th>
                                                <th style="text-align: left !important;">Per Tonnage</th>
                                                <th style="text-align: left !important;">Application Stage</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($other_options['location_discounts'] as $key => $option)
                                                <tr>
                                                    <td>{{$option->location}}</td>
                                                    <td>{{$option->per_unit_discount}}</td>
                                                    <td>{{$option->percentage_of_invoice}}</td>
                                                    <td>{{$option->per_tonnage_discount_value}}</td>
                                                    <td>{{@$option->application_stage}}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @if($discount->discount_type == 'Payment Discount')
                                <div >
                                    <p >Invoices are discounted when
                                        payment is made within a specified period.</p>
                                    <table class="selected-product-list">
                                        
                                        <tbody>
                                            <tr>
                                                    @foreach ($other_options as $key => $option)
                                                    <th style="text-align: left !important;">
                                                    @if($key == 'thirty_days')
                                                    30 Days
                                                    @elseif($key == 'twenty_one_days')    
                                                    21 Days
                                                    @elseif($key == 'fourteen_days')    
                                                    14 Days
                                                    @elseif($key == 'advance_upfront')    
                                                    Advance/Upfront Payment
                                                    @elseif($key == 'three_days')    
                                                    3 Days
                                                    @else
                                                    7 Days
                                                    @endif    
                                                    </th>
                                                    @endforeach
                                                </tr>
                                                <tr>
                                                    @foreach ($other_options as $key => $option)
                                                   
                                                    <td style="text-align: left !important;">{{$option}}</td>
                                                    @endforeach
                                                </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            @if($discount->discount_type == 'Target discount on quantity')
                                <div >
                                    <p >Products are routinely
                                        discounted at the end of each month.</p>
                                    <div >
                                        @php
                                        $discounts_value = [];
                                        @endphp
                                        @foreach ($other_options as $key => $option)
                                            @php
                                                $discounts_value['Target Quantity: '.$option->quantity.' and Discount Value: '.$option->discount][] = $option->stock_id;
                                            @endphp
                                        @endforeach
        
                                        @foreach($discounts_value as $key => $dis)
                                            <b>{{$key}}: </b> {{count($dis)}} Products,
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($discount->discount_type == 'Performance Discount')
                                <div >
                                    <div >
                                        @php
                                        $discounts_value = [];
                                        @endphp
                                        @foreach ($other_options as $key => $option)
                                        <b>{{'From: '.manageAmountFormat($option->from).' and To: '.manageAmountFormat($option->to)}}: </b> {{$option->value}}%,
                                           
                                        @endforeach
        
                                    </div>
                                </div>
                            @endif
                           
                            
                        </td>
                    </tr>
                </tbody>        
            </table>
           
                @endforeach
        <br>
        <br>
        <br>
        <table style="width: 100%">
            <tr>
                <td style="width: 50%">
                    <b>Director - Kanini Haraka Enterprises Ltd</b>
                    <br>
                    <p style="border-bottom:1px dotted">
                        Sign: 
                    </p>
                    <p style="border-bottom:1px dotted">
                        Date: 
                    </p>
                </td>
                <td style="width: 50%">
                    <b>On-Behalf of the Supplier</b>
                    <br>
                    <p style="border-bottom:1px dotted">
                        Name: 
                    </p>
                    <p style="border-bottom:1px dotted">
                        Designation: 
                    </p>

                    <p style="border-bottom:1px dotted">
                        Sign: 
                    </p>

                    <p style="border-bottom:1px dotted">
                        Date: 
                    </p>
                    
                </td>
            </tr>
        </table>
      
    </div>   
</body>
</html>