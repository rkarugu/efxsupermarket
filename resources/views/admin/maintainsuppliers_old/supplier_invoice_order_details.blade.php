@extends('layouts.admin.admin')
@section('content')

<form action="{{route('maintain-suppliers.supplier_invoice_process')}}" method="post" class="addExpense">
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border  no-padding-h-b"><h3 class="box-title"> {!! $title !!} </h3>
            @include('message')
          
                {{ csrf_field() }}
                <input type="hidden" name="id" value="{{$invoices->id}}">
                <input type="hidden" name="supplier" value="{{$supplierList->id}}">
                <div class="row">
                    <div class="col-md-4 no-padding-h ">
                        <div class="form-group">                            
                            <label for="">Supplier</label>
                            <span class="form-control">{{$supplierList->name}} - {{$supplierList->supplier_code}}</span>
                        </div>
                    </div>
                    <div class="col-md-4 no-padding-h ">
                        <div class="form-group">                            
                            <label for="">Order</label>
                            <span class="form-control">{{$invoices->purchase_no}}</span>
                        </div>
                    </div>
                    <div class="col-md-4 no-padding-h ">
                        <div class="form-group">                            
                            <label for="">Order</label>
                            <span class="form-control">{{$invoices->purchase_date}}</span>
                        </div>
                    </div>
                    <div class="col-md-6 no-padding-h ">
                        <div class="form-group">
                            <label for="">Supplier Invoice Number</label>
                            <input type="text" class="form-control" name="supplier_invoice_number">
                        </div>
                    </div>
                    <div class="col-md-6 no-padding-h ">
                        <div class="form-group">
                            <label for="">Supplier Invoice Date</label>
                            <input type="date" class="form-control" name="supplier_invoice_date">
                        </div>
                    </div>
                </div>
            
        </div>
    </div>

        <!-- Small boxes (Stat box) -->
        @if (count($invoices->getRelatedItem)>0)
        <div class="box box-primary" id="invoices">
            <div class="box-header with-border no-padding-h-b">
                 
              
                <div class="col-md-12 no-padding-h ">
                <h3 class="box-title"> Invoices </h3>                           
                    <table class="table table-bordered table-hover" id="mainItemTable">
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th style="width: 90px;">Unit</th>
                            <th style="width: 90px;">QTY</th>
                            <th>Incl. Price</th>
                            <th>Location</th>
                            <th>VAT%</th>
                            <th style="width: 90px;">Disc%</th>
                            <th style="width: 90px;">Discount</th>
                            <th>Exclusive</th>
                            <th>VAT</th>
                            <th>Total</th>
                        </tr>
                        @php
                            $total_total = $total_exclusive = $total_vat = 0;
                        @endphp
                       @foreach ($invoices->getRelatedItem as $b => $item)
                        <tr>
                                                      
                            <td>{{$item->item_no}}</td>
                            <td>{{@$item->getInventoryItemDetail->description}}</td>
                            <td>{{@$item->pack_size->title}}</td>
                            <td>
                                @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                    <input type="hidden" value="{{(int)$item->quantity}}" name="quantity[{{$item->id}}]">
                                    {{(int)$item->quantity}}
                                @else
                                    <input type="number" onkeyup="getTotal(this)" name="quantity[{{$item->id}}]" value="{{(int)$item->quantity}}" class="form-control quantity">
                                @endif
                            </td>                                
                            <td>
                                @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                    {{$item->order_price}}
                                    <input type="hidden" value="{{$item->order_price}}" name="price[{{$item->id}}]">
                                @else                                
                                <input type="number" onkeyup="getTotal(this)" name="price[{{$item->id}}]" value="{{$item->order_price}}" class="form-control standard_cost"></td>
                                @endif
                            <td>{{@$item->location->location_name}}</td>
                            <td>{{$item->vat_rate}}
                            <input type="hidden" value="{{$item->vat_rate}}" class="vat_percentage">
                            </td>
                            <td class="">
                                @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                    <span class="show_discount_per">{{$item->discount_percentage}}</span>
                                    <input type="hidden" value="{{$item->discount_percentage}}" name="discount[{{$item->id}}]" class="discount_per">
                                @else
                                <input type="number" name="discount[{{$item->id}}]" value="{{$item->discount_percentage}}" class="form-control discount_per" onkeyup="getTotal(this);"></td>
                                @endif
                            </td>                        
                            <td>
                                @if (!isset($permission['suppliers-invoice___edit']) && $permission != 'superadmin')
                                    <span class="show_discount">{{$item->discount_amount}}</span>
                                    <input type="hidden" value="{{$item->discount_amount}}" name="discount[{{$item->id}}]" class="discount">
                                @else
                                <input type="number" name="discount[{{$item->id}}]" value="{{$item->discount_amount}}" class="form-control discount" onhange="discount();"></td>
                                @endif
                            <td class="exclusive">{{$item->total_cost}}</td>
                            <td class="vat">{{$item->vat_amount}}</td>
                            <td class="total">{{$item->total_cost_with_vat}}</td>

                            @php
                                $total_total += $item->total_cost_with_vat;
                                $total_exclusive += $item->total_cost;
                                $total_vat += $item->vat_amount;
                            @endphp
                        </tr>

                       @endforeach
                        <tr>
                          <th colspan="11" style="text-align:right">
                          Total Exclusive
                          </th>
                          <td colspan="2">KES <span id="total_exclusive">{{$total_exclusive}}</span></td>
                        </tr>
                        <tr>
                          <th colspan="11" style="text-align:right">
                          Total VAT		
                          </th>
                          <td colspan="2">KES <span id="total_vat">{{$total_vat}}</span></td>
                        </tr>
                        @php
                            $roundOff = fmod($total_total, 1); //0.25
                            if($roundOff!=0){
                                if($roundOff > '0.50'){
                                    $roundOff = '+'.round((1-$roundOff),2);
                                }else{
                                    $roundOff = '-'.round($roundOff,2);
                                }
                                $total_total += $roundOff;
                            }
                        @endphp 
                        <tr>
                            <th colspan="11" style="text-align:right">
                            Round Off
                            </th>
                            <td colspan="2">KES <span id="round_off_total">{{$roundOff}}</span></td>
                        </tr>
                        <tr>
                          <th colspan="11" style="text-align:right">
                          Total
                          </th>
                          <td colspan="2">KES <span id="total_total">{{$total_total}}</span></td>
                        </tr>
                        <tr>
                            <th colspan="13" style="text-align:center">
                                <button type="submit" class="btn btn-primary btn-sm">Process Invoice</button>
                            </th>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        @endif


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

