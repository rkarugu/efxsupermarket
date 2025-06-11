
@extends('layouts.admin.admin')
@section('content')

<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> Receive controlled item {{$item->item_no}}</h3></div>
        <div class="box-body ">
            <form method="post" enctype="multipart/form-data"  onsubmit="return false;">
                {{csrf_field()}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-check">
                            <label class="form-check-label" style="margin-right:10px">
                                <input type="radio" class="form-check-input" name="EntryType" value="KEYED" checked>
                                Keyed Entry
                            </label>
{{--                         
                            <label class="form-check-label" style="margin-right:10px">
                                <input type="radio" class="form-check-input" name="EntryType" value="SEQUENCE">
                                Sequential
                            </label> --}}
                        
                            <label class="form-check-label">
                                <input type="radio" class="form-check-input" id="FileEntry" name="EntryType" value="FILE">
                                Import Serials
                            </label>
                            {{-- <label class="form-check-label">
                                <input type="file" name="ImportFile" onclick="document.getElementById('FileEntry').checked=true;">
                            </label> --}}
                        </div>
                        <br>
                      
                        
                    </div>
                </div>
                
            </form>
        </div>
    </div>
    <div class="box box-primary">
        <div class="box-body ">
            @include('message')
            <div class="row">
                <div class="col-md-12 hideme align_float_center" id="KEYED">
                        <div class="row">

                            <div class="col-md-4">
                                @if (count($itemSerials->where('status','New')) == 0)
                                <h4>Serial No</h4>
                                <hr>
                                @else
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Serial No</th>
                                            <th>Purchase Price</th>
                                            <th>Purchase Weight</th>
                                            <th>Value</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($itemSerials as $itesm)
                                        <tr>
                                            <td scope="row">{{$itesm->serial_no}}</td>
                                            <td scope="row">{{manageAmountFormat($itesm->purchase_price)}}</td>
                                            <td scope="row">{{$itesm->purchase_weight}}</td>
                                            <td scope="row">{{manageAmountFormat($itesm->value)}}</td>
                                            
                                            <td>
                                                <form  method="post" action="{{route('tyre-receive.EnterSerialNo.update',['id'=>$item->id,'controlled_id'=>$itesm->id])}}" class="submitMe">
                                                    {{csrf_field()}}
                                                    <button class="btn btn-sm btn-info">Delete</button>
                                                </form>
                                            </td>
                                        </tr>                                        
                                        @endforeach
                                    </tbody>
                                </table>
                                @endif
                                
                                <h5>Total Quantity: {{count($itemSerials)}}</h5>
                                <div >
                                    <form action="{{route('tyre-receive.EnterSerialNo.update',['id'=>$item->id])}}" method="post" class="submitMe" style="    display: inline-block;
                                        text-align: center;
                                        float: left;">
                                        {{csrf_field()}}
                                        @foreach ($itemSerials->where('status','New') as $itesm)
                                            {!! Form::hidden('controlled_id[]', $itesm->id) !!}                          
                                        @endforeach
                                        <button class="btn btn-sm btn-info">Update</button>
                                    </form>
                                    <a href="{{route($model.'.show', $item->getTyrePurchaseOrder->slug)}}" class="btn btn-primary btn-sm" style="    display: inline-block;
                                        text-align: center;
                                        float: right;">Close</a>
                                </div>

                            </div>
                            <div class="col-md-8">
                                <form  method="post" id="save_serialForm" action="{{route('tyre-receive.EnterSerialNo.save')}}" class="submitMe">
                                    {{csrf_field()}}
                                    {!! Form::hidden('id', $item->id, []) !!}
                                @php
                                    $totalItem =      $item->quantity - count($itemSerials);                          
                                @endphp
                                <table class="table table-bordered totalOfTotal">
                                    <thead>
                                        <tr>
                                            <th>Serial No</th>
                                            <th>Purchase Price</th>
                                            <th>Purchase Weight</th>
                                            <th>Value</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for ($i = 1;$i<=$totalItem;$i++)
                                            <tr>
                                                <td><input type="text" name="serial[{{$i}}]"  class="form-control" placeholder="" aria-describedby="helpId"></td>
                                                <td><input type="number" name="price[{{$i}}]"  class="form-control p_price updateTotalValue" placeholder="" aria-describedby="helpId"></td>
                                                <td><input type="number" name="weight[{{$i}}]" value="1" class="form-control p_weight updateTotalValue" placeholder="" aria-describedby="helpId"></td>
                                                <td>
                                                    <input type="text" readonly  class="form-control updateMyValueText" placeholder="" aria-describedby="helpId">
                                                    <input type="hidden" readonly  class="form-control updateMyValue" placeholder="" aria-describedby="helpId">
                                                </td>
                                            </tr>
                                        @endfor

                                    </tbody>       
                                    <tfoot>
                                        <tr>
                                            <td style="text-align: right">Total</td>
                                            <td id="TotalPrice">Total</td>
                                            <td></td>
                                            <td id="totalOfTotal"></td>
                                        </tr>
                                    </tfoot>                             
                                </table>

                                <button type="button" class="btn btn-lg btn-primary enterButton">Enter</button>
                            </form>
                            </div>
                            

                        </div>
                </div>
                {{-- <div class="col-md-12 hideme" id="SEQUENCE" style="display: none">
                    <form  method="post" onsubmit="return false;">
                        {{csrf_field()}}
                        <h4>Serial No</h4>
                        <hr>
                        <div class="form-group">
                          <label for="">Begin</label>
                          <input type="text" name="" id="" class="form-control" placeholder="" aria-describedby="helpId">
                        </div>
                        <div class="form-group">
                            <label for="">End</label>
                            <input type="text" name="" id="" class="form-control" placeholder="" aria-describedby="helpId">
                          </div>
                          <button type="submit" class="btn btn-lg btn-primary">Enter</button>
                    </form>
                </div> --}}
                <div class="col-md-12 hideme" id="FILE" style="display: none">
                    <div class="col-md-6" style="margin-bottom: 15px">
                        <a href="{{route('tyre-purchase.downloadSerials')}}" class="btn btn-success">Download Excel</a>
                    </div>
                    <div class="col-md-6" style="margin-bottom: 15px">
                        <form action="{{route('tyre-purchase.importSerials')}}" enctype="multipart/form-data" method="post">
                            {{csrf_field()}}
                            {!! Form::hidden('id', $item->id, []) !!}
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="file" name="import_serials" id="import_serials" class="form-control" style="color:black" required>
                                      </div>
                                </div>
                                <div class="col-sm-4">
                                    <button type="submit" class="btn btn-primary">Import Serials</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <br>
                    <br>
                    <form  method="post" onsubmit="return false;">
                        {{csrf_field()}}
                        <h4>Total Qunatity : {{count($itemSerials)}}</h4>
                        <hr>
                       
                    </form>
                </div>
            </div>
        </div>
    </div>



</section>


     


@endsection

@section('uniquepagestyle')


 <link rel="stylesheet" href="{{asset('assets/admin/dist/datepicker.css')}}">

 <style type="text/css">
   .select2{
    width: 100% !important;
   }
   #last_total_row td {
  border: none !important;
}

