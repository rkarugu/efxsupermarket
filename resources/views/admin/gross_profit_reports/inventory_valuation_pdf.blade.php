<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Gross Profit Summary Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">
</head>
<body>
    <div class="row">
    <p style="font-size: 12px;"><b>Gross Profit Summary Report</b></p>        
    </div>
    <div class="row">
    <p style="font-size: 12px !important;"><b>Date From:-</b>{{$start_date}}   <b>Date To:-</b>{{$end_date}}</p>
    </div>
    <div class="row">
    <p style="font-size: 12px;"><b>Location:-</b>{{$location_name}}</p>
    </div>
    <div class="row">
    <p style="font-size: 12px;"><b>Doc Type :- </b><?php if($invoice =='CS-'){echo "Cash Sales";}elseif($invoice =='INV-'){echo "Invoice";}else{echo "Return";}?></p>
     </div>
    <br>
       <table class="table " style="">
        <thead >
        <tr>
            <th colspan="6"></th>
        </tr>
        
        <tr style="font-size: 13px;">
            
            <th >Category</th>
            
            <th >Total Sale</th>
            <th>Total Cost</th>
            <th>Gross Profit</th>

        </tr>
        </thead>
        <tbody style="font-size: 10px;">
            @php 
                $grandtotal = 0;
                $grandprice = 0;
                $grandprofit =0;
            @endphp
            @foreach($data as $key => $val)
            <tr>
                
                <td >{{$val->category_description}}</td>
               @php
                $total_cost = $val->standard_cost_sum;
                $grandtotal  += $total_cost;
                $grandprice  += $val->price_sum;
                $grandprofit += $val->price_sum + $total_cost;
               @endphp
                <td >{{number_format($val->price_sum,2)}}</td>
                <td>{{number_format(abs($total_cost),2)}}</td>
                <td>{{number_format($val->price_sum + $total_cost,2)}}</td>
            </tr>
            @endforeach
            <tr>
                <td></td>
              
                <td>{{number_format($grandprice,2)}}</td>
                <td>{{number_format(abs($grandtotal),2)}}</td>
                <td>{{number_format($grandprofit,2)}}</td>
            </tr>

        </tbody>
      
        </table>       
       
</body>
</html>