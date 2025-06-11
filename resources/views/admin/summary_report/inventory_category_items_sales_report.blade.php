@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <h4>{{ $category->category_description }}</h4>  
            </div>
            <div class="box-body">
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('summary_report.category_items_sales', ['category' => $category, 'date' => $date]) }}">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Location</label>
                                <select name="location" id="location_id" class='form-control'>
                                    <option value="">Show All</option>
                                    @foreach ($locations as $location)
                                        <option
                                            value="{{ $location->id }}" 
                                            {{ request()->location == $location->id ? 'selected' : '' }}>
                                            {{ $location->location_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label style="display: block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="submit" name="action" value="print"class="btn btn-primary">
                                    Print PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Description</th>
                                <th>QOH As At</th>
                                <th>Current Cost</th>
                                <th>Cost Used</th>
                                <th>Total Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>{{ $item->stock_id_code }}</td>
                                    <td>{{ $item->title }}</td>
                                    <td>{{ $item->qoh }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->standard_cost) }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->cost_used) }}</td>
                                    <td class="text-right">{{ manageAmountFormat($item->total) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th colspan="5">Total: </td>
                                <th class="text-right">{{ manageAmountFormat($items->sum('total')) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
