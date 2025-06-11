<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatch Sheet Summary</title>
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

        .invoice-box table tr.item td {
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

        .horizontal_dotted_line {
            text-align: left !important;
        }
    </style>
</head>
<body>

<div class="invoice-box">
    <table style="text-align: center;">
        <tbody>
        <tr class="top">
            <th colspan="2">
                <h2 style="font-size:18px !important;font-weight: bold;">{{@$company->description}}.</h2>
            </th>
        </tr>
        <tr class="top">
            <td colspan="2" style="text-align: center;">{{@$address->description}}, {{ @$location->description }}</td>
        </tr>
        <tr class="top">
            <th colspan="2">
                <h2>{{ @$shift->shift_id }}  Dispatch List</h2>
            </th>
        </tr>
        <tr class="top">
            <td colspan="2" style="text-align: center;font-weight: bold;">{{Carbon\Carbon::now()->format('l, M, d Y') }} </td>
        </tr>


        <tr class="top">
            <th style="text-align:left;vertical-align: top;width:67%">
                <table class="table">
                    <th style="text-align:left">
                    </th>
                    <tr style="text-align:left">

                    </tr>
                    <tr style="text-align:left">

                    </tr>
                </table>
            </th>

        </tr>
        </tbody>
    </table>


    <br>

    <table>
        <thead>
        <tr class="heading">
            <th style="text-align:left">#</th>
            <th style="text-align:left">Product</th>
            <th style="text-align:left">Store Location</th>
            <th style="text-align:left">Total Quantity</th> 
        </tr>
        </thead>
        <tbody>
        @foreach ($items as $key=>$item)
            <tr class="item">
                
                <td style="text-align:left;">{{ ++$key }}</td>
                <td style="text-align:left;">{{ $item->item_name ?? '' }}</td>
                <td style="text-align:left;">{{ $item->store_location ?? '' }}</td>
                <td style="text-align:left;">{{ $item->total_quantity ?? '' }}</td> 
            </tr>
        @endforeach

       

        
        </tbody>
    </table>

</div>
</body>
</html>