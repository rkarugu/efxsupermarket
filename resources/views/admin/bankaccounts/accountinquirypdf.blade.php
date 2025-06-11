<!doctype html>
<html>
   <head>
      <meta charset="utf-8">
      <title>{{$title}}</title>

      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="{{asset('public/css/pdf.css')}}">
      <style>
         .invoice-box *{
            font-size:12px !important
         }

            table { 
             
               width: 100% !important;
               font-size: 12px !important;
            }
           
            .invoice-box td, .invoice-box th {
       
               padding:  0px 2px !important;line-height: 18px !important;
            }
      </style>
   </head>
   <body> 
       <div class="invoice-box">
         <table class="table no-border m-0">
            <tbody>
                <tr>
                    <td>
                        <h1>{{ getAllSettings()['COMPANY_NAME'] }}</h1>
                    </td>
                </tr>
            </tbody>
        </table>
         <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="2" style="text-align: left">
                    <h4 style="text-align: left">
                        {{$heading}}
                        
                        {{-- <hr>     --}}
                    </h4>            
                    <h5 style="text-align: left;padding:0;margin:0">
                     Bank : {{$bank->account_name}}
                   </h4>                
               </td>
             
            </tr>
            {{-- <tr class="details">
               <td colspan="4" style="text-align: left">
                                       
              </td>
           </tr> --}}

            <tr class="details">
               <td><b>From</b>: {{getDateFormatted(request()->from)}}</td>
               <td><b>To</b>: {{getDateFormatted(request()->to)}}</td>
               @if (request()->filled('narrative_type'))
                  <td><b>Type</b>: {{request()->narrative_type}}</td>
               @endif
               <td><b>Total Record</b>: {{count($row)}}</td>
               <td style="text-align: right;">
                  <b>Opening Balance : {{ manageAmountFormat($getOpeningBlance) }}</b><br>
               </td>
           </tr>
         </table>
         <br>
         <table cellpadding="0" cellspacing="0" style="border: 1px solid #ddd;font-size:14px;width:100% !important">
          <thead>
            <tr class="heading " >
               {{-- 20 --}}
               <td width="9%" style="padding: 0 2px !important;line-height: 18px !important;border-top: 1px solid #212121;border-bottom: 1px solid #212121;">Date</td>
               {{-- <td width="9%" style="padding:  0px 2px !important;line-height: 18px !important;">Type</td> --}}
               <td width="10%" style="padding:  0px 2px !important;line-height: 18px !important;border-top: 1px solid #212121;border-bottom: 1px solid #212121;">Trans No</td>
               {{-- <td width="15%" style="padding:  0px 2px !important;line-height: 18px !important;">Parent Acc</td> --}}
               <td width="30%" style="padding:  0px 2px !important;line-height: 18px !important;border-top: 1px solid #212121;border-bottom: 1px solid #212121;">Narration</td>
               {{-- <td width="19%" style="padding:  0px 2px !important;line-height: 18px !important;">GL Acc</td> --}}
               <td width="15%" style="padding:  0px 2px !important;line-height: 18px !important;border-top: 1px solid #212121;border-bottom: 1px solid #212121;" >Particulars</td>
               <td width="9%" style="padding:  0px 2px !important;line-height: 18px !important;border-top: 1px solid #212121;border-bottom: 1px solid #212121;" >Debit</td>
               <td width="9%" style="padding:  0px 2px !important;line-height: 18px !important;border-top: 1px solid #212121;border-bottom: 1px solid #212121;" >Credit</td>
               <td width="13%"  style="padding:  0px 2px !important;line-height: 18px !important; text-align: right;border-top: 1px solid #212121;border-bottom: 1px solid #212121;">Running Balance</td>
            </tr>
          </thead>
          <tbody>
            <?php 
               $OpeningBlance = $getOpeningBlance;
               $total_amount = [];
               $credit_amount = [];
               $debit_amount = [];
                                         //echo "<pre>"; print_r($row); die;
            // $accountsss = \App\Model\WaChartsOfAccount::get();

                                        ?>
            @foreach($row as $list)
                 <tr class="details"  > 
                    <td style="padding:  0px 2px !important;line-height: 18px !important;border-bottom: 1px solid #c1c1c1;" >{!! date('d/M/Y',strtotime($list['trans_date'])) !!}</td>
                     <td style="padding:  0px 2px !important;line-height: 18px !important;border-bottom: 1px solid #c1c1c1;" >{!! $list['document_no'] !!}</td>
                     <td style="padding:  0px 2px !important;line-height: 18px !important;border-bottom: 1px solid #c1c1c1;font-size:11px !important;" >{!! $list['short_narration'] ?? NULL !!}</td>
                     <td style="padding:  0px 2px !important;line-height: 18px !important;border-bottom: 1px solid #c1c1c1;" >{!! $list['reference'] !!}</td>
                     <td style="padding:  0px 2px !important;line-height: 18px !important;border-bottom: 1px solid #c1c1c1;" >{!! $list['amount'] > 0 ? @manageAmountFormat($list['amount']) : '-' !!}</td>
                     <td style="padding:  0px 2px !important;line-height: 18px !important;border-bottom: 1px solid #c1c1c1;" >{!! $list['amount'] < 0 ? @manageAmountFormat(abs($list['amount'])) : '-' !!}</td>
                     <td  style="padding:  0px 2px !important;line-height: 18px !important; text-align: right;border-bottom: 1px solid #c1c1c1;">{!! @manageAmountFormat($list['amount']+$OpeningBlance) !!}</td>
                     
                 <?php 
                       $total_amount[] = $list['amount'];
                        $credit_amount[] = $list['amount'] < 0 ? $list['amount'] : 0;
                        $debit_amount[] = $list['amount'] > 0 ? $list['amount'] : 0;
                        $OpeningBlance += $list['amount']
                 ?>
                    
                
                 </tr>
               
                         
            @endforeach
            <tr class="details"  > 
               <td style="font-weight: bold;border-top: 1px solid #000;border-bottom: 1px solid #000;" colspan="3"></td>
               <td style="font-weight: bold;border-top: 1px solid #000;border-bottom: 1px solid #000;" colspan="1">B/F : {{ manageAmountFormat($getOpeningBlance) }}</td>
               <td style="font-weight: bold;border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ manageAmountFormat(array_sum($debit_amount))}}</td>
               <td style="font-weight: bold;border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ manageAmountFormat(array_sum($credit_amount))}}</td>
               <td style="font-weight: bold;text-align: right;border-top: 1px solid #000;border-bottom: 1px solid #000;">{{ manageAmountFormat($OpeningBlance)}}</td>
             </tr>
            </tbody>
         </table>
        </div>
  
     </body>
  </html>