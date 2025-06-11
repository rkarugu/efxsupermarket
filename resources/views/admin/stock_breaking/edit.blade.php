@extends('layouts.admin.admin')
@section('content')
<form method="POST" action="{{ route($model.'.update',base64_encode($data->id)) }}" accept-charset="UTF-8" class="" onsubmit="return false;" enctype="multipart/form-data" >

<a href="{{ route($model.'.index') }}" class="btn btn-primary">Back</a>
<br>
<section class="content"  style="min-height: 10px">    
    <div class="box box-primary" style="margin-bottom: 0px">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
     
            {{ csrf_field() }}
            {{method_field('PUT')}}
            <input type="hidden" class="form-control" name="id" id="id" value="{{$data->id}}">
            <div class = "row">
                <div class = "col-sm-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="">Date</label>
                                <input value="{{$data->date}}" readonly name="date" class="form-control">
                            </div>
                        </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Time</label>
                            <input value="{{$data->time}}" readonly name="time" class="form-control">
                        </div>
                    </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">User</label>
                            <span class="form-control">{{@$data->user->name}}</span>
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
                             
                          
                            <div class="col-md-12 no-padding-h ">
                           <h3 class="box-title"> ITEMS </h3>
                           <button type="button" class="btn btn-danger btn-sm addNewrow" style="position: fixed;bottom: 45%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            <div id = "requisitionitemtable" name="item_id[0]">
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Source Item</th>
                                      <th>Description</th>
                                      <th >Bal Stock</th>
                                      <th >Destination Code</th>
                                      <th >Destination Item</th>
                                      <th>Source Qty</th>
                                      <th>Conversion Factor</th>
                                      <th>Destination Qty</th>
                                      <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data->items as $item)
                                        <tr>                                      
                                            <td>
                                                <input type="hidden" name="item_id[{{$item->id}}]" class="itemid" value="{{$item->source_item->id}}">
                                                <input style="padding: 3px 3px;"  type="text" class="testIn form-control" value="{{$item->source_item->stock_id_code}}">
                                                <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
                                            </td>
                                            <td class="item_description">{{$item->source_item->description}}</td>
                                            <td>{{($item->source_item->getAllFromStockMoves->sum('qauntity') ?? 0)}}</td>
                                            <td>
                                                <input type="hidden" name="alternateid[{{$item->source_item->id}}]" class="alternateid" value="{{$item->destination_item->id}}">
                                                <input style="padding: 3px 3px;"  type="text" class="alternateIn form-control" value="{{$item->destination_item->stock_id_code}}">
                                                <div class="alternateid_data" style="width: 100%;position: relative;z-index: 99;"></div>
                                            </td>
                                            <td class="alternate_desc">{{$item->destination_item->description}}</td>
                                            <td><input style="padding: 3px 3px;"  type="number" name="source_qty[{{$item->source_item->id}}]" class="form-control quantity_cal" value="{{(int)$item->source_qty}}" onchange="quantity_packsize_cal(this)" onkeyup="quantity_packsize_cal(this)"></td>
                                            <td><input style="padding: 3px 3px;"  type="number" name="conversion_factor[{{$item->source_item->id}}]" class="form-control packsize_cal" value="{{(int)$item->conversion_factor}}" onchange="quantity_packsize_cal(this)" onkeyup="quantity_packsize_cal(this)"></td>
                                            <td class="quantity_packsize_cal">{{(int)$item->destination_qty}}</td>
                                            <td>
                                            <button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                    
                                </table>
                              </div>
                            </div>
                       


                            <div class="col-md-12">
                              <div class="col-md-6">  
                                <div class="box-body">
                                  <div class="form-group">
                                      <label for="">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-sm addExpense" value="save">Save</button>
                                        <button type="submit" class="btn btn-primary btn-sm addExpense processIt" value="send_request">Process</button>
                                  </div>
                                </div>
                              </div>
                              
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
    .textData table tr:hover, .alternateid_data table tr:hover{
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
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
  <script type="text/javascript">
  function quantity_packsize_cal(params) {
      var quantity_cal = $(params).parents('tr').find('.quantity_cal').val();
      var packsize_cal = $(params).parents('tr').find('.packsize_cal').val();
      if(packsize_cal == '' || packsize_cal <= 0){
        packsize_cal = 0;
      }
      if(quantity_cal == '' || quantity_cal <= 0){
        quantity_cal = 0;
      }
      $(params).parents('tr').find('.quantity_cal').val(quantity_cal);
      $(params).parents('tr').find('.packsize_cal').val(packsize_cal);
      $(params).parents('tr').find('.quantity_packsize_cal').html(packsize_cal*quantity_cal);
  }
  $(document).on('keypress',".quantity",function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $(".addNewrow").click();
    }
  });
  $(document).on('keypress',".start_process",function(event) {
    if (event.keyCode === 13) {
      event.preventDefault();
      $(".processIt").click();
    }
  });
  function makemefocus(){
    if($(".makemefocus")[0]){
        $(".makemefocus")[0].focus();
    }
  }

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
    </script>
    
    <script>
      
  $(document).on('keyup','.testIn',function(e){ var vale = $(this).val();
      $(this).parent().find(".textData").show();
      var $this = $(this);
      $.ajax({
        type: "GET",
        url: "{{route('stock-breaking.inventoryItems')}}",
        data: {
          'search':vale,'type':'item'
        },
        success: function (response) {
          $this.parent().find('.textData').html(response);
        }
      });
  });
  $(document).on('keyup','.alternateIn',function(e){ var vale = $(this).val();
      $(this).parent().find(".alternateid_data").show();
      $(this).parents('tr').find('.alternateid').val('');
    //   $(this).parents('tr').find('.alternateIn').val('');
      $(this).parents('tr').find('.alternate_desc').html('');
      var $this = $(this);
      $.ajax({
        type: "GET",
        url: "{{route('stock-breaking.inventoryItems')}}",
        data: {
          'search':vale,'type':'alternate'
        },
        success: function (response) {
          $this.parent().find('.alternateid_data').html(response);
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
    var container = $(".textData");
    // if the target of the click isn't the container nor a descendant of the container
    if (!container.is(e.target) && container.has(e.target).length === 0) 
    {
        container.hide();
    }
  });
    function fetchInventoryDetails(varia){
        var $this = $(varia);
        var type = $(varia).data('type');
        var itemids = $('.itemid');
        var furtherCall = true;
        if(type == 'item'){
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
                    url: "{{route('stock-breaking.getInventryItemDetails')}}",
                    data: {
                        'id':$this.data('id')
                    },
                    success: function (response) {
                        $this.parents('tr').replaceWith(response);
                    }
                });
            }
        }else{
            var itemid = $this.parents('tr').find('.itemid').val();
            if(itemid != $this.data('id')){
                $this.parents('tr').find('.alternateid').val($this.data('id'));
                $this.parents('tr').find('.alternateIn').val($this.data('stock_id_code'));
                $this.parents('tr').find('.alternate_desc').html($this.data('description'));
                $this.parents('tr').find('.alternateid_data').hide();
            }else{
                form.errorMessage('Source Item and Destination Item needs to be different');
            }
        }
    }
    $(document).on('click','.deleteparent',function(){
      $(this).parents('tr').remove();
      
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
                                    +'<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
                                    +'</tr>');
        makemefocus();
    });

    
        

    </script>
@endsection