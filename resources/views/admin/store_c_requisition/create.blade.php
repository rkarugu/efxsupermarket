
@extends('layouts.admin.admin')
@section('content')
<form method="POST" action="{{ route($model.'.store') }}" accept-charset="UTF-8" class="" onsubmit="return false;" enctype="multipart/form-data" >
  <a href="{{ route($model.'.index') }}" class="btn btn-primary">Back</a>
  <br>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
            {{ csrf_field() }}
             <?php 
                    $requisition_no = getCodeWithNumberSeries('INTERNAL REQUISITIONS');
                    $default_branch_id = getLoggeduserProfile()->restaurant_id;
                    $default_department_id = getLoggeduserProfile()->wa_department_id;
                    $default_wa_location_and_store_id = null;
                    $requisition_date = date('Y-m-d');


                    ?>

            <div class = "row">

              <div class = "col-sm-6">
              

                    <div class = "row">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                <div class="col-sm-7">
                                    {!! Form::text('emp_name', getLoggeduserProfile()->name, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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
                    <div class = "row">
                      <div class="box-body">
                          <div class="form-group">
                              <label for="inputEmail3" class="col-sm-5 control-label">Manual Doc No</label>
                              <div class="col-sm-7">
                                  {!! Form::text('manual_doc_no', NULL, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control']) !!}  
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
                         {!!Form::select('wa_department_id',getDepartmentDropdown(getLoggeduserProfile()->restaurant_id), $default_department_id, ['class' => 'form-control ','required'=>true,'placeholder' => 'Please select department','id'=>'department','disabled'=>true  ])!!} 
                    </div>
                </div>
            </div>
                     </div>
                  <?php
                  $location_arr = getStoreLocationDropdownByBranch(getLoggeduserProfile()->restaurant_id);
                  ?>
                <div class = "row">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                            <div class="col-sm-6">
                                 {!!Form::select('to_store_id',$location_arr, null, ['class' => 'form-control mlselec6t','required'=>true,'placeholder' => 'Please select store','id'=>'to_store_id','placeholder'=>'Please select'  ])!!} 
                                 <span id = "error_msg_to_store_id"></span>
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
                             
                          
                            <div class="col-md-12 no-padding-h ">
                           <h3 class="box-title"> ITEMS </h3>
                           <button type="button" class="btn btn-danger btn-sm addNewrow" style="position: fixed;bottom: 45%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                            <div id = "requisitionitemtable" name="item_id[0]">
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Selection</th>
                                      <th>Description</th>
                                      <th >Bal Stock</th>
                                      <th >Unit</th>
                                      <th >QTY</th>
                                      <th>Location</th>
                                      <th>
                                      
                                      </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                      <tr>                                      
                                      <td>
                                        <input type="text" class="testIn form-control makemefocus" >
                                        <div class="textData" style="width: 100%;position: relative;z-index: 99;"></div>
                                      </td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td><button type="button" class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>
                                      </tr>
                        
                                   


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
    height: 80px !important;
   }
   .align_float_right
{
  text-align:  right;
}
 </style>
@endsection

@section('uniquepagescript')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
<link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">
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
        url: "{{route('store-c-requisitions.inventoryItems')}}",
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
          url: "{{route('store-c-requisitions.getInventryItemDetails')}}",
          data: {
            'id':$this.data('id')
          },
          success: function (response) {
            if(response == ''){
              form.errorMessage('Item Not found');
              return false;
            }
            $this.parents('tr').replaceWith(response);
            
            
          }
        });
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
                                    +'<td><button class="btn btn-danger btn-sm deleteparent"><i class="fa fa-trash" aria-hidden="true"></i></button></td>'
                                    +'</tr>');
        makemefocus();
    });

    
        

    </script>
@endsection


