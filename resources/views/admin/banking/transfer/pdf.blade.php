<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <title>{{$title}}</title>

      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="{{asset('public/css/pdf.css')}}">

   </head>
   <body> 
       <div class="invoice-box">
         <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="3" style="text-align: center">
                    <h4 style="text-align: center">{{$heading}}
                        <hr>    
                    </h4>                        
               </td>
            </tr>
        

            <tr class="details">
               <td><b>From</b>: {{getDateFormatted(request()->from)}}</td>
               <td><b>To</b>: {{getDateFormatted(request()->to)}}</td>
               <td><b>Total Record</b>: {{count($data)}}</td>
           </tr>
         </table>
         <br>
         <table cellpadding="0" cellspacing="0" style="border: 1px solid #ddd;font-size:14px">
            <tr class="heading " >
                <th>S.No</th>
                <th>Transfer From</th>
                <th>Transfer to</th>
                <th>Date</th>
                <th>Amount</th>        
            </tr>
             @foreach ($data as $item)
                 <tr class="details "  > 
                     <td style="width:50px ">{{$loop->iteration}}</td>
                     <td>{{$item->from_code}}</td>
                     <td>{{$item->to_code}}</td>
                     <td>{{getDateFormatted($item->date)}}</td>
                     <td>{{manageAmountFormat($item->amount)}}</td>
                 </tr>
               
             @endforeach
         </table>
        </div>
  
     </body>
  </html>