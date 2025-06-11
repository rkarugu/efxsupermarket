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
                                  <label for="">Bank Account</label> <span class="form-control">{{@$data->bank_account->account_number}}</span>
                                </div>
                            </div>
                            
                       
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payment Date</label>
                                  <span class="form-control">{{$data->payment_date ? date('Y-m-d',strtotime($data->payment_date)) : NULL}}</span>
                                </div>
                            </div>
                            <div class="col-sm-4">
                                <div class="form-group">
                                  <label for="">Payment Method</label>
                                  <span class="form-control">
  {{@$data->payment_method->title}}
</span>
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
                                @foreach ($data->items as $item)
                                    <tr class="item">
                                        <td>{{@$item->chart_of_account->account_name}}
                                        </td>
                                        <td>{{@$item->branch->name}}
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
                    
                </div>
            </div>
        </section>
         

    @endsection

