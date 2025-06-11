@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Location Stock Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Inventory Reports </a>
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                <form action="" method="get" role="form">
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Category</label>
                                <select name="category" class="form-control mtselect">
                                    <option value="" selected>Show All</option>
                                    @foreach ($categories as $key => $category)
                                        <option value="{{ $category->id }}"
                                            {{ $category->id == request()->category ? 'selected' : '' }}>
                                            {{ $category->category_description }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="">Supplier</label>
                                <select name="supplier" id="supplier" class="form-control mtselect">
                                    <option value="" selected>Show All</option>
                                    @foreach ($suppliers as $supplier)
                                        <option data-emails="{{ $supplier->users->pluck('email') }}"
                                            data-email="{{ $supplier->email }}" value="{{ $supplier->id }}"
                                            @selected($supplier->id == request()->supplier)>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="type">Type</label>
                                <select name="type" id="type" class="form-control mtselect">
                                    <option value="quantity" {{ request()->type == 'quantity' ? 'selected' : '' }}>Quantity
                                    </option>
                                    <option value="values" {{ request()->type == 'values' ? 'selected' : '' }}>Values
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="can_order">Can Order</label>
                                <select name="can_order" id="can_order" class="form-control mtselect">
                                    <option value="" selected>Show All</option>
                                    <option value="1" {{ request()->can_order === '1' ? 'selected' : '' }}>Full Packs
                                    </option>
                                    <option value="0" {{ request()->can_order === '0' ? 'selected' : '' }}>Inner Packs
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="categorize">
                                    Categorize
                                    <input type="checkbox" id="categorize" name="categorize" value="1"
                                        {{ request()->categorize ? 'checked' : '' }}>
                                </label>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <button type="submit" class="btn btn-primary">Cancel</button>
                    <button type="submit" class="btn btn-primary" name="print" value="1">Print PDF</button>
                    <button type="button" class="btn btn-primary" id="sendEmailBtn" data-toggle="modal"
                        data-target="#send-to-supplier-modal" disabled>Send Email</button>
                </form>
                <div class="col-md-12 no-padding-h">
                    @isset($categorized)
                        @include('admin.maintaininvetoryitems.partials.categorized_stock_list')
                    @else
                        @include('admin.maintaininvetoryitems.partials.stock_list')
                    @endisset
                </div>
                <div class="text-right">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
        <x-inventory.location-stock-email />
    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style type="text/css">
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function(e) {
            $('.mtselect').select2();

            $("#supplier, #type").change(function() {
                enableEmail();
            });

            enableEmail();
        });

        function enableEmail() {
            if ($("#supplier").val().length > 0 && $("#type").val() == 'quantity') {
                $("#sendEmailBtn").prop('disabled', false);
            } else {
                $("#sendEmailBtn").prop('disabled', true);
            }
        }
    </script>
@endpush
