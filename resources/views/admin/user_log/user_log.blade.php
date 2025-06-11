<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <title>User Logs</title>

      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="{{asset('public/css/pdf.css')}}">

   </head>
   <body> 
       <div class="invoice-box">
         <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="3" style="text-align: center">
                    <h4 style="text-align: center">User Logs
                        <hr>    
                    </h4>                        
               </td>
            </tr>
        

            <tr class="details">
               <td><b>From</b>: {{getDateFormatted(request()->date)}}</td>
               <td><b>To</b>: {{getDateFormatted(request()->todate)}}</td>
               <td><b>Total Record</b>: {{count($data)}}</td>
           </tr>
         </table>
         <br>
         <table cellpadding="0" cellspacing="0" style="border: 1px solid #ddd;font-size:14px">
           
                    <tr class="heading nnewHHad" >
                       <th width="4%">S.No.</th>
                                       
                                        <th width="8%"  >user_name</th>
                                        <th width="10%"  >user_ip</th>
                                        <th width="15%"  >User agent</th>
                                         <th width="12%"  >created_at</th>
                    </tr>
                                   @if(isset($data) && !empty($data))
                                        @php $b = 1;@endphp
                                        @foreach($data as $list)
                                         
                                            <tr class="details">
                                                <td>{!! $b !!}</td>
                                               
                                               <td>{!! ucfirst($list->user_name) !!}</td>    
                                               <td>{!! $list->user_ip  !!}</td>
                                               <td>{!! $list->user_agent!!}</td>
                                              <td>{!! $list->created_at  !!}</td>
                                            </tr>
                                          @php $b++; @endphp
                                        @endforeach
                                    @endif
                         
         </table>
        </div>
  
     </body>
  </html>