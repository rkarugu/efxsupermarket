<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bootstrap 4 Responsive Datatable and Export to PDF, CSV</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">
</head>
<body>
   

    <div class="row">
       
        <div class="col-sm-5"></div>
        <div class="col-sm-5">Gross Profit Summary Report</div>

    </div>
    <br><br>
       <table class="table table-bordered">
        <thead >
        <tr>
            <th colspan="6"></th>
        </tr>
        
        <tr>
            
            <th>Category</th>
            
            <th>Total Sale</th>
            <th>Total Cost</th>
            <th>Gross Profit</th>

        </tr>
        </thead>
        <tbody>
            @php 
                $grandtotal = 0;
                $grandprice = 0;
                $grandprofit =0;
            @endphp
            @foreach($data as $key => $val)
            <tr>
                
                <th>{{$val->category_description}}</th>
               @php
                $total_cost = $val->standard_cost_sum;
                $grandtotal  += $total_cost;
                $grandprice  += $val->price_sum;
                $grandprofit += $val->price_sum + $total_cost;
               @endphp
                <th>{{number_format($val->price_sum,2)}}</th>
                <th>{{number_format(abs($total_cost),2)}}</th>
                <th>{{number_format($val->price_sum + $total_cost,2)}}</th>
            </tr>
            @endforeach
            <tr>
                <th></th>
              
                <th>{{number_format($grandprice,2)}}</th>
                <th>{{number_format(abs($grandtotal),2)}}</th>
                <th>{{number_format($grandprofit,2)}}</th>
            </tr>

        </tbody>
      
        </table>       
       
</body>
</html>