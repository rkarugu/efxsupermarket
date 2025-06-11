
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

        <section class="content" style="min-height: 0;">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">                
                    @include('message')
                    <div class="col-md-8 no-padding-h">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payee</label>                                  
                                  <select disabled readonly class="form-control" name="supplier" id="selectsupplier">
                                      @if ($data->payee)
                                      <option value="{{$data->payee->id}}" selected>{{$data->payee->supplier_code}}</option>
                                      @endif
                                  </select>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Bank Account</label>                                  
                                  <select disabled readonly class="form-control" name="bank_account" id="selectBankAccount">
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
                                  <textarea disabled readonly name="mailing_address" class="form-control" cols="30" rows="5">{{$data->mailing_address}}</textarea>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                  <label for="">Payment Date</label>                                  
                                  <input disabled readonly type="date" class="form-control due_datewa_payment_terms" name="payment_date" id="bill_date" value="{{$data->payment_date}}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Cheque No.</label>
                                  <input disabled readonly type="text" name="cheque_no" class="form-control" value="{{$data->cheque_no}}" placeholder="" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Branch</label>
                                  <select disabled readonly class="form-control branches" name="branch" id="branches">
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
                                <select disabled readonly class=" form-control taxCheck" name="tax_check" id="taxCheck" onchange="getTotal();">
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->categories as $item)
                                <tr>
                                    <td>
                                        <select disabled readonly class=" form-control category_list" name="category_list[{{$item->id}}]" >
                                            <option value="{{$item->category->id}}" selected>{{$item->category->account_name}} ({{$item->category->account_code}})</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input disabled readonly type="text" placeholder="Description" value="{{$item->description}}" name="description[{{$item->id}}]" class="form-control description">
                                    </td>
                                    <td>
                                        @if ($data->tax_amount_type == 'Inclusive of Tax')
                                        <input disabled readonly type="number" placeholder="Amount" value="{{$item->total}}" name="amount[{{$item->id}}]" class="amount thisChange form-control">
                                        @else
                                        <input disabled readonly type="number" placeholder="Amount" value="{{$item->amount}}" name="amount[{{$item->id}}]" class="amount thisChange form-control">
                                        @endif
                                    </td>
                                    <td class="hideme">
                                        <select disabled readonly class="vat_list form-control thisChange" name="vat_list[{{$item->id}}]" >
                                            
                                            @if ($item->tax_manager)
                                            <option value="{{$item->tax_manager->id}}" selected>{{$item->tax_manager->title}} ({{$item->tax_manager->tax_value}})</option>
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
                            <textarea disabled readonly class="form-control" name="memo" id="memo" rows="3">{{$data->memo}}</textarea>
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
                
                 
                </div>
            </div>
        </section>
         
    @endsection
@section('uniquepagestyle')

@endsection
@section('uniquepagescript')
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
    
    </script>
@endsection
