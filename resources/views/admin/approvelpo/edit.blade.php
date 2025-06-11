
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
       <form method="POST" action="" accept-charset="UTF-8" class="" enctype="multipart/form-data" novalidate="novalidate">

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
                         {!!Form::select('wa_location_and_store_id',getStoreLocationDropdown(), null, ['class' => 'form-control mlselec6t','required'=>true,'id'=>'wa_location_and_store_id' ,'disabled'=>true  ])!!} 
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
                               <div class="box-body">
                <div class="form-group">
                   
                    <div class="col-sm-12">
                          @include('message')
                                  
                    </div>
                </div>
            </div>

           
                          
                            <div class="col-md-12 no-padding-h table-responsive">
                           <h3 class="box-title"> Requisition Line </h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                    <tr>
                                      <th>S.No.</th>
                                      {{--<th>Item Category</th>--}}
                                      <th>Item No</th>
                                      <th>Description</th>
                                       <th>Supplier UOM</th>
                                        <th>Supplier QTY</th>



                                      <th>QOH</th>
                                      <th>Max Stock</th>
                                       <th>Re Order Level</th>


                                      {{--<th>System Qty</th>--}}
                                      <th> Price</th>
                                      <th>Total Price</th>
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
                                         {{--<td >{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>--}}


                                      
                                         <td >{{ @$getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ @$getRelatedItem->getInventoryItemDetail->title }}</td>

                                            <td >{{ @$getRelatedItem->getInventoryItemDetail->pack_size->title }}</td>
                                            <td class="align_float_right">{{ $getRelatedItem->supplier_quantity }}</td>

                                          
                                            @php
            
                                          $stock_manage = \DB::table('wa_inventory_location_stock_status')
                                          ->where('wa_location_and_stores_id',$row->wa_location_and_store_id)
                                          ->where('wa_inventory_item_id',$getRelatedItem->getInventoryItemDetail->id)
                                          ->first();
                                          @endphp
                                          <td>{{@$getRelatedItem->getInventoryItemDetail->getAllFromStockMoves->where('wa_location_and_store_id',$row->wa_location_and_store_id)->sum('qauntity')}}</td>
                                         <td >{{@$stock_manage->max_stock}}</td>
                                          <td class="align_float_right">{{@$stock_manage->re_order_level}}</td>


                                       



                                       {{-- <td class="align_float_right">{{ $getRelatedItem->quantity }}</td> --}}
                                        <td class="align_float_right">{{ $getRelatedItem->order_price }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                        <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td>
                                        <td >{{ $getRelatedItem->note }}</td>
                                        <td class = "action_crud">
                                               
                                                   

                                                   

                                                  

                                                 

                                                    <span>

                                                      <span>
                                                    <a title="Trash" href="{{ route('approve-lpo.items.delete',[$row->purchase_no,$getRelatedItem->id])}}" ><i class="fa fa-trash" aria-hidden="true"></i>
                                                    </a>
                                                    </span>

                                                    <span>
                                                    <a class="left-padding-small" data-href="{!! route('approve-lpo.editPurchaseItem',[ $purchase_no,$getRelatedItem->id]) !!}" onclick="editRequisitionItem('{!! route('approve-lpo.editPurchaseItem',[ $purchase_no,$getRelatedItem->id]) !!}')"  data-toggle="modal" data-target="#edit-Requisition-Item-Model" data-dismiss="modal" ><i class="fa fa-edit" style="color: #444; cursor: pointer;" title="Edit"></i></a></span>
                                                  
                                                  
                                                   





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
                       

                           
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
            

            
                               <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label">Note</label>
                    <div class="col-sm-9">
                         {!! Form::textarea('authorizer_note', null, ['maxlength'=>'1000','placeholder' => 'Note', 'required'=>false, 'class'=>'form-control','id'=>'authorizer_note']) !!} 
                                  
                    </div>
                </div>
            </div>
              <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-3 control-label"></label>
                    <div class="col-sm-2">
                    <button type="submit" name="requisition_status" class="btn btn-success" value = "approve">Approve</button>          
                    </div>
                     <div class="col-sm-2">
                        <button type="submit" name="requisition_status" class="btn btn-warning" value = "reject">Reject</button>
                                  
                    </div>
                </div>
            </div>
            </form>

                             

                               
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
#authorizer_note
  {
    height: 100px !important;
  }
   #authorizer_note{
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
    

function editRequisitionItem(link)
{
  
  $('#edit-Requisition-Item-Model').find(".modal-content").load(link);
}


</script>

@endsection


