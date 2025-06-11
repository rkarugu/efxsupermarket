@extends('layouts.admin.admin')
@section('content')
<form class="validate form-horizontal" role="form" method="POST" action="{{ route('stock-processing.sales.store') }}"
                    enctype="multipart/form-data">
                @csrf
    <section class="content" style="padding-bottom:0px;">
        <div class="box box-primary" style="margin-bottom: 0px;">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h4> Edit Sales (Short) </h4>
                    <a href="{{ route("stock-processing.sales") }}" role="button" class="btn btn-primary"> Back </a>
                </div>
            </div>
            <div class="session-message-container">
                @include('message')
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Employee Name</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" class="form-control" value="{{ \Auth::user()->name }}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 ">Date</label>
                            <div class="col-sm-7">
                                <input type="text" class="form-control" name="date" value="{{date('m/d/Y',strtotime($trans->created_at))}}" disabled>                                       
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Location</label>
                            <div class="col-sm-7">
                                <input type="text" id="employee_location" class="form-control" value="{{$trans->debtor->employee->location_stores->location_name}}" disabled>                        
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Bin Location</label>
                            <div class="col-sm-7">
                                <input type="text" id="employee_bin_location" class="form-control" value="{{$trans->debtor->employee->uom ? $trans->debtor->employee->uom->title : '-' }}" disabled>                                    
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="internal_debtor" class="col-sm-5 text-left">Internal Debtor</label>
                            <div class="col-sm-7">
                                <select name="internal_debtor" id="internal_debtor" class="select2 form-control" required>
                                    <option value="">Choose Employee</option>
                                    @foreach ($employees as $item)
                                        <option value="{{$item->id}}" @if ($trans->stock_debtors_id == $item->id) selected @endif
                                            data-location_id="{{$item->employee->wa_location_and_store_id}}"
                                            data-phone="{{$item->employee->phone_number}}"
                                            data-bin_location ="{{$item->employee->uom ? $item->employee->uom->title : '-' }}"
                                            data-location = "{{$item->employee->location_stores->location_name}}"
                                            data-balance = {{$item->getCurrentBalance()}}
                                            >
                                            {{$item->employee->name}}
                                        </option>
                                    @endforeach
                                </select>
                                
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Phone</label>
                            <div class="col-sm-7">
                                <input type="text" id="employee_phone" class="form-control" value="{{$trans->debtor->employee->phone_number}}" disabled>                        
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group" style="margin-bottom: 0px">
                            <label for="inputEmail3" class="col-sm-5 text-left">Current Balance</label>
                            <div class="col-sm-7">
                                <input type="text" id="current_balance" class="form-control" value="{{$trans->debtor->getCurrentBalance()}}" disabled>               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
                    


                {{-- <div class="box-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div> --}}
            
        </div>
    </section>

    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h" >
                           <h3 class="box-title"> Invoice Line</h3>

                           <div id = "requisitionitemtable" name="item_id[0]">
                             
                                <button type="button" class="btn btn-danger btn-sm addNewrow" style="position: fixed;bottom: 30%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Selection <span style="color: red;">(Search Atleast 3 Keyword)</span></th>
                                      <th>Description</th>
                                      <th style="width: 90px;">QOH</th>
                                      <th style="width: 90px;">Unit</th>
                                      <th style="width: 90px;">QTY</th>
                                      <th>Selling Price</th>
                                      <th>VAT Type</th>
                                      <th style="width: 90px;">Disc%</th>
                                      <th style="width: 90px;">Discount</th>
                                      <th>VAT</th>
                                      <th>Total</th>
                                      <th>
                                      
                                      </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($trans->items as $item)
                                        <tr>    
                                            <td>
                                                <input type="hidden" name="item_id[{{$item->inventoryItem->id}}]" class="itemid" value="{{$item->inventoryItem->id}}">
                                                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="{{$item->inventoryItem->stock_id_code}}">
                                                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
                                            </td>
                                            <td>
                                                <input style="padding: 3px 3px;" readonly type="text" name="item_description[{{$item->inventoryItem->id}}]" data-id="{{$item->inventoryItem->id}}"  class="form-control" value="{{$item->inventoryItem->description}}">
                                            </td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>
                                                <input style="padding: 3px 3px;" readonly type="text" name="item_unit[{{$item->inventoryItem->id}}]" data-id="{{$item->inventoryItem->id}}"  class="form-control" value="{{$item->inventoryItem->packSize->title ?? NULL}}" readonly>
                                            </td>
                                            <td>
                                                <input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)"  type="text" name="item_quantity[{{$item->inventoryItem->id}}]" data-id="{{$item->inventoryItem->id}}"  class="quantity form-control" value="" required>
                                                <input type="hidden" value="{{ $item->quantity }}" name="item_old_quantity[{{$item->inventoryItem->id}}]">
                                            </td>
                                            <td>
                                                <input style="padding: 3px 3px;" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_selling_price[{{$item->inventoryItem->id}}]" data-id="{{$item->inventoryItem->id}}"  class="selling_price form-control send_me_to_next_item" value="{{$item->price}}">
                                            </td>
                                            

                                            {{-- <td><select class="form-control vat_list send_me_to_next_item" name="item_vat[{{$item->inventoryItem->id}}]">
                                                @php
                                                    
                                                
                                                $per = 0;
                                                $vat = 0.00;
                                                if ($item->inventoryItem->getTaxesOfItem) {
                                                    $view .= '<option value="' . $item->inventoryItem->getTaxesOfItem->id . '" selected>' . $item->inventoryItem->getTaxesOfItem->title . '</option>';
                                                    $per = $item->inventoryItem->getTaxesOfItem->tax_value;
                                                    $vat = round($item->inventoryItem->selling_price - (($item->inventoryItem->selling_price * 100) / ($per + 100)), 2) * 0;
                                                }
                                                @endphp
                                                </select>
                                                <input type="hidden" class="vat_percentage" value="' . $per . '"  name="item_vat_percentage[' . $data->id . ']">
                                            </td> --}}
                                            
                                            <td>
                                                <select class="form-control vat_list send_me_to_next_item select2-hidden-accessible" name="item_vat[{{$item->inventoryItem->id}}]" tabindex="-1" aria-hidden="true">
                                                    <option value="1" selected="">VAT 16%</option>
                                                </select>
                                                <span class="select2 select2-container select2-container--default" dir="ltr" style="width: 33.75px;">
                                                    <span class="selection">
                                                        <span class="select2-selection select2-selection--single" role="combobox" aria-haspopup="true" aria-expanded="false" tabindex="0" aria-labelledby="select2-item_vat{{$item->inventoryItem->id}}-4z-container">
                                                            <span class="select2-selection__rendered" id="select2-item_vat{{$item->inventoryItem->id}}-4z-container" title="VAT 16%">VAT 16%</span>
                                                            <span class="select2-selection__arrow" role="presentation"><b role="presentation"></b></span>
                                                        </span>
                                                    </span>
                                                    <span class="dropdown-wrapper" aria-hidden="true"></span></span>
                                                <input type="hidden" class="vat_percentage" value="16" name="item_vat_percentage[{{$item->inventoryItem->id}}]">
                                            </td> 
                                            <td><input style="padding: 3px 3px;" readonly="" onchange="getTotal(this)" onkeyup="getTotal(this)" type="text" name="item_discount_per[{{$item->inventoryItem->id}}]" data-id="{{$item->inventoryItem->id}}" class="discount_per form-control" value="0.00"></td>
                                            <td><input style="padding: 3px 3px;" readonly="" type="text" name="item_discount[{{$item->inventoryItem->id}}]" data-id="{{$item->inventoryItem->id}}" class="discount form-control" value="0.00"></td>
                                           
                                            <td><span class="vat">0</span></td>
                                            <td><span class="total">0</span></td>
                                            <td>
                                            <button type="button" class="btn btn-primary btn-sm deleteparent"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                            </td>
                                            </tr>
                                        @endforeach
                                      <tr>                                      
                                      <td>
                                        <input type="text" placeholder="Search Atleast 3 Keyword" class="testIn form-control">
                                        <div class="textData"  style="width: 100%;position: relative;z-index: 99;"></div>
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
                                      <td><button type="button" class="btn btn-primary btn-sm deleteparent"><i class="fas fa-trash" aria-hidden="true"></i></button></td>
                                      </tr>
                        
                                   


                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th colspan="10" style="text-align:right">
                                        Total Price
                                        </th>
                                        <td colspan="2">KES <span id="total_exclusive">0.00</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="10" style="text-align:right">
                                        Discount
                                        </th>
                                        <td colspan="2">KES <span id="total_discount">0.00</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="10" style="text-align:right">
                                        Total VAT		
                                        </th>
                                        <td colspan="2">KES <span id="total_vat">0.00</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="10" style="text-align:right">
                                        Total
                                        </th>
                                        <td colspan="2">KES <span id="total_total">0.00</span></td>
                                      </tr>
                                    </tfoot>
                                </table>
                              </div>
                            </div>
                       


                              <div class="col-md-12">
                              <div class="col-md-6 request_type">
                             
                                  <button type="submit" class="btn btn-success addExpense" value="save">Process</button>
                                  {{-- <button type="submit" class="btn btn-success btn-lg addExpense processIt" value="send_request">Send Request</button> --}}
                         
                              </div>
                              <div class="col-md-3"></div>
                              <div class="col-md-3"></div>
                              </div>


                               
                        </div>
                    </div>


    </section>
    <input type="hidden" id="store_location_id" name="store_location_id" value="{{$trans->debtor->employee->wa_location_and_store_id}}">
</form>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>

    <style type="text/css">
            
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
position: fixed;
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
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $('body').addClass('sidebar-collapse');
            $(".select2").select2();
            // $(".testIn").prop('disabled', true);
            // $('.addNewrow').hide();
        });
    </script>

