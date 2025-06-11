
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
    <form action="{{route('bills.store')}}" method="post" class="addbills">
        {{csrf_field()}}
        <section class="content" style="min-height: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                
                    @include('message')
                    <div class="col-md-8 no-padding-h">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Supplier</label>                                  
                                  <select class="form-control" name="supplier" id="selectsupplier">
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Branch</label>                                  
                                    <select class="form-control branches" name="main_branch" id="branches">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                  <label for="">Mailing Address</label>
                                  <textarea name="mailing_address" class="form-control" cols="30" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-8">
                                <div class="row">
                                    <div class="col-sm-6">                               
                                        <div class="form-group">
                                            <label for="">Terms</label>                                  
                                            <select class="form-control payment_terms" name="wa_payment_terms" id="wa_payment_terms">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="">Bill Date</label>                                  
                                          <input type="date" class="form-control payment_terms" name="bill_date" id="bill_date" value="{{date('Y-m-d')}}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="">Due Date</label>
                                          <input type="date" class="form-control" name="due_date" id="due_date" value="{{date('Y-m-d')}}">
                                        </div>
                                    </div>
                                    {{-- <div class="col-sm-6">
                                        <div class="form-group">
                                          <label for="">Bill No.</label>
                                          <input type="text" name="bill_no" class="form-control" placeholder="" aria-describedby="helpId">
                                        </div>
                                    </div> --}}
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
                        <div class="form-group row">
                            <label class="col-sm-4" style="margin-top: 4px;">Amounts are</label>       
                            <div class="col-sm-8">
                                <select class=" form-control taxCheck" name="tax_check" id="taxCheck" onchange="getTotal();">
                                    @foreach (tax_amount_type() as $key => $item)
                                        <option value="{{$key}}">{{$item}}</option>
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
                                    <th> Branch </th>
                                    <th> Bill No </th>
                                    <th> Project </th>
                                    <th> GL Tag </th>
                                    <th> Amount </th>
                                    <th class="hideme"> Vat </th>
                                    <th> <button class="btn btn-primary newItem" data-id="0"> Add Items </button> </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select class=" form-control category_list" name="category_list[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Description" name="description[0]" class="form-control description">
                                    </td>
                                    <td>
                                        <select class="form-control branches" name="branch[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Bill No" name="item_bill_no[0]" class="form-control item_bill_no">
                                    </td>
                                    <td>
                                        <select class=" form-control project_list" name="project_list[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <select class=" form-control gltag_list" name="gltag_list[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" placeholder="Amount" name="amount[0]" class="amount thisChange form-control">
                                    </td>
                                    <td class="hideme">
                                        <select class="vat_list form-control thisChange" name="vat_list[0]" >
                                        </select>
                                    </td>
                                    <td>
                                        <input type="hidden" class="vat_percentage" value="">
                                        <button class="btn btn-primary deleteMe"> <i class="fas fa-trash" aria-hidden="true"></i> </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="">Memo</label>
                            <textarea class="form-control" name="memo" id="memo" rows="3"></textarea>
                          </div>
                    </div>
                    <div class="col-md-4 col-md-offset-4">
                        <br>
                        <table class="table">
                            <tr>
                                <td><b>Subtotal</b></td>
                                <td id="subTotal">0.00</td>
                            </tr>
                            <tr class="hideme">
                                <td>Tax</td>
                                <td id="totalTax">0.00</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td class="totalAll">0.00</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12" style="text-align: right">
                        <button class="btn btn-danger" type="submit" style="margin-right: 10px">Submit</button>
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
                <select class="form-control branches branch_name" >
                </select>
            </td>
            <td>
                <input type="text" placeholder="Bill No" class="form-control item_bill_no item_bill_no_name">
            </td>
            <td>
                <select class=" form-control project_list" >
                </select>
            </td>
            <td>
                <select class=" form-control gltag_list" >
                </select>
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
                <button class="btn btn-primary deleteMe"> <i class="fas fa-trash" aria-hidden="true"></i> </button>
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
    <script type="text/javascript">
        $(document).on('keyup change','.thisChange',function(e){
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
            $(".project_list").select2('destroy');
            $(".gltag_list").select2('destroy');
            $(".vat_list").select2('destroy');
            $(".branches").select2('destroy');
            var itemNo = $(this).data('id');
            var newItemNo = parseInt(parseInt(itemNo) + 1);
            
            $('.cat_name').attr('name','category_list['+newItemNo+']');
            $('.description_name').attr('name','description['+newItemNo+']');
            $('.amount_name').attr('name','amount['+newItemNo+']');
            $('.item_bill_no_name').attr('name','item_bill_no['+newItemNo+']');
            $('.val_name').attr('name','vat_list['+newItemNo+']');
            $('.branch_name').attr('name','branch['+newItemNo+']');
            $('.project_name').attr('name','project_list['+newItemNo+']');
            $('.gltag_name').attr('name','gltag_list['+newItemNo+']');
            var item = $('#newRow tbody tr').clone();
            $(item).find('.cat_name').removeClass('cat_name');
            $(item).find('.description_name').removeClass('description_name');
            $(item).find('.amount_name').removeClass('amount_name');
            $(item).find('.val_name').removeClass('val_name');
            $(item).find('.item_bill_no_name').removeClass('item_bill_no_name');
            $(item).find('.project_name').removeClass('project_name');
            $(item).find('.gltag_name').removeClass('gltag_name');
            $(item).find('.branches').removeClass('branch_name');
            $(item).find('.remove_error').remove();
            $('.newItem').data('id',newItemNo);
            $('.categoryTable').append(item);
            category_list();
            vat_list();
            project_list();
            gltag_list();
            branches();

        });
        var supplierList = function(){
            $("#selectsupplier").select2(
            {
                placeholder:'Select supplier',
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
        var project_list = function(){
            $(".project_list").select2(
            {
                placeholder:'Select Project',
                ajax: {
                    url: '{{route("projects.list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        console.log(data);
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.title};
                            });
                        return {
                            results: res
                        };
                    }
                },
            });
        };
        var gltag_list = function(){
            $(".gltag_list").select2(
            {
                placeholder:'Select GL Tag',
                ajax: {
                    url: '{{route("gl_tags.list")}}',
                    dataType: 'json',
                    type: "GET",
                    delay: 250,
                    data: function (params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function (data) {
                        console.log(data);
                            var res = data.map(function (item) {
                                return {id: item.id, text: item.title};
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
        $(document).ready(function () {
            supplierList();
            category_list();
            vat_list();
            wa_payment_terms();
            branches();
            project_list();
            gltag_list();
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
