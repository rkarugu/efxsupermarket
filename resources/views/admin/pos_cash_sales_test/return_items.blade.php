@extends('layouts.admin.admin')
@section('content')
<a href="{{ route('pos-cash-sales-test.index') }}" class="btn btn-primary">Back kkkkk</a>
<br>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border"><h3 class="box-title"> {!! $title !!} </h3></div>
         @include('message')
            <div class = "row">
                <div class = "col-sm-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="">Date</label>
                                <span class="form-control">{{$data->date}}</span>
                            </div>
                        </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Time</label>
                            <span class="form-control">{{$data->time}}</span>
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
            <div class = "row">
                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Customer</label>
                            <span class="form-control">{{$data->customer}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Payment Method</label>                                  
                            <span class="form-control">{{@$data->payment->title}}</span>
                        </div>
                    </div>
                </div>
                
                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Cash</label>
                            <span class="form-control">{{$data->cash}}</span>
                        </div>
                    </div>
                </div>

                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Change</label>
                            <span class="form-control">{{$data->change}}</span>
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
                           <h3 class="box-title"> Cash Sales</h3>

                            <form action="{{route('pos-cash-sales-test.return_items_post',base64_encode($data->id))}}" method="POST" class = "addExpense">
                                {{ csrf_field() }}
                                <input type="hidden" name="id" value="{{$data->id}}">                               
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Item</th>
                                      <th>Description</th>
                                      <th style="width: 90px;">Unit</th>
                                      <th style="width: 90px;">QTY</th>
                                      <th>Selling Price</th>
                                      <th>Location</th>
                                      <th style="width: 90px;">Discount</th>
                                      <th>VAT</th>
                                      <th>Total</th>
                                      <th>Dispatch Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $totalprice = $discount = $totalvat = $total =0;
                                    @endphp
                                    @foreach ($data->items as $item)
                                    @php
                                        $totalprice += $item->selling_price;
                                        $discount += $item->discount_amount;
                                        $totalvat += $item->vat_amount;
                                        $total +=$item->total;
                                    @endphp
                                       <tr>
                                           <td>
                                                <div class="checkbox" style="margin-top: 0;margin-bottom: 0;">
                                                    <label>
                                                        <input class="checked_return_items" type="checkbox" name="item[{{$item->id}}]" value="{{$item->id}}" onchange="enableQuantity(this,'#quantity-{{$item->id}}')">{{@$item->item->stock_id_code}}
                                                        <input type="hidden" name="stock_id_code" value="{{@$item->item->stock_id_code}}">
                                                    </label>
                                                </div>
                                            </td>
                                           <td>{{@$item->item->description}}</td>
                                           <td>{{@$item->item->pack_size->title}}</td>
                                           <td>
                                            <div class="form-group">
                                              <input type="text" name="quantity[{{$item->id}}]" max="{{$item->qty}}" readonly id="quantity-{{$item->id}}" class="form-control" value="{{$item->qty}}">
                                            </div>   
                                            </td>
                                           <td>{{$item->selling_price}}</td>
                                           <td>{{@$item->location->title}}</td>
                                           <td>{{$item->discount_amount}}</td>
                                           <td>{{$item->vat_amount}}</td>
                                           <td>{{$item->total}}</td>
                                           <td>
                                            @if ($item->is_dispatched == 1)
                                                <span>Dispatched By: {{$item->dispatch_by->name}}</span><br>
                                                <span>Date: {{date('d-M-Y',strtotime($item->dispatched_time))}}</span><br>
                                                <span>Time: {{date('H:i A',strtotime($item->dispatched_time))}}</span><br>
                                                <span>Disp No: {{$item->dispatch_no}}</span>
                                            @endif
                                           </td>
                                       </tr>
                                    @endforeach


                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Total Price
                                        </th>
                                        <td colspan="2">KES <span id="total_exclusive">{{$totalprice}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Discount
                                        </th>
                                        <td colspan="2">KES <span id="total_discount">{{$discount}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Total VAT		
                                        </th>
                                        <td colspan="2">KES <span id="total_vat">{{$totalvat}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Total
                                        </th>
                                        <td colspan="2">KES <span id="total_total">{{$total}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="11" style="text-align:left">
                                            <button type="submit" class="btn return_btn  btn-primary">Return</button>
                                        </th>
                                      </tr>
                                    </tfoot>
                                </table>
                            </form>
                            </div>
                       


                        </div>
                    </div>


    </section>
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

function enableQuantity(pro,param) {
    if($(pro).is(':checked')){
        $(param).removeAttr('readonly');
    }else{
        $(param).attr('readonly',true);
    }
  }
var form = new Form();
$(document).on('submit','.addExpense',function(e){
    e.preventDefault();
    var is_checked = 0;
    $('.checked_return_items').each(function () {
        if($(this).prop('checked')==true){
          is_checked=1;  
        }
    });
    
    if(is_checked==true){

        $('.return_btn').attr('disabled',true);

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
            success:async function (out) {
                $('#loader-on').hide();

                $(".remove_error").remove();
                if (out.result == 0) {
                    for (let i in out.errors) {
                        var id = i.split(".");
                        if (id && id[1]) {
                            $("[name='" + id[0] + "[" + id[1] + "]']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                        } else {
                            $("[name='" + i + "']").parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                            $("." + i).parent().append('<label class="error d-block remove_error w-100" id="' + i + '_error">' + out.errors[i][0] + '</label>');
                        }
                    }
                }
                if (out.result === 1) {


                    // var api_response=sendInvoiceRequestApi(out.data);
                    await printReceipt(out.receipt_url)
                    form.successMessage(out.message);
                    // if (out.location) {
                    //     setTimeout(() => {
                    //         location.href = out.location;
                    //     }, 1000);
                    // }
                }
                if (out.result === -1) {
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
    }else{
        alert('Please select any item first!')
    }

    
    
});

function printReceipt(slug) {
    console.log(slug)
    jQuery.ajax({
        url: slug,
        type: 'GET',
        async: false,   //NOTE THIS
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function (response) {
            var divContents = response;
            var printWindow = window.open('', '', 'width=600');
            printWindow.document.write(divContents);
            printWindow.document.close();
            printWindow.print();
            printWindow.close();

        }
    });

}
function sendInvoiceRequestApi(request_json){

    var raw = JSON.stringify(request_json);
    
    console.log(raw);

    var myHeaders = new Headers();
    myHeaders.append("Accept", "application/json");
    myHeaders.append("Content-Type", "application/json");
    myHeaders.append("Authorization", "Basic ZxZoaZMUQbUJDljA7kTExQ==");

    /*var raw = JSON.stringify({
      "invoice_date": "25_02_2022",
      "invoice_number": "2502202211344",
      "invoice_pin": "P051201909L",
      "customer_pin": "P051241778C", // optional
      "customer_exid": "", // tax exception number
      "grand_total": "219.50",
      "net_subtotal": "193.92",
      "tax_total": "25.58",
      "net_discount_total": "0",
      "sel_currency": "KSH",
      "rel_doc_number": "",
      "items_list": [
        "FLOURWHEATEXE2KG 1.50 50.00 75.00", // description quantity cost quantity total
        "SUGARNZOIA50KG 1.00 100.00 100.00",
        "0001.13.09 SPIRITTYPEJETFUEL 1.00 19.50 19.50", // hscode description quantity cost quantity total
        "0039.11.16 SOMEZERORATEDITEM 1.00 15.00 15.00",
        "0001.11.00 EXEMPTITEM 1.00 10.00 10.00"
      ]
    });*/



   
    var requestOptions = {
      method: 'POST',
      headers: myHeaders,
      body: raw,
      redirect: 'follow'
    };

    var esd_url="{{$esd_url}}";
    
    
    console.log(esd_url+"/api/sign?invoice+1");

    //var apiUrl="{{url('test/')}}";
    //console.log(testUrl); 

    // fetch("http://localhost:8089/api/sign?invoice+1", requestOptions) // url stored in db where it can be changed
    
    fetch(esd_url+"/api/sign?invoice+1", requestOptions) // url stored in db where it can be changed
      .then(response => response.text())
      .then(result => {
        // response was successful
        //console.log(result);
        var successval=0;    
        var save_esd_url ="{{route('confirm-invoice.save_esd')}}";
        //console.log('save_esd_url',save_esd_url);

        $.ajax({
            url:save_esd_url,
            data: {"apiData":result,status:1,"_token":"{{csrf_token()}}"},
            method:'POST',
            async:false,
            success:function(res){
                successval=1;
            }, error: function() {
                successval=0;
            }
        });        

        //console.log(successval)

        return successval;
        //new QRCode(document.getElementById("qrcode"), result);

      })
      .catch(error =>{
        console.log('error', error)
            var save_esd_url ="{{route('confirm-invoice.save_esd')}}";
            $.ajax({
                url:save_esd_url,
                data: {status:0,error:error,invoice_number:request_json.invoice_number,"_token":"{{csrf_token()}}"},
                method:'POST',
                async:false,
                success:function(res){
                    successval=0;
                }
            });


             return successval;
      });

        


}

</script>
<script src="{{asset('assets/admin/dist/bootstrap-datepicker.js')}}"></script>
    <script>
        $('.datepicker').datepicker({
        format: 'yyyy-mm-dd'
        });
    </script>
@endsection


