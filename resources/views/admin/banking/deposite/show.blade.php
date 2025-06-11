
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
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('banking.deposite.list')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>

        <input readonly type="hidden" name="id" value="{{$data->id}}">
        <section class="content" style="min-height: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                
                    @include('message')
                    <div class="col-md-8 no-padding-h">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Account</label>                                  
                                  <select readonly disabled class="form-control" name="payment_account" id="selectBankAccount">
                                    @if ($data->account)
                                        <option value="{{$data->account->id}}" selected>{{$data->account->account_name}} ({{$data->account->account_code}})</option>
                                    @endif
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <br>
                                <div class="form-group">
                                    <h5>Balance: <span id="balance">0</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Date</label>                                  
                                    <input readonly type="date" name="date" class="form-control" value="{{$data->date}}">
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Branch</label>                                  
                                  <select readonly disabled class="form-control branches" name="branch" id="branches">
                                    @if ($data->branch)
                                        <option value="{{$data->branch->id}}" selected>{{$data->branch->name}}</option>
                                    @endif
                                </select>
                                </div>
                            </div>
                        </div>
                    
                    </div>
                    <div class="col-md-4" style="text-align: right">
                        <br>
                        <h5 style="margin: 0; margin-bottom: 0;">AMOUNT</h5>
                        <h1 style="margin: 0; margin-top: 0;">KSH <span class="totalAll"> {{manageAmountFormat($data->total)}} </span></h1>
                    </div>
                </div>
            </div>
        </section>

        <section class="content" style="padding-top: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">  
                    <div class="col-md-4">
                        <h4>
                            <b>Add funds to this deposit</b>
                        </h4>
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <div class="form-group row">
                            <label class="col-sm-4" style="margin-top: 4px;">Type</label>       
                            <div class="col-sm-8">
                                <select readonly disabled class=" form-control receivedType" name="type" >
                                    @foreach (receivedType() as $key => $item)
                                    @if ($item == $data->receiver_type)
                                    <option value="{{$key}}" selected>{{$item}}</option>
                                    @else
                                    <option value="{{$key}}">{{$item}}</option>
                                    @endif    
                                    @endforeach
                                </select>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-4" style="text-align: right;">
                        <div class="form-group row">
                            <label class="col-sm-4" style="margin-top: 4px;">Amounts are</label>       
                            <div class="col-sm-8">
                                <select readonly disabled class=" form-control taxCheck" name="tax_check" id="taxCheck" onchange="getTotal();">
                                    @foreach (tax_amount_type() as $key => $item)
                                    @if ($item == $data->tax_check)
                                    <option value="{{$key}}" selected>{{$item}}</option>
                                    @else
                                    <option value="{{$key}}">{{$item}}</option>
                                    @endif
                                    @endforeach                                    
                                </select>
                            </div>                           
                        </div>
                    </div>
                    <div class="col-md-12 no-padding-h table-responsive">
                        <div>
                            <div class="receivers"> </div>
                        </div>
                        <table class="table table-bordered table-hover categoryTable w-100" >
                            <thead>
                                <tr>
                                    <th style="max-width: 10%"> Received From </th>
                                    <th style="max-width: 10%"> Account </th>
                                    <th style="min-width: 100px;"> Description </th>
                                    <th style="max-width: 10%"> Payment Method </th>
                                    <th style="min-width: 100px;"> Ref No. </th>
                                    <th style="min-width: 100px;"> Amount </th>
                                    <th class="hideme" style="max-width: 10%"> Vat </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->categories as $category)
                                <tr>
                                    <input readonly type="hidden" name="category_id[{{$category->id}}]" value="{{$category->id}}">
                                    <td style="max-width: 10%">
                                        <select readonly disabled class=" form-control selectsupplier" name="receiver[{{$category->id}}]" >
                                            <option value="{{$category->received_from_id}}" selected>{{$category->received_from->code}}</option>
                                        </select>
                                    </td>
                                    <td  style="max-width: 10%">
                                        <select readonly disabled class=" form-control category_list" name="account[{{$category->id}}]" >
                                            <option value="{{$category->account_id}}" selected>{{$category->account->account_name}} ({{$category->account->account_code}})</option>
                                        </select>
                                    </td>
                                    <td  style="min-width: 100px;">
                                        <input readonly type="text" placeholder="Description" name="description[{{$category->id}}]" class="form-control description" value="{{$category->description}}">
                                    </td>
                                    <td  style="max-width: 10%">
                                        <select readonly disabled class=" form-control payment_method" name="payment_method[{{$category->id}}]" >
                                            <option value="{{$category->payment_method_id}}" selected>{{$category->payment_method->title}}</option>
                                        </select>
                                    </td>
                                    <td  style="min-width: 100px;">
                                        <input readonly type="text" placeholder="Ref No" name="ref_no[{{$category->id}}]" class="form-control ref_no" value="{{$category->ref_no}}">
                                    </td>
                                    <td  style="min-width: 100px;">
                                        @if ($data->tax_check == 'Inclusive of Tax')
                                        <input readonly type="number" placeholder="Amount" name="amount[{{$category->id}}]" class="amount thisChange form-control" value="{{$category->total}}">

                                        @else
                                        <input readonly type="number" placeholder="Amount" name="amount[{{$category->id}}]" class="amount thisChange form-control" value="{{$category->amount}}">
                                        @endif
                                    </td  style="max-width: 10%">
                                    <td class="hideme"  style="max-width: 10%">
                                        <select readonly disabled class="vat_list form-control thisChange" name="vat_list[{{$category->id}}]" >
                                            @if ($category->vat)
                                            <option value="{{$category->vat_id}}" selected>{{$category->vat->title}} ({{$category->vat->tax_value}})</option>
                                                
                                            @endif
                                        </select>
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
                    
                    <div class="col-md-6 col-md-offset-2">
                        <table class="table">
                            <tr>
                                <td colspan="2" class="text-right"><b>Subtotal</b></td>
                                <td class="subTotal">{{manageAmountFormat($data->sub_total)}}</td>
                            </tr>
                            <tr class="hideme">
                                <td colspan="2" class="text-right">Tax</td>
                                <td id="totalTax">{{manageAmountFormat($data->total - $data->sub_total)}}</td>
                            </tr>
                            {{-- <tr>
                                <td>
                                    <div class="form-group">
                                      <label for="">Cash back goes to</label>
                                        <select readonly disabled class=" form-control category_list" name="cashback_goes" >
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="">Cash back memo</label>
                                        <textarea name="" cols="30" class="form-control" rows="1"></textarea>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="">Cash back amount</label>
                                        <input readonly type="number" name="" class="form-control cashbackAmount thisChange">
                                    </div>
                                </td>
                            </tr> --}}
                            <tr>
                                <td colspan="2" class="text-right">Total</td>
                                <td class="totalAll">{{manageAmountFormat($data->total)}}</td>
                            </tr>
                        </table>
                    </div>
                
                </div>
            </div>
        </section>
       
    
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
@if ($data->tax_check == 'Out Of Scope of Tax')
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
        $(document).on('keyup change','.thisChange',function(e){
            e.preventDefault();
            if($('#taxCheck').val() == "Out Of Scope")
            {
                outOfScopeTax();
                return true;
            }
            // if($(this).val() == '' || $(this).parents('tr').find('.amount').val() == '' || $(this).parents('tr').find('input.vat_percentage').val() == '')
            if($(this).val() == '' || $(this).parents('tr').find('.amount').val() == '')
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
            $('.subTotal').html((subTotal).toFixed(2));           
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
                    var vat_percentage  = parseFloat($(val).parents('tr').find('input.vat_percentage').val());
                    if($('#taxCheck').val() == "Inclusive"){
                        if($(val).parents('tr').find('input.vat_percentage').val() != ""){
                            subTotal = parseFloat(subTotal) + parseFloat($(val).val() - parseFloat(($(val).val() * (vat_percentage)) / 100));
                        }else
                        {
                            subTotal = parseFloat(subTotal) + parseFloat($(val).val());
                        }
                        total = parseFloat(total) + parseFloat($(val).val());
                    }else
                    {
                        subTotal = parseFloat(subTotal) + parseFloat($(val).val());
                        if($(val).parents('tr').find('input.vat_percentage').val() != ""){
                            total = parseFloat(total) + parseFloat($(val).val()) + parseFloat(($(val).val() * (vat_percentage)) / 100);
                        }else
                        {
                            total = parseFloat(total) + parseFloat($(val).val());
                        }
                    }
                }
            });
            $('.subTotal').html((subTotal).toFixed(2));           
            $('#totalTax').html((total-subTotal).toFixed(2));
            if($('.cashbackAmount').val() != '' && total != 0){
                $('.cashbackAmount').parent().find('.remove_error').remove();
                if($('.cashbackAmount').val() <= total){
                    total = parseFloat(total) - parseFloat($('.cashbackAmount').val());
                }else
                {
                    $('.cashbackAmount').val(0);
                    $('.cashbackAmount').parent().append("<label class='error d-block remove_error w-100' >Cashback can't be greater that total</label>");
                }
            }
            $('.totalAll').html((total).toFixed(2));
        }
        function getTotalVat(input)
        {
            // if($(input).val() == '' || $(input).parents('tr').find('.amount').val() == '' || $(input).parents('tr').find('input.vat_percentage').val() == '')
            if($(input).val() == '' || $(input).parents('tr').find('.amount').val() == '')
            {
                return false;                
            }
            var amount  = parseFloat($(input).parents('tr').find('.amount').val());
            var vat_percentage  = parseFloat($(input).parents('tr').find('input.vat_percentage').val());
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
            $(".selectsupplier").select2('destroy');

            $(".payment_method").select2('destroy');
            $(".vat_list").select2('destroy');
            var itemNo = $(this).data('id');
            var newItemNo = parseInt(parseInt(itemNo) + 1);
            $('#newRow tbody tr td').each(function(key,val){
                var attName = $(val).find('input,select').data('name');
                $(val).find('input,select').attr('name',attName+'['+newItemNo+']');
            });
            var item = $('#newRow tbody tr').clone();
            $(item).find('.remove_error').remove();
            $(this).data('id',newItemNo);
            $('.categoryTable').append(item);
            category_list();
            supplierList();
            vat_list();
            payment_method();
        });
        
    </script>
@endsection
