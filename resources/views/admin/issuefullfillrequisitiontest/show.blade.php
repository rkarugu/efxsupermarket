@extends('layouts.admin.admin')
@section('content')
{!! Form::model($row, ['method' => 'PATCH','route' => [$model.'.update', $row->slug],'class'=>'','enctype'=>'multipart/form-data']) !!}
{{ csrf_field() }}
@if(Request::url() != URL::previous())
<a href="{!! URL::previous() !!}" class="btn btn-primary">Back</a>
@endif
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
        @include('message')


        <?php

        $getLoggeduserProfile = getLoggeduserProfile();
        $requisition_no = $row->requisition_no;
        $default_branch_id = $row->restaurant_id;
        $default_department_id = $row->wa_department_id;
        $requisition_date = $row->requisition_date;
        $getLoggeduserProfileName = $row->getrelatedEmployee->name;

        $default_wa_location_and_store_id = $row->wa_location_and_store_id;
        $default_to_store_id = $row->to_store_id;
        ?>



        <div class = "row">

            <div class = "col-sm-6">
                <div class = "row">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Invoice No.</label>
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
                            <label for="inputEmail3" class="col-sm-5 control-label">Invoice Date</label>
                            <div class="col-sm-7">
                                {!! Form::text('purchase_date', $requisition_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                            </div>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Route</label>
                            <div class="col-sm-7">
                                {!!Form::select('route_id',$route, NULL, ['class' => 'form-control ','id'=>'route_id'  ])!!} 
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Shift ID</label>
                            <div class="col-sm-7">
                                <span id = "shift_id"></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">No. of Invoices</label>
                            <div class="col-sm-7">
                                <span id = "invoices"></span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Invoices Total</label>
                            <div class="col-sm-7">
                                <span id = "invoicesItems"></span>
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
                            <label for="inputEmail3" class="col-sm-5 control-label">Salesman</label>
                            <div class="col-sm-6">
                                {!!Form::select('to_store_id',getStoreLocationDropdownByBranch($row->restaurant_id), $default_to_store_id, ['class' => 'form-control ','id'=>'to_store_id' ,'disabled'=>true ])!!} 
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Customer</label>
                            <div class="col-sm-6">
                               <span class="form-control">{{@$row->customer}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Create New Shift ID</label>
                            <div class="col-sm-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        <input type="checkbox" class="form-check-input" name="create_new_shift" id="create_new_shift" value="1">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">Route Customer Name</label>
                            <div class="col-sm-6">
                                <div class="form-check">
                                    <label class="form-check-label">
                                        {{ @$row->getRouteCustomer->name }}  

                                        <input type="hidden" value="{{@$row->getRouteCustomer->id}}" name="wa_route_customer_id">
                                    </label>
                                </div>
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


            <div class="col-md-12 no-padding-h">
                <h3 class="box-title"> Invoice Line</h3>

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
                                <th>Qty Issued</th>
                                <th> Price</th>
                                <th>Total Price</th>
                                <th>VAT Rate</th>
                                <th> VAT Amount</th>
                               

                                <th>Total Price In VAT</th>
                                <th>Note</th>

                            </tr>
                        </thead>
                        <tbody>

                            @if($row->getRelatedItem && count($row->getRelatedItem)>0)
                            <?php
                            $i = 1;
                            $total_with_vat_arr = [];
                            ?>
                            @foreach($row->getRelatedItem as $getRelatedItem)
                            <input type ="hidden" name = "related_item_ids[]" value = "{{ $getRelatedItem->id }}" >
                            <input type="hidden" name="standard_cost" value="{{$getRelatedItem->standard_cost}}">
                            <tr>
                                <td >{{ $i }}</td>
                                <td >{{ @$getRelatedItem->getInventoryItemDetail->getInventoryCategoryDetail->category_description  }}</td>



                                <td >{{ @$getRelatedItem->getInventoryItemDetail->stock_id_code }}</td>
                                <td >{{ @$getRelatedItem->getInventoryItemDetail->title }}</td>
                                <td >{{ @$getRelatedItem->getInventoryItemDetail->getUnitOfMeausureDetail->title }}</td>






                                <td class="align_float_right">{{ $getRelatedItem->quantity }}</td>
                                <td>

                                    {!! Form::number('delivered_quantity_'.$getRelatedItem->id,  $getRelatedItem->quantity , ['required'=>'required', 'min'=>'0','max'=> $getRelatedItem->quantity,'class'=>'form-control delivered_quantity','id'=>'delivered_quantity_'.$getRelatedItem->id,'data'=>$getRelatedItem->id]) !!}  
                                </td>
                                
                                <td class="align_float_right">{{ $getRelatedItem->selling_price }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->total_cost }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->vat_rate }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->vat_amount }}</td>
                                <td class="align_float_right">{{ $getRelatedItem->total_cost_with_vat }}</td>
                                <td >{{ $getRelatedItem->note }}</td>

                            </tr>
                            <?php
                            $i++;

                            $total_with_vat_arr[] = $getRelatedItem->total_cost_with_vat;
                            ?>

                            @endforeach

                            <tr id = "last_total_row" >
                                <td colspan="7">  
                                    @if($getLoggeduserProfile->upload_data == 1 || $getLoggeduserProfile->role_id == 1)
                                    <div>
                                      <label for="test">Upload Data</label>
                                     <div style="display: flex">
                                      <input type="file" style="width: 80%" name="upload_data[]" id="upload_data" class="form-control" multiple accept="text/plain">
                                      <button type="button" style="width: 20%" class="btn btn-primary btnUploadData">Upload</button>
                                     </div>
                                      <br>
                                    </div>
                                      @else
                                      <label for="test">&nbsp;</label>
                                      @endif
                                    <button type= "submit" class="btn btn-success convirmInvoice">Process</button>
                                </td>
                               
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
                <h4 class="modal-title">Add Item To Invoice</h4>
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
    #requisitionitemtable input[type=number]{
        width:100px;

    } 
    .align_float_right
    {
        text-align:  right;
    }

