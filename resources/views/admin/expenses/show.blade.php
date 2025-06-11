
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
                                    <span class="form-control">{{$data->payment_account->account_name}} ({{$data->payment_account->account_code}})</span>      
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                    <label for="">Branch</label>                                  
                                    <span class="form-control">
                                        @if ($data->branch)
                                        {{$data->branch->name}}
                                        @endif
                                    </span>  
                                </div>
                            </div>
                            {{-- <div class="col-sm-4">
                                <br>
                                <div class="form-group">
                                    <h5>Balance: <span>0</span></h5>
                                </div>
                            </div> --}}
                        </div>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Payment Date</label>                                  
                                  <span class="form-control">{{$data->payment_date}}</span>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Payment Method</label>                
                                    <span class="form-control">
                                        {{$data->payment_method->title}}
                                    </span>     
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                  <label for="">Ref No.</label>
                                  <span class="form-control" >{{$data->ref_no}}</span>
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
                        <div class="form-group roe">
                            <label class="col-sm-4" >Amounts are</label>       
                            <div class="col-sm-8">
                                <span class="form-control">
                                    {{$data->tax_amount_type}}
                                </span>   
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
                                        <span class="form-control">
                                            {{$item->category->account_name}} ({{$item->category->account_code}})
                                        </span>  
                                    </td>
                                    <td>
                                        <span class="form-control">
                                            {{$item->description}}
                                        </span>  
                                    </td>
                                    <td>
                                        <span class="form-control">
                                            @if ($data->tax_amount_type == 'Inclusive of Tax')
                                            {{$item->total}}
                                            @else
                                            {{$item->amount}}
                                            @endif
                                        </span>  
                                   </td>
                                    <td class="hideme">
                                        <span class="form-control">
                                            {{$item->tax_manager->title}} ({{$item->tax_manager->tax_value}})
                                        </span>  
                                    </td>
                                  
                                </tr>
                                @endforeach
                            </tbody>
                           
                        </table>
                    </div>
                    <div class="col-md-4">
                          <div class="form-group">
                            <label for="">Memo</label>
                            <span class="form-control" style="min-height: 70px;">{{$data->memo}}</span>
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
    
@endsection
