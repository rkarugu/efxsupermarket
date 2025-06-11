
@extends('layouts.admin.admin')

@section('content')

<style>
    .makeBackgroundGrey{
        background: #eee !important;
    }

</style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                            <form action="" method="get">
                                <div class="row">
                                    <div class="col-sm-2">
                                        <div class="form-group">
                                          <label for="">From</label>
                                          <input type="date" name="date_from" id="date_from" class="form-control" value="{{request()->date_from}}">
                                        </div>
                                    </div>

                                    <div class="col-sm-2">
                                       
                                        <div class="form-group">
                                            <label for="">To</label>
                                            <input type="date" name="date_to" id="date_to" class="form-control" value="{{request()->date_to}}" >
                                        </div>
                                    </div>

                                    <br>
                                    <div class="col-sm-2">
                                       
                                        <div class="form-group">
                                            <label for=""></label>
                                            <button type="submit" name="manage" value="filter" class="btn btn-danger">Filter</button>
                                            <button type="submit" name="manage" value="pdf" class="btn btn-danger">PDF</button>
                                            <button type="submit" name="manage" value="excel" class="btn btn-danger">Excel</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <br>
                            @include('message')
                            <div class="col-md-12 no-padding-h">
                                <table class="table table-bordered table-hover" id="maintable">
                                    <thead>
                                    <tr>
                                        <th >S.No.</th>
                                        <th >Date</th>
                                        <th >Purchase No</th>
                                        <th >User Name</th>
                                        <th >Branch</th>
                                        <th >Note</th>
                                        <th >Supplier</th>
                                       
                                        <th >Total Lists</th>
                                        <th >Status</th> 
                                        <th >Grn No</th>                                         
                                        <th >Tonnage</th>
                                        <th >LPO Amount</th>

                                    </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($lists) && !empty($lists))
                                            <?php $b = 1;?>
                                            @foreach($lists as $list)
                                                <tr class="{{ $b%2 == 0 ? 'makeBackgroundGrey' : NULL }}" >
                                                    <td>{!! $b !!}</td>
                                                    <td>{!! $list->purchase_date  !!}</td>
                                                    <td>{!! $list->purchase_no !!}</td>
                                                    <td>{!! @$list->getrelatedEmployee->name !!}</td>
                                                    <td>{!! @$list->getBranch->name !!}</td>
                                                    <td>{!! $list->note !!}</td>
                                                    <td >{{ @$list->getSupplier->name }}</td>
                                                   
                                                    <td>{{ count(@$list->getRelatedItem)}}</td>
                                                    <td>{!! $list->status !!}
                                                        {{$list->is_hide != 'No' ? ' - Archived' : NULL}}
                                                    </td>
                                                     <td>{{ @$list->getGrnNo->grn_number}}</td>
                                                     @php
                                                     $tonnage = 0;
                                                     @endphp
                                                     @foreach($list->getRelatedItem as $getRelatedItem)
                                                     @if($getRelatedItem->getInventoryItemDetail->net_weight)
                                                     @php
                                                     $tonnage += $getRelatedItem->getInventoryItemDetail->net_weight * $getRelatedItem->quantity;
                                                     @endphp
                                                     @endif
                                                     @endforeach

                                                     <td>{{ manageAmountFormat($tonnage)}}</td>
                                                     <td>{{ manageAmountFormat(@$list->getRelatedItem->sum('total_cost_with_vat'))}}</td>

                                                </tr>
@if('Detailed' == request()->report)
                                                <tr class="{{ $b%2 == 0 ? 'makeBackgroundGrey' : NULL }} ">
                                                    <td colspan="11">
                                                       
                            <div class="col-md-12 no-padding-h">
                                <h3 class="box-title"> Requisition Line</h3>
     
                                 <span id = "requisitionitemtable">
                                     <table class="table table-bordered table-hover">
                                         <thead>
                                         <tr>
                                           <th>S.No.</th>
                                           {{-- <th>Item Category</th> --}}
                                           <th>Item No</th>
                                           <th>Description</th>
                                           <th>UOM</th>
                                           <th>Qty Req</th>
                                           {{-- <th> Cost</th>
                                           <th>Total Cost</th>
                                           <th>VAT Rate</th>
                                           <th> VAT Amount</th>
                                           <th>Total Cost In VAT</th> --}}
                                           <th>Note</th>
                                          
                                         </tr>
                                         </thead>
                                         <tbody>
     
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
                                     </span>
                                 </div>
                            
                                 @if($list->getRelatedAuthorizationPermissions && count($list->getRelatedAuthorizationPermissions)>0)
                                 <div class="col-md-12 no-padding-h">
                                    <h3 class="box-title">Approval Status</h3>
         
                                     <span id = "requisitionitemtablea">
                                         <table class="table table-bordered table-hover">
                                             <thead>
                                             <tr>
                                               <th>S.No.</th>
                                               <th>Authorizer Name</th>
                                               <th>Level</th>
                                               <th>Note</th>
                                               <th>Time Approved</th>
                                               <th>Time Diff</th>
                                               <th>Status</th>
                                              
                                              
                                             </tr>
                                             </thead>
         
                                             <tbody>
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
                                         </span>
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

                                 {{$lists->links()}}
                            </div>
                        </div>
                    </div>




</section>
@endsection
@section('uniquepagescript')
<div id="loader-on" style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
" class="loder">
  <div class="loader " id="loader-1"></div>
</div>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>

</script>
@endsection