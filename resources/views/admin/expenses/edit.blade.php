
@extends('layouts.admin.admin')

@section('content')
<style>
    .span-action {

    display: inline-block;
    margin: 0 3px;

}
</style>
@php
    $totalwithVat = $data->categories->sum('total');
@endphp
<div class="row ">
    <div class="col-md-12" style="padding-left: 29px;">
        <a class="btn btn-danger remove-btn mr-xs  ml-2 btn-sm" style="margin-right:51px" href="{{route('expense.list')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i>   Back</a>
    </div>
</div>
    <!-- Main content -->
    <form action="" method="post" >
        {{csrf_field()}}
        <input type="hidden" name="id" value="{{$data->id}}">
        <section class="content" style="min-height: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                
                    @include('message')
                    <div class="col-md-8 no-padding-h">
                        <div class="row">
                            {{-- <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payee</label>                                  
                                  <select class="form-control" name="payee" id="selectPayee">
                                      <option value="{{$data->payee_id}}" selected>{{$data->payee->supplier_code}}</option>
                                  </select>
                                </div>
                            </div> --}}
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Payment Account</label>                                  
                                    <select class="form-control" name="payment_account" id="paymentAccount">
                                      <option value="{{$data->payment_account_id}}" selected>{{$data->payment_account->account_name}} ({{$data->payment_account->account_code}})</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Branch</label>                                  
                                    <select class="form-control branches" name="branch" id="branches">
                                        @if ($data->branch)
                                        <option value="{{$data->restaurant_id}}" selected>{{$data->branch->name}}</option>
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
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Payment Date</label>                                  
                                  <input type="date" class="form-control" name="payment_date" id="payment_date" value="{{$data->payment_date}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Payment Method</label>                                  
                                    <select class="form-control" name="payment_method" id="payment_method">
                                      <option value="{{$data->payment_method_id}}" selected>{{$data->payment_method->title}}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Ref No.</label>
                                  <input type="text" name="ref_no" class="form-control" placeholder="" aria-describedby="helpId" value="{{$data->ref_no}}">
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-4" style="text-align: right">
                        <br>
                        <h5 style="margin: 0; margin-bottom: 0;">AMOUNT</h5>
                        <h1 style="margin: 0; margin-top: 0;">KSH <span class="totalAll"> {{manageAmountFormat($totalwithVat)}} </span></h1>
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
                                        @if ($item == $data->tax_amount_type)
                                        <option value="{{$key}}" selected>{{$item}}</option>
                                        @else
                                        <option value="{{$key}}">{{$item}}</option>
                                        @endif
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
                                    <th> Project </th>
                                    <th> GL Tag </th>
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
                                        <select class="form-control project_list" name="project_list[{{$item->id}}]" >
                                            @if ($item->project)
                                                <option value="{{$item->project->id}}" selected>{{$item->project->title}} ({{$item->project->title}})</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td>
                                        <select class=" form-control gltag_list" name="gltag_list[{{$item->id}}]">
                                            @if ($item->gltag)
                                                <option value="{{$item->gltag->id}}" selected>{{$item->gltag->title}} ({{$item->gltag->title}})</option>                                               
                                            @endif
                                        </select>
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
                                        <button class="btn btn-primary deleteMe" data-id="{{$item->id}}"> <i class="fas fa-trash" aria-hidden="true"></i> </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th> Category </th>
                                    <th> Description </th>
                                    <th> Project </th>
                                    <th> GL Tag </th>
                                    <th> Amount </th>
                                    <th class="hideme"> Vat </th>
                                    <th> <button class="btn btn-primary newItem" data-id="{{$data->categories ? $data->categories[0]->id : '0'}}"> Add Items </button> </th>
                                </tr>
                            </tfoot>
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
                                @php
                                    $subtotal = $data->categories->sum('amount');
                                @endphp
                                <td id="subTotal">{{manageAmountFormat($subtotal)}}</td>
                            </tr>
                            <tr class="hideme">
                                <td>Tax</td>
                                <td id="totalTax"> {{manageAmountFormat($totalwithVat-$subtotal)}}</td>
                            </tr>
                            <tr>
                                <td>Total</td>
                                <td class="totalAll"> {{manageAmountFormat($totalwithVat)}}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-12" style="text-align: right">
                        <button class="btn btn-danger addExpense" data-action="{{route('expense.update')}}" type="submit" style="margin-right: 10px" >Update</button>
                        <button class="btn btn-danger addExpense" data-action="{{route('expense.processExpense')}}" type="submit" style="margin-right: 10px" >Process</button>
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
@if ($data->tax_amount_type == 'Out Of Scope of Tax')
<script>
    $('.hideme').css('display','none');
</script>
@endif
    <script type="text/javascript">
    $(document).ready(function () {
        var val = $('#paymentAccount').val();
            var $this = $('#paymentAccount');
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
      $('#paymentAccount').change(function (e) {
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
            var itemNo = $(this).data('id');
            var newItemNo = parseInt(parseInt(itemNo) + 1);
            console.log(itemNo);
            console.log(newItemNo);
            $('.cat_name').attr('name','category_list['+newItemNo+']');
            $('.description_name').attr('name','description['+newItemNo+']');
            $('.amount_name').attr('name','amount['+newItemNo+']');
            $('.val_name').attr('name','vat_list['+newItemNo+']');
            $('.project_name').attr('name','project_list['+newItemNo+']');
            $('.gltag_name').attr('name','gltag_list['+newItemNo+']');
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
            project_list();
            gltag_list();
        });
        var payeeList = function(){
            $("#selectPayee").select2(
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
        var paymentAccount = function(){
            $("#paymentAccount").select2(
            {
                placeholder:'Select Payment Account',
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
        var payment_method = function(){
            $("#payment_method").select2(
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
            payeeList();
            paymentAccount();
            payment_method();
            category_list();
            vat_list();
            branches();
            project_list();
            gltag_list();
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