<script>
    var form = new Form();

$(document).on('click','.addExpense',function(e){
    e.preventDefault();
    
    $('#loader-on').show();
    var postData = new FormData($(this).parents('form')[0]);
var url = $(this).parents('form').attr('action');
postData.append('_token',$(document).find('input[name="_token"]').val());
postData.append('request_type',$(this).val());
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
                    // setTimeout(() => {
                      $('#mainItemTable tbody').html('');
                      $('#mainItemTable tbody').append('<tr><td><input type="text" class="testIn form-control makemefocus"><div class="textData" style="width: 100%;position: relative;z-index: 99;"></div></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td></td>'
                                    +'<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
                                    +'</tr>');
                      
                      
                    location.href = out.location;
                    // }, 1000);
                }
            }
            if(out.result === -1) {
                form.errorMessage(out.message);
            }
        },
        
        error:function(err)
        {
            console.log(err);
          $('#loader-on').hide();
            $(".remove_error").remove();
            form.errorMessage('Something went wrong');							
        }
    });
});
    var valueTest = null;
      $(document).on('keyup keypress click','.testIn',function(e){ 
        var vale = $(this).val();
        $(this).parent().find(".textData").show();
        var objCurrentLi, obj = $(this).parent().find(".textData tbody tr.SelectedLi"),
        objUl = $(this).parent().find('.textData tbody'),
        code = (e.keyCode ? e.keyCode : e.which);
        console.log(code);
        if (code == 40) { //Up Arrow
  
            //if object not available or at the last tr item this will roll that back to first tr item
            if ((obj.length === 0) || (objUl.find('tr:last').hasClass('SelectedLi') === true)) {
                objCurrentLi = objUl.find('tr:first').addClass('SelectedLi').addClass('industryli');
            } 
            //This will add class to next tr item
            else {
                objCurrentLi = obj.next().addClass('SelectedLi').addClass('industryli');
            }
  
            //this will remove the class from current item
            obj.removeClass('SelectedLi');
  
            var listItem = $(this).parent().find('.SelectedLi.industryli');
            var selectedLi = $(this).parent().find(".textData tbody tr").index(listItem);
  
            var len = $(this).parent().find('.textData tbody tr').length;
  
  
            if (selectedLi > 1) {
                var scroll = selectedLi + 1;
                $(this).parent().find('.textData table').scrollTop($(this).parent().find('.textData table').scrollTop() + obj.next().height());
            }
            if (selectedLi == 0) {
              $(this).parent().find('.textData table').scrollTop($(this).parent().find('.textData table tr:first').position().top);
            }
  
            return false;
        }
        else if (code == 38) {//Down Arrow
          if ((obj.length === 0) || (objUl.find('tr:first').hasClass('SelectedLi') === true)) {
                  objCurrentLi = objUl.find('tr:last').addClass('SelectedLi').addClass('industryli');
              } else {
                  objCurrentLi = obj.prev().addClass('SelectedLi').addClass('industryli');
              }
              obj.removeClass('SelectedLi');
  
              var listItem = $(this).parent().find('.SelectedLi.industryli');
              var selectedLi = $(this).parent().find(".textData tbody tr").index(listItem);
  
              var len = $(this).parent().find('.textData tbody tr').length;
  
  
              if (selectedLi > 1) {
                  var scroll = selectedLi - 1;
                  $(this).parent().find('.textData table').scrollTop(
                    $(this).parent().find('.textData table tr:nth-child(' + scroll + ')').position().top - 
                    $(this).parent().find('.textData table tr:first').position().top);
              }
          return false;
        }
        else if (code == 13) {
              obj.click();
              return false;
          }
        else if (valueTest != vale && (e.type == 'keyup' || e.type == 'click') && code != 13 && code != 38 && code != 40 && vale != ''){
          var $this = $(this);
          
          if(vale.length>=3){
              $.ajax({
                type: "GET",
                url: "{{route('purchase-orders.inventoryItems')}}",
                data: {
                  'search':vale,
                  'store_location_id':$('#store_location_id').val()
                },
                success: function (response) {
                  $this.parent().find('.textData').html(response);
                }
              });
              valueTest = vale;
          }
  
          return true;
        }
  
        
    });  
  
    
  
    $(document).click(function(e){
        $('#internal_debtor').change(function(){
            if ($(this).val()) {
                $('#store_location_id').val($(this).find(':selected').data('location_id'));
                $('#employee_phone').val($(this).find(':selected').data('phone'));
                $('#employee_bin_location').val($(this).find(':selected').data('bin_location'));
                $('#employee_location').val($(this).find(':selected').data('location'));
                $('#current_balance').val($(this).find(':selected').data('balance'));
                $(".testIn").prop('disabled', false);
                $('.addNewrow').show();
            } else {
                $(".testIn").prop('disabled', true);
                $('.addNewrow').hide();
            }            
        });

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
            url: "{{route('sales-invoice.getInventryItemDetails')}}",
            data: {
              'id':$this.data('id'),
              'hide_location':true,
            },
            success: function (response) {
              if(response.result){
                form.errorMessage(response.message);	
                return true;
              }
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
        totalofAllTotal()
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
                                      +'<td><button class="btn btn-primary btn-sm deleteparent"><i class="fas fa-trash" aria-hidden="true"></i></button></td>'
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
              var price = $(vara).parents('tr').find('.selling_price').val();
              if(price < 0){
                  $(vara).parents('tr').find('.selling_price').val(0);
                  price = 0;
              }
              var quantity = $(vara).parents('tr').find('.quantity').val();
              if(quantity <= 0){
                  $(vara).parents('tr').find('.quantity').val('');
                  quantity = 0;
              }
              var discount_per = $(vara).parents('tr').find('.discount_per').val();
              if(discount_per < 0){
                  $(vara).parents('tr').find('.discount_per').val(0);
                  discount_per = 0;
              }
              var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
              if(vat_percentage < 0){
                  $(vara).parents('tr').find('.vat_percentage').val(0);
                  vat_percentage = 0;
              }
              var discount = ((parseFloat(price)*parseFloat(quantity))*parseFloat(discount_per))/100;
              var exclusive = ((parseFloat(price)*parseFloat(quantity))-parseFloat(discount));
              var vat = parseFloat(exclusive) - parseFloat((parseFloat(exclusive)*100) / (parseFloat(vat_percentage)+100));
              var total = parseFloat(exclusive);
              $(vara).parents('tr').find('.discount').val((discount).toFixed(2));
              $(vara).parents('tr').find('.vat').html((vat).toFixed(2));
              $(vara).parents('tr').find('.total').html((total).toFixed(2));
  
              totalofAllTotal();
          }
          $(document).on('keyup','.discount',function(e){
            var discount = $(this).val();
            if(discount < 0){
                  $(this).parents('tr').find('.discount').val(0);
                  discount = 0;
              }
            var price = $(this).parents('tr').find('.selling_price').val();
            if(price < 0){
                  $(this).parents('tr').find('.selling_price').val(0);
                  price = 0;
              }
            var quantity = $(this).parents('tr').find('.quantity').val();
            if(quantity <= 0){
                  $(this).parents('tr').find('.quantity').val('');
                  quantity = 0;
              }
            var vat_percentage = $(this).parents('tr').find('.vat_percentage').val();  
            if(vat_percentage < 0){
                  $(this).parents('tr').find('.vat_percentage').val(0);
                  vat_percentage = 0;
              }        
            var discount_per = (discount/parseFloat(price)*parseFloat(quantity))*100;
            var exclusive = ((parseFloat(price)*parseFloat(quantity))-parseFloat(discount));
            var vat = parseFloat(exclusive) - parseFloat((parseFloat(exclusive)*100) / (parseFloat(vat_percentage)+100));
            var total = parseFloat(exclusive);
            $(this).parents('tr').find('.discount_per').val((discount_per).toFixed(2));
            $(this).parents('tr').find('.vat').html((vat).toFixed(2));
            $(this).parents('tr').find('.total').html((total).toFixed(2));
            totalofAllTotal();
          });
          function totalofAllTotal(){
            var alld = $(document).find('.discount');
            var allv = $(document).find('.vat');
            var allt = $(document).find('.total');
            var alle = $(document).find('.selling_price');
            var exclusive = 0;
            var vat = 0;
            var total = 0;
            var discount = 0;
            $.each(alld, function (indexInArray, valueOfElement) { 
              discount = parseFloat(exclusive) + parseFloat($(valueOfElement).val());
            });
            $.each(alle, function (indexInArray, valueOfElement) { 
              exclusive = parseFloat(exclusive) + parseFloat($(valueOfElement).val());
            });
            $.each(allv, function (indexInArray, valueOfElement) { 
              vat = parseFloat(vat) + parseFloat($(valueOfElement).text());
            });
            $.each(allt, function (indexInArray, valueOfElement) { 
              total = parseFloat(total) + parseFloat($(valueOfElement).text());
            });
            $('#total_vat').html((vat).toFixed(2));
            $('#total_total').html((total).toFixed(2));
            $('#total_exclusive').html((parseFloat(total)-parseFloat(vat)).toFixed(2));
            $('#total_discount').html((discount).toFixed(2));
            
          }
  
          
          var payment_method = function(){
              $("#payment_method").select2(
              {
                  placeholder:'Select Payment Method',
                  ajax: {
                      url: '{{route("expense.payment_method")}}',
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
  
  
          /* New js start here  */
  
  
          $(document).on('submit','.addSubCustomer',function(e){
              e.preventDefault();
              $('#loader-on').show();
              var postData = new FormData($(this)[0]);
          var url = $(this).attr('action');
          postData.append('_token',$(document).find('input[name="_token"]').val());
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
                          if(out.data)
                          {
                              $('#subCustomers').append('<option value="'+out.data.id+'" selected>'+out.data.name+' : '+out.data.bussiness_name+' : '+out.data.phone+' </option>');
                              $('#modelId').modal('hide');
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
  
  
          
  
          $(document).ready(function() {
    
  });
  
  
  
  
      </script>
@endsection
