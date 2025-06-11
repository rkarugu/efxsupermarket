@extends('layouts.admin.admin')

@section('content')
    <section class="content" id="return-from-store">
        <div class="session-message-container">
            @include('message')
        </div>
        
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Return To Supplier From Store (Rejected Details) - {{ $return->rfs_no }} </h3>
            </div>

            <div class="box-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Item Code</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Weight</th>
                            <th>Cost</th>
                            <th>Total Cost</th>
                        </tr>
                        </thead>
                        <tbody>
                            @foreach ($return->storeReturnItems as $i => $returnItem)
                                <tr>
                                    <th style="width: 3%;"> {{ $i + 1 }}</th>
                                    <td>{{ $returnItem->inventoryItem->stock_id_code }}</td>
                                    <td>{{ $returnItem->inventoryItem->description }}</td>
                                    <td>{{ number_format($returnItem->quantity) }}</td>
                                    <td>{{ number_format($returnItem->weight) }}</td>
                                    <td>{{ number_format($returnItem->cost, 2) }}</td>
                                    <td>{{ number_format($returnItem->total_cost, 2) }}</td>
                                </tr>                                
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

