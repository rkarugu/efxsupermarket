
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        
             <?php 
                    $requisition_no = $row->requisition_no;
                    $default_branch_id = $row->restaurant_id;
                    $default_department_id = $row->wa_department_id;
                    $requisition_date = $row->requisition_date;
                    $getLoggeduserProfileName =  $row->getrelatedEmployee->name;

                    $default_to_store_id = $row->to_store_id;

                    ?>



              <div class = "row">

                <div class = "col-sm-6">
                  <div class = "row">
                      <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Requisition No.</label>
                    <div class="col-sm-7">

                   
                        {!! Form::text('requisition_no',  $requisition_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                  </div>

                    <div class = "row">
                       <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                    <div class="col-sm-7">
                        {!! Form::text('emp_name',$getLoggeduserProfileName, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
                  </div>

                   <div class = "row">

                      <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Requisition Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('purchase_date', $requisition_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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
                    <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                    <div class="col-sm-6">
                          {!!Form::select('to_store_id',getStoreLocationDropdownByBranch($row->restaurant_id), $default_to_store_id, ['class' => 'form-control ','id'=>'to_store_id' ,'disabled'=>true ])!!} 
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
                             
                          <div class="col-md-12 no-padding-h ">
                            <h3 class="box-title"> Items</h3>
                           <div id = "requisitionitemtable">
                                 <table class="table table-bordered table-hover" id="mainItemTable">
                                     <thead>
                                     <tr>
                                       <th>Selection</th>
                                       <th>Description</th>
                                       <th >Bal Stock</th>
                                       <th >Unit</th>
                                       <th >QTY</th>
                                       <th>Location</th>
                                     </tr>
                                     </thead>
                                     <tbody>
                                    
                                     @foreach ($row->getRelatedItem as $item)
                                   
                                      <tr>                                      
                                         <td>
                                             <input style="padding: 3px 3px;" readonly="true" type="text" class="testIn form-control" value="{{@$item->getInventoryItemDetail->stock_id_code}}">
                                         </td>
                                         <td><input style="padding: 3px 3px;" readonly="true" type="text" name="item_description[{{@$item->wa_inventory_item_id}}]" data-id="{{@$item->wa_inventory_item_id}}" class="form-control" value="{{@$item->getInventoryItemDetail->title}}"></td>
                                         <td>{{@$item->getInventoryItemDetail->getAllFromStockMovesC->where('wa_location_and_store_id',$item->store_location_id)->sum('qauntity')}}</td>
                                         <td><input style="padding: 3px 3px;" readonly="true" type="text" name="item_unit[{{@$item->wa_inventory_item_id}}]" data-id="{{@$item->wa_inventory_item_id}}" class="form-control" value="{{@$item->getInventoryItemDetail->pack_size->title}}"></td>
                                         <td><input style="padding: 3px 3px;" readonly="true"  type="number" name="item_quantity[{{@$item->wa_inventory_item_id}}]" data-id="{{@$item->wa_inventory_item_id}}" class="quantity form-control" value="{{$item->quantity}}"></td>
                                      <td><input type="hidden" name="store_location_id[{{@$item->wa_inventory_item_id}}]">{{@$item->location->location_name}}</td>
                                       
                                         </tr>
                                         @endforeach
 
 
                                     </tbody>
                                     
                                 </table>
                               </div>
                             </div>
                        

                            


                               
                        </div>
                    </div>


    </section>


  @if($row->getRelatedAuthorizationPermissions && count($row->getRelatedAuthorizationPermissions)>0)
  <?php //echo "<pre>"; print_r($row->getRelatedAuthorizationPermissions); die; ?>
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
                                      <td>{{ $permissionResponse->getInternalAuthorizerProfile->name}}</td>
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

<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
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
       
        var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);

    });

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






  

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


