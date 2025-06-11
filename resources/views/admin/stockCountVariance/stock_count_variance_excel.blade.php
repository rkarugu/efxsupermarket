<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Count Variation</title>
    <style>
        
        body, html {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #000;
            margin-top: 9px;
            margin-left: 0px;
            margin-right: 0px !important;
            padding: 0;
            width: 100%;
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
        table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
        }
        .t-head{
            border: 1px solid black;
            border-collapse: collapse;
            font-size: 11px !important;
            font-weight: bold;


        }
        th{
            font-size: 11px !important;
           
        }
                    
        .data{
            font-size: 10px;
            border: 1px solid black !important;
            border-right: 1px solid black !important; 
            border-collapse: collapse !important;
            /* color: #000 !important; */

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

    <div class="">
        <table width="100%">         
            <tr >
                <td colspan="2" style="font-weight: 700; font-size: 16px;text-align: left !important">{{ getAllSettings()['COMPANY_NAME'] }}
             </td>
            <tr >
                <td style="font-weight: 700; font-size: 16px;text-align: left !important">Detailed Stock Count Variation Report - Excess
             </td>
             <td  style="font-weight: 700; font-size: 16px;text-align: left !important">{{$branch->location_name}}</td>
            </tr>             
            <tr>
                <td colspan="1" style="font-weight: 500; font-size: 14px;text-align: left !important">Date: {{\Carbon\Carbon::parse($start_date)->toDateString()}} </td>
                @if ($bin)
                <td  style="font-weight: 700; font-size: 14px;text-align: left !important">{{$bin->title}}</td>
                @else
                <td  style="font-weight: 700; font-size: 14px;text-align: left !important"></td>
                @endif
            </tr>
          
        </table>
        <table class="table table-bordered table-hover" id="positive_variance_table" width="100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Bin</th>
                    <th>Bizwiz</th>
                    <th>Physical</th>
                    <th>Excess</th>
                    <th>Excess value</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPositiveLines = 0;
                    $totalExcessAmount = 0;
                @endphp
                
                @foreach ($categories as $category)
                @php
                    $numbering = 0;
                @endphp
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">{{$category->category_description}}</td>
                </tr>
                @foreach ($data as $row)
                @if ($row->category_id == $category->id && isset($row->variation) && $row->variation >= 0)
                <tr>
                    <td class="data" style="text-align: left !important;">{{$row->getInventoryItemDetail->title}}</td>
                    <td class="data">{{$row->getUomDetail->title}}</td>
                    <td class="data" style="text-align: center;">{{$row->current_qoh}}</td>
                    <td class="data" style="text-align: center;">{{$row->quantity_recorded ?? 'NCE'}}</td>
                    <td class="data" style="text-align: center;">{{$row->variation}}</td>
                    <td class="data" style="text-align: right;">{{manageAmountFormat($row->variation * $row->getInventoryItemDetail?->selling_price)}}</td>
                    <td class="data">{{$row->reference}}</td>
                </tr>
                @php    
                    $numbering ++;
                    $totalPositiveLines++;
                    $totalExcessAmount += ($row->variation * $row->getInventoryItemDetail?->selling_price);
                @endphp
                @endif
                @endforeach
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">{{'Lines : '.$numbering}}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="t-head" colspan="4" style="text-align: left !important;">Total Excess Amount</td>
                    <td class="data" style="text-align: right;" colspan="2">{{manageAmountFormat($totalExcessAmount) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <hr>
            <p style="margin: 2px 2px !important; padding: 5px 2px 2px 2px !important; text-align: left !important; font-size:12px !important; ">STOREKEEPER</p>
        <table width="100%">
          
           
            <tr>
                <td class="t-head" colspan="1" style="text-align: left !important;"></td>
                <td class="t-head" colspan="3" style="text-align: center !important;">NAME</td>
                <td class="t-head" colspan="2" style="text-align: center !important;">ID</td>
                <td class="t-head" colspan="1" style="text-align: center !important;">SIGN</td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">1.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">2.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">3.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">4.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
        </table>
        <hr>
            <p style="margin: 2px 2px !important; padding: 5px 2px 2px 2px !important; text-align: left !important; font-size:12px !important; ">STOCK CONTROLLER </p>
            <table width="100%" style="border: none !important;">
                <tr>
                    <td  colspan="4" style="text-align: left !important; border:none !important;">NAME:______________________ </td>
                    <td  colspan="2" style="text-align: left !important; border:none !important;">ID:___________________ </td>
                    <td  colspan="1" style="text-align: left !important; border:none !important;">SIGN:________________ </td>
                </tr>
              
            </table>
        <div style="page-break-before: always;"></div>
        <table width="100%">         
            <tr >
                <td colspan="2" style="font-weight: 700; font-size: 16px;text-align: left !important">{{ getAllSettings()['COMPANY_NAME'] }}
             </td>
            <tr >
                <td style="font-weight: 700; font-size: 16px;text-align: left !important">Detailed Stock Count Variation Report - Missing
             </td>
             <td  style="font-weight: 700; font-size: 16px;text-align: left !important">{{$branch->location_name}}</td>
            </tr>             
            <tr>
                <td colspan="1" style="font-weight: 500; font-size: 14px;text-align: left !important">Date: {{\Carbon\Carbon::parse($start_date)->toDateString()}} </td>
                @if ($bin)
                <td  style="font-weight: 700; font-size: 14px;text-align: left !important">{{$bin->title}}</td>
                @else
                <td  style="font-weight: 700; font-size: 14px;text-align: left !important"></td>
                @endif
            </tr>
          
        </table>

        <table class="table table-bordered table-hover" id="negative_variance_table" width="100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Bin</th>
                    <th>Bizwiz</th>
                    <th>Physical</th>
                    <th>Missing</th>
                    <th>Missing Value</th>
                    <th>Reference</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalNegativeLines = 0;
                    $totalMissingAmount = 0;
                @endphp
                
                @foreach ($categories as $category)
                @php
                    $numbering = 0;
                @endphp
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">{{$category->category_description}}</td>
                </tr>
                @foreach ($data as $row)
                @if ($row->category_id == $category->id && isset($row->variation) && $row->variation < 0)
                <tr>
                    <td class="data" style="text-align: left !important;">{{$row->getInventoryItemDetail->title}}</td>
                    <td class="data">{{$row->getUomDetail->title}}</td>
                    <td class="data" style="text-align: center;">{{$row->current_qoh}}</td>
                    <td class="data" style="text-align: center;">{{$row->quantity_recorded ?? 'NCE'}}</td>
                    <td class="data" style="text-align: center;">{{$row->variation}}</td>
                    <td class="data" style="text-align: right;">{{manageAmountFormat($row->variation * $row->getInventoryItemDetail?->selling_price * -1)}}</td>
                    <td class="data">{{$row->reference}}</td>
                </tr>
                @php    
                    $numbering ++;
                    $totalNegativeLines++;
                    $totalMissingAmount += ($row->variation * $row->getInventoryItemDetail?->selling_price * -1);
                @endphp
                @endif
                @endforeach
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">{{'Lines : '.$numbering}}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="t-head" colspan="4" style="text-align: left !important;">Total Missing Amount</td>
                    <td class="data" style="text-align: right;" colspan="2">{{manageAmountFormat($totalMissingAmount) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <hr>
        <p style="margin: 2px 2px !important; padding: 5px 2px 2px 2px !important; text-align: left !important; font-size:12px !important; ">STOREKEEPER</p>
    <table width="100%" style="border:none !important;">
      
       
        <tr style="border:none !important;" >
            <td class="t-head" colspan="1" style="text-align: left !important; border:none !important;"></td>
            <td class="t-head" colspan="3" style="text-align: center !important; border:none !important;">NAME</td>
            <td class="t-head" colspan="2" style="text-align: center !important; border:none !important;">ID</td>
            <td class="t-head" colspan="1" style="text-align: center !important; border:none !important;">SIGN</td>
        </tr>
        <tr>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">1.</td>
            <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
        </tr>
        <tr>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">2.</td>
            <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
        </tr>
        <tr>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">3.</td>
            <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
        </tr>
        <tr>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">4.</td>
            <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
        </tr>
    </table>
    <hr>
        <p style="margin: 2px 2px !important; padding: 5px 2px 2px 2px !important; text-align: left !important; font-size:12px !important; ">STOCK CONTROLLER </p>
        <table width="100%" style="border: none !important;">
            <tr>
                <td  colspan="4" style="text-align: left !important; border:none !important;">NAME:______________________ </td>
                <td  colspan="2" style="text-align: left !important; border:none !important;">ID:___________________ </td>
                <td  colspan="1" style="text-align: left !important; border:none !important;">SIGN:________________ </td>
            </tr>
          
        </table>
        <div style="page-break-before: always;"></div>

        {{-- ITEMS WITH NO COUNTS --}}
        <table width="100%">         
            <tr >
                <td colspan="2" style="font-weight: 700; font-size: 16px;text-align: left !important">{{ getAllSettings()['COMPANY_NAME'] }}
             </td>
            <tr >
                <td style="font-weight: 700; font-size: 16px;text-align: left !important">Detailed Stock Count Variation Report - Uncounted
             </td>
             <td  style="font-weight: 700; font-size: 16px;text-align: left !important">{{$branch->location_name}}</td>

            </tr>             
            <tr>
                <td colspan="1" style="font-weight: 500; font-size: 14px;text-align: left !important">Date: {{\Carbon\Carbon::parse($start_date)->toDateString()}} </td>
                @if ($bin)
                <td  style="font-weight: 700; font-size: 14px;text-align: left !important">{{$bin->title}}</td>
                @else
                <td  style="font-weight: 700; font-size: 14px;text-align: left !important"></td>
                @endif
            </tr>
          
        </table>

        <table class="table table-bordered table-hover" id="negative_variance_table" width="100%">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Bin</th>
                    <th>Bizwiz</th>
                    <th>Physical</th>
                    <th>Value</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalNCELines = 0;
                    $totalNCEAmount = 0;
                @endphp
                
                @foreach ($categories as $category)
                @php
                    $numbering = 0;
                @endphp
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">{{$category->category_description}}</td>
                </tr>
                @foreach ($data as $row)
                @if ($row->category_id == $category->id && !isset($row->variation) )
                <tr>
                    <td class="data" style="text-align: left !important;">{{$row->getInventoryItemDetail->title}}</td>
                    <td class="data">{{$row->getUomDetail->title}}</td>
                    <td class="data" style="text-align: center;">{{$row->current_qoh}}</td>
                    <td class="data" style="text-align: center;">{{ 'NCE'}}</td>
                    <td class="data" style="text-align: right;">{{manageAmountFormat($row->current_qoh * $row->getInventoryItemDetail?->selling_price )}}</td>
                </tr>
                @php    
                    $numbering ++;
                    $totalNCELines++;
                    $totalNCEAmount += ($row->current_qoh * $row->getInventoryItemDetail?->selling_price);
                @endphp
                @endif
                @endforeach
                <tr>
                    <td class="t-head" colspan="5" style="text-align: left !important;">{{'Lines : '.$numbering}}</td>
                </tr>
                @endforeach
                <tr>
                    <td class="t-head" colspan="3" style="text-align: left !important;">Total Uncounted Amount</td>
                    <td class="data" style="text-align: right;" colspan="2">{{manageAmountFormat($totalNCEAmount) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <hr>
            <p style="margin: 2px 2px !important; padding: 5px 2px 2px 2px !important; text-align: left !important; font-size:12px !important; ">STOREKEEPER</p>
        <table width="100%">
          
           
            <tr>
                <td class="t-head" colspan="1" style="text-align: left !important;"></td>
                <td class="t-head" colspan="3" style="text-align: center !important;">NAME</td>
                <td class="t-head" colspan="2" style="text-align: center !important;">ID</td>
                <td class="t-head" colspan="1" style="text-align: center !important;">SIGN</td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">1.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">2.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">3.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
            <tr>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;">4.</td>
                <td class="t-head" colspan="3" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="2" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
                <td class="t-head" colspan="1" style="padding: 10px 2px 10px 2px !important; text-align: left !important;"></td>
            </tr>
        </table>
        <hr>
            <p style="margin: 2px 2px !important; padding: 5px 2px 2px 2px !important; text-align: left !important; font-size:12px !important; ">STOCK CONTROLLER </p>
            <table width="100%" style="border: none !important;">
                <tr>
                    <td  colspan="4" style="text-align: left !important; border:none !important;">NAME:______________________ </td>
                    <td  colspan="2" style="text-align: left !important; border:none !important;">ID:___________________ </td>
                    <td  colspan="1" style="text-align: left !important; border:none !important;">SIGN:________________ </td>
                </tr>
              
            </table>

    </div>   
</body>
</html>



