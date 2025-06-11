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
       
        .hr-with-text {
            position: relative;
            height: 2px;
            border: none;
            background-color: #000;
            margin: 20px 0;
        }
        /* .hr-with-text::after {
            content: "DRIVER";
            display: inline-block;
            position: absolute;
            top: 30%;
            left: 45%;
            transform: translate(-50%, -50%);
            padding: 0 10px;
            background-color: #fff;
        } */
        .hr-with-text2 {
            position: relative;
            height: 2px;
            border: none;
            background-color: #000;
            margin: 20px 0;
        }
        /* .hr-with-text2::after {
            content: "GATE MAN";
            display: inline-block;
            position: absolute;
            top: 30%;
            left: 45%;
            transform: translate(-50%, -50%);
            padding: 0 10px;
            background-color: #fff;
        } */
      
     

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
                <h2 style="font-size:21px !important;">GATE PASS</h2>
            </th>
        </tr>   


        </tbody>
    </table>
    <hr style="height: 2px !important; color:#000 !important;">

    <table style="text-align: left !important; margin-bottom: 20px;">
        <tbody style="text-align: left !important; font-size:15px;">
            <tr style="text-align: left !important; font-size:15px; padding-bottom:45px !important;">
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">DELIVERY NO</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : ". @$schedule->deliveryNumber}}</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">DATE</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : " . Carbon\Carbon::now()->toDateString()  }}</th>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr style="text-align: left !important; font-size:15px; margin-bottom:15px !important;">
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">ROUTE</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : " . @$schedule->route->route_name}}</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">TONNAGE</th>
                <th style="text-align: left !important; font-size:15px;  margin-bottom:45px !important; width:35% !important">{{ " : " . $schedule->shift->shift_tonnage }}</th>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr style="text-align: left !important; font-size:15px; margin-bottom:15px !important;">
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">DRIVER</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : " . $schedule->vehicle->driver->name ?? '__' }}</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">CTNS</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : " . $schedule->shift->shift_ctns }}</th>

            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr style="text-align: left !important; font-size:15px; margin-bottom:15px !important;">
                <th style="text-align: left !important; font-size:15px;  margin-bottom:45px !important; width:15% !important">VEHICLE</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : " . $schedule->vehicle->license_plate_number ?? '___' }}</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">DZNS</th>
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:35% !important">{{ " : " . $schedule->shift->shift_dzns }}</th>

            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr style="text-align: left !important; font-size:15px; margin-bottom:15px !important;">
                <th style="text-align: left !important; font-size:15px; margin-bottom:45px !important; width:15% !important">SALESMAN</th>
                <th style="text-align: left !important; font-size:15px;  margin-bottom:45px !important; width:35% !important">{{ " : " . @$salesman->name }}</th>
            

            </tr>
         
          
        </tbody>

    </table>
    <hr class="hr-with-text">
    <h2 style="text-align: left !important">DRIVER DETAILS</h2>
        <table>
            <tr>
                <th style="text-align: left !important; font-size:12px; width:15% !important;">SIGN :</th>
                <th style="text-align: left !important; font-size:12px; border-bottom:1px dotted black !important; width:30% !important;"></th>
                <th style="text-align: left !important; font-size:12px; width:15% !important;">DATE :</th>
                <th style="text-align: left !important; font-size:12px; border-bottom:1px dotted black !important; width:30% !important;"></th>
            </tr>
        </table>    
        <hr class="hr-with-text2">
        <h2 style="text-align: left !important">GATE MAN DETAILS</h2>
        <table>
        <tr>
            <th style="text-align: left !important; font-size:12px; width:30% !important;">BIZWIZ PHONE: </th>
            <th style="text-align: left !important; font-size:12px; width:30% !important;" >YES / NO</th>

        </tr>
        <tr>
            <td></td>
        </tr>
        <tr>
            <td></td>
        </tr>
        <tr style="margin: 20pxx !important;">
            <th style="text-align: left !important; font-size:12px; width:12% !important;">NAME:</th>
            <th style="text-align: left !important; font-size:12px; border-bottom:1px dotted black !important; width:26% !important;"></th>
            <th style="text-align: left !important; font-size:12px; width:12% !important;">SIGN:</th>
            <th style="text-align: left !important; font-size:12px;  border-bottom:1px dotted black !important; width:17% !important;"></th>
            <th style="text-align: left !important; font-size:12px;  width:12% !important;">DATE:</th>
            <th style="text-align: left !important; font-size:12px; border-bottom:1px dotted black !important; width:17% !important;"></th>
        </tr>

    </table>






</div>
</body>
</html>