$(document).on('submit','.addExpense',function(e){
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
  
</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });

        function getTotal(vara){
            var price = $(vara).parents('tr').find('.standard_cost').val();
            var quantity = $(vara).parents('tr').find('.quantity').val();
            var discount_per = $(vara).parents('tr').find('.discount_per').val();
            var vat_percentage = $(vara).parents('tr').find('.vat_percentage').val();
            var discount = ((parseFloat(price)*parseFloat(quantity))*parseFloat(discount_per))/100;
            var exclusive = ((parseFloat(price)*parseFloat(quantity))-parseFloat(discount));
            var vat = parseFloat(exclusive)-((parseFloat(exclusive)*100) / (parseFloat(vat_percentage)+100));
            var total = parseFloat(parseFloat(exclusive));
            exclusive = parseFloat(parseFloat(exclusive)-parseFloat(vat));
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
          
          var discount_per = (discount/parseFloat(price)*parseFloat(quantity))*100;
          var exclusive = ((parseFloat(price)*parseFloat(quantity))-parseFloat(discount));
          var vat = parseFloat(exclusive)-((parseFloat(exclusive)*100) / (parseFloat(vat_percentage)+100));
        var total = parseFloat(parseFloat(exclusive));
        exclusive = parseFloat(parseFloat(exclusive)-parseFloat(vat));

          $(this).parents('tr').find('.discount_per').val((discount_per).toFixed(2));
          $(this).parents('tr').find('.exclusive').html((exclusive).toFixed(2));
          $(this).parents('tr').find('.vat').html((vat).toFixed(2));
          $(this).parents('tr').find('.total').html((total).toFixed(2));
          totalofAllTotal();
        });
        function totalofAllTotal(){
          var alle = $(document).find('.exclusive').html(exclusive);
          var allv = $(document).find('.vat').html(vat);
          var allt = $(document).find('.total').html(total);
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
          var roundoff = total%1;
          if(roundoff!=0){
              if(roundoff >= 0.5){
                roundoff = (roundoff).toFixed(2);
              }else{
                roundoff = -(roundoff).toFixed(2);
              }
              total = parseFloat(total) + parseFloat(roundoff);
          }
          $('#round_off_total').html((roundoff).toFixed(2));

          $('#total_total').html((total).toFixed(2));
        }
    </script>
    
@endsection


