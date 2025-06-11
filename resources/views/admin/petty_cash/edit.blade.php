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
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('petty-cash.index')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>
    <!-- Main content -->
    <form action="{{route('petty-cash.update',$data->id)}}" method="post" class="addExpense">
        {{csrf_field()}}
        {{method_field("PUT")}}
        <section class="content" style="padding-top: 10px;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                      
                    <div class="col-md-12 no-padding-h">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">User</label>
                                  <span class="form-control">{{@$data->user->name}}</span> 
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
                                    <span class="form-control">{{$data->petty_cash_no}}</span> 
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Bank Account</label>
                                  <select name="wa_bank_account_id" class="form-control payment_account">
                                    <option value="{{@$data->bank_account->id}}" selected>
                                        {{@$data->bank_account->account_number}}
                                    </option>

                                  </select>
                                </div>
                            </div>
                            
                       
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payment Date</label>
                                  <input type="date" name="payment_date" class="form-control" value="{{$data->payment_date ? date('Y-m-d',strtotime($data->payment_date)) : NULL}}">
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payment Method</label>
                                  <select name="payment_method_id" class="form-control payment_method">

                                    <option value="{{@$data->payment_method->id}}" selected>
                                        {{@$data->payment_method->title}}
                                    </option>

                                  </select>
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
                        <div>
                            <div class="category_lists"></div>
                        </div>
                        <button class="btn btn-primary newItem" data-id="0"style="position: fixed;top: 30%;left:4%;"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        <table class="table table-bordered table-hover categoryTable" >
                            <thead>
                                <tr>
                                    <th style="width: 17.5%"> Account No. </th>
                                    <th > Branch </th> 
                                    <th style="width: 15%"> Payment For </th>
                                    <th style="width: 15%"> Collected By </th>
                                    <th style="width: 17.5%"> Amount </th>
                                    <th style="width: 5%"> <button class="btn btn-primary newItem" data-id="0"> <i class="fa fa-plus" aria-hidden="true"></i> </button> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $gross_amount = 0;
                                @endphp
                                @foreach ($data->items as $item)
                                    <tr class="item">
                                        <td>
                                            <select class=" form-control category_list" name="category_list[{{$item->id}}]" >
                                                <option value="{{@$item->chart_of_account->id}}" selected>
                                                    {{@$item->chart_of_account->account_name}}
                                                </option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control branch_id" name="branch_id[{{$item->id}}]" >
                                                <option value="{{@$item->branch_id}}" selected>
                                                {{@$item->branch->name}}
                                                </option>
                                            </select>
                                        </td>
                                        <td><input type="text" placeholder="Payment For" name="payment_for[{{$item->id}}]" class="form-control payment_for" value="{{$item->payment_for}}"></td>
                                        <td><input type="text" placeholder="Collected By" name="collected_by[{{$item->id}}]" class="form-control collected_by" value="{{$item->collected_by}}"></td>
                                        
                                        <td><input type="number" placeholder="Amount" name="amount[{{$item->id}}]" class="amount thisChange form-control" value="{{($item->amount)}}"></td>
                                        <td>
                                            <button class="btn btn-primary deleteMe"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                        </td>
                                    </tr>
                                    @php
                                    $gross_amount += $item->amount;
                                @endphp
                                @endforeach         
                                <tr>
                                    <td>
                                        <select class=" form-control category_list" name="category_list[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control branch_id" name="branch_id[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Payment For" name="payment_for[0]" class="form-control payment_for">
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Collected By" name="collected_by[0]" class="form-control collected_by">
                                    </td>
                                    <td>
                                        <input type="number" placeholder="Amount" name="amount[0]" class="amount thisChange form-control">
                                    </td>
                                    <td>
                                        <button class="btn btn-primary deleteMe"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                    </td>
                                </tr>
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
                        <button class="btn btn-danger addPettyCash" type="submit" value="process" style="margin-right: 10px">Process</button>
                        <button class="btn btn-danger addPettyCash" type="submit" value="save" style="margin-right: 10px">Save</button>
                    </div>
                </div>
            </div>
        </section>
         
    </form>
    <table id="newRow" style="display: none">
    <tr>
            <td>
                <select class=" form-control category_list cat_name" >
                </select>
            </td>
            <td>
                <select class="form-control branch_id branch_id_name" >
                </select>
            </td>
            <td>
                <input type="text" placeholder="Payment For"  class="form-control payment_for payment_for_name">
            </td>
            <td>
                <input type="text" placeholder="Collected By"  class="form-control collected_by collected_by_name">
            </td>
            <td>
                <input type="number" placeholder="Amount"  class="amount thisChange form-control amount_name">
            </td>
            <td>
                <button class="btn btn-primary deleteMe"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
            </td>
        </tr>
    </table>
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
        $(document).on('keyup change','.thisChange',function(e){
            e.preventDefault();
            if($(this).val() == '' || $(this).parents('tr').find('.amount').val() == '')
            {
                return false;                
            }
            getTotal();
        });
        var form = new Form();

