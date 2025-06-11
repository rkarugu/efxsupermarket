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
                <td  style="text-align: center">
                    <h4 style="text-align: center">{{$heading}}
                        <hr>    
                    </h4>                        
               </td>
            </tr>
        

            <tr class="details">
               <td><b>Total Record</b>: {{count($data)}}</td>
           </tr>
         </table>
         <br>
         <table cellpadding="0" cellspacing="0" style="border: 1px solid #ddd;font-size:14px">
            <tr class="heading tddHead" >
                <th   >Asset</th>
                <th >Description</th>
                <th>Serial No Purchased</th>
                <th>Cost B/Fwd</th>
                <th>Depn B/Fwd</th>
                <th>Additions</th>
                <th >Depreciation</th>
                <th >Cost C/Fwd</th>                
                <th >Depn C/Fwd</th>                
                <th >Net Book Value</th>                
            </tr>
             @foreach ($data as $item)
                 <tr class="details "  > 
                     <td   >{{$item->asset_description_short}}</td>
                     <td >{{$item->task_description}}</td>
                     <td>{{$item->serial_number}}</td>
                     <td></td>
                     <td></td>
                     <td></td>
                     <td >{{$item->depreciation_rate}}</td>
                     <td ></td>                
                     <td ></td>                
                     <td ></td>       
                 </tr>
                         
             @endforeach
         </table>
        </div>
  
     </body>
  </html>