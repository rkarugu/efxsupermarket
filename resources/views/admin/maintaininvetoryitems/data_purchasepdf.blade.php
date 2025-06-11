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

        <table width="100">
            <tr >
                <td colspan="14" align="center" style="font-weight: 700; font-size: 16px;text-align: center !important">{!!$title!!}
             </td>
            </tr>
              <tr>
               <td colspan="14" align="center" style="font-weight: 700; font-size: 15px;text-align: center !important">{{ $supplierexcel }}</td>
            </tr>
              <tr>
                <td colspan="14" align="center" style="font-weight: 500; font-size: 14px;text-align: center !important">Date Range: {{$from}} to {{ $to}} </td>
            </tr>
        </table> 

        <table>
        <tbody>
                <tr class="heading">
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">ITEM CODE</td>
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">ITEM NAME</td>
                    @foreach ($locations as $location)
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">{{ $location->location_name }}</td>
                     @endforeach
                    <td style="font-weight: 500; font-size: 11;text-align: center !important">TOTALS</td>
                </tr>
                @foreach ($data as $val)
                    <tr class="item">
                        <td>{{ $val->stock_id_code}}</td>
                        <td>{{ $val->title}}</td>
                        <?php $total = 0; ?>
                        @foreach ($locations as $local)
                        <?php
                        $key ='total_item_sales_'.$local->id;
                        $v =$val[$key];
                        $total += $v;
                        ?>
                        <td>{{ $v }}</td>

                   @endforeach
                    <td><STRONG>{{ $total }}</STRONG></td>
                          
                    </tr>
                @endforeach
                </tbody>
        </table>
     
    </div>   
</body>
</html>