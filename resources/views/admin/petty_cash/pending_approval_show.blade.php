@extends('layouts.admin.admin')
@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
<div class="row ">
    <div class="col-md-12" style="padding-left: 29px;">
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('petty-cash.pending_approvals')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>
    <!-- Main content -->
    <form action="{{route('petty-cash.pending_approval_update',$data->id)}}" method="post" class="addExpense">
        {{csrf_field()}}
        {{method_field("PUT")}}
        <section class="content" style="padding-top: 10px;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                      
                    <div class="col-md-12 no-padding-h">
                        <h3>{{$data->petty_cash->petty_cash_no}} : Approval Stage {{$data->stage}}</h3>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">User</label>
                                  <span class="form-control">{{@$data->petty_cash->user->name}}</span> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">Date</label>
                                    <span class="form-control">{{date('d-M-Y',strtotime($data->created_at))}}</span> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">PETTY CASH NO</label>
                                    <span class="form-control">{{$data->petty_cash->petty_cash_no}}</span> 
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Bank Account</label>
                                  <span class="form-control">{{@$data->petty_cash->bank_account->account_number}}</span> 
                                </div>
                            </div>
                            
                       
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payment Date</label>
                                  <span class="form-control">{{date('Y-m-d',strtotime($data->petty_cash->payment_date))}}</span> 
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payment Method</label>
                                  <span class="form-control">{{@$data->petty_cash->payment_method->title}}</span> 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section class="content" style="padding-top: 10px;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">  
                    
                    <div class="col-md-12 no-padding-h">
                        
                        <table class="table table-bordered table-hover categoryTable" >
                            <thead>
                                <tr>
                                    <th style="width: 17.5%"> Account No. </th>
                                    <th > Branch </th> 
                                    <th style="width: 15%"> Payment For </th>
                                    <th style="width: 15%"> Collected By </th>
                                    <th style="width: 17.5%"> Amount </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $gross_amount = 0;
                                @endphp
                                @foreach ($data->petty_cash->items as $item)
                                    <tr class="item">
                                        <td>
                                        {{@$item->chart_of_account->account_name}}

                                        </td>
                                        <td>
                                        {{@$item->branch->name}}
                                        </td>
                                        <td>{{$item->payment_for}}</td>
                                        <td>{{$item->collected_by}}</td>
                                        
                                        <td>{{($item->amount)}}</td>
                                      
                                    </tr>
                                    @php
                                    $gross_amount += $item->amount;
                                @endphp
                                @endforeach         
                                
                            </tbody>
                            
                        </table>
                    </div>
                 
                    <div class="col-md-4 col-md-offset-8">
                        <br>
                        <table class="table">                            
                            <tr>
                                <td style="text-align:right">Total</td>
                                <td class="totalAll">{{$gross_amount}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12" style="text-align: right">
                        <button class="btn btn-danger addPettyCash" type="submit" value="approve" style="margin-right: 10px">Approve</button>
                        <button class="btn btn-danger addPettyCash" type="submit" value="reject" style="margin-right: 10px">Reject</button>
                    </div>
                </div>
            </div>
        </section>
         
    </form>
   
    @endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />
    <style>
        .select2.select2-container.select2-container--default
        {
            width: 100% !important;
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

@keyframes  spin{
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
        function printBill(slug)
        {
            jQuery.ajax({
                url: slug,
                type: 'GET',
                async:false,
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
                    location.href="{{route('petty-cash.index')}}";
                }
            });
                
        }
        var form = new Form();

        $(document).on('click','.addPettyCash',function(e){
            e.preventDefault();
            $('#loader-on').show();
            var postData = new FormData($(this).parents('form')[0]);
            var url = $(this).parents('form').attr('action');
            postData.append('_token',$(document).find('input[name="_token"]').val());
            postData.append('status',$(this).val());
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
                            if(out.print == 1){
                                $('.categoryTable tbody').html('');
                                printBill(out.location);
                            }
                            else{
                                location.href=out.location;
                            }
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

       
    </script>
@endsection
