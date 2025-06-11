
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')

         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

             <?php 
                    $transfer_no = $row->transfer_no;
                    $default_branch_id = $row->restaurant_id;
                    $default_department_id = $row->wa_department_id;
                    $transfer_date = $row->transfer_date;
                     $getLoggeduserProfileName =  $row->getrelatedEmployee->name;


                    ?>

                     <div class = "row">

              <div class = "col-sm-6">
                   <div class = "row">
                       <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Transfer No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('transfer_no',  $transfer_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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
                    <label for="inputEmail3" class="col-sm-5 control-label">Transfer Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('transfer_date', $transfer_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                     </div>
            <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Vehicle Registration No.</label>
                    <div class="col-sm-7">
                         {!!Form::select('vehicle_reg_no',getVehicleRegList(), $row->vehicle_register_no, ['class' => 'form-control','id'=>'vehicle_reg_no' ,'disabled'=>true ])!!} 
                          <span id = "error_msg_to_store_location_id"></span>
                    </div>
                </div>
            </div>
            </div>
            <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Route</label>
                    <div class="col-sm-7">
                         {!!Form::select('route',getRouteList(), $row->route, ['class' => 'form-control','id'=>'route','disabled'=>true ])!!} 
                          <span id = "error_msg_to_store_location_id"></span>
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
                    <label for="inputEmail3" class="col-sm-5 control-label">From Store</label>
                    <div class="col-sm-6">
                         {!!Form::select('from_strore_location_id',getStoreLocationDropdownByBranch($row->restaurant_id), $row->from_store_location_id, ['class' => 'form-control ','id'=>'from_strore_location_id'  ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                        <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                    <div class="col-sm-6">
                         {!!Form::select('to_store_location_id',getStoreLocationDropdownByBranch($row->restaurant_id), $row->to_store_location_id, ['class' => 'form-control ','id'=>'to_store_location_id'  ])!!} 
                    </div>
                </div>
            </div>
                     </div>
            <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Customer</label>
                    <div class="col-sm-6">
                        {!! Form::text('customer', null, ['maxlength'=>'255','placeholder' => 'Customer','id'=>'customer', 'required'=>true, 'class'=>'form-control','disabled'=>true]) !!}  
                      <span id = "error_msg_customer"></span>

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
                             
                          
                            <div class="col-md-12 no-padding-h">
                           <h3 class="box-title"> Transfer Line</h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th>S.No.</th>
                                      <th>Item Category</th>
                                      <th>Item No</th>
                                      <th>Description</th>
                                      <th>UOM</th>
                                      <th>Qty Req</th>
                                      <th> Cost</th>
                                      <th>Total Cost</th>
                                      <th>VAT Rate</th>
                                      <th> VAT Amount</th>
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
                                         <td >{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>


                                      
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->title }}</td>
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>


                                       



                                        <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->standard_cost }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td>
                                        <td >{{ $getRelatedItem->note }}</td>
                                        <td class = "action_crud">
                                               
                                                   

                                                   

                                                  

                                                 

                                                    <span>

                                                      <span>
                                                    <a title="Trash" href="{{ route('transfers.items.delete',[$row->transfer_no,$getRelatedItem->id])}}" ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                    </span>

                                                    <span>
                                                    <a class="left-padding-small" data-href="{!! route('transfers.editPurchaseItem',[ $transfer_no,$getRelatedItem->id]) !!}" onclick="editRequisitionItem('{!! route('transfers.editPurchaseItem',[ $transfer_no,$getRelatedItem->id]) !!}')"  data-toggle="modal" data-target="#edit-Requisition-Item-Model" data-dismiss="modal" ><i class="fa fa-edit" style="color: #444; cursor: pointer;" title="Edit"></i></a></span>
                                                  
                                                  
                                                   





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
                                          <td class="align_float_right">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td>
                                           <td></td>
                                           <td></td>
                                        </tr>

                                      @else
                                        <tr>
                                          <td colspan="13">Do not have any item in list.</td>
                                      
                                        </tr>
                                    @endif
                                       
                        
                                   


                                    </tbody>
                                </table>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             

                              <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addRequisitionItemModel">Add Multiple Item To Transfer</button>
                            <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addManualItemModel" onclick="getmanualitemlist()">Manual Entry</button>
                              </span></div>

                               <div class="col-md-3"></div>
                              <div class="col-md-3">
                              @if($row->status == 'PENDING' && $row->getRelatedItem && count($row->getRelatedItem)>0)
                                 <a href = "{{ route('transfers.processTransfer',$transfer_no)}}" style="float: right;" class= "btn btn-success btn-lg" >Process Transfer</a>
                                 @endif
                              </div>
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
        <h4 class="modal-title">Add Item To Transfer</h4>
      </div>
      <div class="modal-body">
        {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

            
               {!! Form::hidden('transfer_no', $transfer_no, []) !!}  
                {!! Form::hidden('transfer_date', $transfer_date, []) !!} 

               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Category</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),null, ['maxlength'=>'255','placeholder' => 'Please select category', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'inventory_category']) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                     <div class="col-sm-12">
						<div id="itemsList">
						</div>
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                       <div class="box-footer">
                <button type="submit" class="btn btn-primary">Add</button>
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
<div id="addManualItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 60%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Multiple Item To Transfer</h4>
      </div>
      <div class="modal-body">


      



        {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

            
               {!! Form::hidden('transfer_no', $transfer_no, []) !!}  
                {!! Form::hidden('type', 'manual_item', []) !!} 
                {!! Form::hidden('transfer_date', $transfer_date, []) !!}  
             <div class="box-body">
                <div class="form-group">
                     <div class="col-sm-12">
						<div id="manualitemsList">
						</div>
                    </div>
                </div>
            </div>

              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"></label>
                    <div class="col-sm-9">
                       <div class="box-footer">
                <button type="button" class="btn btn-warning" onclick="addmoremanualitemlist()">Add More Items</button>
                <button type="submit" class="btn btn-primary" class="addItemByCreate">Save</button>
                
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
    height: 80px !important;
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

    $(function () {
        $(".mlselec6t_modal").select2({dropdownParent: $('.modal')});
    });


/*

     $("#inventory_category").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
       
        var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);

    });
*/

     $("#item").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
        var selected_item_id = $("#item").val();
       
        getItemDetails(selected_item_id);

    });

    function getItemDetails(selected_item_id)
   {
   
    
        if(selected_item_id != "")
        {
            jQuery.ajax({
                url: '{{route('external-requisitions.items.detail')}}',
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

                  
                }
            });
        }
       
   }

      function manageitem(selected_inventory_category)
   {
   
        if(selected_inventory_category != "")
        {
            jQuery.ajax({
                url: '{{route('external-requisitions.items')}}',
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





     $("#inventory_category").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
       $("#standard_cost").val('');
        $("#prev_standard_cost").val('');
        $("#tax_value").val('');

       
       
        var selected_inventory_category = $("#inventory_category").val();
        manageitemlist(selected_inventory_category);

    });



   function manageitemlist(selected_inventory_category){
   
        if(selected_inventory_category != "")
        {
            jQuery.ajax({
                url: '{{route('purchase-orders.itemsList')}}',
                type: 'POST',
                data:{selected_inventory_category:selected_inventory_category},
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#itemsList").val('');
                    $("#itemsList").html(response);
                }
            });
        }
        else
        {
           $("#itemsList").val('');
           $("#itemsList").html('<h5>Data not found.</h5>');
        }
   }




   function getmanualitemlist(){
 
            jQuery.ajax({
                url: '{{route('transfers.getManualItemsList')}}',
                type: 'POST',
                 headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                    $("#manualitemsList").val('');
                    $("#manualitemsList").html(response);
                }
            });
       
   }


   function addmoremanualitemlist(){
 
            jQuery.ajax({
                url: '{{route('transfers.getManualItemsList')}}',
                type: 'POST',
                 headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                     $("#manualitemsList").append(response);
                }
            });
       
   }
  
  

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


