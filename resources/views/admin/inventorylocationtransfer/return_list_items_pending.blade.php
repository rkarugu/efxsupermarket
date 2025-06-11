@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Process Sales Invoice Return </h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary"> Back </a>
                 
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="{{ route('transfers.returns.process_return_pending') }}" method="post" id='return-form'>
                    {{ csrf_field() }}

                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="" class="control-label"> Return Number </label>
                            <input type="text" class="form-control" name="return_number" value="{{ $return->return_number }}" readonly>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="" class="control-label"> Return Date </label>
                            <input type="text" class="form-control" value="{{ $return->return_date }}" readonly>
                        </div>

                        <div class="col-md-3 form-group">
                            <label for="" class="control-label"> Route </label>
                            <input type="text" class="form-control" name="route" value="{{ $return->route }}" readonly>
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
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Physical Quantity</th>
                                <th>Return Reason</th>
                                <th>Comments</th>
                                <th><input type="checkbox" onclick="CheckAll('reject-checkbox', this)" />
                                <label for="reject" class="form-check-label"> Reject</label>
                            </th>
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
                                    <td>{{ $returnItem->sp }}</td>
                                    <td>{{ number_format(($returnItem->return_quantity * $returnItem->sp),2) }}</td>
                                    <td>
                                        <input type="number" name="received_quantity[]" class="form-control received_quantity" placeholder="Qty to receive now" value="{{$returnItem->return_quantity}}" readonly>
                                    </td>
                                    <td>{{ $returnItem->return_reason }}</td>
                                    <td>
                                    <input type="text" name="note[]" id='note'  class="form-control" placeholder="Comment, return reason" >
                                    </td>
                                   <td>
                                      <input type="checkbox" class="form-check-input reject-checkbox"
                                        data-target=".physical-quantity" value="yes" style="margin-top: 10px;" name="reject-{{ $returnItem->id }}"  id="reject" readonly onclick="return false;">
                                        <label for="reject" class="form-check-label"></label>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end">
                        <div class="otp-send row ">
                            <input type="submit" class="btn btn-primary btn-sn " value=" Confirm Return" >
                             <button type="button" class="btn btn-primary btn-sn opt"  value="proceed" >
                                      Reject Return 
                                </button>
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

//check or uncheck reject
function CheckAll(className, elem) {
    console.log(className);
        var elements = document.getElementsByClassName(className);
        var l = elements.length;
          let val = $('#note').val();
        if (elem.checked) {
            for (var i = 0; i < l; i++) {
                elements[i].checked = true;
                $("input[name='note[]']").val(val);

            }
        } else {
            for (var i = 0; i < l; i++) {
                elements[i].checked = false;
                //$("input[name='note[]']").val('');
            }
        }
    }
</script>
<script type="text/javascript">
$( document ).ready(function() {
    var comment ='';
// disable buttons before actions
$('#prbtn').prop('disabled', true);
if({{count($returnItems)}} === 0){
    $('.opt').prop('disabled', true);
}

$("#modelId").on("hidden.bs.modal", function () {
    // put your default event here
     $('#loader-on').hide();
});


$("button").click(function(e) {
    e.preventDefault();

      // check if reject checked  or comment inputted
  var names= document.getElementsByName('note[]');
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
    var selected = document.querySelectorAll('.reject-checkbox');
       let checkboxes = [];

        for(key=0; key < selected.length; key++)  {
            console.log(selected[key].checked);
            if(selected[key].checked){
                checkboxes.push(selected[key].checked)
            }

        }
     //console.log(checkboxes);
    let url = "{{route('transfers.returns.process_return')}}";
    var postData = new FormData($(this).parents('form')[0]);
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('request_type',$(this).val());
    //console.log(postData.get("request_type"));
    if(postData.get("request_type") ===''){
      $('#loader-on').hide();
      }
    if(postData.get("request_type") ==='proceed' &&  checkboxes.length > 0){
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
                    window.location.href = "{{route('transfers.return_list_groups')}}";
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


})

</script>
@endsection