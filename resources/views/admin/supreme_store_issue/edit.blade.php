
@extends('layouts.admin.admin')
@section('content')
<form method="POST" action="{{ route($model.'.update',$row->slug) }}" accept-charset="UTF-8" class="" onsubmit="return false;" enctype="multipart/form-data" >
  <a href="{{ route($model.'.index') }}" class="btn btn-primary">Back</a>
  <br>
  <section class="content">    
    <div class="box box-primary">
      <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
      @include('message')
      {{ csrf_field() }}
      {{method_field('PATCH')}}
      <?php 
        $requisition_no = $row->requisition_no;
        $default_branch_id = $row->restaurant_id;
        $default_department_id = $row->wa_department_id;
        $requisition_date = $row->requisition_date;
        $getLoggeduserProfileName =  $row->getrelatedEmployee->name;
        $default_to_store_id = $row->to_store_id;
      ?>
      <div class = "row">
        <div class = "col-sm-6">
          <div class = "row">
            <div class="box-body">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label">Requisition No.</label>
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
                  {!! Form::text('emp_name', $getLoggeduserProfileName, ['maxlength'=>'255','placeholder' => '', 'required'=>true, 'class'=>'form-control','readonly'=>true]) !!}  
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
                <label for="inputEmail3" class="col-sm-5 control-label">To Store</label>
                <div class="col-sm-6">
                  <span class="form-control">{{ @$row->getRelatedToLocationAndStore->location_name }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class = "row">
            <div class="box-body">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-5 control-label">Manual Doc No</label>
                <div class="col-sm-6">
                  <span class="form-control">{{ @$row->manual_doc_no }}</span>
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
          <h3 class="box-title"> Items</h3>
          <div id = "requisitionitemtable">
            <table class="table table-bordered table-hover" id="mainItemTable">
              <thead>
                <tr>
                  <th>Selection</th>
                  <th>Description</th>
                  <th >Bal Stock</th>
                  <th >Unit</th>
                  <th >QTY</th>
                  <th >Issue QTY</th>
                  <th>Location</th>
                </tr>
              </thead>
              <tbody>                    
                @foreach ($row->getRelatedItem as $item)                                       
                  <tr>                                      
                    <td>
                        <input type="hidden" name="item_id[{{@$item->id}}]" class="itemid" value="{{@$item->id}}">
                        {{@$item->getInventoryItemDetail->stock_id_code}}
                    </td>
                    <td>{{@$item->getInventoryItemDetail->title}}</td>
                    <td>{{@$item->getInventoryItemDetail->getAllFromStockMovesC->where('wa_location_and_store_id',$item->store_location_id)->sum('qauntity')}}</td>
                    <td>{{@$item->getInventoryItemDetail->pack_size->title}}</td>
                    <td>{{$item->quantity}}</td>
                    <td><input style="padding: 3px 3px;" onkeyup="getTotal(this)" onchange="getTotal(this)" type="number" name="item_quantity[{{@$item->id}}]" data-id="{{@$item->id}}" class="quantity form-control" value="{{$item->quantity}}"></td>
                    <td>{{@$item->location->location_name}}</td>    
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
</script>
@endsection