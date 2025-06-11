
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

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
                         {!!Form::select('wa_supplier_id',getSupplierDropdown(),null, ['class' => 'form-control  ','required'=>true,'id'=>'wa_supplier_id','disabled'=>true   ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                      <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                    <div class="col-sm-6">
                         {!!Form::select('wa_location_and_store_id',getStoreLocationDropdown(), null, ['class' => 'form-control ','required'=>true,'id'=>'wa_location_and_store_id' ,'disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                     </div>
              </div>
              </div>














          

           


           



           

           

             
        </form>
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



                                      <th>System UOM</th>
                                       <th>unit Conversion</th>


                                      <th>System Qty</th>
                                      <th> Price</th>
                                      <th>Total Price</th>
                                      <th>VAT Rate</th>
                                      <th> VAT Amount</th>
                                      <th> Round Off</th>
                                      <th>Total Cost In VAT</th>
                                      <th>Note</th>
                                      <th>Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if($row->getRelatedItem && count($row->getRelatedItem)>0)
                                      <?php $i=1;
                                      $total_with_vat_arr = [];
                                      ?>
                                        @foreach($row->getRelatedItem as $getRelatedItem)
                                        <tr>
                                        <td >{{ $i }}</td>
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>


                                      
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->title }}</td>

                                            <td >{{ $getRelatedItem->getSupplierUomDetail->title }}</td>
                                            <td class="align_float_right">{{ $getRelatedItem->supplier_quantity }}</td>




                                         <td >{{ $getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->unit_conversion }}</td>


                                       



                                        <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->order_price }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->round_off }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td>
                                        <td >{{ $getRelatedItem->note }}</td>
                                        <td class = "action_crud">
                                               
                                                   

                                                   

                                                  

                                                 

                                                    <span>

                                                      <span>
                                                    <a title="Trash" href="{{ route('purchase-orders.items.delete',[$row->purchase_no,$getRelatedItem->id])}}" ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                    </span>

                                                    <span>
                                                    <a class="left-padding-small" data-href="{!! route('purchase-orders.editPurchaseItem',[ $purchase_no,$getRelatedItem->id]) !!}" onclick="editRequisitionItem('{!! route('purchase-orders.editPurchaseItem',[ $purchase_no,$getRelatedItem->id]) !!}')"  data-toggle="modal" data-target="#edit-Requisition-Item-Model" data-dismiss="modal" ><i class="fa fa-edit" style="color: #444; cursor: pointer;" title="Edit"></i></a></span>
                                                  
                                                  
                                                   





                                                </td>
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
                                            <td></td>
                                         <td></td>
                                          <td></td>
                                           <td></td>
                                            <td></td>
                                         <td></td>
                                         <td></td>
                                           <td></td>
                                             <td></td>
                                          <td class="align_float_right">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td>
                                           <td></td>
                                           <td></td>
                                        </tr>

                                      @else
                                        <tr>
                                          <td colspan="16">Do not have any item in list.</td>
                                      
                                        </tr>
                                    @endif
                                       
                        
                                   


                                    </tbody>
                                </table>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-4"><span>
                             

                              <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addRequisitionItemModel">Add Item To Purchase Order</button>
                              </span></div>
                              <div class="col-md-3">
                              @if($row->status == 'UNAPPROVED' && $row->getRelatedItem && count($row->getRelatedItem)>0)
                                 <a href = "{{ route('purchase-orders.sendRequisitionRequest',$purchase_no)}}" class= "btn btn-success btn-lg" >Send Request</a>
                                 @endif
                              </div>
                               <div class="col-md-3"></div>
                              <div class="col-md-2"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>


    <!-- Modal -->


     <div class="modal" id="edit-Requisition-Item-Model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div> 

<div id="addRequisitionItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">Add Item To Purchase Order</h4>
      </div>
      <div class="modal-body">
        {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

            
               {!! Form::hidden('purchase_no', $purchase_no, []) !!}  
                {!! Form::hidden('purchase_date', $purchase_date, []) !!} 

               
            <!--div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Category</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),null, ['maxlength'=>'255','placeholder' => 'Please select category', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'inventory_category']) !!}  
                    </div>
                </div>
            </div-->

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_item_id', inventoryUItemDropDown(),null, ['maxlength'=>'255','placeholder' => 'Please select item', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'item']) !!}  
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item No</label>
                    <div class="col-sm-9">
                       
                        {!! Form::text('item_no', null, ['maxlength'=>'255', 'class'=>'form-control','id'=>'item_no','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

          <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Standard Cost</label>
                    <div class="col-sm-3">
                       
                        {!! Form::text('standard_cost', null, ['maxlength'=>'255', 'class'=>'form-control','id'=>'standard_cost','readonly'=>true]) !!}  
                    </div>

                     <label for="inputEmail3" class="col-sm-3 control-label">Last Purchase Price</label>
                    <div class="col-sm-3">
                       
                         {!! Form::text('prev_standard_cost', null, ['maxlength'=>'255', 'class'=>'form-control','id'=>'prev_standard_cost','readonly'=>true]) !!}   
                    </div>
                </div>
            </div>

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">System UOM</label>
                    <div class="col-sm-3">
                      


                         {!! Form::select('unit_of_measure', getUnitOfMeasureList(),null, ['maxlength'=>'255','placeholder' => '',  'class'=>'form-control ','id'=>'unit_of_measure','disabled'=>true]) !!}  
                    </div>

                    <label for="inputEmail3" class="col-sm-3 control-label">System Quantity</label>
                    <div class="col-sm-3">
                      


                        {!! Form::number('quantity', null, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'quantity','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">VAT Category</label>
                    <div class="col-sm-9">
                       
                    {!! Form::text('tax_value', null, ['maxlength'=>'255', 'class'=>'form-control','id'=>'tax_value','readonly'=>true]) !!}   
                    </div>
                </div>
            </div>


            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Order Price</label>
                    <div class="col-sm-4">
                        {!! Form::number('order_price', null, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'order_price']) !!}  
                    </div>
                    
                    <div class="col-sm-4">
                        <a href="javascript:void(0);" class="btn btn-primary" id ="view_last_price_button">View last Purchases Price</a>
                    </div>
                </div>
            </div>
            
            <div class="box-body" id="price-box-sec">
                
            </div>
            
            <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Price Inclusive of VAT?</label>
                    <div class="col-sm-9">
                    Yes
                    {!! Form::radio('is_exclusive_vat', 'Yes', ['class'=>'form-control','id'=>'is_exclusive_vat']) !!}   
                    No
                    {!! Form::radio('is_exclusive_vat', 'No', ['class'=>'form-control','id'=>'is_exclusive_vat']) !!}   
                    </div>
                </div>
            </div>     

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier UOM</label>
                    <div class="col-sm-9">
                         {!! Form::select('supplier_uom_id', getUnitOfMeasureList(),null, ['maxlength'=>'255','placeholder' => 'Please Select UOM',  'class'=>'form-control ','id'=>'supplier_uom_id','required'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Supplier Quantity</label>
                    <div class="col-sm-9">
                        {!! Form::number('supplier_quantity', null, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'supplier_quantity']) !!}  
                    </div>
                </div>
            </div>

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Unit Conversion</label>
                    <div class="col-sm-9">
                        {!! Form::number('unit_conversion', 1, ['min'=>'0', 'required'=>true, 'class'=>'form-control','id'=>'unit_conversion']) !!}  
                    </div>
                </div>
            </div>


             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                    <div class="col-sm-9">
                       {!! Form::textarea('note', null, ['maxlength'=>'1000', 'class'=>'form-control','id'=>'note']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                       <div class="box-footer">
                <button type="submit" class="btn btn-primary" id="add_submitform">Add</button>
            </div>
                    </div>
                </div>
            </div>

            

              
            </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>
@endsection

@section('uniquepagestyle')

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #last_total_row td {
  border: none !important;
}
 #note{
    height: 60px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
    $(function () {
      $(".mlselec6t").select2();
     
     
});




     $("#inventory_category").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
        $("#standard_cost").val('');
           $("#prev_standard_cost").val('');
           $("#tax_value").val('');
        var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);

    });

     $("#item").change(function(){
        $( "#view_last_price_button" ).trigger( "click" );
      $("#item_no").val('');
      $("#unit_of_measure").val('');
        $("#standard_cost").val('');
           $("#prev_standard_cost").val('');
           $("#tax_value").val('');

        var selected_item_id = $("#item").val();
       
        getItemDetails(selected_item_id);

    });

    function getItemDetails(selected_item_id)
   {
   
    
        if(selected_item_id != "")
        {
            jQuery.ajax({
                url: '{{route('purchase-orders.items.detail')}}',
                type: 'POST',
                data:{selected_item_id:selected_item_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {

                  var obj = jQuery.parseJSON(response);
                    $("#item_no").val(obj.stock_id_code);
                     $("#unit_of_measure").val(obj.unit_of_measure);
                      $("#standard_cost").val(obj.standard_cost);
                      $("#prev_standard_cost").val(obj.prev_standard_cost);
                        $("#tax_value").val(obj.vat_rate);

                  
                }
            });
        }
       
   }

      function manageitem(selected_inventory_category)
   {
   
        if(selected_inventory_category != "")
        {
            jQuery.ajax({
                url: '{{route('purchase-orders.items')}}',
                type: 'POST',
                data:{selected_inventory_category:selected_inventory_category},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#item").val('');
                    $("#item").html(response);
                   
                   

                }
            });
        }
        else
        {
           $("#item").val('');
           $("#item").html('<option selected="selected" value="">Please select item</option>');
        }
   }


function editRequisitionItem(link)
{
  
  $('#edit-Requisition-Item-Model').find(".modal-content").load(link);
}


  $("#unit_conversion,#supplier_quantity").on("keyup", function (e) {
      manageSystemQuantity();
    });

   $("#unit_conversion,#supplier_quantity").change(function (e) {
      manageSystemQuantity();
    });

   function manageSystemQuantity()
   {  
      var unit_conversion =   $("#unit_conversion").val();
      var supplier_quantity =   $("#supplier_quantity").val();
      var quantity = null;
      if(unit_conversion && supplier_quantity)
      {
        quantity = unit_conversion*supplier_quantity;
      }

      $("#quantity").val(quantity);
   }


    $("#add_submitform").on("mouseover", function (e) {
          $("#unit_of_measure").attr('disabled',false);
    });

   $("#add_submitform").on("mouseleave", function (e) {
   
        $("#unit_of_measure").attr('disabled',true);
    });

  

</script>
    <script>
        $('#view_last_price_button').click(function(){
            item_id = $('#item').val();
            jQuery.ajax({
                url: '{{route('admin.purchase-orders.view-last-purchases-price')}}',
                type: 'POST',
                //dataType: "json",
                data:{'item_id':item_id},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    $('#price-box-sec').html(response);
                }
            });
        });
    </script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


