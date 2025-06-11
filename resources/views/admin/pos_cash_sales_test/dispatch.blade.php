@extends('layouts.admin.admin')
@section('content')
<form method="POST" action="{{route('pos-cash-sales.post_dispatch')}}" accept-charset="UTF-8" class="addExpense" enctype="multipart/form-data" >

    <section class="content">    
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
            <div class="box-body">
                @include('message')     
                {{ csrf_field() }}
                <?php 
                    $getLoggeduserProfile = getLoggeduserProfile();               
                    $purchase_date = date('d-M-Y');
                    $purchase_time = date('H:i A');
                ?>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Date</label>
                            <input type="text" value="{{$purchase_date}}" class="form-control" readonly name="date">
                        </div>
                    </div>   
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Time</label>
                            <input type="text" value="{{$purchase_time}}" class="form-control" readonly  name="time">
                        </div>
                    </div>   
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="type" id="type" class="form-control">
                                <option value="Cash Sales"> Cash Sales </option>
                                <option value="Sales Invoice"> Sales Invoice </option>
                            </select>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Receipt No.</label>
                            <Select class="form-control" id="receipt_select"  name="receipt_no"></Select>
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Customer</label>
                            <input class="form-control" id="customer" readonly>
                        </div>
                    </div>   
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Sold By</label>
                            <input class="form-control" id="sold_by" readonly>
                        </div>
                    </div>                  
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">No. of Items</label>
                            <input class="form-control" id="quantity" readonly>
                        </div>
                    </div>  
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Disp By</label>
                            <input class="form-control" id="desp_by" value="{{getLoggeduserProfile()->name}}" readonly >
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Disp date</label>
                            <input class="form-control" id="desp_date" readonly value="{{$purchase_date}}">
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Disp time</label>
                            <input class="form-control" id="desp_time" readonly value="{{$purchase_time}}">
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Disp No.</label>
                            <input class="form-control" readonly value="{{$invoice}}" name="disp_no">
                        </div>
                    </div> 
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input class="form-control" id="amount" readonly>
                        </div>
                    </div> 
                </div>
                <span class="item_id"></span>
                <div class="row" id="dispatch_items">                    
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
.remove_error
{
    color:red;
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
  function getTotal(input) {
      var qty = parseFloat($(input).val());
      var qtyMax = parseFloat($(input).data('max'));
      $(input).parents('tr').find('.remove_error').remove();
      if(qty <= 0 || qty > qtyMax){
        $(input).parent().append('<label class="error d-block remove_error w-100">Invalid Quantity</label>');
        return false;        
      }
      var selling = $(input).parents('tr').find('.selling_price').text();
      var total = parseFloat(selling) * parseFloat(qty);
      $(input).parents('tr').find('.total').text(total);
  }
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
       $("#receipt_select").change(function(e){
           e.preventDefault();
           var id = $(this).val();
            $.ajax({
                type: "GET",
                url: "{{route('pos-cash-sales.get_sales_list_details')}}",
                data: {"id":id,'type' : $('#type').val()},
                success: function (response) {
                    if(response.result == '0'){
                        form.errorMessage(response.message);
                        $.each(response.data,function(key,value){
                            $('#'+key).val(value);
                        });
                    }else{
                        $.each(response.data,function(key,value){
                            $('#'+key).val(value);
                        });
                        $('#dispatch_items').html(response.items);
                    }
                    console.log(response);
                }
            });
       });
       function receipt_select(){
            $("#receipt_select").select2(
            {
                placeholder:"Receipt No",
                ajax: {
                    url: '{{route("pos-cash-sales.get_sales_list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            type : $('#type').val(),
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                },

            });
        }
    receipt_select();

  });
  $('#type').change(function(){
        $("#receipt_select").select2('destroy');
        $("#receipt_select").html('');

        $("#receipt_select").select2(
            {
                placeholder:"Receipt No",
                ajax: {
                    url: '{{route("pos-cash-sales.get_sales_list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term,
                            type : $('#type').val(),
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: data
                        };
                    }
                },

            });
            $('#dispatch_items').html('');
            $('#customer').val('');
            $('#sold_by').val('');
            $('#amount').val('');
            $('#quantity').val('');
    });
   

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


