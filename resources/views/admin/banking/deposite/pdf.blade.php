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
            <tr class="heading tddHead" >
                <th style="width:50px"  >S.No.</th>
                <th   >Account</th>
                <th >Branch</th>
                <th>Sub Total</th>
                <th>Tax</th>
                <th>Total</th>
                <th >Date</th>                
                <th >Receiver</th>                
            </tr>
             @foreach ($data as $item)
                 <tr class="details detailsParent tddHead"  > 
                     <td style="width:50px ">{{$loop->iteration}}</td>
                     <td>{{$item->account}}</td>
                     <td>{{$item->name}}</td>
                     <td>{{manageAmountFormat($item->sub_total)}}</td>
                     <td>
                        @if ($item->tax_check != 'Out Of Scope of Tax')
                            {{manageAmountFormat($item->total - $item->sub_total)}}
                        @else
                        NA
                        @endif
                     </td>
                     <td >{{manageAmountFormat($item->total)}}</td>
                     <td>{{getDateFormatted($item->date)}}</td>
                     <td>{{$item->receiver_type}}</td>
                 </tr>
               
                    <tr class="heading nnewHHad" >
                        <th > </th>
                        <th > Received From </th>
                        <th > Account </th>
                        <th > Description </th>
                        <th > Payment Method </th>
                        <th > Ref No. </th>
                        <th > Amount </th>
                        <th class="hideme" > Vat </th>
                    </tr>
                    @foreach ($item->categories as $category)
                        <tr class="details  @if($loop->last) detailschild @endif ">
                            <td></td>
                            <td >
                                {{$category->received_from->code}}
                            </td>
                            <td  >
                                {{$category->account->account_name}} ({{$category->account->account_code}})
                            </td>
                            <td  >
                                {{$category->description}}
                            </td>
                            <td  >
                                {{$category->payment_method->title}}
                            </td>
                            <td  >
                                {{$category->ref_no}}
                            </td>
                            <td  >
                                @if ($item->tax_check == 'Inclusive of Tax')
                                {{$category->total}}
                                @else
                                {{$category->amount}}
                                @endif
                            </td  >
                            <td class="hideme"  >
                                @if ($category->vat)
                                {{$category->vat->title}} ({{$category->vat->tax_value}})                                    
                                @endif
                            </td>
                        
                        </tr>
                    @endforeach
                         
             @endforeach
         </table>
        </div>
  
     </body>
  </html>