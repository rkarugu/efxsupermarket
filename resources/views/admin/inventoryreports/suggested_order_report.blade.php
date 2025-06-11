<html>
<title>Suggested Order Report</title>

<head> 
<style type="text/css">
.underline{
  text-decoration: underline;
  }

.item_table td, .data_detail th, tr{
  border-right: 1px solid;
}
.align_float_right{
  text-align:  right;
}

.align_float_center{
  text-align:  center;
}
table {
    font-family: arial, sans-serif;
    border-collapse: collapse;
    width: 100%;
}

td, th {
    padding: 8px;
}

</style>
</head>
<body>

<table style="width:100%; border:1px solid black; margin-top:40px;">
@if(count($data) > 0)
<?php
//echo "<pre>"; print_r($data); die;
?>
    <thead>
        <tr>
            <th colspan="2" style="text-align: left;">{{$restuarantname}}</th>
            <th colspan="4" style="text-align: left;">{{ date('d M,Y H:i A') }}</th>
        </tr>
        <tr style="border:1px solid #ddd; margin-bottom: 20px;">
            <th colspan="6" style="text-align: left;">Suggested Order Report</th>
        </tr>
        <tr class="item_table">
            <th style="text-align: left;">Category/Items</th>
            <th></th>
            <th style="text-align: left;">Description</th>
            <th style="text-align: left;">Minimum Stock</th>
            <th style="text-align: left;">Available Stock</th>
            <th style="text-align: right;">Re-Order Qty</th>
        </tr>
        </thead>
        <tbody>
             @php 
                $grandtotalcost = 0;
             @endphp
            @foreach($data as $key => $val)
            <tr class="item_table">
                <th  style="text-align: left;">{{$val['category_code']}}</th>
                <th style="text-align: left;">{{$val['category_description']}}</th>
                <th colspan="4"></th>
            </tr>
             @php 
             $totalqty = 0;
             $totalcost = 0;
             @endphp
                @foreach($val['getinventoryitemshowroomstock'] as $key => $value)
             @php 
            $totalqty += $storeBiseQty[$value['getstockmoves']['id']];
             @endphp

             @if(($value['minimum_order_quantity']-$storeBiseQty[$value['getstockmoves']['id']]) > 0)
	             @php 
	             $totalcost += $value['minimum_order_quantity']-$storeBiseQty[$value['getstockmoves']['id']];
	             @endphp			 
			 @endif

             <?php // echo "<pre>"; print_r($value); die; ?>
                <tr class="item_table">
                    <td  style="text-align: left;">{{$value['stock_id_code']}}</td>
                    <td style="text-align: left;">{{$value['title']}}</td>
                    <td style="text-align: left;">{{$value['description']}}</td>
                    <td style="text-align: left;">{{$value['minimum_order_quantity']}}</td>
                    <td style="text-align: left;">{{$storeBiseQty[$value['getstockmoves']['id']]}}</td>
             @if(($value['minimum_order_quantity']-$storeBiseQty[$value['getstockmoves']['id']]) > 0)
                    <td style="text-align: right;">{{$value['minimum_order_quantity']-$storeBiseQty[$value['getstockmoves']['id']]}} </td>
             @else
                    <td style="text-align: right;">{{ '0' }} </td>             
             @endif
                </tr>
                @endforeach
             @php 
             $grandtotalcost += $totalcost;
             @endphp
                <tr>
                <th colspan="2" style="text-align: left;">Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; for  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {{$val['category_code']}}  - {{$val['category_description']}}</th>
                <th colspan="3"></th>
                <th style="text-align: right;">{{number_format($totalcost,2)}}</th>
            </tr>
                <tr>
                    <th colspan="6"></th>
                </tr>

            @endforeach
            <tr>
                <th colspan="6"></th>
            </tr>            

            <tr>
                <th colspan="2" style="text-align: left;">Grand Total &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </th>
                <th colspan="3"></th>
                <th style="text-align: right;">{{number_format($grandtotalcost,2)}}</th>
            </tr>
        </tbody>
 @else       
<center><h4>Data not found.</h4></center>
        
@endif        
        
</table>      

</body>
</html>   
