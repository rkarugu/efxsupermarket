@extends('layouts.admin.admin')
@section('content')
<br>
<section class="content">    
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> {!! $title !!} </h3>
                <div>
                    <a href="{{ route('pos-cash-sales.index') }}" class="btn btn-success btn-sm"> <i class="fas fa-arrow-left"></i> Back</a>
                </div>
            </div>
        </div>
         @include('message')
            <div class = "row">
                <div class = "col-sm-4">
                        <div class="box-body">
                            <div class="form-group">
                                <label for="">Date</label>
                                <span class="form-control">{{$data->date}}</span>
                            </div>
                        </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Time</label>
                            <span class="form-control">{{$data->time}}</span>
                        </div>
                    </div>
                </div>

                <div class = "col-sm-4">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">User</label>
                            <span class="form-control">{{@$data->user->name}}</span>
                        </div>
                    </div>
                </div>
            </div>       
            <div class = "row">
                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Customer</label>
                            <span class="form-control">{{$data->customer}}</span>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Payment Method</label>                                  
                            <span class="form-control">{{@$data->payment->title}}</span>
                        </div>
                    </div>
                </div>
                
                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Cash</label>
                            <span class="form-control">{{$data->cash}}</span>
                        </div>
                    </div>
                </div>

                <div class = "col-sm-3">
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Change</label>
                            <span class="form-control">{{$data->change}}</span>
                        </div>
                    </div>
                </div>
            </div>  
    </div>
</section>

       <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
                    <div class="box box-primary">
                        <div class="box-header with-border no-padding-h-b">
                             
                          
                            <div class="col-md-12 no-padding-h ">
                           <h3 class="box-title"> Cash Sales</h3>

                            <span id = "requisitionitemtable">
                                <table class="table table-bordered table-hover" id="mainItemTable">
                                    <thead>
                                    <tr>
                                      <th>Item</th>
                                      <th>Description</th>
                                      <th style="width: 90px;">Unit</th>
                                      <th style="width: 90px;">QTY</th>
                                      <th style="width: 90px;">Returned QTY</th>
                                      <th>Selling Price</th>
                                      <th>Location</th>
                                      <th style="width: 90px;">Discount</th>
                                      <th>VAT</th>
                                      <th>Total</th>
                                      <th>Dispatch Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php
                                        $totalprice = $discount = $totalvat = $total =0;
                                    @endphp
                                    @foreach ($data->items as $item)
                                    @php
                                        $discount += $item->discount_amount;
                                        $totalvat += $item->vat_amount;
                                        $total +=$item->selling_price*$item->qty;
                                    @endphp
                                       <tr>
                                           <td>{{@$item->item->stock_id_code}}</td>
                                           <td>{{@$item->item->description}}</td>
                                           <td>{{@$item->item->pack_size->title}}</td>
                                           <td>{{$item->qty}}</td>
                                           <td>{{$item->return_quantity}}</td>
                                           <td>{{$item->selling_price}}</td>
                                           <td>{{@$item->location->location_name}}</td>
                                           <td>{{$item->discount_amount}}</td>
                                           <td>{{$item->vat_amount}}</td>
                                           <td>{{$item->selling_price*$item->qty}}</td>
                                           <td>
                                            @if ($item->is_dispatched == 1)
                                                <span>Dispatched By: {{$item->dispatch_by->name}}</span><br>
                                                <span>Date: {{date('d-M-Y',strtotime($item->dispatched_time))}}</span><br>
                                                <span>Time: {{date('H:i A',strtotime($item->dispatched_time))}}</span><br>
                                                <span>Disp No: {{$item->dispatch_no}}</span>
                                            @endif
                                           </td>
                                       </tr>
                                    @endforeach


                                    </tbody>
                                    <tfoot>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Total Price
                                        </th>
                                        <td colspan="2">KES <span id="total_exclusive">{{$total - $totalvat}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Discount
                                        </th>
                                        <td colspan="2">KES <span id="total_discount">{{$discount}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Total VAT		
                                        </th>
                                        <td colspan="2">KES <span id="total_vat">{{$totalvat}}</span></td>
                                      </tr>
                                      <tr>
                                        <th colspan="9" style="text-align:right">
                                        Total
                                        </th>
                                        <td colspan="2">KES <span id="total_total">{{ceil($total-$discount)}}</span></td>
                                      </tr>
                                    </tfoot>
                                </table>
                                </span>
                            </div>
                       


                        </div>
                    </div>


    </section>
@endsection