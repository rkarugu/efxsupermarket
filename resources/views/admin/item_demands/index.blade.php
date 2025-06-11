@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Item Supplier Demands </h3>
                </div>
            </div>

            <div class="box-body">
                <form action="" method="get">
                    {{-- <div class="form-group col-sm-4">
                        <select name="item" class="form-control" id="item-id">
                            <option value="" disabled selected></option>
                            @foreach($myItems as $item)
                                <option value="{{ $item->id }}" @if(request()->item == $item->id) selected @endif> ({{ $item->stock_id_code }}) {{ $item->title }} </option>
                            @endforeach
                        </select>
                    </div> --}}

                    <div class="form-group col-sm-4">
                        <select name="supplier" class="form-control" id=supplier-id>
                            <option value="" selected disabled></option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @if(request()->supplier == $supplier->id) selected @endif> {{ $supplier->name }} </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary col-sm-2">Filter</button>
                </form>

                <div style="clear:both;">
                    <hr>
                </div>
{{-- 
                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                    <tr>
                        <th style="width: 3%;"> #</th>
                        <th>Date</th>
                        <th>Item</th>
                        <th>Supplier</th>
                        <th>Previous Cost</th>
                        <th>Available Qty</th>
                        <th>Total Valuation</th>
                        <th>New Cost</th>
                        <th>New Valuation</th>
                        <th>Demand</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($demands as $index => $demand)
                        <tr>
                            <th style="width: 3%;" scope="row"> {{ $index + 1 }}</th>
                            <td> {{ \Carbon\Carbon::parse($demand->created_at)->toFormattedDayDateString() }} </td>
                            <td>{{ $demand->item_name }} ({{ $demand->item_code }})</td>
                            <td>{{ $demand->supplier }}</td>
                            <td>{{ number_format($demand->current_cost, 2) }}</td>
                            <td>{{ number_format($demand->demand_quantity, 2) }}</td>
                            <td>{{ number_format($demand->valuation_before, 2) }}</td>
                            <td>{{ number_format($demand->new_cost, 2) }}</td>
                            <td>{{ number_format($demand->valuation_after, 2) }}</td>
                            <td>{{ number_format($demand->valuation_before - $demand->valuation_after, 2) }}</td>
                            <td></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table> --}}


                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                    <tr>
                        <th style="width: 3%;"> #</th>
                        <th>Supplier</th>
                        <th>No. of Items</th>
                        <th>Total Item Quantity</th>
                        <th>Total Delta</th>
                        
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($groupedDemands as $index => $demand)
                        <tr>
                            <th style="width: 3%;" scope="row"> {{ $loop->index+1 }}</th>
                           
                            <td>{{ $demand['supplier_name'] }}</td>
                            <td>{{ $demand['total_item_count'] }}</td>
                            <td>{{ $demand['total_item_quantity']}}</td>
                            <td>{{ number_format($demand['total_demand_amount'], 2) }}</td>
                            
                            <td><a href="{{route('demands.item-demands', $demand['supplier_id'])}}"><i class="fas fa-eye" style="color: #337ab7;"></i></a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>




            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $("#supplier-id").select2({
                placeholder: 'Select supplier',
                allowClear: true
            });

            $("#item-id").select2({
                placeholder: 'Select item',
                allowClear: true
            });
        });
    </script>
@endsection

