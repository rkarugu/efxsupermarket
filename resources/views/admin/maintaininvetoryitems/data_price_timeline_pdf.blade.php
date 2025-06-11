<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{!!$title!!}</title>
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

        <table width="100%">
            <tr >
                <td colspan="15" align="left" style="font-weight: 700; font-size: 16px;text-align: left !important">{!!$title!!}
             </td>
            </tr>
        </table> 

        <table width="100%">
        <tbody width="100%"> 
                            <th>Date</th>
                            <th>Time</th>
                            <th>Stock Id Code</th>
                            <th>Item</th>
                            <th>Transcation Type</th>
                            <th>Branch</th>
                            <th>Processed By</th>
                            <th>Before SOH</th>
                            <th>Incoming SOH</th>                            
                            <th>New SOH</th>
                            <th>Incoming Standard Cost</th>
                            <th>Current Standard Cost</th>
                            <th>Current Selling Price</th>
                            <th>Incoming Selling Price</th>
                            <th>Delta</th>
                <tr class="heading">
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Date</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Time</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Stock Id Code</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Item </td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Transcation Type </td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important"> Branch </td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important"> Processed By </td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Before SOH</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Incoming SOH</td>                   
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">New SOH</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Incoming Standard Cost</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Current Standard Cost</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Current Selling Price</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Previous Selling Price</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">Delta</td>
                </tr>
                  
                  
                        @php
                        use Carbon\Carbon;
                         use App\Model\WaStockMove;
                        @endphp
                        
                        @foreach($timelines as $record)
                        @php
                        $qoh_new = ($record->qty_received ? : 0) + ($record->current_stock ? : 0 );

                        @endphp
                             <tr class="item">
                                <td>{{ \Carbon\Carbon::parse($record->updated_at)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($record->updated_at)->format('h:i:sa') }}</td> 
                                <td>{{ $record->stock_id_code }}</td>
                                <td>{{ $record->title }}</td>
                                <td>{{ $record->transcation_type }}</td>
                                <td>THIKA STORE(Nampak)</td>
                                <td>{{ $record->username }}</td>
                                <td>
                                    @if($record->transcation_type == 'Price Change')
                                   {{ number_format( $record->qoh_before) }}
                                    @else                                 
                                    {{ number_format($record->current_stock + $record->qoh_before) }}
                                    @endif
                                 </td>
                                <td>{{ number_format($record->qty_received, 2) }}</td>
                                <td>
                                    @if($record->transcation_type == 'Price Change')
                                   {{ number_format($record->qoh_before) }}
                                    @else                                 
                                    {{ number_format($qoh_new ,2 )}} 
                                    @endif
                                </td>
                                <td>{{ number_format($record->standart_cost_unit, 2) }}</td>
                                <td>{{ number_format($record->current_standard_cos_moves, 2) }}</td>
                                <td>
                                    @if($record->transcation_type == 'Price Change')
                                   {{ number_format($record->current_selling_price, 2)}}
                                    @else                                 {{number_format($record->current_selling_moves, 2)}} 
                                    @endif
                                    </td>
                                <td>{{ number_format($record->selling_price, 2) }}</td>
                                <td>{{ number_format($record->delta, 2) }}</td>
                               
                               
                            </tr>
                        @endforeach
                        
                </tbody>
        </table>
     
    </div>   
</body>
</html>



