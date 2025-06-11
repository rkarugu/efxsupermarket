
@extends('layouts.admin.admin')
@section('content')
 {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        

             <?php 
                    $purchase_no = $row->purchase_no;
                    $default_branch_id = $row->restaurant_id;
                    $default_department_id = $row->wa_department_id;
                      $purchase_date = $row->purchase_date;
                       $getLoggeduserProfileName =  $row->getrelatedEmployee->name;

                    ?>


                     <div class = "row">

              <div class = "col-sm-6">
                   <div class = "row">
                       <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Purchase Order No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('purchase_no',  $purchase_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                   </div>

                    <div class = "row">
                        <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                    <div class="col-sm-7">
                        {!! Form::text('emp_name', $getLoggeduserProfileName, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                    </div>

                     <div class = "row">
                      <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-5 control-label">Purchase Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('purchase_date', $purchase_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                     </div>

                      <div class = "row">
                      <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-5 control-label">Supplier Invoice No</label>
                    <div class="col-sm-7">
                        {!! Form::text('supplier_invoice_number', null, ['maxlength'=>'255','placeholder' => 'Invoice number', 'required'=>true, 'class'=>'form-control']) !!}  
                    </div>
                </div>
            </div>

                     </div>


                      <div class = "row">
                      <div class="box-body">
                <div class="form-group">
                   <label for="inputEmail3" class="col-sm-5 control-label">Supplier Invoice Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('supplier_invoice_date', null, ['maxlength'=>'255','placeholder' => 'Please Select', 'required'=>true, 'class'=>'form-control datepicker','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                     </div>


              </div>

               <div class = "col-sm-6">

                  <div class = "row">
                      <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                    <div class="col-sm-6">
                         {!!Form::select('restaurant_id', getBranchesDropdown(),$default_branch_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select branch','id'=>'branch','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                  </div>

                   <div class = "row">
                        <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_department_id',getDepartmentDropdown($default_branch_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                   </div>


                      <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_supplier_id',getSupplierDropdown(),null, ['class' => 'form-control  mlselec6t','required'=>true,'id'=>'wa_supplier_id','disabled'=>true   ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                    <div class = "row">

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                <div class="col-sm-6">
                                     {!!Form::select('wa_location_and_store_id',getStoreLocationDropdown(), null, ['class' => 'form-control mlselec6t','required'=>true,'id'=>'wa_location_and_store_id' ,'disabled'=>false  ])!!} 
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class = "row">

                         <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Tyre Status</label>
                                <div class="col-sm-6">
                                     {!!Form::select('tyre_status',['new_tyre_in_stock'=>'New Tyre In Stock','retread_tyre_in_stock'=>'Retread Tyre In Stock'], null, ['class' => 'form-control mlselec6t','placeholder'=>'Select Tyre Status','required'=>true,'id'=>'tyre_status' ])!!} 
                                </div>
                            </div>
                        </div>
                    </div>


              </div>
              </div>






         

            


           



            
            

           
       
    </div>



</section>

             <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h table-responsive">
                           <h3 class="box-title"> Purchase Order Line</h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th style="width: 50px">S.No.</th>
                                      <th>Type</th>

                                      <th>Item Category</th>
                                      <th>Item No</th>
                                      <th>Description</th>
                                       {{-- <th>Supplier UOM</th> --}}
                                        <th>Supplier QTY</th>
                                        <th>Delivered QTY</th>
                                        <th>This Delivery QTY</th>
                                        



                                    {{--  <th>System UOM</th> --}}
                                       <th>unit Conversion</th>


                                      <th>System Qty</th>
                                      <th> Price</th>

                                           <th> Supplier Discount %</th>
                                                <th> Discount Amount</th>



                                      <th>Total Price</th>
                                      <th>VAT Rate</th>
                                      <th> VAT Amount</th>
                                      <th>Total Cost In VAT</th>
                                      <th>Note</th>
                                      <th></th>
                                     
                                     
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if($row->getRelatedItem && count($row->getRelatedItem)>0)
                                      <?php $i=1;
                                      $total_with_vat_arr = [];
                                      ?>
                                        @foreach($row->getRelatedItem as $getRelatedItem)
                                        <span id = "{{ $getRelatedItem->id }}" class= "rendered_id"></span>

                                        <input type ="hidden" name = "purchase_order_ids[]" value = "{{ $getRelatedItem->id }}" >
                                        <tr>
                                           <td style="width: 50px" >{{ $i }}</td>
                                           <td >{{ $getRelatedItem->item_type }}</td>

                                            @if($getRelatedItem->item_type == 'Stock')                                   
                                              <td >{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>
                                              <td >{{ @$getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                              <td >{{ @$getRelatedItem->getInventoryItemDetail->title }}</td>
                                            @else
                                              <td>{{ @$getRelatedItem->getNonStockItemDetail->gl_code->account_name  }}</td>
                                              <td>{{$getRelatedItem->item_no}}</td>
                                              <td>{{@$getRelatedItem->getNonStockItemDetail->item_description}}</td>
                                            @endif
                                            
                                            <td >{{ @$getRelatedItem->getSupplierUomDetail->title }}</td>
                                            <td class="align_float_right">{{ $getRelatedItem->supplier_quantity }}</td>
                                            @if (isset($getRelatedItem->getInventoryItemDetail) && @$getRelatedItem->getInventoryItemDetail->serialised == 'Yes')
                                                @php
                                                    $qun = $getRelatedItem->controlled_items->where('status','Approved');
                                                @endphp  
                                                <td>
                                                    {{count($qun)}}
                                                    {!! Form::hidden('delivered_quantity_'.$getRelatedItem->id,  1 , ['min'=>'0','max'=> 1,'class'=>'form-control delivered_quantity','id'=>'delivered_quantity_'.$getRelatedItem->id,'data'=>$getRelatedItem->id]) !!}  
                                                  </td>
                                                  <td>
                                                  
                                                  <a href="{{route('tyre-receive.EnterSerialNo',['id'=>$getRelatedItem->id])}}">
                                                    {{count($getRelatedItem->controlled_items->where('status','New'))}}</a>
                                                  </td>

                                            @else
                                                <td>
                                                  {{ $getRelatedItem->supplier_quantity}}
                                                </td>
                                                <td>
                                                  {!! Form::number('delivered_quantity_'.$getRelatedItem->id,  $getRelatedItem->supplier_quantity , ['min'=>'0','max'=> $getRelatedItem->supplier_quantity,'class'=>'form-control delivered_quantity','id'=>'delivered_quantity_'.$getRelatedItem->id,'data'=>$getRelatedItem->id]) !!}  

                                                </td>
                                            @endif

                                           {{-- @if($getRelatedItem->item_type == 'Stock')
                                                <td >{{ @$getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>
                                            @else
                                                <td>{{ @$getRelatedItem->getSupplierUomDetail->title }}</td>
                                            @endif
                                            --}}
                                            <td class="align_float_right">{{ @$getRelatedItem->unit_conversion }}</td>


                                       



                                            <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                            <td class="">
                                        

                                        
                                                  @if ($getRelatedItem->item_type == 'Stock' &&  isset($getRelatedItem->getInventoryItemDetail) && $getRelatedItem->getInventoryItemDetail->serialised == 'Yes')
                                                  @php
                                                      $priceT = $getRelatedItem->controlled_items->sum('value');
                                                  @endphp
                                                  {{manageAmountFormat($priceT)}}
                                                  {!! Form::hidden('order_price_'.$getRelatedItem->id,  $priceT, ['min'=>'0','class'=>'form-control order_price','id'=>'order_price_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true]) !!} 

                                                  @else                                          
                                                    @if(isset($permission[$pmodule.'___edit-price']) || $permission == 'superadmin') 
                                                    {!! Form::number('order_price_'.$getRelatedItem->id,  $getRelatedItem->order_price , ['min'=>'0','class'=>'form-control order_price','id'=>'order_price_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true]) !!} 
                                                    @else
                                                    {!! Form::number('order_price_'.$getRelatedItem->id,  $getRelatedItem->order_price , ['min'=>'0','class'=>'form-control order_price','id'=>'order_price_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true,'readonly'=>true]) !!} 
                                                    @endif
                                                  @endif

                                            </td>


                                            <td>
                                       
                                                {!! Form::number('supplier_discount_'.$getRelatedItem->id,  0 , ['max'=>100,'min'=>'0','class'=>'form-control supplier_discount','id'=>'supplier_discount_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true]) !!}  


                                            </td>
                                            <td class="align_float_right" id="discount_amount_{{ $getRelatedItem->id }}">0.00</td>

                                           
                                            <td class="align_float_right" id="total_price_{{ $getRelatedItem->id }}">
                                              @if ($getRelatedItem->item_type == 'Stock' && isset($getRelatedItem->getInventoryItemDetail) && $getRelatedItem->getInventoryItemDetail->serialised == 'Yes')
                                              <a href="#"  data-toggle="modal" data-target="#modelId{{ $getRelatedItem->id }}">
                                                {{manageAmountFormat($priceT)}}
                                              </a>                                        
                                              
                                              <!-- Modal -->
                                              <div class="modal fade" id="modelId{{ $getRelatedItem->id }}" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                                                <div class="modal-dialog" role="document">
                                                  <div class="modal-content">
                                                   
                                                    <div class="modal-body">
                                                      <table class="table">
                                                        <thead>
                                                          <tr>
                                                              <th>Serial No</th>
                                                              <th>Purchase Price</th>
                                                              <th>Purchase Weight</th>
                                                              <th>Value</th>
                                                          </tr>
                                                      </thead>
                                                      <tbody>
                                                          @foreach ($getRelatedItem->controlled_items as $itesm)
                                                          <tr>
                                                              <td scope="row">{{$itesm->serial_no}}</td>
                                                              <td scope="row">{{manageAmountFormat($itesm->purchase_price)}}</td>
                                                              <td scope="row">{{$itesm->purchase_weight}}</td>
                                                              <td scope="row">{{manageAmountFormat($itesm->value)}}</td>
                                                          </tr>                                        
                                                          @endforeach
                                                      </tbody>
                                                      </table>
                                                    </div>
                                                    <div class="modal-footer">
                                                      <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                                    </div>
                                                  </div>
                                                </div>
                                              </div>
                                              @else     
                                              {{ $getRelatedItem->total_cost }}
                                              @endif
                                            </td>
                                            <td class="align_float_right" id="vat_rate_{{ $getRelatedItem->id }}"> {{ $getRelatedItem->vat_rate }}
                                            </td>
                                            <td class="align_float_right" id="vat_amount_{{ $getRelatedItem->id }}">
                                              @if ($getRelatedItem->item_type == 'Stock' &&  isset($getRelatedItem->getInventoryItemDetail) && $getRelatedItem->getInventoryItemDetail->serialised == 'Yes')
                                                {{manageAmountFormat(($getRelatedItem->controlled_items->sum('value')*$getRelatedItem->vat_rate)/100)}}
                                              @else    
                                                {{ $getRelatedItem->vat_amount }}
                                              @endif
                                            </td>
                                            <td class="align_float_right" id="total_cost_with_vat_{{ $getRelatedItem->id }}">
                                              @if ($getRelatedItem->item_type == 'Stock' && isset($getRelatedItem->getInventoryItemDetail) && $getRelatedItem->getInventoryItemDetail->serialised == 'Yes')
                                              @php
                                                  $am = $getRelatedItem->controlled_items->sum('value');
                                              @endphp  
                                              {{manageAmountFormat((($am*$getRelatedItem->vat_rate)/100)+$am)}}
                                              @php
                                                  $total_with_vat_arr[] = (($am*$getRelatedItem->vat_rate)/100)+$am;
                                              @endphp
                                              @else    
                                                {{ $getRelatedItem->total_cost_with_vat }}
                                                @php
                                                    $total_with_vat_arr[] = $getRelatedItem->total_cost_with_vat;
                                                @endphp
                                              @endif                                          
                                            </td>
                                            <td >{{ $getRelatedItem->note }}</td>
                                            <td >
                                              @if ($getRelatedItem->item_type == 'Stock' && isset($getRelatedItem->getInventoryItemDetail) &&  $getRelatedItem->getInventoryItemDetail->serialised == 'Yes')
                                              <a href="{{route('tyre-receive.EnterSerialNo',['id'=>$getRelatedItem->id])}}">Enter Serial Nos</a>
                                                  
                                              @endif
                                            </td>
                                      
                                        </tr>
                                        <?php $i++;

                                        
                                        ?>

                                        @endforeach

                                        <tr id = "last_total_row" >
                                        <td colspan="5">  <button type= "submit" class="btn btn-success">Process Goods Received</button></td>
                                        
                                            <td></td>
                                         <td></td>

                                         <td></td>
                                            <td></td>
                                         <td></td>
                                           <td></td>
                                         <td></td>
                                         
                                          
                                            <td></td>
                                         <td></td>
                                          <td></td>
                                         <td></td>
                                          <td></td>
                                          <td></td>
                                          <td class="align_float_right" id= "main_all_total">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td>
                                           <td></td>
                                          
                                        </tr>

                                      @else
                                        <tr>
                                          <td colspan="12">Do not have any item in list.</td>
                                      
                                        </tr>
                                    @endif
                                       
                        
                                   


                                    </tbody>
                                </table>

                                </span>
                            </div>
                       


                            


                               
                        </div>
                    </div>


    </section>

     </form>


  @if($row->getRelatedAuthorizationPermissions && count($row->getRelatedAuthorizationPermissions)>0)
  <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
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
                                      <th>Status</th>
                                     
                                     
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <?php 
                                    $p = 1;
                                      ?>
                                    @foreach($row->getRelatedAuthorizationPermissions as $permissionResponse)
                                      <tr>
                                      <td>{{ $p }}</td>
                                      <td>{{ $permissionResponse->getExternalAuthorizerProfile->name}}</td>
                                      <td>{{ $permissionResponse->approve_level}}</td>
                                      <td>{{ $permissionResponse->note }}</td>
                                      <td>{{ $permissionResponse->status=='NEW'?'PROCESSING':$permissionResponse->status }}</td>
                                      </tr>
                                      <?php $p++; ?>
                                      @endforeach

                                      

                                     

                                     
                                      

                                    </tbody>
                                  
                                </table>
                                </span>
                            </div>
                       


                            


                               
                        </div>
                    </div>


    </section>
     @endif
    <!-- Modal -->


     


@endsection

@section('uniquepagestyle')


 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #last_total_row td {
  border: none !important;
}

.align_float_right
{
  text-align:  right;
}
.align_float_center
{
  text-align:  center;
}

#requisitionitemtable input[type=number]{
  width:100px;

 } 
 #requisitionitemtable td{
  width:100px;

 } 
 </style>

@endsection



@section('uniquepagescript')
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });



    $(".delivered_quantity,.order_price,.supplier_discount").on("keyup", function (e) {
      managepricing($(this).attr('data'));
    });

     $(".delivered_quantity,.order_price,.supplier_discount").on("change", function (e) {
      managepricing($(this).attr('data'));
    });

    function managepricing(data_id)
    {
      var delivered_quantity = $("#delivered_quantity_"+data_id).val();
      var order_price = $("#order_price_"+data_id).val();
      var supplier_discount =  $("#supplier_discount_"+data_id).val();
      var total_price = delivered_quantity*order_price;
      $("#total_price_"+data_id).html(total_price.toFixed(2));
      if(supplier_discount>0)
      {
        var gettedDiscount = (supplier_discount*total_price)/100;
        $("#discount_amount_"+data_id).html(gettedDiscount.toFixed(2));
        total_price =  total_price-gettedDiscount;
      }
      var vat_rate = parseFloat($("#vat_rate_"+data_id).html());
      if(vat_rate>0)
      {
        var vat_amount = (vat_rate*total_price)/100;
        $("#vat_amount_"+data_id).html(vat_amount.toFixed(2));
        total_price =  total_price+vat_amount;
      
      }
        $("#total_cost_with_vat_"+data_id).html(total_price.toFixed(2));
      var rows_total_data = 0.00;


      $( ".rendered_id" ).each(function( index ) 
      {
        rows_total_data += parseFloat($("#total_cost_with_vat_"+$( this ).attr('id')).html()); 
      });
      $("#main_all_total").html(rows_total_data.toFixed(2))
    }

          
    </script>
@endsection


