<?php //echo "<pre>"; print_r($data); die; ?>
<table style="border:1px solid #ddd; width: 100%; margin-top:40px;">
    @if($summrytype=="2")        
    <thead>
        <tr>
            <th colspan="5" style="text-align:left">{{$restuarantname}}</th>
        </tr>
        <tr style="border:1px solid #ddd;text-align:left">
            <th colspan="5" style="text-align:left">Inventory Valuation for Categories between and at {{ ($locationname!="") ? $locationname : "All" }}</th>
        </tr>
        <tr>
            <th>Category/Items</th>
            <th></th>
            <th>Quantity</th>
            <th>Standard Cost</th>
            <th>Total Value</th>
        </tr>
        </thead>
        <tbody>
             @php 
                $grandtotalcost = 0;
             @endphp
            @foreach($data as $key => $val)
            <tr>
                <th scope="row" style="border-bottom:1px solid black !important;border-top:1px solid black !important;">{{$val['category_code']}}</th>
                <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;">{{$val['category_description']}}</th>
                <th colspan="3" style="border-bottom:1px solid black !important;border-top:1px solid black !important;">Bin Location : {{@$val['getinventoryitems'][0]['unitofmeasures']['title']}}</th>
            </tr>
             @php 
             $totalqty = 0;
             $totalcost = 0;
             @endphp
                @foreach($val['getinventoryitems'] as $key => $value)
             @php 
            $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
             $totalcost += $value['selling_price'] * $value['getstockmoves']['qauntity'];
             @endphp

                <tr>
                    <td scope="row">{{$value['stock_id_code']}}</td>
                    <td>{{$value['title']}}</td>
                    <td>{{$storeBiseQty[$value['getstockmoves']['id']]}}</td>
                    <td>{{$value['selling_price']}}</td>
                    <td>{{ number_format($value['selling_price'] * $storeBiseQty[$value['getstockmoves']['id']],2) }}</td>
                </tr>
                @endforeach
             @php 
             $grandtotalcost += $totalcost;
             @endphp
                <tr>
                <th colspan="2">Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; for  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$val['category_code']}}  - {{$val['category_description']}}</th>
                <th colspan="2"></th>
                <th>{{number_format($totalcost,2)}}</th>
            </tr>
                <tr>
                    <th colspan="5"></th>
                </tr>

            @endforeach
            <tr>
                <th colspan="5"></th>
            </tr>            

            <tr>
                <th colspan="2">Grand Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </th>
                <th colspan="2"></th>
                <th>{{number_format($grandtotalcost,2)}}</th>
            </tr>
        </tbody>
        @else
    <thead>
        <tr>
            <th colspan="6" style="text-align:left">{{$restuarantname}}</th>
        </tr>
        <tr style="border:1px solid #ddd;text-align:left">
            <th colspan="6" style="text-align:left">Inventory Valuation for Categories between and at {{ ($locationname!="") ? $locationname : "All" }}</th>
        </tr>
        <tr>
            <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;"></th>
            <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;"></th>
            <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;">Category</th>
            <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;">Quantity</th>
            <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;">Bin Location</th>
            <th style="border-bottom:1px solid black !important;border-top:1px solid black !important;">Total Value</th>
        </tr>
        </thead>
        <tbody>
            @php 
                $grandtotal = 0;
            @endphp
            @foreach($data as $key => $val)
            <tr>
                <th>{{$val['category_code']}}</th>
                <th>-</th>
                <th>{{$val['category_description']}}</th>
                @php 
                    $totalqty = 0;
                    $totalcost = 0;
                @endphp
                    @foreach($val['getinventoryitems'] as $key => $value)
                        @php 
                        $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
                        $totalcost += $value['selling_price'] * $storeBiseQty[$value['getstockmoves']['id']];
                        @endphp
                    @endforeach
                <th>{{$totalqty}}</th>
                <th>{{@$val['getinventoryitems'][0]['unitofmeasures']['title']}}</th>
                <th>{{number_format($totalcost,2)}}</th>
            @php 
                $grandtotal += $totalcost;
            @endphp                
            </tr>
            @endforeach
            <tr>
                <th></th>
                <th colspan="4">Total Cost</th>
                <th>{{number_format($grandtotal,2)}}</th>
            </tr>

        </tbody>
        @endif
</table>        
                     

<style type="text/css">
    table{
        font-family: arial;
    }
</style>