</style>
@endsection

@section('uniquepagescript')
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
  <script type="text/javascript">

    function addCommas(nStr){
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        var test = x1 + x2;
        return test;
    }
    
    var num=10000;
    
    var newnum =  num.toFixed(2)
    //alert(addCommas(newnum));


var form = new Form();
$(document).on('click','.btnUploadData',function(e){
  e.preventDefault();
  $('#loader-on').show();
  var postData = new FormData();

  var url = $(this).parents('form').attr('action');
  postData.append('_token',$(document).find('input[name="_token"]').val());
  $.each($('#upload_data')[0].files, function (indexInArray, valueOfElement) { 
    postData.append('upload_data['+indexInArray+']',$('#upload_data')[0].files[indexInArray]);
  });
  $.ajax({
    type: "POST",
    url: "{{route('pos-cash-sales.esd_upload')}}",
    data: postData,
    contentType: false,
        cache: false,
        processData: false,
    success: function (response) {
      $('#loader-on').hide();
      $('#upload_data').replaceWith('<input type="file" style="width: 80%" name="upload_data[]" id="upload_data" class="form-control" multiple accept="text/plain">');
      if(response.result === -1) {
                form.errorMessage(response.message);
            }else{
              form.successMessage(response.message);
            }
    }
  });
});

$(document).ready(function(){
    var myval = $('#to_store_id').val();
    $.ajax({
      type: "get",
      url: "{{route('sales-invoice.getsalesmanroute')}}",
      data: {
        'id':myval
      },
      success: function (response) {
        if(response.result){
          if(response.result === -1){
            form.errorMessage(response.message);				
            $('#shift_id').html('').removeClass('form-control');	
            $('#invoices').html('').removeClass('form-control');	
            $('#invoicesItems').html('').removeClass('form-control');	
          }else{
            $('#shift_id').html(response.shift_id).addClass('form-control');
            $('#invoices').html(response.invoices).addClass('form-control');
            $('#invoicesItems').html(response.invoicesItems).addClass('form-control');
          }
        }
      }
    });
  });

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


if (selected_item_id != "")
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

if (selected_inventory_category != "")
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



