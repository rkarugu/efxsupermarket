@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Process Sales Invoice Return </h3>
                    <a href="{{ route('transfers.return_list') }}" class="btn btn-primary"> Back </a>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                {{-- <form action="{{ route('transfers.returns.process', $return->return_number) }}" method="post" id='return-form'> --}}
                    <form action="{{ route('transfers.returns.process_return', $return->return_number) }}" method="post" id='return-form'>

                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="" class="control-label"> Return Number </label>
                            <input type="text" class="form-control" name="invoice" value="{{ $return->return_number }}" readonly>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="" class="control-label"> Return Date </label>
                            <input type="text" class="form-control" value="{{ $return->return_date }}" readonly>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="" class="control-label"> Route </label>
                            <input type="text" class="form-control" name="route" value="{{ $return->route }}" readonly>
                        </div>
                        <div class="col-md-3 form-group" style="display:none;">
                            <label for="" class="control-label"> Bin </label>
                            <input type="text" class="form-control" name="bin" value="{{ $returnItems->first()->bin}}" readonly>
                        </div>
                    </div>

                    <hr>
                    <h3 style="font-weight: bold; font-size: 16px;"> Return Lines </h3>
                    <hr>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th> Item Code</th>
                                <th> Item Name</th>
                                <th> Bin Location</th>
                                <th> Returned Qty</th>
                                <th> Physical Quantity</th>
                                <th>Comments</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($returnItems as $returnItem)
                                <tr>
                                    <input type="hidden" name="item_ids[]" value="{{ $returnItem->id }}">
                                    <th style="width: 3%;" scope="row">{{ $loop->index + 1 }}</th>
                                    <td>{{ $returnItem->stock_id_code }}</td>
                                    <td>{{ $returnItem->description }}</td>
                                    <td>{{ $returnItem->bin }}</td>
                                    <td>{{ $returnItem->return_quantity }}</td>
                                    <td>
                                        <input type="number" name="received_quantity[]" class="form-control received_quantity" placeholder="Qty to receive now" value="{{$returnItem->return_quantity}}" readonly>
                                    </td>
                                    <td>
                                    <input type="text" name="note[]" id='note'  class="form-control" placeholder="Comment, return reason" required>
                                    </td>
                                    <td>
                                        <input type="checkbox" class="form-check-input reject-checkbox"
                                        data-target=".physical-quantity" value="yes" style="margin-top: 10px;" name="reject-{{ $returnItem->id }}"  id="reject">
                                        <label for="reject" class="form-check-label"> Reject</label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="otp-send">
                        <!-- <input type="submit" value="Process Returns" class="btn btn-primary"> -->
                         <button type="button" class="btn btn-primary btn-sn opt"  value="proceed" >
                                      Continue 
                                    </button>
                                </div>
                    </div>

                    <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Verify Otp</h5>

                              <button type="button" class="close" data-dismiss="modal" value="proceed" aria-label="Close" style="margin-top:-22px !important">
                                <span aria-hidden="true">&times;</span>
                              </button>
                          </div>
                          <div class="modal-body">
                        <div id="message" style="font-size: 14px; color:green;padding: 5px; text-align: center;"></div>
                        <div id="error" style="font-size: 14px; color:red;padding: 5px; text-align: center;"></div>

                        <input type="text" class="form-control"  id="otp"  value="" placeholder="Enter Otp">
                      
                          <div class="modal-footer">
                            <!-- <input type="submit" value="Process Returns"  class="btn btn-primary"> -->
                             <button type="submit" class="btn btn-primary btn-sm " id="prbtn3" value="send_request">
                            Send OTP</button>
                             <button type="submit" class="btn btn-primary btn-sm " id="prbtn2" value="validate">
                            Validate OTP</button>
        
                            <button type="submit" class="btn btn-primary btn-sm " id="prbtn" value="process">Process Returns</button>
                           
                           
                          </div>
                        </div>
                      </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('js/sweetalert.js')}}"></script>
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
<script>
         document.addEventListener("DOMContentLoaded", function() {
            const rejectCheckboxes = document.querySelectorAll('.reject-checkbox');

            rejectCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {

                    const physicalQuantityInput = this.closest('tr').querySelector('.received_quantity');
                    if (this.checked) {
                        physicalQuantityInput.removeAttribute('readonly');
                
                    } else {
                        physicalQuantityInput.setAttribute('readonly', true);
                       
                    }
                });
            });
        });
