<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operating Assigement Cost</title>
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
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
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
               <!--  <tr class="top">
                    <th colspan="1">
                        <h2>{{getAllSettings()['COMPANY_NAME']}}</h2>
                    </th>
                </tr> -->
               <!--  <tr class="top">
                    <td colspan="1" style="    text-align: center;">P.O. BOX 910 MOLO.TEL 0736776306, 0718743015, 0723958887, 0708203710, 0705242835</td>
                   
                </tr>
               -->
             <!--    <tr class="top">
                    <th  colspan="1">
                        AGE DEBTORS LIST FOR THE DATE : {{request()->date ? date('d/m/Y',strtotime(request()->date)) : date('d/m/Y')}}
                    </th>
                </tr> -->
            </tbody>        
        </table>
        <br>
        <br>
        <br>
        <table>
            <tbody>
                <tr class="heading">
                    <th width="5%">S.No.</th>
                        <th scope="col">Vehicle</th>
                        <th scope="col">Service Cost</th>
                        <th scope="col">Fuel Cost</th>
                        <th scope="col">Total Cost</th>
                        <th scope="col">Cost/Meter</th>



                       
                </tr>
                
                <tr>
                    <td colspan="9"  style="  border-bottom: 2px dashed #cecece;"></td>
                </tr>
                <?php $b = 1; $grandtotal=0; $sum=0; $cost=0;?> 
                @if(isset($operatingcostsummary) && !empty($operatingcostsummary))
                @foreach($operatingcostsummary as $operatingcostsummary) 
                <?php
                $sum  = isset($operatingcostsummary->ServiceCost->total)?number_format((float)$operatingcostsummary->ServiceCost->total, 2, '.', '') + number_format((float)$operatingcostsummary->FuelCost->total, 2, '.', ''):'';

                 $cost = ((int)$sum/1000);
                ?>

                    <tr class="item">
                        <td>{!! $b !!}</td>
                        <th>{{$operatingcostsummary->license_plate}}</th>
                        <td>{!! isset($operatingcostsummary->ServiceCost->total) ? $operatingcostsummary->ServiceCost->total:'N/A' !!}</td>
                        <td>{!! isset($operatingcostsummary->FuelCost->total )? $operatingcostsummary->FuelCost->total:'N/A' !!}</td>
                        <td>{{$sum}}</td>
                        <td>{{$cost}}</td>


                    </tr>
                   <?php $b++; ?>
                @endforeach
                @endif
                
            </tbody>
        </table>
    </div>   
</body>
</html>