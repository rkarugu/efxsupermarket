
@extends('layouts.admin.admin')
@section('content')
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
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
                                <span class="form-control">
                                {{@$row->getDepartment->department_name}}
                                </span> 
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
                             
                          
                            <div class="col-md-12 no-padding-h table-responsive">
                           <h3 class="box-title"> Purchase Order Line</h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th>Type</th>
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
                                          <td >{{ $getRelatedItem->item_type }}</td>
  
  
                                          @if($getRelatedItem->item_type == 'Stock')
                                           <td >{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->title }}</td>
                                           @else
                                           <td>{{ @$getRelatedItem->getNonStockItemDetail->gl_code->account_name  }}</td>
                                           <td>{{$getRelatedItem->item_no}}</td>
                                           <td>{{@$getRelatedItem->getNonStockItemDetail->item_description}}</td>
                                           @endif
                                           <td >{{ @$getRelatedItem->getSupplierUomDetail->title }}</td>
  
  
                                              <td class="align_float_right">{{ $getRelatedItem->supplier_quantity }}</td>
  
  
                                              @if($getRelatedItem->item_type == 'Stock')
  
  
                                           <td >{{ $getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>
                                           @else
                                           <td>{{ @$getRelatedItem->getSupplierUomDetail->title }}</td>
                                           @endif
                                            <td class="align_float_right">{{ $getRelatedItem->unit_conversion }}</td>
  
  
                                         
  
  
  
                                          <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->order_price }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                          <td class="align_float_right">{{ $getRelatedItem->round_off }}</td>
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
                                             <td></td>
                                               <td></td>
                                            <td class="align_float_right">{{ manageAmountFormat(array_sum($total_with_vat_arr))}}</td>
                                             <td></td>
                                            
                                          </tr>
  
                                        @else
                                          <tr>
                                            <td colspan="15">Do not have any item in list.</td>
                                        
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
                                    $rendered = [];
                                      ?>
                                    @foreach($row->getRelatedAuthorizationPermissions as $permissionResponse)
                                    @if(!in_array($permissionResponse->getExternalAuthorizerProfile->id,$rendered))
                                    <?php 
                                    $rendered[] = $permissionResponse->getExternalAuthorizerProfile->id;

                                    ?>
                                      <tr>
                                      <td>{{ $p }}</td>
                                      <td>{{ $permissionResponse->getExternalAuthorizerProfile->name}}</td>
                                      <td>{{ $permissionResponse->approve_level}}</td>
                                      <td>{{ $permissionResponse->note }}</td>
                                      <td>{{ $permissionResponse->status=='NEW'?'PROCESSING':$permissionResponse->status }}</td>
                                      </tr>
                                      <?php $p++; ?>
                                      @endif
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

@endsection