</script>
<script type="text/javascript">
$( document ).ready(function() {
    var comment ='';
    $('.opt').click(function(){
            $(this).prop('disabled', true);
        });
// disable buttons before actions
$('#prbtn').prop('disabled', true);
// $('.opt').prop('disabled', true);
//$('.opt').prop('disabled', true);
if({{count($returnItems)}} === 0){
    $('.opt').prop('disabled', true);
}

$("#modelId").on("hidden.bs.modal", function () {
    // put your default event here
     $('#loader-on').hide();
});

$("button").click(function(e) {
    e.preventDefault();

    let url = "{{route('transfers.returns.process_return')}}";
  // check if reject checked  or comment inputted
  var names= document.getElementsByName('note[]');
    var selected = document.querySelectorAll('.reject-checkbox');
       let checkboxes = [];
        for(key=0; key < selected.length; key++)  {
            console.log(selected[key].checked);
            if(selected[key].checked){
                checkboxes.push(selected[key].checked)
            }

        }
     //console.log(checkboxes);
        let notes= [];
        for(key=0; key < names.length; key++)  {
            //console.log(names[key].value);
            if(names[key].value){
                notes.push(names[key].value)
            }

        }
        if(notes.length === 0){
            form.errorMessage('Please enter comment to proceed.');  
            return;  
        }
       

       
    var postData = new FormData($(this).parents('form')[0]);
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('request_type',$(this).val());
    if(postData.get("request_type") ===''){
      $('#loader-on').hide();
      }

  //console.log($('#reject').is(":checked"))
    if(postData.get("request_type") ==='proceed' && checkboxes.length === 0){
        $('#loader-on').show();

            $.ajax({
                url:url,
                data:postData,
                contentType: false,
                cache: false,
                processData: false,
                method:'POST',
                success:function(out){
                   // console.log(JSON.parse(out))
                let  result = JSON.parse(out)
                $('#loader-on').hide();
                    if(result.status === 1) {
                        form.successMessage(result.message);
                         window.location.href = "{{route('transfers.return_list')}}";
                    }
                    if(result.status === 0) {
                        form.errorMessage(result.message);
                    }
                },
                
                error:function(err)
                {
                  $('#loader-on').hide();
                    form.errorMessage('Something went wrong');                          
                }
            });
    
    }else{
           $("#modelId").modal('show');
    }
});


$("button").click(function(e) {
    e.preventDefault();

    let url = "{{route('transfers.returns.otp')}}";
//
    var postData = new FormData($(this).parents('form')[0]);
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('request_type',$(this).val());
    if(postData.get("request_type") ===''){
      $('#loader-on').hide();
      }

    if(postData.get("request_type") ==='send_request' && postData.get("note[]") ){
        $('#loader-on').show();

    $.ajax({
        url:url,
        data:postData,
        contentType: false,
        cache: false,
        processData: false,
        method:'POST',
        success:function(out){
           // console.log(JSON.parse(out))
        let  result = JSON.parse(out)
        $('#loader-on').hide();
            if(result.status === 1) {
                form.successMessage(result.message);

            }
            if(result.status === 0) {
                form.errorMessage(result.message);
            }
        },
        
        error:function(err)
        {
          $('#loader-on').hide();
            form.errorMessage('Something went wrong');                          
        }
    });
    
    }


});


$("button").click(function(e) {
    e.preventDefault();

    let url = "{{route('transfers.returns.process_return')}}";
    var postData = new FormData($(this).parents('form')[0]);
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('request_type',$(this).val());
    //console.log(postData.get("request_type"));
    if(postData.get("request_type") ===''){
      $('#loader-on').hide();
      }

    if(postData.get("request_type") ==='process'){
        console.log($('#reject').is(":checked"));
        if($('#reject').is(":checked")){

         $('#loader-on').show();
        $.ajax({
            url:url,
            data:postData,
            contentType: false,
            cache: false,
            processData: false,
            method:'POST',
            success:function(out){
            let  result = JSON.parse(out)
            $('#loader-on').hide();
             if(result.status === 1) {
                    form.successMessage(result.message);
                    $('#modelId').modal('toggle')
                    window.location.href = "{{route('transfers.return_list')}}";
                }
             if(result.status === 0) {
                    form.errorMessage(result.message);
                }
            },
            
            error:function(err)
            {
              $('#loader-on').hide();
                $(".remove_error").remove();
                form.errorMessage('Something went wrong');                          
            }
        });

         }
        
        }
    });


$("button").click(function(e) {
    e.preventDefault();
    $('#error').html('');
    $('#message').html('');
 let url = "{{route('transfers.returns.check')}}";

    if($("#otp").val().length === 6){
    var postData = new FormData($(this).parents('form')[0]);
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('otp',$("#otp").val());
    postData.append('invoice',"<?php echo $return->return_number; ?>");   
    postData.append('request_type',$(this).val());
    //console.log(postData.get("request_type"));
    if(postData.get("request_type") ===''){
      $('#loader-on').hide();
      }  
   
     // console.log($("#otp").val(),"<?php echo $return->return_number; ?>");
    if(postData.get("request_type") ==='validate'){
        $('#loader-on').show();
    $.ajax({
        url:url,
        data:postData,
        contentType: false,
        cache: false,
        processData: false,
        method:'POST',
        success:function(out){
         $('#loader-on').hide();
           // console.log(JSON.parse(out))
        let  result = JSON.parse(out);
            if(result.status === 1) {
                //window.location.reload();
                //form.successMessage(result.message);
                $('#prbtn').prop('disabled', false);
                 $('#message').html(result.message);

            }
            if(result.status === 0) {

                 $('#prbtn').prop('disabled', true);
                 $('#error').html(result.message);
                //form.errorMessage(result.message);
            }
        },
        
        error:function(err)
        {
          $('#loader-on').hide();
          $('#prbtn').prop('disabled','disabled');
            form.errorMessage('Something went wrong');                          
        }
    });
    }


     }else{
        //$('#error').html('Enter OTP of 6 digits');
     }
 });
})
    //}
</script>
@endsection