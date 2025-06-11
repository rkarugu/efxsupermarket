
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
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('bills.list')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>
    <!-- Main content -->
    <form action="{{route('bills.bill_payment_process',$data->id)}}" method="post" class="addbills">
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
                                  <label for="">Supplier</label>                                  
                                  <select class="form-control" name="supplier" id="selectsupplier" disabled>
                                    <option value="{{$data->supplier->id}}" selected>{{$data->supplier->name}} ({{$data->supplier->supplier_code}})</option>
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Bank/Credit account</label>                                  
                                    <select class="form-control selectBankAccount" name="bank_account" id="selectBankAccount">
                                    </select>
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
                                  <input type="date" class="form-control due_datewa_payment_terms" name="payment_date" id="bill_date" value="{{date('Y-m-d')}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Ref No.</label>
                                  <input type="text" name="ref_no" class="form-control" placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4" style="text-align: right">
                        <br>
                        <h5 style="margin: 0; margin-bottom: 0;">{{strtoupper('Balance Due')}}</h5>
                        <h1 style="margin: 0; margin-top: 0;">KSH <span class="totalAll"> 0.00 </span></h1>
                    </div>
                </div>
            </div>
        </section>


        <section class="content" style="padding-top: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">  
                    <div class="col-md-4 col-md-offset-8" style="text-align: right;">
                        <div class="form-group roe">
                            <label class="col-sm-4" >Amount</label>       
                            <div class="col-sm-8">
                                <input type="text"  class="form-control thisChange amount" value="{{$data->balance}}">
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
                                    <th> Description </th>
                                    <th> Due Date </th>
                                    <th> Original Amount </th>
                                    <th> Opening Balance </th>
                                    <th> Paymemt </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{$data->bill_no}}
                                    </td>
                                    <td>
                                        {{$data->due_date}}
                                    </td>
                                    <td>
                                        {{manageAmountFormat($data->total)}}                                        
                                    </td>
                                    <td >
                                        {{manageAmountFormat($data->balance)}}                                        
                                    </td>
                                    <td >
                                        <input type="text" name="payment_amount" class="form-control thisChange amount" value="{{$data->balance}}">
                                    </td>
                                </tr>
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
                                <td><b>Amount to Apply
                                </b></td>
                                <td id="subTotal">0.00</td>
                            </tr>
                           
                        </table>
                    </div>
                    <div class="col-md-12" style="text-align: right">
                        <button class="btn btn-danger" type="submit" style="margin-right: 10px">Process</button>
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
    </style>
@endsection
@section('uniquepagescript')
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script type="text/javascript">
    getTotal();
        $(document).on('keyup change','.thisChange',function(e){
            e.preventDefault();      
            var num = new Number($(this).val()).toLocaleString("en-US");
            $('#subTotal').html(num);           
            $('.amount').val($(this).val());           
            $('.totalAll').html(num);
        });
        function getTotal(){
            var subTotal = 0;
            var total = 0;
            subTotal = parseFloat(subTotal) + parseFloat($(document).find('.amount').val());   
            var num = new Number(subTotal).toLocaleString("en-US");
            $('#subTotal').html(num);           
            $('.amount').val(subTotal);           
            $('.totalAll').html(num);
        }
        
        var supplierList = function(){
            $("#selectsupplier").select2();
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
        var wa_payment_terms = function(){
            $("#wa_payment_terms").select2(
            {
                placeholder:'Select Payment Term',
                ajax: {
                    url: '{{route("bills.wa_payment_terms")}}',
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
        $('.payment_terms').change(function(e){
            e.preventDefault();
            var id = $('#wa_payment_terms').val();
            if(id){
                $.ajax({
                    type: "GET",
                    url: "{{route('bills.payment_terms_find')}}",
                    data: {
                        'id':id,
                        'bill_date':$('#bill_date').val(),
                    },
                    success: function (response) {
                        $('#due_date').val(response.due_date);
                    }
                });
            }
        });
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
        };
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
        $(document).ready(function () {
            supplierList();
            category_list();
            vat_list();
            wa_payment_terms();
            branches();
            selectBankAccount();
        });
    var form = new Form();

        $(document).on('submit','.addbills',function(e){
            e.preventDefault();
            // form.successMessage('Bill Added');
            // return true;
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
