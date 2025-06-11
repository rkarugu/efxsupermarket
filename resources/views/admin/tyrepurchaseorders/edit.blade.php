
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
                                        <td class = "action_crud">
                                            <span>
                                            <a title="Trash" href="{{ route('tyre-purchase-orders.items.delete',[$row->purchase_no,$getRelatedItem->id])}}" ><i class="fa fa-trash" aria-hidden="true"></i>
                                            </a>
                                            </span>

                                            <span>
                                            <a class="left-padding-small" data-href="{!! route('tyre-purchase-orders.editPurchaseItem',[ $purchase_no,$getRelatedItem->id]) !!}" onclick="editRequisitionItem('{!! route('tyre-purchase-orders.editPurchaseItem',[ $purchase_no,$getRelatedItem->id]) !!}')"  data-toggle="modal" data-target="#edit-Requisition-Item-Model" data-dismiss="modal" ><i class="fa fa-edit" style="color: #444; cursor: pointer;" title="Edit"></i></a></span>
                                                  
                                                  
                                                   





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
                       


                              <div class="col-md-12"><span>
                             

                              <button type="button" class="btn btn-success" style="margin-bottom:5px" data-toggle="modal" data-target="#addRequisitionItemModel">Add Multiple Item To Purchase Order</button>
                              </span>
                              @if($row->status == 'UNAPPROVED' && $row->getRelatedItem && count($row->getRelatedItem)>0)
                                 <a href = "{{ route('tyre-purchase-orders.sendRequisitionRequest',$purchase_no)}}" style="margin-bottom:5px" class= "btn btn-success " >Send Request</a>
                                 @endif
                                 <button type="button" class="btn btn-success " data-toggle="modal" style="margin-bottom:5px" data-target="#modelId">
                                    Order Non-Stock Item
                                </button>


                               
                        </div>
                    </div>


    </section>

    <div class="modal fade" id="modelId"  role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body ">
                    <form action="{{route('addNonStockItem')}}" method="post" class="submitMe">
                        {{csrf_field()}}
                        {!! Form::hidden('purchase_no', $purchase_no, []) !!}  
                        {!! Form::hidden('purchase_no', $purchase_no, []) !!}  
                        {!! Form::hidden('id', $row->id, []) !!} 
        
                       
                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Item Description</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" name="item_description" class="form-control" placeholder="Item Description">
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>General Ledger Code</label>
                            </div>
                            <div class="col-sm-9">
                                <select  class="form-control select2Select"  name="item_gl">
                                    @foreach ($chart_of_accounts as $item)
                                    <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>OR Asset ID</label>
                            </div>
                            <div class="col-sm-5">
                                <select  class="form-control assetId select2Select" name="item_asset">
                                    <option value="" selected>Not an Asset</option>
                                    @foreach ($assets as $item)
                                        <option value="{{$item->id}}">{{$item->asset_description_short}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-4 text-left" style="padding-top:5px;">
                                <a href="#" data-toggle="modal" data-target="#newAsset">New Fixed Asset</a>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Quantity To purchase</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="item_quantity" placeholder="Quantity To purchase">
                            </div>
                        </div>
                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Price Per Item</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" name="item_price" placeholder="Price Per Item">
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Unit</label>
                            </div>
                            <div class="col-sm-9">
                                <select  class="form-control select2Select" name="item_unit">
                                    @foreach ($units as $item)
                                    <option value="{{$item->id}}">{{$item->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Vat Category</label>
                            </div>
                            <div class="col-sm-9">
                                <select  class="form-control select2Select" name="item_vat">
                                    @foreach ($vats as $item)
                                    <option value="{{$item->id}}">{{$item->tax_value}}% - {{$item->title}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Price Inclusive of VAT?</label>
                            </div>
                            <div class="col-sm-9 text-left">
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="item_inclusive_vat"  value="Yes" checked>
                                    Yes
                                </label>
                                <label class="form-check-label">
                                    <input type="radio" class="form-check-input" name="item_inclusive_vat"  value="No">
                                    No
                                </label>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom:10px">
                            <div class="col-sm-3 " style="padding-top:5px;">
                                <label>Delivery Date</label>
                            </div>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" name="item_delivery_date">
                            </div>
                        </div>
                        <br>
                        <button type="submit" class="btn btn-primary">Save</button>                                                
                    </form>
                </div>
            </div>
        </div>
    </div>
<!-- Modal -->
<!-- Button trigger modal -->
<!-- Modal -->
<div class="modal fade" id="newAsset"  role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">New Asset</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
        <div class="modal-body">
            <form action="{{route('addAsset')}}"  method="post"  data-modal="newAsset"  class="addAssetParts">
                {{csrf_field()}}
                <input type="hidden" name="modal" value="assetId">
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Asset Description (Short):</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="asset_description_short">
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Asset Description (Long):</label>
                </div>
                <div class="col-sm-9">
                    <textarea type="text" class="form-control" name="asset_description_long"></textarea>
                </div>
            </div>

            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Asset Category:</label>
                </div>
                <div class="col-sm-5">
                    <select  class="form-control assetCategory select2Select" name="wa_asset_categorie_id">
                        @foreach ($asset_category as $item)
                        <option value="{{$item->id}}">{{$item->category_code}} - {{$item->category_description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 text-left" style="padding-top:5px;">
                    <a href="#" data-toggle="modal" data-target="#newAssetCategory">Add Asset Category</a>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Asset Location:</label>
                </div>
                <div class="col-sm-5">
                    <select  class="form-control locationId select2Select" name="wa_asset_location_id">
                        @foreach ($asset_location as $item)
                            <option value="{{$item->id}}">{{$item->location_ID}} - {{$item->location_description}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-4 text-left" style="padding-top:5px;">
                    <a href="#" data-toggle="modal" data-target="#newAssetLocation">Add Asset Location</a>
                </div>
            </div>


            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Bar Code:</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="bar_code">
                </div>
            </div>

            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Serial Number:</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="serial_number">
                </div>
            </div>

            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Depreciation Type:</label>
                </div>
                <div class="col-sm-9">
                    <select  class="form-control select2Select" name="wa_asset_depreciation_id">
                        @foreach ($asset_depreciation as $item)
                            <option value="{{$item->id}}">{{$item->title}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Depreciation Rate:</label>
                </div>
                <div class="col-sm-5">
                    <input type="number"  class="form-control" name="depreciation_rate"> 
                </div>
                <div class="col-sm-4 text-left" style="padding-top:5px;">
                    %
                </div>
            </div>


            <br>
            <div class=""><button type="submit" class="btn btn-info float-right">Save</button></div>
            </form>
        </div>
       
    </div>
</div>
</div>

<div class="modal fade" id="newAssetCategory"  role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">New Asset Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
        <div class="modal-body">
            <form action="{{route('addAssetCategory')}}" method="post" data-modal="newAssetCategory"  class="addAssetParts">
            {{csrf_field()}}
            <input type="hidden" name="modal" value="assetCategory">

            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-5 " style="padding-top:5px;">
                    <label>Category Code:</label>
                </div>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="category_code">
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-5 " style="padding-top:5px;">
                    <label>Category Description:</label>
                </div>
                <div class="col-sm-7">
                    <input type="text" class="form-control" name="category_description">
                </div>
            </div>

            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-5 " style="padding-top:5px;">
                    <label>Fixed Asset Cost GL Code:</label>
                </div>
                <div class="col-sm-7">
                    <select  name="fixed_asset_id" class="form-control select2Select" >
                        @foreach ($profit_loss as $item)
                            <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-5 " style="padding-top:5px;">
                    <label>Profit and Loss Depreciation GL Code:</label>
                </div>
                <div class="col-sm-7">
                    <select  name="profit_loss_depreciation_id" class="form-control select2Select" >
                        @foreach ($gl as $item)
                            <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-5 " style="padding-top:5px;">
                    <label>Profit or Loss on Disposal GL Code:</label>
                </div>
                <div class="col-sm-7">
                    <select  name="profit_loss_disposal_id" class="form-control select2Select" >
                        @foreach ($gl as $item)
                            <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-5 " style="padding-top:5px;">
                    <label>Balance Sheet Accumulated Depreciation GL Code:</label>
                </div>
                <div class="col-sm-7">
                    <select  name="balance_sheet_id" class="form-control select2Select" >
                        @foreach ($profit_loss as $item)
                            <option value="{{$item->id}}">{{$item->account_code}} - {{$item->account_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <br>
            <div class=""><button type="submit" class="btn btn-info float-right">Save</button></div>
            </form>
        </div>
       
    </div>
</div>
</div>
<div class="modal fade" id="newAssetLocation"  role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">New Asset Location</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
        </div>
        <div class="modal-body">
            <form action="{{route('addAssetLocation')}}" method="post" data-modal="newAssetLocation" class="addAssetParts">
            {{csrf_field()}}
            <input type="hidden" name="modal" value="locationId">
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Location ID:</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" name="location_id" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Location Description:</label>
                </div>
                <div class="col-sm-9">
                    <input type="text" name="location_description" class="form-control">
                </div>
            </div>
            <div class="row" style="margin-bottom:10px">
                <div class="col-sm-3 " style="padding-top:5px;">
                    <label>Parent Location:</label>
                </div>
                <div class="col-sm-9">
                    <select name="location_parent" class="form-control select2Select" >
                        <option value="">Select Parent</option>
                        @foreach ($asset_location as $item)
                            <option value="{{$item->id}}">{{$item->location_ID}} - {{$item->location_description}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <br>
            <div class=""><button type="submit" class="btn btn-info float-right">Save</button></div>
            </form>
        </div>
       
    </div>
</div>
</div>
<!-- Modal -->

     <div class="modal" id="edit-Requisition-Item-Model" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      
    </div>
  </div>
</div> 

<div id="addRequisitionItemModel" class="modal fade" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: 60%;">

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

<!--
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
-->

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

    $(function () {
        $(".mlselec6t_modal").select2({dropdownParent: $('.modal')});
    });



/*
     $("#inventory_category").change(function(){
      $("#item_no").val('');
      $("#unit_of_measure").val('');
        $("#standard_cost").val('');
           $("#prev_standard_cost").val('');
           $("#tax_value").val('');
        var selected_inventory_category = $("#inventory_category").val();
        manageitem(selected_inventory_category);

    });
*/

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
                url: '{{route('tyre-purchase-orders.items.detail')}}',
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
                url: '{{route('tyre-purchase-orders.items')}}',
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
                url: '{{route('tyre-purchase-orders.itemsList')}}',
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

</script>
    <script>
        $('#view_last_price_button').click(function(){
            item_id = $('#item').val();
            jQuery.ajax({
                url: '{{route('admin.tyre-purchase-orders.view-last-purchases-price')}}',
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
    
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>
    var form = new Form();
    $('.addAssetParts').submit(function (e) { 
        e.preventDefault();
        var data = $(this).serialize();
            var url = $(this).attr('action');
            var method = $(this).attr('method');
            var $this = $(this);
            var form = new Form();

            $.ajax({
                url:url,
                method:method,
                data:data,
                success:function(out)
                {
                    $(".remove_error").remove();
                    if(out.result == 0) {
                        console.log(out.errors);
                        for(let i in out.errors) {                        
                            $this.find("[name='"+i+"']").
                            parent().
                            append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        }
                    }
                    if(out.result === 1) {
                        form.successMessage(out.message);
                        $this.find('input:not(:hidden)').val('');
                        $this.find('textarea').val('');
                        if(out.modal && out.data_id)
                        {
                            $('#'+$this.data('modal')).modal('hide');
                            $(document).find('.'+out.modal).append('<option selected value="'+out.data_id+'">'+out.data_value+'</option>');
                        }
					}
                    if(out.result === -1) {
						form.errorMessage(out.message);							
					}
                },
                error:function(err)
                {
                    $(".remove_error").remove();
                    $('.loder').css('display','none');
                    form.errorMessage('Something went wrong');							
                }
            });
    });
    $('.select2Select').parent().css('text-align','left');
    $('.select2Select').select2();
</script>
@endsection


