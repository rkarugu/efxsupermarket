
@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">                            
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th >S.No.</th>                                       
                            <th   >Supplier</th>
                            <th   >Price</th>
                            {{-- <th   >Supplier Unit</th>
                            <th   >Conversion Factor</th> 
                            <th   >Min Order Oty</th>
                            <th   >Lead Time </th>
                            --}}
                            <th   >Cost Per Our Unit</th>
                            <th   >Currency</th>
                            <th   >Effective From</th>
                            <th   >Preferred</th>
                            <th   class="noneedtoshort" >Action</th>
                        </tr>
                        </thead>
                        <tbody>
                            @if(count($item_suppliers)>0)
                                <?php $b = 1;?>
                                @foreach($item_suppliers as $list)                                         
                                    <tr>
                                        <td>{!! $b !!}</td>
                                        <td>{!! $list->supplier?->supplier_code !!}</td>
                                        <td>{!! $list->price !!}</td>
                                        {{--<td>{!! $list->supplier_unit_of_measure !!}</td>
                                        <td>{!! $list->conversion_factor !!}</td>
                                        <td>{!! $list->lead_time_days !!}</td>
                                        <td>{!! $list->minimum_order_quantity !!}</td>--}}
                                        <td>{!! $list->our_unit_of_measure !!}</td>
                                        <td>{!! $list->currency !!}</td>
                                        <td>{!! $list->price_effective_from !!}</td>
                                        <td>{!! $list->preferred_supplier !!}</td>
                                        <td style="display:flex">
                                            {!! buttonHtmlCustom('edit', route('maintain-items.purchaseDataEdit',['stockid'=>encrypt($inventoryItem->id), 'itemid'=>encrypt($list->id)])) !!}
                                            {!! buttonHtmlCustom('delete', route('maintain-items.purchaseDataDelete',['stockid'=>encrypt($inventoryItem->id), 'itemid'=>encrypt($list->id)])) !!}
                                        </td>
                                    </tr>
                                <?php $b++; ?>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
   
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">                            
                <div class="col-md-12 no-padding-h">
                    @if(isset($suppliers) && count($suppliers)>0)
                    <form action="{{route('maintain-items.purchaseDataAdd',['stockid'=>$inventoryItem->id])}}" method="get">
                        <input type="hidden" name="stockid" value="{{$inventoryItem->id}}">
                        <table class="table table-bordered table-hover">
                            <thead>
                            <tr>
                                <th >S.No.</th>                                       
                                <th   >Supplier Code</th>
                                <th   >Name</th>
                            </tr>
                            </thead>
                            <tbody>
                                    <?php $b = 1;?>
                                    @foreach($suppliers as $supplier)                                         
                                        <tr>
                                            <td>{!! $b !!}</td>
                                            <td><input type="submit" name="supplier_code" value="{!! $supplier->supplier_code !!}" class="btn btn-primary btn-sm"></td>
                                            <td>{!! $supplier->name !!}</td>
                                        </tr>
                                    <?php $b++; ?>
                                    @endforeach
                            </tbody>
                        </table>
                    </form>
                    @else
                    <center><b>No Supplier found</b></center>
                    {{--<form action="{{route('maintain-items.purchaseDataFetch',['stockid'=>$inventoryItem->id])}}" method="get">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label for="supplier_name">Text in the Supplier NAME</label>
                                  <input type="text" name="supplier_name" id="supplier_name" class="form-control" placeholder="Supplier NAME" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                  <label for="supplier_code">Text in Supplier CODE</label>
                                  <input type="text" name="supplier_code" id="supplier_code" class="form-control" placeholder="Supplier CODE" aria-describedby="helpId">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-danger btn-sm">Find Supplier Now</button>
                            </div>
                        </div>
                    </form>--}}
                    @endif
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">
    </script>
   
@endsection