$(document).on('click','.addPettyCash',function(e){
    e.preventDefault();
    $('#loader-on').show();
    var postData = new FormData($(this).parents('form')[0]);
    var url = $(this).parents('form').attr('action');
    postData.append('_token',$(document).find('input[name="_token"]').val());
    postData.append('type',$(this).val());
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
function printBill(slug)
    {
        jQuery.ajax({
            url: slug,
            type: 'GET',
            async:false,   //NOTE THIS
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
            location.reload();
            }
        });
              
    }
        function getTotal(){
            $('.hideme').removeAttr('style');
            var total = 0;
            $(document).find('.amount').each(function(key,val){
                if($(val).val() != ''){
                    total = parseFloat(total) + parseFloat($(val).val());
                }
            });  
            $('.totalAll').html((total).toFixed(2));
        }
        $(document).on('click','.deleteMe',function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            getTotal();
        });
        $(document).on('click','.newItem',function(e){
            e.preventDefault();
            $(".category_list").select2('destroy');
            $(".vat_list").select2('destroy');
            $(".branch_id").select2('destroy');
            var itemNo = $(this).data('id');
            var newItemNo = parseInt(parseInt(itemNo) + 1);
            $('.cat_name').attr('name','category_list['+newItemNo+']');
            $('.branch_id_name').attr('name','branch_id['+newItemNo+']');
            // $('.receive_from_name').attr('name','receive_from['+newItemNo+']');
            $('.payment_for_name').attr('name','payment_for['+newItemNo+']');
            // $('.collected_by_name').attr('name','collected_by['+newItemNo+']');
            $('.amount_name').attr('name','amount['+newItemNo+']');
            $('.gl_tag_name').attr('name','gl_tag['+newItemNo+']');
            $('.department_name').attr('name','department['+newItemNo+']');
            var item = $('#newRow tbody tr').clone();
            $(item).find('.cat_name').removeClass('cat_name');
            // $(item).find('.name_name').removeClass('name_name');
            $(item).find('.branch_id_name').removeClass('branch_id_name');
            $(item).find('.payment_for_name').removeClass('payment_for_name');
            // $(item).find('.collected_by_name').removeClass('collected_by_name');
            $(item).find('.amount_name').removeClass('amount_name');
            $(item).find('.remove_error').remove();
            $('.newItem').data('id',newItemNo);
            $('.categoryTable').append(item);
            category_list();
            branch_id();
          
        });
        
        var branch_id = function(){
            $(".branch_id").select2(
            {
                placeholder:'Select branch',
                ajax: {
                    url: '{{route("expense.branches")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        }
        var payment_account = function(){
            $(".payment_account").select2(
            {
                placeholder:'Select Account',
                ajax: {
                    url: '{{route("petty-cash.bank_accounts")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        }
       
        var category_list = function(){
            $(".category_list").select2(
            {
                placeholder:'Select category',
                ajax: {
                    url: '{{route("petty-cash.category_list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        
        var payment_method = function(){
            $(".payment_method").select2(
            {
                placeholder:'Select Payment Method',
                ajax: {
                    url: '{{route("expense.payment_method")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.text};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        $(document).ready(function () {
            category_list();
           
            branch_id();
            payment_account();
            payment_method();
            $('body').addClass('sidebar-collapse');
        });
  
    </script>
@endsection