$(document).on('click','.convirmInvoice',function(e){
    e.preventDefault();
    $('#loader-on').show();
    var postData = new FormData($(this).parents('form')[0]);
    var url = $(this).parents('form').attr('action');



    postData.append('_token',$(document).find('input[name="_token"]').val());
    //postData.append('request_type',$(this).val());
    var $this = $(this);
    $.ajax({
        url:url,
        data:postData,
        contentType: false,
        cache: false,
        processData: false,
        method:'POST',
        success:function(out){

            //console.log(out);
            $('#loader-on').hide();

            $(".remove_error").remove();

            console.log(out);

            if(out.result == 0) {
                for(let i in out.errors) {
                    var id = i.split(".");
                    if(id && id[1]){
                        $("[name='"+id[0]+"["+id[1]+"]']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                    }else
                    {
                        $("[name='"+i+"']").parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                        $("."+i).parent().append('<label class="error d-block remove_error w-100" id="'+i+'_error">'+out.errors[i][0]+'</label>');
                    }
                }
            }
            if(out.result === 1) {
                  
                ///var requestData=JSON.stringify(out.data);
                //console.log(out.data);
                var api_response=sendInvoiceRequestApi(out.data);
                
                //console.log('api_response',api_response);

                form.successMessage(out.message);
                if(out.location){
                    setTimeout(() => {
                        location.href = out.location;
                    }, 4000);
                }
            }
            if(out.result === -1) {
                form.errorMessage(out.message);
            }
    },
    
    error:function(err)
    {
      $('#loader-on').hide();
        $(".remove_error").remove();
        form.errorMessage('Something went wrong');                          
    }
});
    });



function sendInvoiceRequestApi(request_json){
    var myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");
    myHeaders.append("Content-Type", "application/json");
    myHeaders.append("Authorization", "Basic ZxZoaZMUQbUJDljA7kTExQ==");

    /*var raw = JSON.stringify({
      "invoice_date": "25_02_2022",
      "invoice_number": "2502202211344",
      "invoice_pin": "P051201909L",
      "customer_pin": "P051241778C", // optional
      "customer_exid": "", // tax exception number
      "grand_total": "219.50",
      "net_subtotal": "193.92",
      "tax_total": "25.58",
      "net_discount_total": "0",
      "sel_currency": "KSH",
      "rel_doc_number": "",
      "items_list": [
        "FLOURWHEATEXE2KG 1.50 50.00 75.00", // description quantity cost quantity total
        "SUGARNZOIA50KG 1.00 100.00 100.00",
        "0001.13.09 SPIRITTYPEJETFUEL 1.00 19.50 19.50", // hscode description quantity cost quantity total
        "0039.11.16 SOMEZERORATEDITEM 1.00 15.00 15.00",
        "0001.11.00 EXEMPTITEM 1.00 10.00 10.00"
      ]
    });*/



    var raw = JSON.stringify(request_json);
    var requestOptions = {
      method: 'POST',
      headers: myHeaders,
      body: raw,
      redirect: 'follow'
    };

    var esd_url="{{$esd_url}}";
    
    console.log(raw);
    console.log(esd_url+"/api/sign?invoice+1");

    //var apiUrl="{{url('test/')}}";
    //console.log(testUrl); 

    // fetch("http://localhost:8089/api/sign?invoice+1", requestOptions) // url stored in db where it can be changed
    
    fetch(esd_url+"/api/sign?invoice+1", requestOptions) // url stored in db where it can be changed
      .then(response => response.text())
      .then(result => {
        // response was successful
        //console.log(result);
        var successval=0;    
        var save_esd_url ="{{route('confirm-invoice.save_esd')}}";
        //console.log('save_esd_url',save_esd_url);

        $.ajax({
            url:save_esd_url,
            data: {"apiData":result,status:1,"_token":"{{csrf_token()}}"},
            method:'POST',
            async:false,
            success:function(res){
                successval=1;
            }, error: function() {
                successval=0;
            }
        });        

        //console.log(successval)

        return successval;
        //new QRCode(document.getElementById("qrcode"), result);

      })
      .catch(error =>{
        console.log('error', error)
            var save_esd_url ="{{route('confirm-invoice.save_esd')}}";
            $.ajax({
                url:save_esd_url,
                data: {status:0,error:error,invoice_number:request_json.invoice_number,"_token":"{{csrf_token()}}"},
                method:'POST',
                async:false,
                success:function(res){
                    successval=0;
                }
            });


             return successval;
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


