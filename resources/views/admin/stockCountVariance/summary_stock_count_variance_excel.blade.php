<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock Count Variation</title>
    <style>
        
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #000;
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
                <td colspan="6"  style="font-weight: 700; font-size: 16px;text-align: left !important">{{ getAllSettings()['COMPANY_NAME'] }}
             </td>
            <tr >
                <td colspan="6"  style="font-weight: 700; font-size: 16px;text-align: left !important"> Summary Stock Count Variation Report
             </td>
            </tr>             
            <tr>
                <td colspan="6"  style="font-weight: 500; font-size: 14px;text-align: left !important">Date : {{$start_date}} to {{ $end_date}} </td>
            </tr>
          
        </table>

        <table class="table table-bordered table-hover" id="create_datatable"  width="100%">
            <thead>
            <tr>
                <th>#</th>
                {{-- <th>Stock Id Code</th> --}}
                <th width="30%">Title</th>
                <th>Bin</th>
                @foreach ($uniqueDatesArray as $date)
                <th style="text-align: center;">{{$date}}</th>
                @endforeach
            
            </tr>
           
            </thead>
             <tbody>
                @php
                    $numbering = 1;
                    $totalColumns = 3 + count($uniqueDatesArray);

                @endphp
                @foreach ($categories as $category)
                <tr>
                    <td class="t-head" colspan="{{ $totalColumns }}">{{$category->category_description}}</td>
                </tr>
                @foreach ($data as $row)
                @if($category->id == $row[0]->category_id)

                <tr>
                    <th class="t-head">{{$numbering}}</th>
                    <td class="data" width="30%">{{$row[0]->getInventoryItemDetail->title}}</td>
                    <td class="data">{{$row[0]->getUomDetail->title}}</td>
                    @foreach ($uniqueDatesArray as $date)
                    <td class="data" style="text-align: center;">
                        @php
                            $found = false; 
                        @endphp
                        @foreach ($row as $count)
                            @if ($count->created_at->format('Y-m-d') == $date)
                                {{ $count->variation ?? 'NCE'  }}
                                @php
                                    $found = true;
                                    break; 
                                @endphp

                            @endif
                        @endforeach
                        @if (!$found)
                            NCE
                        @endif
                    </td>
                @endforeach

                </tr>
                @php
                    $numbering ++;
                @endphp
                @endif
                    
                @endforeach
                
                    
                @endforeach
              
            

            </tbody> 
          
            <tfoot>
        
            </tfoot>
        </table>
    </div>   
</body>
</html>



