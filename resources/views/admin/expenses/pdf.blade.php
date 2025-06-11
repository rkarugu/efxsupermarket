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
                <th >Payment Method</th>
                <th>Sub Total</th>
                <th>Tax</th>
                <th>Total</th>
                <th >Ref No</th>
                <th >Date</th>                
            </tr>
             @foreach ($data as $item)
                 <tr class="details detailsParent tddHead"  > 
                     <td style="width:50px ">{{$loop->iteration}}</td>
                     <td>{{$item->account_name}}</td>
                     <td>{{$item->title}}</td>
                     <td>{{manageAmountFormat($item->subTotal)}}</td>
                     <td>
                        @if ($item->tax_amount_type != 'Out Of Scope of Tax')
                            {{manageAmountFormat($item->totalAmount - $item->subTotal)}}
                        @else
                        NA
                        @endif
                     </td>
                     <td >{{manageAmountFormat($item->totalAmount)}}</td>
                     <td>{{$item->ref_no}}</td>
                     <td>{{getDateFormatted($item->payment_date)}}</td>
                 </tr>
               
                    <tr class="heading nnewHHad" >
                        <th colspan="1"></th>
                        <th colspan="2">Category</th>
                        <th colspan="2">Description</th>
                        <th colspan="1">Amount</th>
                        <th colspan="2">VAT</th>
                    </tr>
                    @foreach ($item->categories as $category)
                    <tr class="details @if($loop->last) detailschild @endif ">
                        <td colspan="1"></td>

                        <td colspan="2">{{$category->category->account_name}} ({{$category->category->account_code}})</td>
                        <td colspan="2">{{$category->description}}</td>
                        <td colspan="1">
                            @if ($item->tax_amount_type == 'Inclusive of Tax')
                            {{$category->total}}
                            @else
                            {{$category->amount}}
                            @endif</td>
                        <td colspan="2">@if($category->tax_manager) {{$category->tax_manager->title}} ({{$category->tax_manager->tax_value}}) @endif</td>
                    </tr>                                
                    @endforeach
                         
             @endforeach
         </table>
        </div>
  
     </body>
  </html>