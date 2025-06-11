@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">DEMAND {{$demand->demand_no}} ITEMS </h3>
                    <a href="{{route('return-demands.index')}}" class="btn btn-primary">{{'<< '}} Back</a>
                </div>
            </div>

            <div class="box-body">
                <form>
                    <div class="form-group col-sm-4">
                        <select name="item" class="form-control" id="item-id">
                            <option value="" disabled selected></option>
                            @foreach($inventoryItems as $item)
                                <option value="{{ $item->id }}" @if(request()->item == $item->id) selected @endif> ({{ $item->stock_id_code }}) {{ $item->title }} </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary col-sm-2">Filter</button>
                </form>

                <div style="clear:both;">
                    <hr>
                </div>

                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th>Date</th>
                            <th>Stock ID</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Cost</th>
                            <th>Demand</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandItems as $i => $demandItem)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $i + 1 }}</th>
                                <td> {{ $demandItem->created_at->format('Y-m-d') }} </td>
                                <td>{{ $demandItem->inventoryItem?->stock_id_code ?? '-' }}</td>
                                <td>{{ $demandItem->inventoryItem?->title ?? '-' }}</td>
                                <td>{{ number_format($demandItem->quantity) }}</td>
                                <td>{{ number_format($demandItem->cost, 2) }}</td>
                                <td>{{ number_format($demandItem->demand_cost, 2) }}</td>
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

