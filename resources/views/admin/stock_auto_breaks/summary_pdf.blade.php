<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Break Summary</title>
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
                <td style="font-weight: 700; font-size: 16px;text-align: left !important">Stock Break Summary Report
             </td>
             <td  style="font-weight: 700; font-size: 16px;text-align: left !important">{{$branch}}</td>
            </tr>             
            <tr>
                <td colspan="2" style="font-weight: 500; font-size: 14px;text-align: right !important">Date: {{\Carbon\Carbon::parse($start)->toDateString() .' - '. \Carbon\Carbon::parse($end)->toDateString()}} </td>
            </tr>
          
        </table>
        <table class="table table-bordered table-hover" id="positive_variance_table" width="100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Mother Bin</th>
                    <th>Mother Item</th>
                    <th>Mother Qty</th>
                    <th>Child Bin</th>
                    <th>Child Item</th>
                    <th>Child Qty</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">Auto Breaks</td>
                </tr>
                @foreach ($records as $break)
                    <tr>
                        <th>{{$loop->index+1}}</th>
                        <td class="data" style="text-align: left !important;">{{$break->created_at}}</td>
                        <td class="data">{{$break->mother_bin}}</td>
                        <td class="data">{{$break->mother_code.' - '. $break->mother_name}}</td>
                        <td class="data" style="text-align: center;">{{$break->mother_quantity}}</td>
                        <td class="data">{{$break->child_bin}}</td>
                        <td class="data">{{$break->child_code.' - '.$break->child_name }}</td>
                        <td class="data" style="text-align: center;">{{$break->child_quantity}}</td>
                    </tr>
                @endforeach
                <tr>
                    <td class="t-head" colspan="7" style="text-align: left !important;">Manual Breaks</td>
                </tr>
                @foreach ($manualBreaks as $break)
                <tr>
                    <th>{{$loop->index+1}}</th>
                    <td class="data" style="text-align: left !important;">{{$break->created_at}}</td>
                    <td class="data">{{$break->mother_bin_location}}</td>
                    <td class="data">{{$break->mother_code.' - '. $break->mother_name}}</td>
                    <td class="data">{{$break->mother_quantity}}</td>
                    <td class="data">{{$break->child_bin_location}}</td>
                    <td class="data">{{$break->child_code.' - '.$break->child_name }}</td>
                    <td class="data" style="text-align: center;">{{$break->child_quantity}}</td>
                </tr>
            @endforeach
                
            </tbody>
        </table>

    </div>   
</body>
</html>



