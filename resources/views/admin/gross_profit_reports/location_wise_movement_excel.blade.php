<table class="table table-responsive" style="border:1px solid #ddd; margin-top:40px;">
@php
@endphp
@if(count($data) > 0)
      
    <thead>
        <tr>
            <th colspan="6">{{$restuarantname}}</th>
        </tr>
        <tr style="border:1px solid #ddd;">
            <th colspan="6">Inventory Valuation for Categories between and at {{ ($locationname!="") ? $locationname : "All" }}</th>
        </tr>
        <tr>
            <th>Category/Items</th>
            <th></th>
            <th>UOM</th>
            <th>Opening Stock</th>
            <th>Purchases</th>
            <th>Transfers</th>
            <th>Issues</th>
            <th>Sales</th>
            <th>Closing Balance</th>
        </tr>
        </thead>
        <tbody>
             @php 
                $grandtotalcost = 0;

                $ostotal = 0;
                $ptotal = 0;
                $ttotal = 0;
                $itotal = 0;
                $stotal = 0;
                $cbtotal = 0;


            @endphp
            @foreach($data as $key => $val)
            <tr>
                <th scope="row">{{$val['category_code']}}</th>
                <th>{{$val['category_description']}}</th>
                <th colspan="3"></th>
            </tr>
             @php 
             $totalqty = 0;
             $totalcost = 0;
             @endphp
                @foreach($val['getinventoryitems'] as $key => $value)
             @php 
            $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
             $totalcost += $value['getstockmoves']['price'] * $storeBiseQty[$value['getstockmoves']['id']];
             @endphp

                <tr>
                    <td scope="row">{{$value['stock_id_code']}}</td>
                    <td>{{$value['title']}}</td>
                    <td>{{$value['unitofmeasures']['title']}}</td>
                   
                    <td>{{abs($storeBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>{{abs($purchaseBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>{{abs($transfersBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>{{abs($issuesBiseQty[$value['getstockmoves']['id']])}}</td>
                    <td>{{$salesBiseQty[$value['getstockmoves']['id']]}}</td>
                    @php
                      $cs = abs($storeBiseQty[$value['getstockmoves']['id']] + $purchaseBiseQty[$value['getstockmoves']['id']] + ($transfersBiseQty[$value['getstockmoves']['id']] + $issuesBiseQty[$value['getstockmoves']['id']] ) + $salesBiseQty[$value['getstockmoves']['id']]);
                      $closing_stock = number_format($cs,2);
                    @endphp
                  
                  <td>{{ $closing_stock }}</td>
                  

                </tr>
                @php
                     $ostotal += $storeBiseQty[$value['getstockmoves']['id']];                

                     $ptotal += $purchaseBiseQty[$value['getstockmoves']['id']];
                     $ttotal += $transfersBiseQty[$value['getstockmoves']['id']]; 
                     $itotal += $issuesBiseQty[$value['getstockmoves']['id']]; 
                     $stotal += $salesBiseQty[$value['getstockmoves']['id']];      
                     $cbtotal += $storeBiseQty[$value['getstockmoves']['id']] + $purchaseBiseQty[$value['getstockmoves']['id']] + ($transfersBiseQty[$value['getstockmoves']['id']] + $issuesBiseQty[$value['getstockmoves']['id']] ) - $salesBiseQty[$value['getstockmoves']['id']]; 
                                                                                                                    
                     


                @endphp
                @endforeach
             @php 
             $grandtotalcost += $totalcost;
             @endphp
            @endforeach
            <tr>
                <th colspan="6"></th>
            </tr>            

            <tr>
                <th colspan="3">Grand Total </th>
                <th>{{number_format(abs($ostotal),2)}}</th>
                <th>{{number_format(abs($ptotal),2)}}</th>
                <th>{{number_format(abs($ttotal),2)}}</th>
                <th>{{number_format(abs($itotal),2)}}</th>
                <th>{{number_format(abs($stotal),2)}}</th>
                <th>{{number_format(abs($cbtotal),2)}}</th>
            </tr>
        </tbody>
       
@endif        
        
</table>  

<style type="text/css">
    table{
        font-family: arial;
    }
</style>