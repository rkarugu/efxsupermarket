@extends('layouts.admin.admin')

@section('content')
@php
    $settings = getAllSettings();
@endphp
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="box-header-flex">
                <h3 class="box-title"> Edit {{ $title }} </h3>
                <div>
                    <a href="{{ route($model.'.index') }}" class="btn btn-primary"><i class="fas fa-long-arrow-alt-left"></i> Back</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>
            <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.update',$repair->id) }}">
                @csrf
                @method('PATCH')
                <div class="box-body">
                    <div class="form-group">
                        <label for="device" class="col-sm-2 control-label">Device</label>
                        <div class="col-sm-10">
                            <select name="device" id="device" class="form-control select2">
                                <option value="">Choose Device</option>
                                @foreach ($devices as $device)
                                    <option value="{{$device['id']}}" @if ($repair->device_id==$device['id']) selected @endif>{{ $device['device_no'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="repair_cost" class="col-sm-2 control-label">Repair Cost</label>
                        <div class="col-sm-10">
                            <input class="form-control" name="repair_cost" id="repair_cost" type="number" value="{{$repair->repair_cost}}">  
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="charge_to" class="col-sm-2 control-label">Charge To</label>
                        <div class="col-sm-10">
                            <select name="charge_to" id="charge_to" class="form-control select2">
                                <option value="">Choose </option>
                                <option value="{{ $settings['COMPANY_NAME'] }}" @if ($repair->charge_to==$settings['COMPANY_NAME']) selected @endif >{{ $settings['COMPANY_NAME'] }}</option>
                                <option value="Staff" @if ($repair->charge_to=="Staff") selected @endif >Staff</option>
                            </select>
                        </div>
                    </div>
                    <div id="chargedUserDiv" class="form-group">
                        <label for="charged_user" class="col-sm-2 control-label">Charged Staff</label>
                        <div class="col-sm-10">
                            <select name="charged_user" id="charged_user" class="form-control select2">
                                <option value="">Choose </option>
                                @foreach ($staffs as $staff)
                                    <option @if ($repair->charged_user==$staff->id) selected @endif value="{{$staff->id}}">{{ $staff->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="comment" class="col-sm-2 control-label">Comment</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="comment" id="comment">{{$repair->comment}}</textarea>
                        </div>
                    </div>
                </div>      
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary editDeviceSimCard">Edit</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection


@section('uniquepagestyle')
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
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
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
   
<script>
    $(function () {
        $('.select2').select2();

        if ($('#charge_to').val() === "Staff") {
            $('#chargedUserDiv').show();
        } else {
            $('#chargedUserDiv').hide();
        }
        
        
        

        $('#charge_to').on('change', function () {
            $('#chargedUserDiv').hide();
            $('#charged_user').val(null).trigger('change');

            if ($(this).val() === 'Staff') {
                $('#chargedUserDiv').show(); 
            } else {
                $('#chargedUserDiv').hide();
            }
        });
    });
    var form = new Form();

    $(document).on('click','.editDeviceSimCard',function(e){
        e.preventDefault();

        let processDataBtn = $('.editDeviceSimCard');
    
        processDataBtn.prop('disabled', true).text('Processing...');
        processDataBtn.prop('disabled', true);
        
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
                    setTimeout(
                    function() 
                    {                        
                        location.href = `{{route($model.'.index')}}`;
                    }, 3000);                    
                    
                }
                if(out.result === -1) {
                    let errorMessage = '';
                        if (out.message) {
                            errorMessage = out.message
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            html: errorMessage,
                        });
                }
            },
            
            error:function(err)
            {
                $(".remove_error").remove();
                
                let errorMessage = '';
                    if (err?.responseJSON?.errors) {
                        for (let key in err.responseJSON.errors) {
                            if (err.responseJSON.errors.hasOwnProperty(key)) {
                                errorMessage += err.responseJSON.errors[key].join('<br>') + '<br>';
                            }
                        }
                    } else if (err?.responseJSON?.error) {
                        errorMessage = err.responseJSON.error
                    }else{
                        errorMessage = 'Something went wrong.'
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        html: errorMessage,
                    });
            },
                complete: function() {
                    getDataBtn.prop('disabled', false).text(originalGetDataText);
                    processDataBtn.prop('disabled', false).text(originalProcessDataText);
                }
        });
    });       
      </script>
@endsection
