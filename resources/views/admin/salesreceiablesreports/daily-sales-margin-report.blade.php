@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Daily Sales Margin Report </h3>
                </div>
            </div>

            <div class="box-body">
                <div style="margin-bottom: 20px; text-align: right">
                    <a href="{{ route('daily-sales-margin-download') }}" target="_blank" class="btn btn-primary">Export to Excel</a>
                </div>
                <table class="table table-bordered" id="create_datatable">
                    <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th>Item Id</th>
                            <th>Description</th>
                            <th>Supplier</th>
                            <th>Qty</th>
                            <th>Cost</th>
                            <th>Total Cost</th>
                            <th>Cost Excl</th>
                            <th>Total Cost Excl</th>
                            <th>Price</th>
                            <th>Total Price</th>
                            <th>Price Excl</th>
                            <th>Total Price Excl</th>
                            <th>Profit</th>
                            <th>Profit Excl</th>
                            <th>Margin</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($salesData as $i => $saleData)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $i + 1 }}</th>
                                <td>{{ $saleData->inventoryItem->stock_id_code }}</td>
                                <td>{{ $saleData->inventoryItem->description }}</td>
                                <td>{{ $saleData->inventoryItem->suppliers->first()?->name }}</td>
                                <td>{{ $saleData->quantity_sold }}</td>
                                <td>{{ number_format($saleData->standard_cost) }}</td>
                                <td>{{ number_format($saleData->total_standard_cost) }}</td>
                                <td>{{ number_format($saleData->standard_cost_excl) }}</td>
                                <td>{{ number_format($saleData->total_standard_cost_excl) }}</td>
                                <td>{{ number_format($saleData->selling_price) }}</td>
                                <td>{{ number_format($saleData->total_selling_price) }}</td>
                                <td>{{ number_format($saleData->selling_price_excl) }}</td>
                                <td>{{ number_format($saleData->total_selling_price_excl) }}</td>
                                <td>{{ number_format($saleData->profit) }}</td>
                                <td>{{ number_format($saleData->profit_excl) }}</td>
                                <td>{{ $saleData->margin . '%' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script>
        $("body").addClass('sidebar-collapse');
    </script>
    
@endsection

