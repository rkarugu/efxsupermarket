@extends('layouts.admin.admin')
@section('content')
<form method="POST" action="{{ route($model.'.update',$row->id) }}" accept-charset="UTF-8"  enctype="multipart/form-data" >
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
         <input type="hidden" value="{{$row->id}}" name="id">
       {{method_field('PUT')}}
            {{ csrf_field() }}
             <?php 
             $user = getLoggeduserProfile();
                    $default_branch_id = $user->restaurant_id;
                    $default_department_id = $user->wa_department_id;
                    $default_wa_location_and_store_id = $row->to_store_location_id;
                    $requisition_date = $row->requisition_date;


                    ?>

            <div class = "row">

              <div class = "col-sm-6">

                   <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                    <div class="col-sm-7">
                        {!! Form::text('emp_name', $user->name, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>

                   </div>
                    <div class = "row">
                     <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Requisition Date</label>
                    <div class="col-sm-7">
                        {!! Form::text('requisition_date', $requisition_date, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
                    </div>
                </div>
            </div>
            </div>

            {{--
            <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Vehicle Registration No.</label>
                    <div class="col-sm-7">
                         {!!Form::select('vehicle_reg_no',getVehicleRegList(), $row->vehicle_reg_no, ['class' => 'form-control mlselec6t','id'=>'vehicle_reg_no' ,'placeholder'=>'Please select' ])!!} 
                     </div>
                </div>
            </div>
            </div>
            --}}
            {{-- <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Route</label>
                    <div class="col-sm-7">
                         {!!Form::select('route',getRouteList(), $row->route, ['class' => 'form-control mlselec6t','id'=>'route' ,'placeholder'=>'Please select' ])!!} 
                          <span id = "error_msg_route"></span>
                    </div>
                </div>
            </div>
            </div> --}}
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
                         {!!Form::select('wa_department_id',getDepartmentDropdown($user->restaurant_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                     </div>

                     
            @php
            $allToStore = [];
            $tostore = \DB::table('wa_location_and_stores')->get();
            @endphp
            @foreach($tostore as $t)
            @php
            $allToStore[$t->id] = $t->location_name .' ( '.$t->location_code.')';
            @endphp
            @endforeach
           <div class = "row">
             <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                    <div class="col-sm-6">
                         {!!Form::select('to_store_location_id',$allToStore, $row->to_store_location_id, ['class' => 'form-control mlselec6t','id'=>'to_store_location_id' ,'placeholder'=>'Please select' ])!!} 
                          <span id = "error_msg_to_store_location_id"></span>
                    </div>
                </div>
            </div>
                     
              {{-- <div class="box-body">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-5 control-label">Customer</label>
                    <div class="col-sm-6">
                        {!! Form::text('customer', $row->customer, ['maxlength'=>'255','placeholder' => 'Customer','id'=>'customer', 'required'=>true, 'class'=>'form-control']) !!}  
                      <span id = "error_msg_customer"></span>

                    </div>
                </div>
            </div> --}}
  
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
                             
                          
                            <div class="col-md-12 no-padding-h ">
                           <h3 class="box-title"> Purchase Order Line</h3>
                           <button type="button" class="btn btn-danger btn-sm addNewrow" style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Selection</th>
                                      <th>Description</th>
                                      <th style="width: 90px;">Unit</th>
                                      <th style="width: 90px;">QTY</th>
                                      <th>Incl. Price</th>
                                      <th>Location</th>
                                      <th>VAT Type</th>
                                      <th style="width: 90px;">Disc%</th>
                                      <th style="width: 90px;">Discount</th>
                                      <th>Exclusive</th>
                                      <th>VAT</th>
                                      <th>Total</th>
                                      <th>
                                      <!-- <button type="button" class="btn btn-danger btn-sm addNewrow "><i class="fa fa-plus" aria-hidden="true"></i></button> -->
                                      </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                      @if(count($row->getRelatedItem)>0)
                                        @foreach($row->getRelatedItem as $items)
                                      @php
                                      $uniqid = uniqid();
                                      @endphp
                                      <tr>                                      
                                        <td>
                                            <input type="hidden" name="item_id[{{$uniqid}}]" class="itemid" value="{{$items->getInventoryItemDetail->id}}">
                                            <input style="padding: 3px 3px;" type="text" class="testIn form-control" value="{{$items->getInventoryItemDetail->stock_id_code}}">
                                            <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
                                        </td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text" name="item_description[{{$uniqid}}]" data-id="{{$items->getInventoryItemDetail->id}}" class="form-control" value="{{@$items->getInventoryItemDetail->title}}"></td>
                                        <td><input style="padding: 3px 3px;" readonly="" type="text" name="item_unit[{{$uniqid}}]" data-id="{{$items->getInventoryItemDetail->id}}" class="form-control" value="{{@$items->getInventoryItemDetail->pack_size->title}}"></td>
                                        <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)" type="text" name="item_quantity[{{$uniqid}}]" data-id="{{$items->getInventoryItemDetail->id}}" class="quantity form-control" value="{{$items->quantity}}"></td>
                                        <td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_standard_cost[{{$uniqid}}]" data-id="{{$items->getInventoryItemDetail->id}}" class="standard_cost form-control" value="{{$items->standard_cost}}"></td>
                                        <td>{{@$items->getInventoryItemDetail->location->location_name}}</td>
                                        <td>
                                          <select class="form-control vat_list" name="item_vat[{{$uniqid}}]" >
                                            <?php
                                            $per = 0;
                                            $view = '';
                                            $vat = 0.00;
                                            if($items->getInventoryItemDetail->getTaxesOfItem){
                                                $view .='<option value="'.$items->getInventoryItemDetail->getTaxesOfItem->id.'" selected>'.$items->getInventoryItemDetail->getTaxesOfItem->title.'</option>';
                                                $per = $items->getInventoryItemDetail->getTaxesOfItem->tax_value;
                                                $vat = ($items->standard_cost*$per)/100;
                                            }
                                            ?>
                                            {!! $view !!}
                                          </select>
                                          <input type="hidden" class="vat_percentage" value="{{$per}}" name="item_vat_percentage[{{$uniqid}}]">
                                        </td><td><input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_discount_per[{{$uniqid}}]" data-id="{{$items->getInventoryItemDetail->id}}" class="discount_per form-control" value="{{$items->discount_percentage}}"></td>
                                        <td><input style="padding: 3px 3px;" type="text" name="item_discount[{{$uniqid}}]" data-id="{{$items->getInventoryItemDetail->id}}" class="discount form-control" value="{{$items->discount_amount}}"></td>
                                        <td><span class="exclusive">{{$items->total_cost}}</span></td>
                                        <td><span class="vat">{{$items->vat_amount}}</span></td>
                                        <td><span class="total">{{$items->total_cost_with_vat}}</span></td>
                                        <td>
                                        <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                        </td>
                                      </tr>
                                      @endforeach
                                      @else
                                      <tr>                                      
                                      <td>
                                        <input type="text" class="testIn form-control makemefocus" name="item_id['0']">
                                        <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
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
                                      <td></td>
                                      <td></td>
                                      <td><button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                      </tr>
                                      @endif
                                   


                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th colspan="11" style="text-align:right">
                                        Total Exclusive
                                        </th>
                                        <td colspan="2">KES <span id="total_exclusive">0.00</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="11" style="text-align:right">
                                        Total VAT		
                                        </th>
                                        <td colspan="2">KES <span id="total_vat">0.00</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="11" style="text-align:right">
                                        Total
                                        </th>
                                        <td colspan="2">KES <span id="total_total">0.00</span></td>
                                      </tr>
                                    </tfoot>
                                </table>
                                </span>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6"><span>
                                <button type="submit" class="btn btn-primary btn-sm addExpense" value="save" name="request_type">Save</button>
                                <button type="submit" class="btn btn-primary btn-sm addExpense processIt" value="send_request">Process</button>
                              <!-- <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addRequisitionItemModel" id="addItemForm">Add Multiple Item To Purchase Order</button> -->
                              <!-- <button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#addManualItemModel" id="addbuttonpu1" onclick="getmanualitemlist()">Manual Entry</button> -->
                              </span></div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>
  </form>
@endsection

@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

<style type="text/css">
   .select2{
     width: 100% !important;
    }
    #note{
      height: 60px !important;
    }
    .align_float_right
    {
      text-align:  right;
    }
    .textData table tr:hover{
      background:#000 !important;
      color:white !important;
      cursor: pointer !important;
    }


/* ALL LOADERS */

.loader{
  width: 100px;
  height: 100px;
  border-radius: 100%;
  position: relative;
  margin: 0 auto;
  top: 35%;
}

/* LOADER 1 */

#loader-1:before, #loader-1:after{
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  border-radius: 100%;
  border: 10px solid transparent;
  border-top-color: #3498db;
}

#loader-1:before{
  z-index: 100;
  animation: spin 1s infinite;
}

#loader-1:after{
  border: 10px solid #ccc;
}

@keyframes spin{
  0%{
    -webkit-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }

  100%{
    -webkit-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}

    </style>
@endsection

@section('uniquepagescript')
<div id="loader-on" style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
">
  <div class="loader" id="loader-1"></div>
</div>
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
var form = new Form();
$(document).on('keypress',".quantity",function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $(".addNewrow").click();
    }
  });
  function makemefocus(){
    if($(".makemefocus")[0]){
        $(".makemefocus")[0].focus();
    }
  }
  $(document).on('click','.addExpense',function(e){
    e.preventDefault();
    $('#loader-on').show();
    var postData = new FormData($(this).parents('form')[0]);
    var url = $(this).parents('form').attr('action');
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('request_type',$(this).val());
    var $this = $(this);
    $.ajax({
        url:url,
        data:postData,
        contentType: false,
        cache: false,
        processData: false,
        method:'POST',
        success:function(out){
    $('#loader-on').hide();

            $(".remove_error").remove();
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
                form.successMessage(out.message);
                if(out.location)
                {
                    setTimeout(() => {
                        location.href = out.location;
                    }, 1000);
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

  $(document).ready(function(){
    $('body').addClass('sidebar-collapse');
  });
    $(function () {
        $(".mlselec6t").select2();
    });
    $(function () {
        $(".mlselec6t_modal").select2({dropdownParent: $('.modal')});
    });

  

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
        $(document).ready(function () {
          totalofAllTotal();
          vat_list();
        });
    </script>
    
    <script>
      
  $(document).on('keyup','.testIn',function(e){ var vale = $(this).val();
      $(this).parent().find(".textData").show();
      var $this = $(this);
      $.ajax({
        type: "GET",
        url: "{{route('purchase-orders.inventoryItems')}}",
        data: {
          'search':vale
        },
        success: function (response) {
          $this.parent().find('.textData').html(response);
        }
      });
  });

  $(document).click(function(e){
    var container = $(".textData");
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        container.hide();
    }
  });
    function fetchInventoryDetails(varia){
      var $this = $(varia);
      var itemids = $('.itemid');
      var furtherCall = true;
      $.each(itemids, function (indexInArray, valueOfElement) { 
         if($this.data('id') == $(valueOfElement).val()){
          form.errorMessage('This Item is already added in list');
          furtherCall = false;
          return true;
         }
      });
      if(furtherCall == true){
        $.ajax({
          type: "GET",
          url: "{{route('purchase-orders.getInventryItemDetails')}}",
          data: {
            'id':$this.data('id')
          },
          success: function (response) {
            $(".vat_list").select2('destroy');
            $this.parents('tr').replaceWith(response);
            vat_list();
            totalofAllTotal();
          }
        });
      }
    }
    $(document).on('click','.deleteparent',function(){
      $(this).parents('tr').remove();
      totalofAllTotal();
    });
    $(document).on('click','.addNewrow',function(){
      $('#mainItemTable tbody').append('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
                                    +'</tr>');
                                    makemefocus();
    });
    
    var vat_list = function(){
            $(".vat_list").select2(
            {
                placeholder:'Select Vat',
                ajax: {
                    url: '{{route("expense.vat_list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        $(document).on('change','.vat_list',function(){
            var vat = $(this).val();
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "{{route('expense.vat_find')}}",
                data: {
                    'id':vat
                },
                success: function (response) {
                    $this.parents('tr').find('.vat_percentage').val(response.tax_value);
                    getTotal($this);
                }
            });
            
        });

        function getTotal(vara){
            var price = $(vara).parents('tr').find('.standard_cost').val();
            var quantity = $(vara).parents('tr').find('.quantity').val();
            if(quantity < 0 || quantity == ''){
              quantity = 0;
            }
            var discount_per = $(vara).parents('tr').find('.discount_per').val();
            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
            var discount = ((parseFloat(price)*parseFloat(quantity))*parseFloat(discount_per))/100;
            var exclusive = ((parseFloat(price)*parseFloat(quantity))-parseFloat(discount));
            var vat = parseFloat(exclusive) - ((parseFloat(exclusive)*parseFloat(100)) / (parseFloat(vat_percentage)+100));
            var total = parseFloat(exclusive);
            exclusive = (parseFloat(exclusive) - parseFloat(vat));
            $(vara).parents('tr').find('.discount').val((discount).toFixed(2));
            $(vara).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
            $(vara).parents('tr').find('.vat').html((vat).toFixed(2));
            $(vara).parents('tr').find('.total').html((total).toFixed(2));

            totalofAllTotal();
        }
        $(document).on('keyup','.discount',function(e){
          var discount = $(this).val();
          var price = $(this).parents('tr').find('.standard_cost').val();
          var quantity = $(this).parents('tr').find('.quantity').val();
          var vat_percentage = $(this).parents('tr').find('.vat_percentage').val();
          if(quantity < 0 || quantity == ''){
            quantity = 0;
          }
          var discount_per = (discount/parseFloat(price)*parseFloat(quantity))*100;
          var exclusive = ((parseFloat(price)*parseFloat(quantity))-parseFloat(discount));
          var vat = parseFloat(exclusive) - ((parseFloat(exclusive)*parseFloat(100)) / (parseFloat(vat_percentage)+100));
            var total = parseFloat(exclusive);
            exclusive = (parseFloat(exclusive) - parseFloat(vat));
          $(this).parents('tr').find('.discount_per').val((discount_per).toFixed(2));
          $(this).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
          $(this).parents('tr').find('.vat').html((vat).toFixed(2));
          $(this).parents('tr').find('.total').html((total).toFixed(2));
          totalofAllTotal();
        });
        function totalofAllTotal(){
          var alle = $(document).find('.exclusive');
          var allv = $(document).find('.vat');
          var allt = $(document).find('.total');
          var exclusive = 0;
          var vat = 0;
          var total = 0;
          $.each(alle, function (indexInArray, valueOfElement) { 
            exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).text());
          });
          $.each(allv, function (indexInArray, valueOfElement) { 
            vat = parseFloat(vat) + parseFloat($(valueOfElement).text());
          });
          $.each(allt, function (indexInArray, valueOfElement) { 
            total = parseFloat(total) + parseFloat($(valueOfElement).text());
          });
          $('#total_exclusive').html((exclusive).toFixed(2));
          $('#total_vat').html((vat).toFixed(2));
          $('#total_total').html((total).toFixed(2));
        }
    </script>
@endsection

