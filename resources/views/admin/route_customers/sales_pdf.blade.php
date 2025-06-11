<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <title>Return List Pdf</title>

      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="{{asset('public/css/pdf.css')}}">

   </head>
   <?php $all_settings = getAllSettings(); 
    $getLoggeduserProfile = getLoggeduserProfile();
    ?>
   <body> 
       <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            
            <tr>
                <td colspan="2">{{ $all_settings['COMPANY_NAME']}}</td>
                
            </tr>
            <tr>
                <td colspan="2">Monthly Sales Summary</td>
                
            </tr>
            <tr>
                <td colspan="2"><b>From :{{$dateFrom}}</b></td>
                <td colspan="6"></td>
                <td colspan="3"><b>To :{{$dateTo}}</b></td>

            </tr>
        </table>
       
       <table cellpadding="0" cellspacing="0" style="border: 1px solid #ddd;font-size:14px">
                                     <thead>
                                        <tr class="heading nnewHHad">
                                            <th style="text-align:left;">Salesman</th>
                                            {{-- <th>Customer Name</th>
                                            <th>Phone No.</th>
                                            <th>Business</th>
                                            <th>Town</th>
                                            <th>Contact Person</th> --}}
                                            <th style="text-align:right;">Invoice Amount</th>
                                            <th style="text-align:right;">Return Amount</th>
                                            <th style="text-align:right;">Gross Sales Amount</th>

                                        </tr>
                                    </thead>
                                   <tbody>
                                        @php
                                         $total = 0;
                                         $grand_return = 0;
                                         $grand_gross = 0;
                                         @endphp
                                        @foreach ($lists as $item)   
                                            
                                            @php
                                                $total_sales= (isset($item->total_sales))?$item->total_sales:0;
                                                $total_return= (isset($item->total_return))?$item->total_return:0;

                                                $gross_total =  $total_sales -  $total_return;
                                                $grand_gross +=  $gross_total;
                                                 
                                                 
                                            @endphp    
                                       
                                                                              
                                            <tr class="details" >
                                                <td style="text-align:left;">{{@$item->toStoreDetail->location_name}}</td>
                                               {{--  <td>{{$item->name}}</td>
                                                <td>{{$item->phone}}</td>
                                                <td>{{$item->bussiness_name}}</td>
                                                <td>{{$item->town}}</td>
                                                <td>{{$item->contact_person}}</td> --}}
                                                <td style="text-align:right;">{{ manageAmountFormat(@$item->total_sales)}}</td>
                                                <td style="text-align:right;">{{ manageAmountFormat(@$item->total_return) }}</td>
                                                <td style="text-align:right;">{{ manageAmountFormat($gross_total)}}</td>

                                                @php
                                                $total += $item->total_sales;
                                                $grand_return += $item->total_return;
                                                
                                                @endphp
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                             <th >Total</th>
                                             <th style="text-align:right;">{{manageAmountFormat($total)}}</th>
                                             <th style="text-align:right;">{{manageAmountFormat($grand_return)}}</th>
                                             <th style="text-align:right;">{{manageAmountFormat($grand_gross)}}</th>
                                        </tr>
                                    </tfoot>
                                </table>
        </div>
  
     </body>
  </html>