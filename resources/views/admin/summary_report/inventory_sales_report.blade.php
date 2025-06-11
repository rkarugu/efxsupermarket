@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Inventory Sales Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('summary_report.inventory_sales_report') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Location</label>
                                <select name="location" id="location_id" class='form-control'>
                                    <option value="" @if (!request()->location || request()->location == '-1') selected @endif>Show All
                                    </option>
                                    @php
                                        $collection = getStoreLocationDropdown();
                                    @endphp
                                    @foreach ($collection as $key => $item)
                                        <option value="{{ $key }}"
                                            @if (request()->location == $key) selected @endif>{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="date">As at</label>
                                <input type="date" class="form-control" id="date" name="date" value="{{ request()->date }}">
                            </div>
                            <div class="col-md-3">
                                <label style="display: block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="submit" name="print" value="1"class="btn btn-primary">Print
                                    PDF</button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">

                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <a href="{{ route('summary_report.category_items_sales', ['category' => $category, 'date' => $date]) }}"
                                            target="_blank">
                                            {{ $category->category_description }}
                                        </a>
                                    </td>
                                    <td>{{ manageAmountFormat($category->total) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th>Total: </td>
                                <th>{{ manageAmountFormat($categories->sum('total')) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
