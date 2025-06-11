
@extends('layouts.admin.admin')
@section('content')
 {!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->id],'class'=>'validate','enctype'=>'multipart/form-data']) !!}
            {{ csrf_field() }}
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
        

             <?php 
                    $purchase_no = $row->parent->purchase_no;
                    $default_branch_id = $row->parent->restaurant_id;
                    $default_department_id = $row->parent->wa_department_id;
                      $purchase_date = $row->parent->purchase_date;
                       $getLoggeduserProfileName =  $row->parent->getrelatedEmployee->name;

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
                          <label for="inputEmail3" class="col-sm-5 control-label">Return No.</label>
                          <div class="col-sm-7">                   
                              {!! Form::text('return_no',  $row->return_no , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                          </div>
                      </div>
                    </div>
                  </div>

                  <div class = "row">
                    <div class="box-body">
                      <div class="form-group">
                          <label for="inputEmail3" class="col-sm-5 control-label">Returned At.</label>
                          <div class="col-sm-7">                   
                              {!! Form::text('returned_at',  $row->returned_at , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                          </div>
                      </div>
                    </div>
                  </div>
                  <div class = "row">
                    <div class="box-body">
                      <div class="form-group">
                          <label for="inputEmail3" class="col-sm-5 control-label">Returned BY.</label>
                          <div class="col-sm-7">                   
                              {!! Form::text('returned_by',  $row->return_initiator->name , ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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
                          <label for="inputEmail3" class="col-sm-5 control-label">Vehicle Reg No</label>
                          <div class="col-sm-7">
                              {!!Form::text('vehicle_reg_no',null, ['class' => 'form-control','disabled'=>true,'id'=>'vehicle_reg_no'])!!} 
                          </div>
                      </div>
                    </div>
                  </div>   
                  <div class = "row">
                      <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">CU Invoice Number</label>
                            <div class="col-sm-7">
                                {!!Form::text('cu_invoice_number',null, ['class' => 'form-control','disabled'=>true,'id'=>'cu_invoice_number'])!!} 
                            </div>
                        </div>
                      </div>
                    </div>   
                    <div class = "row">
                      <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Notes</label>
                            <div class="col-sm-7">
                                {!!Form::text('note',null, ['class' => 'form-control','disabled'=>true,'id'=>'notes'])!!} 
                            </div>
                        </div>
                      </div>
                    </div>   
                    <div class = "row">
                      <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Documents: </label>
                            <div class="col-sm-7">
                            <a class="btn btn-warning btn-sm" data-toggle="modal" href='#modal-id'>Check Docs</a>

                            </div>
                        </div>
                      </div>
                    </div>   
                    
                    <div class="modal fade" id="modal-id">
                    <div class="modal-dialog">
                                                          <div class="modal-content">
                                                            <div class="modal-header">
                                                              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                              <h4 class="modal-title">Download Documents</h4>
                                                            </div>
                                                            <div class="modal-body">
                                                              <table class="table table-hover">

                                                              @php
                                                              $files = (array)json_decode($row->documents);
                                                              @endphp
                                                              @if(count($files)>0)
                                                              @foreach($files as $key => $val)
                                                              <tr>
                                                                <th>
                                                                  {{strtoupper(str_replace("_", " ", $key))}}
                                                                </th>
                                                                <td>
                                                                  <a target="_blank" href="{{ asset('uploads/purchases_docs/'.@$val) }}">Download</a>
                                                                </td>
                                                              </tr>
                                                              @endforeach
                                                              @endif
                                                              </table>

                                                            </div>
                                                            <div class="modal-footer">
                                                              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
                          <label for="inputEmail3" class="col-sm-5 control-label">Credit Note Document</label>
                          <div class="col-sm-6">
                              <a href="{{env('SUPPLIER_PORTAL_URI')}}/{{$row->credit_note_doc}}" target="_blank">Download</a>
                          </div>
                      </div>
                    </div>
                  </div>
                    <div class = "row">
                      <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Return Status</label>
                            <div class="col-sm-6">
                                {!!Form::text('return_status', $row->return_status, ['class' => 'form-control ','required'=>true,'readonly'=>true  ])!!} 
                            </div>
                        </div>
                      </div>
                    </div>
                    <div class = "row">
                      <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Return Comment</label>
                            <div class="col-sm-6">
                                {!!Form::text('return_reason', $row->return_reason, ['class' => 'form-control ','required'=>true ,'readonly'=>true ])!!} 
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
                            <label for="inputEmail3" class="col-sm-5 control-label">Store location</label>
                            <div class="col-sm-6">
                                {!!Form::select('wa_location_and_store_id',getStoreLocationDropdown(),null, ['class' => 'form-control store_location_id mlselec6t','required'=>true,'id'=>'wa_location_and_store_id', 'disabled'=>true])!!} 
                            </div>
                        </div>
                      </div>
                    </div>     
                    <div class = "row">
                            <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Bin Location</label>
                                <div class="col-sm-6">
                                    {!!Form::select('wa_unit_of_measures_id', getUnitOfMeasureList(), $row->wa_unit_of_measures_id, ['class' => 'form-control wa_unit_of_measures_id','disabled'=>true,'placeholder' => 'Please select bin location' ])!!} 
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
                            <label for="inputEmail3" class="col-sm-5 control-label">Delivery Note</label>
                            <div class="col-sm-6">
                                {!!Form::text('receive_note_doc_no',null, ['class' => 'form-control','disabled'=>true,'id'=>'receive_note_doc_no'])!!} 
                            </div>
                        </div>
                      </div>
                    </div>         
                    <div class = "row">
                      <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Supplier Invoice No.</label>
                            <div class="col-sm-6">
                                {!!Form::text('supplier_invoice_no',null, ['class' => 'form-control','disabled'=>true,'id'=>'supplier_invoice_no'])!!} 
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
                                      <th>S.No.</th>
                                      <th>Item Category</th>
                                      <th>Item No</th>
                                      <th>Description</th>
                                      <th>Bin Location</th>
                                      <th>This Delivery QTY</th>    
                                      <th>Return QTY</th>
                                      <th>Return Reason</th>
                                      <th>System Qty</th>
                                      <th>Incl Price</th>
                                      <th>Supplier Discount %</th>
                                      <th>Discount Amount</th>
                                      <th>Exclusive</th>
                                      <th>VAT Rate</th>
                                      <th>VAT</th>
                                      <th>Total</th>       
                                    </tr>
                                    </thead>
                                    <tbody>

                                    @if($row->child_items && count($row->child_items)>0)
                                      <?php $i=1;
                                      $total_with_vat_arr = [];
                                      ?>
                                        @foreach($row->child_items as $getRelatedItem)
                                        @if ($getRelatedItem->delivered_quantity > 0)
                                            
                                        <span id = "{{ $getRelatedItem->id }}" class= "rendered_id"></span>

                                        <input type ="hidden" name = "purchase_order_ids[]" value = "{{ $getRelatedItem->id }}" >
                                        <tr>
                                                                               <td >{{ $i }}</td>
                                         <td >{{ @$getRelatedItem->parent->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>


                                      
                                         <td >{{ $getRelatedItem->parent->getInventoryItemDetail->stock_id_code }}</td>
                                           <td >{{ $getRelatedItem->parent->getInventoryItemDetail->title }}</td>
                                           <td >{{ @$getRelatedItem->parent->get_unit_of_measure->title }}</td>


                                            <td>

                                                {{$getRelatedItem->delivered_quantity}}
                                            </td>


                                            <td class="align_float_right">{{$getRelatedItem->return_quantity}}</td>
                                            <td class="align_float_right">{{$getRelatedItem->return_reason}}</td>





                                         {{--<td class="align_float_right">{{ @$getRelatedItem->parent->getInventoryItemDetail->location->location_name }}</td>--}}


                                       



                                        <td class="align_float_right">{{ $getRelatedItem->parent->quantity }}</td>
                                        <td class="align_float_right">
                                        

                                            {!! Form::number('order_price_'.$getRelatedItem->id,  $getRelatedItem->order_price , ['min'=>'0','class'=>'form-control order_price','id'=>'order_price_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'required'=>true,'readonly'=>true]) !!} 


                                        </td>


                                          <td>
                                       
                                           {!! Form::number('supplier_discount_'.$getRelatedItem->id,  $getRelatedItem->supplier_discount , ['max'=>100,'min'=>'0','class'=>'form-control supplier_discount','id'=>'supplier_discount_'.$getRelatedItem->id,'data'=>$getRelatedItem->id,'readonly'=>true]) !!}  


                                          </td>
                                           <td class="align_float_right" id="discount_amount_{{ $getRelatedItem->id }}">0.00</td>

                                           
                                           @php
                                           $t_price = $getRelatedItem->order_price*$getRelatedItem->delivered_quantity;
                                           $vat = $getRelatedItem->parent->vat_rate;
                                           $vat_amount = $t_price - (($t_price*100)/($vat+100));
                                           @endphp
                                           <td class="align_float_right" id="total_price_{{ $getRelatedItem->id }}">{{ $t_price }}</td>


                                        <td class="align_float_right" id="vat_rate_{{ $getRelatedItem->id }}">{{ $getRelatedItem->parent->vat_rate }}</td>
                                        <td class="align_float_right" id="vat_amount_{{ $getRelatedItem->id }}">{{ $vat_amount }}</td>
                                        <td class="align_float_right" id="total_cost_with_vat_{{ $getRelatedItem->id }}">{{ $t_price }}</td>
                                 
                                      
                                        </tr>
                                        <?php $i++;

                                        $total_with_vat_arr[] = $t_price;
                                        ?>
                                        @endif

                                        @endforeach

                                        <tr id = "last_total_row" >
                                        <td colspan="4">  
                                          @if($row->status == 'Confirmed')
                                            <input type="submit" class="btn btn-success" value="Process" name="approval_status">
                                            <input type="submit" class="btn btn-success" value="Reject" name="approval_status">
                                            @endif
                                    </td>
                                        
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
                                   
                                          
                                        </tr>

                                      @else
                                        <tr>
                                          <td colspan="14">Do not have any item in list.</td>                                      
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
                                      <td>{{ @$permissionResponse->getExternalAuthorizerProfile->name}}</td>
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script>
      $(document).ready(function() {
        function displayFileName(input, fileNameId) {
            const fileNameDisplay = $("#" + fileNameId);
            if (input.files && input.files[0]) {
                fileNameDisplay.css('display', 'block');
                fileNameDisplay.text(input.files[0].name);
            } else {
                fileNameDisplay.css('display', 'none');
                fileNameDisplay.text('');
            }
        }

        $('#delivery_noe').on('change', function() {
            displayFileName(this, 'delivery_noe_file_name');
        });

        $('#supplier_invoice').on('change', function() {
            displayFileName(this, 'supplier_invoice_file_name');
        });

        $('#other_documents').on('change', function() {
            displayFileName(this, 'other_documents_file_name');
        });
    });
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        $(function () {

        $('.wa_unit_of_measures_id').select2(
            {
                placeholder:'Select Bin Location',
                ajax: {
                    url: '{{route("uom.search_by_item_location")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        var store_location_id = $('.store_location_id option:selected').val();
                        return {
                            q: params.term,
                            id: store_location_id
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.title};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
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
        vat_amount = parseFloat(parseFloat(total_price)*parseFloat(vat_rate))/100
        $("#vat_amount_"+data_id).html(vat_amount.toFixed(2));
        total_price =  total_price;
      
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


