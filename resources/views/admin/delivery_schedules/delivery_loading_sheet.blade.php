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

        .invoice-box * {
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
                <h2 style="font-size:16px !important;">DELIVERY SCHEDULE</h2>
            </th>
        </tr>
        <tr class="top">
            <td colspan="2" style="text-align: center;font-weight: bold">{{Carbon\Carbon::now()->format('l, M, d Y') }} </td>
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
    <table style="text-align: left !important; margin-bottom: 20px;">
        <tbody>

        <tr class="tops">
            <th style="text-align: left;">
                Delivery No: {{@$schedule->deliveryNumber}}
            </th>

            <th style="text-align: left">
                Route: {{@$schedule->route->route_name}}
            </th>
            <th style="text-align: right;">
                Salesman: {{ @$salesman->name }}
            </th>
        </tr>
        <tr class="top">
            <th style="text-align: left">
                Shift: {{ $schedule->shift->shiftId ?? '___' }}
            </th>

            <th style="text-align: left">
                Vehicle: {{$schedule->vehicle->name ?? '___' }} {{ $schedule->vehicle->license_plate ?? '____' }}
            </th>
            <th style="text-align: right;">
                Driver: {{$schedule->driver->name ?? '__' }}
            </th>
        </tr>
        </tbody>
    </table>
    <table>
        <tbody>
        <tr class="heading">
            <td style="width:50%"> Product</td>
            <td style="width:20%;">Total Quantity</td>
            <td style="width:30%;">Tonnage</td>
        </tr>
        @foreach ($items as $item)
            <tr class="heading">
                @php
                    $product = App\Model\WaInventoryItem::find($item->wa_inventory_item_id);
                @endphp
                <td style="width:50%">  {{@$product->title}} </td>
                <td style="width:20%;float:left;">{{@$item->total_quantity}}</td>
                <td style="width:30%;float:left;">{{@$item->tonnage}} Kg</td>
            </tr>

        @endforeach
        </tbody>
    </table>
    <table>
        <tbody>
        <tr>
            <td colspan="1">{{$items->count() }} Records</td>
            <td style="text-align: right;font-weight: bold;" colspan="1" >Total Tonnage:</td>
            <td colspan="1" style="font-weight: bold;">{{ $schedule->tonnage }} Kg</td>
        </tr>

        </tbody>
    </table>
</div>
</body>
</html>