
@extends('layouts.admin.admin')

@section('content')
<style>
    .bg-secondary
    {
        background-color: #6c757d!important;
        color:#fff !important;
    }
</style>
    <!-- Main content -->
    <section class="content">
        
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">                            
                @include('message')
                <div class="col-md-12 no-padding-h">
                    
                    <form action="{{route('maintain-items.purchaseDataUpdate',['stockid'=>encrypt($inventoryItem->id), 'itemid'=>encrypt($supplier_item->id)])}}" method="post" class="submitMe">
                        <input type="hidden" name="_token" value="{{csrf_token()}}">
                        <input type="hidden" name="stockid" value="{{$inventoryItem->id}}">
                        <input type="hidden" name="supplier" value="{{$supplier->id}}">
                        <div class="row">
                            <div class="col-md-12" style="margin-bottom:10px">
                                <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                        <label for="supplier_name">Supplier NAME:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <span class="form-control bg-secondary">{{$supplier->name}}</span>
                                    </div>
                                </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                        <label for="supplier_name">Currency:</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <select name="currency" class="form-control" id="currency">
                                            @foreach ($currencys as $currency)
                                                <option value="{{$currency->ISO4217}}" {{$supplier_item->currency == $currency->ISO4217 ? 'selected' : ''}}>{{$currency->ISO4217}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                        <label for="price">Price (in Supplier Currency)</label>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="number" value="{{$supplier_item->price}}" name="price" id="price" class="form-control" step="any" min="0" placeholder="Price (in Supplier Currency)" aria-describedby="helpId">
                                    </div>
                                </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="price_effective_from">Price Effective From</label>
                                </div>
                                <div class="col-sm-8">
                                  <input type="date" value="{{$supplier_item->price_effective_from}}" name="price_effective_from" id="price_effective_from" class="form-control" placeholder="Supplier NAME" aria-describedby="helpId">
                                </div>
                            </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="supplier_name">Our Unit of Measure</label> </div>
                                <div class="col-sm-8">
                                    <span class="form-control bg-secondary">
                                        <input type="hidden" name="our_unit_of_measure" value="{{$inventoryItem->getUnitOfMeausureDetail ? $inventoryItem->getUnitOfMeausureDetail->title : 'Each'}}">

                                        {{$inventoryItem->getUnitOfMeausureDetail ? $inventoryItem->getUnitOfMeausureDetail->title : 'Each'}}
                                    </span>
                                </div>
                            </div>
                            </div>
                            {{--
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="supplier_unit">Suppliers Unit of Measure</label> </div>
                                <div class="col-sm-8">
                                    <select name="supplier_unit" class="form-control" id="supplier_unit">
                                        @foreach ($units as $unit)
                                            <option value="{{$unit->title}}" {{$supplier_item->supplier_unit_of_measure == $currency->title ? 'selected' : ''}}>{{$unit->title}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="conversion_factor">Conversion Factor (to our UOM)</label> </div>
                                <div class="col-sm-8">
                                  <input type="text" value="{{$supplier_item->conversion_factor}}" name="conversion_factor" id="conversion_factor" class="form-control" placeholder="Conversion Factor (to our UOM)" aria-describedby="helpId">
                                </div>
                            </div>
                            </div>
                            
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="min_order_qty">MinOrderQty</label> </div>
                                <div class="col-sm-8">
                                  <input type="number" value="{{$supplier_item->minimum_order_quantity}}" name="min_order_qty" id="min_order_qty" class="form-control" placeholder="MinOrderQty" aria-describedby="helpId">
                                </div>
                                </div>
                            </div>
                           
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="lead_time">Lead Time (in days from date of order)</label> </div>
                                <div class="col-sm-8">
                                  <input type="text" value="{{$supplier_item->lead_time_days}}" name="lead_time" id="lead_time" class="form-control" placeholder="Lead Time (in days from date of order)" aria-describedby="helpId">
                                </div>
                                </div>
                            </div>--}}
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="supplier_stock_code">Supplier Stock Code</label> </div>
                                <div class="col-sm-8">
                                  <input type="text" value="{{$supplier_item->supplier_stock_code}}" name="supplier_stock_code" id="supplier_stock_code" class="form-control" placeholder="Supplier Stock Code" aria-describedby="helpId">
                                </div>
                                </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="supplier_stock">Supplier Stock Description</label> </div>
                                <div class="col-sm-8">
                                  <input type="text" value="{{$supplier_item->supplier_stock_description}}" name="supplier_stock" id="supplier_stock" class="form-control" placeholder="Supplier Stock Description" aria-describedby="helpId">
                                </div>
                                </div>
                            </div>
                            <div  style="margin-bottom:10px" class="col-md-12">
                                 <div class="row">
                                    <div class="col-sm-3 text-right" style="padding-top:5px">
                                  <label for="">Preferred Supplier</label> </div>
                                <div class="col-sm-8">
                                    <select name="preferred_supplier" class="form-control" id="">
                                        <option value="No"  {{$supplier_item->preferred_supplier == 'No' ? 'selected' : ''}}>No</option>
                                        <option value="Yes"  {{$supplier_item->preferred_supplier == 'Yes' ? 'selected' : ''}}>Yes</option>
                                    </select>
                                </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-danger btn-sm">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
    @endsection
    
    @section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    
    @endsection