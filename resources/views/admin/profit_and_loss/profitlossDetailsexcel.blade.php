<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profit & Loss</title>
    <style>
    th
    {
        font-weight: 600
    }
    #bgGreen th
    {
        background-color: rgb(0, 199, 0);
        color:#fff;
        font-weight: bold;
    }
    .bgGreen th
    {
        background-color: #86ea86;
        color: #000;
        font-weight: 600;
    }    
    </style>
</head>
<body>  
    <table class="table table-responsive" style="border:1px solid #ddd; margin-top:40px;" id="create_datatable1">
        @if(count($lists) > 0)      
                <tr>
                    <th colspan="5">Profit & Loss Report</th>
                </tr>
                <tr id="bgGreen">
                    <th>Main Category</th>
                    <th>Sub-Category</th>
                    <th>Account Name</th>
                    <th>Account ID</th>
                    <th>Amount</th>
                </tr>
           
                     @php 
                        $grandtotalcost = 0;
                     $a = 0;
                     $b = 0;
                     $c = 0;
                     $d = 0;
                     @endphp
                     
                    @foreach($lists as $key => $val)
                    @if(count($val->getWaAccountGroup) > 0)
                    
                     @php 
                     $totalqty = 0;
                     $totalcost = 0;
                     @endphp
        
                    @foreach($val->getWaAccountGroup as $key => $groupacount)
                    @php
                        $dataChartAccount = $groupacount->getChartAccount->where('amount','!=',0);
                    @endphp
                    
                    @if(count($dataChartAccount)>0)
                        <tr>
                            <th>{{$val->section_name}}</th>
                            <th>{{$groupacount->group_name}}</th>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        @foreach($dataChartAccount as $key => $value)
                            @php 
                            $totalqty += 0;
                            $totalcost += abs($value->amount);
                            @endphp
                            @if(abs($value->amount) > 0)
                            <tr>
                                <td></td>
                                <td></td>
                                <td>{{$value->account_name}}</td>
                                <td>{{$value->account_code}}</td>                                
                                <td style="text-align:right">{{ number_format(abs(abs($value->amount)),2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif
                    @endforeach
                        
                    @if($val->section_name=="INCOME")
                     @php 
                     $a += $totalcost;
                     @endphp
                    @endif
                    
                    @if($val->section_name=="COST OF SALES")
                     @php 
                     $b += $totalcost;
                     @endphp
                    @endif
                    
                    @if($val->section_name=="OVERHEADS")
                     @php 
                     $d += $totalcost;
                     @endphp
                    @endif
                     @php 
                     $grandtotalcost += $totalcost;
                     @endphp
                    <tr class="bgGreen">
                        <th></th>
                        <th>Total</th>
                        <th></th>
                        <th></th>
                        <th style="text-align:right">{{number_format(abs($totalcost),2)}}</th>                        
                    </tr>
                        @php
                        $c = $a-$b;
                        @endphp
                        
                    @if($val->section_name=="COST OF SALES")
                    <tr style="background:#ddd;">
                        <th>GROSS PROFIT </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="text-align:right">{{number_format($c,2)}}</th>
                    </tr>
                    @endif
                    @if($val->section_name=="OVERHEADS")
                    @php
                        $e = $c-$d;
                    @endphp
                    <tr style="background:#ddd;">
                        <th >NET PROFIT </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th style="text-align:right">{{number_format($e,2)}}</th>
                    </tr>
                    @endif
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        </tr>
                    @endif
                    @endforeach
                    
              
                
        
            @endif
            </table>       
        
</body>
</html>
