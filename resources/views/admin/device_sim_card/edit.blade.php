@extends('layouts.admin.admin')

@section('content')
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
            <form class="validate form-horizontal"  role="form" method="POST" action="{{ route($model.'.update',$simCard->id) }}">
                @csrf
                @method('PATCH')
                <div class="box-body">
                    <div class="form-group">
                        <label for="imei" class="col-sm-2 control-label">IMEI</label>
                        <div class="col-sm-10">
                            <input minlength="8" maxlength="50" placeholder="Device Sim Card" class="form-control" name="imei" type="text" id="imei" value="{{ $simCard->phone_number }}">  
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
   
<script>
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
