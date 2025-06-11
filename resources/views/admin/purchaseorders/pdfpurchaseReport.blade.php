<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            text-align: center;
            color: #777;
        }

        body h1 {
            font-weight: 300;
            margin-bottom: 0px;
            padding-bottom: 0px;
            color: #000;
        }

        body h3 {
            font-weight: 300;
            margin-top: 10px;
            margin-bottom: 20px;
            font-style: italic;
            color: #555;
        }

        body a {
            color: #06f;
        }

        .invoice-box {
            margin: auto;
            font-size: 12px;
            line-height: 20px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
            border-collapse: collapse;
        }

        .invoice-box table td {
            padding: 3px;
            vertical-align: top;
        }

        /* .invoice-box table tr td:last-child {
            text-align: right;
        } */

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }  
        .makeBackgroundGrey{
        background: #eee !important;
    }
    </style>
</head>
<body>
                @php $all_settings = getAllSettings(); @endphp

    <div class="invoice-box">
        <table  style="text-align: center;">
            <tbody>
                <tr class="top">
                    <th colspan="3">
                        <h2>{{$all_settings['COMPANY_NAME']}}</h2>
                    </th>
                </tr>
                <tr class="top">
                    <td colspan="3" style="    text-align: center;">Purchase order Report
                    </td>
                </tr>
                <tr class="top">
                    <th colspan="1" style="text-align: left">Project {{@$projects->where('id',request()->project)->first()->title}}</th>
                    <th  colspan="2" style="text-align: right">DATE: {{date('d-M-Y',strtotime(request()->date_from))}} To {{date('d-M-Y',strtotime(request()->date_to))}}</th>
                </tr>
            </tbody>        
        </table>
      
        <table class="table table-bordered table-hover" id="maintable">
           
            <tbody>
                <tr class="heading">
                    <td >S.No.</td>
                    <td >Date</td>
                    <td >Purchase No</td>
                    <td >User Name</td>
                    <td >Branch</td>
                    <td >Note</td>
                    <td >Department</td>
                    <td >Total Lists</td>
                    <td >Status</td>
                                                             
                </tr>
                @if(isset($lists) && !empty($lists))
                    <?php $b = 1;?>
                    @foreach($lists as $list)
                        <tr class="{{ $b%2 == 0 ? 'makeBackgroundGrey' : NULL }} item" >
                            <td>{!! $b !!}</td>
                            <td>{!! $list->purchase_date !!}</td>
                            <td>{!! $list->purchase_no !!}</td>
                            <td>{!! @$list->getrelatedEmployee->name !!}</td>
                            <td>{!! @$list->getBranch->name !!}</td>
                            <td>{!! $list->note !!}</td>
                            <td >{{ @$list->getDepartment->department_name }}</td>
                           
                            <td>{{ count(@$list->getRelatedItem)}}</td>
                            <td>{!! $list->status !!}
                                {{$list->is_hide != 'No' ? ' - Archived' : NULL}}
                            </td>
                        </tr>
@if('Detailed' == request()->report)
                        <tr class="{{ $b%2 == 0 ? 'makeBackgroundGrey' : NULL }} ">
                            <td colspan="11" style="padding-left:10px;margin:10px;padding-bottom:10px; border:1px solid #eee">
                               
    <div class="col-md-12 no-padding-h">
        <h3 class="box-title" style="text-align: left"> Requisition Line</h3>

         
             <table class="table table-bordered table-hover">
               
                <tbody>
                     <tr class="heading">
                       <td>S.No.</td>
                     
                       <td>Item No</td>
                       <td>Description</td>
                       <td>UOM</td>
                       <td>Qty Req</td>
                    
                       <td>Note</td>
                      
                     </tr>

                 @if($list->getRelatedItem && count($list->getRelatedItem)>0)
                   <?php $i=1;
                   $total_with_vat_arr = [];
                   ?>
                     @foreach($list->getRelatedItem as $getRelatedItem)

                     <tr >
                     <td >{{ $i }}</td>
                      {{-- <td >{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td> --}}


                   
                      <td >{{ @$getRelatedItem->item_no }}</td>
                        <td >{{ @$getRelatedItem->getInventoryItemDetail->title }}</td>
                      <td >{{ @$getRelatedItem->unit_of_measures->title }}</td>


                    



                     <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                     {{-- <td class="align_float_right">{{ $getRelatedItem->standard_cost }}</td>
                     <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                     <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                     <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                     <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td> --}}
                     <td >{{ $getRelatedItem->note }}</td>
                   
                     </tr>
                     <?php $i++;

                     $total_with_vat_arr[] = $getRelatedItem->total_cost_with_vat;
                     ?>

                     @endforeach

                     <tr id = "last_total_row" >
                     <td></td>
                      <td></td>
                       <td></td>
                    
                      <td></td>
                       {{-- <td></td>
                        <td></td>
                         <td></td>
                      <td></td>
                       <td class="align_float_right">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td> --}}
                        <td></td>
                       
                     </tr>

                   @else
                     <tr>
                       <td colspan="5">Do not have any item in list.</td>
                   
                     </tr>
                 @endif
                    
     
                


                 </tbody>
             </table>
             
         </div>
    
         @if($list->getRelatedAuthorizationPermissions && count($list->getRelatedAuthorizationPermissions)>0)
         <div class="col-md-12 no-padding-h">
            <h3 class="box-title" style="text-align: left">Approval Status</h3>

             
                 <table class="table table-bordered table-hover">
               
                    <tbody>
                         <tr  class="heading">
                           <td>S.No.</td>
                           <td>Authorizer Name</td>
                           <td>Level</td>
                           <td>Note</td>
                           <td>Time Approved</td>
                           <td>Time Diff</td>
                           <td>Status</td>
                          
                          
                         </tr>
                     <?php 
                     $p = 1;
                       ?>
                     @foreach($list->getRelatedAuthorizationPermissions as $permissionResponse)
                       <tr>
                       <td>{{ $p }}</td>
                       <td>{{ $permissionResponse->getExternalAuthorizerProfile->name}}</td>
                       <td>{{ $permissionResponse->approve_level}}</td>
                       <td>{{ $permissionResponse->note }}</td>
                       <td>{{ $permissionResponse->status=='APPROVED'?
                       date('d/M/Y h:i A',strtotime($permissionResponse->approved_at))
                       :NULL }}</td>
                       <td>
                       @php 
                       
                       if($permissionResponse->status=='APPROVED'){
                         $date1 = new DateTime($permissionResponse->created_at);
                         $date2 = new DateTime($permissionResponse->approved_at);
                         $interval = $date1->diff($date2);
                         echo $interval->h . " Hours ". $interval->i . " Minutes ";
                       }
                       @endphp
                       </td>
                       <td>{{ $permissionResponse->status=='NEW'?'PROCESSING':$permissionResponse->status }}</td>
                       </tr>
                       <?php $p++; ?>
                       @endforeach

                       

                      

                      
                       

                     </tbody>
                   
                 </table>
                 
             </div>
             @endif         
                            </td> 
                        </tr>
@endif
                        <?php $b++; ?>
                    @endforeach
                @endif


            </tbody>
        </table>
    </div>   
</body>
</html>