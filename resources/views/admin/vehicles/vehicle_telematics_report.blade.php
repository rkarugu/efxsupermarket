<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Location Data</title>
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
  

    <div class="">

        <table width="100%">         
            <tr >
                <td colspan="6"  style="font-weight: 700; font-size: 16px;text-align: center !important">{{ getAllSettings()['COMPANY_NAME'] }}
             </td>
            <tr >
                <td colspan="6"  style="font-weight: 700; font-size: 16px;text-align: center !important"> {{$vehicle->license_plate_number ?? ''}} Location Data  
             </td>
            </tr>    
            <tr >
                <td colspan="6"  style="font-weight: 700; font-size: 16px;text-align: center !important">Driver: {{$vehicle->driver?->name}}  
             </td>
            </tr>          
            <tr>
                <td colspan="6"  style="font-weight: 500; font-size: 14px;text-align: center !important">Date Range: {{$start}} to {{ $end}} </td>
            </tr>
          
        </table>

        <table class="table table-bordered table-hover" id="create_datatable">
            <thead>
            <tr>
                <th>Time Stamp</th>
                <th>Latitude</th>
                <th>Longitude</th>
                <th>Speed</th>
                <th>Fuel Level</th>
                <th>Mileage</th>
            </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                <tr>
                    <td>{{$row['time']}}</td>
                    <td>{{$row['latitude']}}</td>
                    <td>{{$row['longitude']}}</td>
                    <td>{{$row['speed']}}</td>
                    <td>{{$row['fuel_level']}}</td>
                    <td>{{$row['mileage']}}</td>
                </tr>
                    
                @endforeach
            </tbody>
            <tfoot>
         
            </tfoot>
        </table>   
    </div>   
</body>
</html>



