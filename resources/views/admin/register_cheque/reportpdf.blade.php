@php
    $getLoggeduserProfile = getLoggeduserProfile();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$title}}</title>
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
                    <th colspan="3">
                        <h2 style="font-size:18px !important">{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
              
                <tr class="top">
                    <th  colspan="3" style="width: 100%;text-align:left">Cheque Report</th>
                </tr>
                <tr class="top">
                    <!-- <th  colspan="1" style="width: 50%">
                        VAT NO:
                    </th> -->
                    <th colspan="2" style="width: 33%;text-align:left">DATE: {{date('d-M-Y',strtotime(request()->from))}} TO {{date('d-M-Y',strtotime(request()->to))}}</th>
                    <th colspan="1" style="width: 33%;text-align:right">
                       Status : {{strtoupper(request()->status)}}
                    </th>

                </tr>
            
            </tbody>        
        </table>
        <table>
            <thead>
                <tr class="heading">
                    <th>Date received</th>
                    <th>Salesman</th>
                    <th>Cheque no</th>
                    <th>Drawers name</th>
                    <th>Drawers bank</th>
                    <th>Cheque date</th>
                    <th>Bank Deposited</th>
                    <th>Date Deposited</th>
                    <th>Status</th> 
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @php
                $total = 0;
            @endphp
              @foreach($data as $key => $item)
              <tr>
                  <td>{{$item->date_received}}</td>
                  <td>{{@$item->salesman->location_name}}</td>
                  <td>{{$item->cheque_no}}</td>
                  <td>{{$item->drawers_name}}</td>
                  <td>{{$item->drawers_bank}}</td>
                  
                  <td>{{$item->cheque_date}}</td>
                  <td>{{$item->bank_deposited}}</td>                         
                 
                  <td>{{$item->deposited_date}}</td>
                  <td>{{@$item->status}}</td>
                  <td>{{manageAmountFormat($item->amount)}}</td>
                  @php
                  $total += $item->amount;
              @endphp
                 
              </tr>
              @endforeach
              <tr>
                <th colspan="10" style="text-align: right">Grand Total : {{manageAmountFormat($total)}}</th>
            </tr>
                </tbody>
        </table>
      
    </div>   
</body>
</html>