.align_float_right
{
  text-align:  right;
}
.align_float_center
{
  text-align:  center;
}

#requisitionitemtable input[type=number]{
  width:100px;

 } 
 #requisitionitemtable td{
  width:100px;

 } 
 </style>

@endsection



@section('uniquepagescript')  
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>
    $('input[name="EntryType"]').change(function()
    {
        $('.hideme').hide();
        $('#'+$(this).val()).show();
    });
    $('.updateTotalValue').change(function(){
        var price = parseFloat($(this).parents('tr').find('.p_price').val());
        var weight = parseFloat($(this).parents('tr').find('.p_weight').val());
        $(this).parents('tr').find('.updateMyValueText').val((price*weight).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        $(this).parents('tr').find('.updateMyValue').val(price*weight);
        totalOfTotal();
    });
    function totalOfTotal()
    {
        var updateMyValue = 0.00;
        $('.updateMyValue').each(function(key,val){
            if($(val).val() != ''){
                updateMyValue = updateMyValue + parseFloat($(val).val());
            }
        });
        $('#totalOfTotal').html(updateMyValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
        var TotalPrice = 0.00;
        $('.p_price').each(function(key,val){
            if($(val).val() != ''){
                TotalPrice = TotalPrice + parseFloat($(val).val());
            }
        });
        $('#TotalPrice').html(TotalPrice.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));

    }
    totalOfTotal();


    $(".enterButton").click(function(){
            //$("#billreceiptcall").submit();


                $('#loader-on').show();
                var postData = new FormData($('body').find('#save_serialForm')[0])
                var url = $('body').find('#save_serialForm').attr('action');

                
                //postData.append('_token',"{{csrf_token()}}");
                //console.log(postData);
                //postData.append('request_type',$(this).val());
                var $this = $(this);
                $.ajax({
                    url:url,
                    data:postData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    method:'POST',
                    success:function(out){

                        console.log(out);
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

                            ///var requestData=JSON.stringify(out.data);
                            //console.log(out.data);

                           
                            
                            //console.log('api_response',api_response);

                            form.successMessage(out.message);
                            if(out.location){
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




         })


</script>
@endsection


