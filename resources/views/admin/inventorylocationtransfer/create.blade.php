
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
      <form method="POST" action="" accept-charset="UTF-8" class="" enctype="multipart/form-data" novalidate="novalidate">
            {{ csrf_field() }}
             <?php 
                    $transfer_no = getCodeWithNumberSeries('TRAN');
                    $default_branch_id = getLoggeduserProfile()->restaurant_id;
                    $default_department_id = getLoggeduserProfile()->wa_department_id;
                    $transfer_date = date('Y-m-d');


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
                        {!! Form::text('emp_name', getLoggeduserProfile()->name, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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
                         {!!Form::select('vehicle_reg_no',getVehicleRegList(), null, ['class' => 'form-control mlselec6t','id'=>'vehicle_reg_no' ,'placeholder'=>'Please select' ])!!} 
                     </div>
                </div>
            </div>
            </div>
            <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Route</label>
                    <div class="col-sm-7">
                         {!!Form::select('route',getRouteList(), null, ['class' => 'form-control mlselec6t','id'=>'route' ,'placeholder'=>'Please select' ])!!} 
                          <span id = "error_msg_route"></span>
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
                         {!!Form::select('wa_department_id',getDepartmentDropdown(getLoggeduserProfile()->restaurant_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                        <div class = "row">

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">From Store</label>
                    <div class="col-sm-6">
                         {!!Form::select('from_strore_location_id',getStoreLocationDropdownByBranch(getLoggeduserProfile()->restaurant_id), null, ['class' => 'form-control mlselec6t','id'=>'from_strore_location_id' ,'placeholder'=>'Please select' ])!!} 
                          <span id = "error_msg_from_strore_location_id"></span>
                    </div>
                </div>
            </div>
                     </div>

           <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                    <div class="col-sm-6">
                         {!!Form::select('to_store_location_id',getStoreLocationDropdownByBranch(getLoggeduserProfile()->restaurant_id), null, ['class' => 'form-control mlselec6t','id'=>'to_store_location_id' ,'placeholder'=>'Please select' ])!!} 
                          <span id = "error_msg_to_store_location_id"></span>
                    </div>
                </div>
            </div>
          </div>
            <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Customer</label>
                    <div class="col-sm-6">
                        {!! Form::text('customer', null, ['maxlength'=>'255','placeholder' => 'Customer','id'=>'customer', 'required'=>true, 'class'=>'form-control']) !!}  
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
                                       <tr>
                                      <td colspan="13">Do not have any item in list.</td>
                                      
                                    </tr>
                        
                                   


                                    </tbody>
                                </table>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                             

                              <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addRequisitionItemModel" id="addbuttonpu">Add Item To Transfer</button>
                              <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addManualItemModel" id="addbuttonpu1" onclick="getmanualitemlist()">Manual Entry</button>
                              </span></div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>


    <!-- Modal -->

<div id="addRequisitionItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 60%;">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Add Multiple Item To Transfer</h4>
      </div>
      <div class="modal-body">


      



         <form class="validate"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data" id="additemformoncreate">
            {{ csrf_field() }}

            
               {!! Form::hidden('transfer_no', $transfer_no, []) !!}  
                {!! Form::hidden('transfer_date', $transfer_date, []) !!} 
                 {!! Form::hidden('to_store_location_id', null, ['id'=>'to_store_location_id_hidden']) !!} 
                  {!! Form::hidden('from_store_location_id', null, ['id'=>'from_store_location_id_hidden']) !!} 
                 {!! Form::hidden('vehicle_reg_no', null, ['id'=>'vehicle_reg_no_hidden']) !!} 
                  {!! Form::hidden('route', null, ['id'=>'route_hidden']) !!} 
                  {!! Form::hidden('customer', null, ['id'=>'customer_hidden']) !!} 


               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Category</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_category_id', getInventoryCategoryList(),null, ['maxlength'=>'255','placeholder' => 'Please select category', 'required'=>true, 'class'=>'form-control mlselec6t_modal','id'=>'inventory_category']) !!}  
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
                <button type="submit" class="btn btn-primary" class="addItemByCreate">Add</button>
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


      



         <form class="validate"  role="form" method="POST" action="{{ route($model.'.store') }}" enctype = "multipart/form-data" id="additemformoncreate">
            {{ csrf_field() }}

            
               {!! Form::hidden('transfer_no', $transfer_no, []) !!}  
                {!! Form::hidden('type', 'manual_item', []) !!} 
                {!! Form::hidden('transfer_date', $transfer_date, []) !!} 
                 {!! Form::hidden('to_store_location_id', null, ['id'=>'to_store_location_id_hidden_m']) !!} 
                  {!! Form::hidden('from_store_location_id', null, ['id'=>'from_store_location_id_hidden_m']) !!} 
                 {!! Form::hidden('vehicle_reg_no', null, ['id'=>'vehicle_reg_no_hidden_m']) !!} 
                  {!! Form::hidden('route', null, ['id'=>'route_hidden_m']) !!} 
                  {!! Form::hidden('customer', null, ['id'=>'customer_hidden_m']) !!} 

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

$(document).ready(function(){
   
 /* if($("#from_strore_location_id").val() == null && $("#to_store_location_id").val() == null)
  {
    $("#addbuttonpu").css('display','none');
  }
  else
  {
    $("#to_store_location_id_hidden").val($("#to_store_location_id").val());
     $("#from_store_location_id_hidden").val($("#from_strore_location_id").val());
     checkAddItemAvailavility();
  }*/

 
});


  $('#addbuttonpu').click(function() {
	      $("#customer_hidden").val($("#customer").val());
     var to_store_location_id = $("#to_store_location_id").val();
       var from_strore_location_id = $("#from_strore_location_id").val();
       var route = $("#route").val();
       var customer = $("#customer").val();
       $(".error_msg").remove();
       if(!from_strore_location_id || !to_store_location_id || !route || !customer)
       {
        if(!from_strore_location_id)
        {
          $("#error_msg_from_strore_location_id").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }
         if(!to_store_location_id)
        {

          $("#error_msg_to_store_location_id").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }
         if(!route)
        {

          $("#error_msg_route").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }
         if(!customer)
        {

          $("#error_msg_customer").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }        
        return false;
       }
 
});


  $('#addbuttonpu1').click(function() {
     var to_store_location_id = $("#to_store_location_id").val();
       var from_strore_location_id = $("#from_strore_location_id").val();
       var route = $("#route").val();
       var customer = $("#customer").val();
       $(".error_msg").remove();
       if(!from_strore_location_id || !to_store_location_id || !route || !customer)
       {
        if(!from_strore_location_id)
        {
          $("#error_msg_from_strore_location_id").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }
         if(!to_store_location_id)
        {

          $("#error_msg_to_store_location_id").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }
         if(!route)
        {

          $("#error_msg_route").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }
         if(!customer)
        {

          $("#error_msg_customer").append('<span class="error_msg" style="color:red;font-weight:700;">This field is required.</span>');
        }        
        return false;
       }
 
});



  $("#to_store_location_id").change(function(){
     $("#to_store_location_id_hidden").val($(this).val());
     $("#to_store_location_id_hidden_m").val($(this).val());
      checkAddItemAvailavility();
  });

   $("#from_strore_location_id").change(function(){
     $("#from_store_location_id_hidden").val($(this).val());
     $("#from_store_location_id_hidden_m").val($(this).val());
      checkAddItemAvailavility();
  });

  $("#vehicle_reg_no").change(function(){
     $("#vehicle_reg_no_hidden").val($(this).val());
     $("#vehicle_reg_no_hidden_m").val($(this).val());
      checkAddItemAvailavility();
  });

   $("#route").change(function(){
     $("#route_hidden").val($(this).val());
     $("#route_hidden_m").val($(this).val());
      checkAddItemAvailavility();
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
                  //$("#quantity").attr('min',obj.minimum_order_quantity);

                  
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

   function checkAddItemAvailavility()
   {


    if($("#from_strore_location_id").val() == $("#to_store_location_id").val() )
    {
   
     $("#addbuttonpu").css('display','none');
    }
    else
    {
       $("#addbuttonpu").css('display','');
    }
   }


    $('#additemformoncreate').submit(function () {
      $(".error_qty").html('');
       if($(this).valid()) 
       {
          var myresponse = false;

            var item_id = $("#item_no").val();
             var quantity = $("#quantity").val();
             var from_strore_location_id = $("#from_strore_location_id").val();

            
            jQuery.ajax({
                url: '{{route('transfers.checkQuantity')}}',
                type: 'POST',
                data:{item_id:item_id,quantity:quantity,from_strore_location_id:from_strore_location_id},
                async:false,
                headers: {
                'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) 
                {
                 if(response == '1')
                 {
                  
                    myresponse = true;
                 }
                 else
                 {
                   $(".error_qty").html('Invalid quantity');
                 }
                
                 
                   
                   

                }
            });

            if(myresponse == false)
            {
              return false;
            }




            
           


       }
       else
       {
        return false;
       }

     
    });




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
	      $("#customer_hidden_m").val($("#customer").val());

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


