
@extends('layouts.admin.admin')
@section('content')
    @if(Request::url() != URL::previous())
        <a href="{!! URL::previous() !!}" class="btn btn-primary">Back</a>
    @endif
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

             <?php 
                    $requisition_no = $row->requisition_no;
                    $default_branch_id = $row->restaurant_id;
                    $default_department_id = $row->wa_department_id;
                    $requisition_date = $row->requisition_date;
                    $getLoggeduserProfileName =  $row->getrelatedEmployee->name;
                    $default_wa_location_and_store_id = $row->wa_location_and_store_id;
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
                                <label for="inputEmail3" class="col-sm-5 control-label">Customer</label>
                                <div class="col-sm-6">
                                    {!!Form::text('customer',$row->name, ['class' => 'form-control ','id'=>'to_store_id' ,'disabled'=>true ])!!}
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
                           <h3 class="box-title"> Requisition Line</h3>

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
                                      <th>Discount</th>
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
                                        <tr>
                                        <td >{{ $i }}</td>
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>


                                      
                                         <td >{{ $getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->title }}</td>
                                         <td >{{ $getRelatedItem->uom}}</td>

                                        <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->standard_cost }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->discount }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td>
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
                                            <td></td>
                                         <td></td>
                                          <td></td>
                                           <td></td>
                                            <td></td>
                                         <td></td>
                                         <td></td>
                                          <td class="align_float_right">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td>
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


  @if($row->getRelatedAuthorizationPermissions && count($row->getRelatedAuthorizationPermissions)>0)
  <?php echo "<pre>"; print_r($row->getRelatedAuthorizationPermissions); die;?>
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
        <h4 class="modal-title">Add Item To Requisition</h4>
      </div>
      <div class="modal-body">
        {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}

            
             
               

               
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
                    <label for="inputEmail3" class="col-sm-3 control-label">Item Name</label>
                    <div class="col-sm-9">
                        {!! Form::select('wa_inventory_item_id', [],null, ['maxlength'=>'255','placeholder' => 'Please select item', 'required'=>true, 'class'=>'form-control mlselec6t','id'=>'item']) !!}  
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
                    <label for="inputEmail3" class="col-sm-3 control-label">Unit Of Measure</label>
                    <div class="col-sm-9">
                      


                         {!! Form::select('unit_of_measure', getUnitOfMeasureList(),null, ['maxlength'=>'255','placeholder' => '',  'class'=>'form-control ','id'=>'unit_of_measure','disabled'=>true]) !!}  
                    </div>
                </div>
            </div>

             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Quantity</label>
                    <div class="col-sm-9">
                        {!! Form::number('quantity', null, ['min'=>'1', 'required'=>true, 'class'=>'form-control','step'=>'1','id'=>'quantity']) !!}  
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

            

              <div class="box-footer">
                <button type="submit" class="btn btn-primary">Add</button>
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


