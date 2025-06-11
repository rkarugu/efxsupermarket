
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
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('cheques.list')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>
    <!-- Main content -->
    <form  method="post" >
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$data->id}}">
        <section class="content" style="min-height: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                
                    @include('message')
                    <div class="col-md-8 no-padding-h">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payee</label>                                  
                                  <select class="form-control" name="supplier" id="selectsupplier">
                                      @if ($data->payee)
                                      <option value="{{$data->payee->id}}" selected>{{$data->payee->supplier_code}}</option>
                                      @endif
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Bank Account</label>                                  
                                  <select class="form-control" name="bank_account" id="selectBankAccount">
                                    @if ($data->payment_account)
                                    <option value="{{$data->payment_account->id}}" selected>{{$data->payment_account->account_name}} ({{$data->payment_account->account_code}})</option>
                                    @endif
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <br>
                                <div class="form-group">
                                    <h5>Balance: <span id="balance">0</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">Mailing Address</label>
                                  <textarea name="mailing_address" class="form-control" cols="30" rows="5">{{$data->mailing_address}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Payment Date</label>                                  
                                  <input type="date" class="form-control due_datewa_payment_terms" name="payment_date" id="bill_date" value="{{$data->payment_date}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Cheque No.</label>
                                  <input type="text" name="cheque_no" class="form-control" value="{{$data->cheque_no}}" placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Branch</label>
                                  <select class="form-control branches" name="branch" id="branches">
                                    @if ($data->branch)
                                    <option value="{{$data->restaurant_id}}" selected>{{$data->branch->name}}</option>
                                    @endif
                                </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="text-align: right">
                        <br>
                        <h5 style="margin: 0; margin-bottom: 0;">{{strtoupper('amount')}}</h5>
                        <h1 style="margin: 0; margin-top: 0;">KSH <span class="totalAll"> {{manageAmountFormat($data->total)}} </span></h1>
                    </div>
                </div>
            </div>
        </section>


        <section class="content" style="padding-top: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">  
                    <div class="col-md-4 col-md-offset-8" style="text-align: right;">
                        <div class="form-group roe">
                            <label class="col-sm-4" >Amounts are</label>       
                            <div class="col-sm-8">
                                <select class=" form-control taxCheck" name="tax_check" id="taxCheck" onchange="getTotal();">
                                    @foreach (tax_amount_type() as $key => $item)
                                        @php
                                            $selected = '';
                                        @endphp
                                        @if ($data->tax_amount_type == $item)
                                            @php
                                                $selected = 'selected';
                                            @endphp
                                        @endif
                                        <option value="{{$key}}" {{$selected}}>{{$item}}</option>
                                    @endforeach                                    
                                </select>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12 no-padding-h">
                        <div>
                            <div class="category_lists"></div>
                        </div>
                        <table class="table table-bordered table-hover categoryTable" >
                            <thead>
                                <tr>
                                    <th> Category </th>
                                    <th> Description </th>
                                    <th> Amount </th>
                                    <th class="hideme"> Vat </th>
                                    <th> <button class="btn btn-primary newItem" data-id="{{$data->categories ? $data->categories[0]->id : '0'}}"> Add Items </button> </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->categories as $item)
                                <tr>
                                    <td>
                                        <select class=" form-control category_list" name="category_list[{{$item->id}}]" >
                                            <option value="{{$item->category->id}}" selected>{{$item->category->account_name}} ({{$item->category->account_code}})</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Description" value="{{$item->description}}" name="description[{{$item->id}}]" class="form-control description">
                                    </td>
                                    <td>
                                        @if ($data->tax_amount_type == 'Inclusive of Tax')
                                        <input type="number" placeholder="Amount" value="{{$item->total}}" name="amount[{{$item->id}}]" class="amount thisChange form-control">
                                        @else
                                        <input type="number" placeholder="Amount" value="{{$item->amount}}" name="amount[{{$item->id}}]" class="amount thisChange form-control">
                                        @endif
                                    </td>
                                    <td class="hideme">
                                        <select class="vat_list form-control thisChange" name="vat_list[{{$item->id}}]" >
                                            
                                            @if ($item->tax_manager)
                                            <option value="{{$item->tax_manager->id}}" selected>{{$item->tax_manager->title}} ({{$item->tax_manager->tax_value}})</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" class="vat_percentage" value="{{$item->tax}}">
                                        <button class="btn btn-primary deleteMe" data-id="{{$item->id}}"> <i class="fa fa-trash" aria-hidden="true"></i> </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                         
                        </table>
                    </div>
                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="">Memo</label>
                            <textarea class="form-control" name="memo" id="memo" rows="3">{{$data->memo}}</textarea>
                          </div>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <br>
                        <table class="table">
                            <tr>
                                <td><b>Subtotal</b></td>
                                <td id="subTotal">{{manageAmountFormat($data->sub_total)}}</td>
                            </tr>
                            <tr class="hideme">
                                <td>Tax</td>
                                <td id="totalTax">{{manageAmountFormat($data->total - $data->sub_total)}}</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td class="totalAll">{{manageAmountFormat($data->total)}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12" style="text-align: right">
                        <button class="btn btn-danger addExpense" data-action="{{route('cheques.update',$data->id)}}" type="submit" style="margin-right: 10px" >Update</button>
                        <button class="btn btn-danger addExpense" data-action="{{route('cheques.processCheque',$data->id)}}" type="submit" style="margin-right: 10px" >Process</button>
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
                <input type="text" placeholder="Description"  class="form-control description description_name">
            </td>
            <td>
                <input type="number" placeholder="Amount"  class="amount thisChange form-control amount_name">
            </td>
            <td class="hideme">
                <select class="vat_list form-control thisChange val_name" >
                </select>
            </td>
            <td>
                <input type="hidden" class="vat_percentage" value="">
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
    </style>
@endsection
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
@if ($data->tax_amount_type == 'Out Of Scope of Tax')
<script>
    $('.hideme').css('display','none');
</script>
@endif
    <script type="text/javascript">
     $(document).ready(function () {
        var val = $('#selectBankAccount').val();
            var $this = $('#selectBankAccount');
            $.ajax({
                type: "POST",
                url: "{{route('banking.transfer.fetch')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':val
                },
                success: function (response) {
                    $this.parents('.row').find('#balance').html(response.amount);
                }
            });
    });
        $('#selectBankAccount').change(function (e) {
            e.preventDefault();
            var val = $(this).val();
            var $this = $(this);
            $.ajax({
                type: "POST",
                url: "{{route('banking.transfer.fetch')}}",
                data: {
                    '_token':'{{csrf_token()}}',
                    'id':val
                },
                success: function (response) {
                    $this.parents('.row').find('#balance').html(response.amount);
                }
            });
        });
        $(document).on('keyup','.thisChange',function(e){
            e.preventDefault();
            if($('#taxCheck').val() == "Out Of Scope")
            {
                outOfScopeTax();
                return true;
            }
            if($(this).val() == '' || $(this).parents('tr').find('.amount').val() == '' || $(this).parents('tr').find('.vat_percentage').val() == '')
            {
                return false;                
            }
            getTotal();
        });
        function outOfScopeTax(){
            var subTotal = 0;
            var total = 0;
            $('.hideme').css('display','none');
            $(document).find('.amount').each(function(key,val){
                if($(val).val() != ''){
                    total = parseFloat(total) + parseFloat($(val).val());
                    subTotal = parseFloat(subTotal) + parseFloat($(val).val());
                }
            });
            $('#subTotal').html((subTotal).toFixed(2));           
            $('.totalAll').html((total).toFixed(2));
        }
        function getTotal(){
            if($('#taxCheck').val() == "Out Of Scope")
            {
                outOfScopeTax();
                return true;
            }
            $('.hideme').removeAttr('style');
            var subTotal = 0;
            var total = 0;
            $(document).find('.amount').each(function(key,val){
                if($(val).val() != ''){
                    var vat_percentage  = parseFloat($(val).parents('tr').find('.vat_percentage').val());
                    if($('#taxCheck').val() == "Inclusive"){
                        if($(val).parents('tr').find('.vat_percentage').val() != ""){
                            subTotal = parseFloat(subTotal) + parseFloat($(val).val() - parseFloat(($(val).val() * (vat_percentage)) / 100));
                            total = parseFloat(total) + parseFloat($(val).val());
                        }
                    }else
                    {
                        if($(val).parents('tr').find('.vat_percentage').val() != ""){
                            subTotal = parseFloat(subTotal) + parseFloat($(val).val());
                            total = parseFloat(total) + parseFloat($(val).val()) + parseFloat(($(val).val() * (vat_percentage)) / 100);
                        }
                    }
                }
            });
            $('#subTotal').html((subTotal).toFixed(2));           
            $('.totalAll').html((total).toFixed(2));
            $('#totalTax').html((total-subTotal).toFixed(2));
        }
        function getTotalVat(input)
        {
            if($(input).val() == '' || $(input).parents('tr').find('.amount').val() == '' || $(input).parents('tr').find('.vat_percentage').val() == '')
            {
                return false;                
            }
            var amount  = parseFloat($(input).parents('tr').find('.amount').val());
            var vat_percentage  = parseFloat($(input).parents('tr').find('.vat_percentage').val());
            if($('#taxCheck').val() == "Inclusive"){
                var vat_amount = amount + ((amount*vat_percentage) / 100);
            }else if($('#taxCheck').val() == "Exclusive"){
                var vat_amount = amount;
            }else
            {
                var vat_amount = 0;
            }
            // $(input).parents('tr').find('.totalVat').val((vat_amount).toFixed(2));
            getTotal();
        }
        $(document).on('click','.deleteMe',function(e){
            e.preventDefault();
            $(this).closest('tr').remove();
            if($('#taxCheck').val() == "Out Of Scope")
            {
                outOfScopeTax();
                return true;
            }
            getTotal();
        });
        $(document).on('click','.newItem',function(e){
            e.preventDefault();
            $(".category_list").select2('destroy');
            $(".vat_list").select2('destroy');
            var itemNo = $(this).data('id');
            var newItemNo = parseInt(parseInt(itemNo) + 1);
            console.log(itemNo);
            console.log(newItemNo);
            $('.cat_name').attr('name','category_list['+newItemNo+']');
            $('.description_name').attr('name','description['+newItemNo+']');
            $('.amount_name').attr('name','amount['+newItemNo+']');
            $('.val_name').attr('name','vat_list['+newItemNo+']');
            var item = $('#newRow tbody tr').clone();
            $(item).find('.cat_name').removeClass('cat_name');
            $(item).find('.description_name').removeClass('description_name');
            $(item).find('.amount_name').removeClass('amount_name');
            $(item).find('.val_name').removeClass('val_name');
            $(item).find('.remove_error').remove();
            $('.newItem').data('id',newItemNo);
            $('.categoryTable').append(item);
            category_list();
            vat_list();
        });
        var supplierList = function(){
            $("#selectsupplier").select2(
            {
                placeholder:'Select Payee',
                ajax: {
                    url: '{{route("expense.payee_list")}}',
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
        var selectBankAccount = function(){
            $("#selectBankAccount").select2(
            {
                placeholder:'Select Bank Account',
                ajax: {
                    url: '{{route("expense.paymentAccount")}}',
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
                    url: '{{route("expense.category_list")}}',
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
        var vat_list = function(){
            $(".vat_list").select2(
            {
                placeholder:'Select Vat',
                ajax: {
                    url: '{{route("expense.vat_list")}}',
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
     
        $(document).on('change','.vat_list',function(){
            var vat = $(this).val();
            var $this = $(this);
            $.ajax({
                type: "GET",
                url: "{{route('expense.vat_find')}}",
                data: {
                    'id':vat
                },
                success: function (response) {
                    $this.parents('tr').find('.vat_percentage').val(response.tax_value);
                    getTotalVat($this);
                }
            });
            
        });
        var branches = function(){
            $(".branches").select2(
            {
                placeholder:'Select Branch',
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
        $(document).ready(function () {
            supplierList();
            category_list();
            vat_list();
            selectBankAccount();
            branches();
        });
    var form = new Form();

    $(document).on('click','.addExpense',function(e){
            e.preventDefault();
            var postData = new FormData($(this).parents('form')[0]);
			var url = $(this).data('action');
			postData.append('_token',$(document).find('input[name="_token"]').val());
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
                        if(out.location)
                        {
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
                    $(".remove_error").remove();
                    form.errorMessage('Something went wrong');							
                }
            });
        });
    </script>
@endsection
