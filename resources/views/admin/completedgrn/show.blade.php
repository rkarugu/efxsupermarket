
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
                     {{--
                      <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_location_and_store_id',getStoreLocationDropdown(), null, ['class' => 'form-control mlselec6t','required'=>true,'id'=>'wa_location_and_store_id' ,'disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                     </div>
--}}
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
                                      <th>S.No.</th>
                                      <th>Item Category</th>
                                      <th>Item No</th>
                                      <th>Description</th>
                                       <th>Supplier UOM</th>
                                        <th>Supplier QTY</th>
                                        <th>Delivered QTY</th>
                                        



                                      <th>System UOM</th>
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
                                                                               <td >{{ $i }}</td>
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>


                                      
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->title }}</td>

                                            <td >{{ $getRelatedItem->getSupplierUomDetail->title }}</td>
                                            <td class="align_float_right">{{ $getRelatedItem->supplier_quantity }}</td>

                                            <td>

                                               {!! Form::number('delivered_quantity_'.$getRelatedItem->id,  $getRelatedItem->supplier_quantity , ['min'=>'0','max'=> $getRelatedItem->supplier_quantity,'class'=>'form-control delivered_quantity','id'=>'delivered_quantity_'.$getRelatedItem->id,'data'=>$getRelatedItem->id]) !!}  


                                            </td>








                                         <td >{{ $getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->unit_conversion }}</td>


                                       



                                        <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                        <td class="align_float_right">
                                        

                                        




                                             @if(isset($permission[$pmodule.'___edit-price']) || $permission == 'superadmin') 


                                            {!! Form::number('order_price_'.$getRelatedItem->id,  $getRelatedItem->order_price , ['min'=>'0','class'=>'form-control order_price','id'=>'order_price_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true]) !!} 


                                             @else


                                            {!! Form::number('order_price_'.$getRelatedItem->id,  $getRelatedItem->order_price , ['min'=>'0','class'=>'form-control order_price','id'=>'order_price_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true,'readonly'=>true]) !!} 

                                             @endif

                                        </td>


                                          <td>
                                       
                                           {!! Form::number('supplier_discount_'.$getRelatedItem->id,  0 , ['max'=>100,'min'=>'0','class'=>'form-control supplier_discount','id'=>'supplier_discount_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true]) !!}  


                                          </td>
                                           <td class="align_float_right" id="discount_amount_{{ $getRelatedItem->id }}">0.00</td>

                                        <td class="align_float_right" id="total_price_{{ $getRelatedItem->id }}">{{ $getRelatedItem->total_cost }}</td>




                                        <td class="align_float_right" id="vat_rate_{{ $getRelatedItem->id }}">{{ $getRelatedItem->vat_rate }}</td>
                                        <td class="align_float_right" id="vat_amount_{{ $getRelatedItem->id }}">{{ $getRelatedItem->vat_amount }}</td>
                                        <td class="align_float_right" id="total_cost_with_vat_{{ $getRelatedItem->id }}">{{ $getRelatedItem->total_cost_with_vat }}</td>
                                        <td >{{ $getRelatedItem->note }}</td>
                                      
                                        </tr>
                                        <?php $i++;

                                        $total_with_vat_arr[] = $getRelatedItem->total_cost_with_vat;
                                        ?>

                                        @endforeach

                                        <tr id = "last_total_row" >
                                        <td colspan="2">  <button type= "submit" class="btn btn-success">Process Goods Received</button></td>
                                        
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


