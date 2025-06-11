<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Stock Variance Report</title>
</head>
<body>
    <table style="font-family: arial, sans-serif; border-collapse: collapse; border: 1px solid;">

<tr>
    <th style="border-bottom: 1px solid;padding-right:8px">Date From</th>
    <th colspan="2" style="border-bottom: 1px solid;">{{$data->start_date}}</th>
    <th colspan="4" style="border-bottom: 1px solid;"></th>
    <th style="border-bottom: 1px solid; padding-right:8px">Date To</th>
    <th colspan="4" style="border-bottom: 1px solid;">{{$data->end_date}}</th>
</tr>
<tr>
    <th style="border-bottom: 1px solid;padding-right:8px">Store Location</th>
    <th colspan="11" style="border-bottom: 1px solid;text-align:left">{{$data->location}}</th>
</tr>
        <tr>
            <th>Category/Items</th>
            <th></th>
            <th>UOM</th>
            <th>Opening Stock</th>
            <th>Purchases</th>
            <th>Transfers</th>
            <th>Issues</th>
            <th>Total</th>
            <th>Closing Stock</th>
            <th>Potential Sales</th>
            <th>Actual Sales</th>
            <th>Variance</th>
        </tr>
        @php 
                $grandtotalcost = 0;
                $ostotal = 0;
                $ptotal = 0;
                $ttotal = 0;
                $itotal = 0;
                $stotal = 0;
                $totalOftotal = 0;    
                $closingstock = 0;
                $potentialstock = 0;
                $variance = 0;        
            @endphp
        @foreach ($data->items as $item)
        <tr>
            <th style="border-bottom: 1px solid;
            text-align: center;">
                {{$item->category_code}}
            </th>
            <th style="border-bottom: 1px solid;
            text-align: center;">
                {{$item->category_description}}
            </th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
            @foreach ($item->items as $value)
                <tr>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{$value->category_code}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{$value->category_name}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{$value->uom}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->opening_stock,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->purchase,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->transfers,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->issues,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->total,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->closing_stocks,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->potential_stocks,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->actual_sales,2)}}</td>
                    <td  style="border-bottom: 1px solid;
            text-align: center;">{{number_format($value->variance,2)}}</td>
                </tr>

                @php
                    $ostotal += $value->opening_stock; 
                    $ptotal += $value->purchase;
                    $ttotal += $value->transfers; 
                    $itotal += $value->issues; 
                    $stotal += $value->actual_sales;                       
                    $totalOftotal += $value->total;                                                                              
                    $closingstock += $value->closing_stocks;
                    $potentialstock += $value->potential_stocks;
                    $variance += $value->variance;
                @endphp
            @endforeach
            
        @endforeach
        <tr>
            <th>Grand Total </th>
            <th></th>
            <th></th>
            <th>{{number_format(($ostotal),2)}}</th>
            <th>{{number_format(($ptotal),2)}}</th>
            <th>{{number_format($ttotal,2)}}</th>
            <th>{{number_format($itotal,2)}}</th>
            <th>{{number_format($totalOftotal,2)}}</th>
            <th>{{number_format($closingstock,2)}}</th>
            <th>{{number_format($potentialstock,2)}}</th>
            <th>{{number_format($stotal,2)}}</th>
            <th>{{number_format($variance,2)}}</th>               
        </tr>
    </table>
</body>
</html>