@extends('layouts.admin.admin')

@section('content')
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $title }}</h3>
                </div>
            </div>

            <div class="box-body">

                <hr>

                @include('message')

                <!-- Nav tabs -->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#promotions" data-toggle="tab">Promotions</a></li>
                    <li><a href="#discounts" data-toggle="tab">Discounts</a></li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane fade in active" id="promotions">
                        <div class="col-md-12 mt-2 mb-3">
                            <table class="table table-bordered table-hover mt-2" id="create_datatable">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Was</th>
                                    <th>Is</th>
                                    <th>Save</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($promotions as $promotion)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $promotion->inventoryItem-> title }}</td>
                                        <td>{{ $promotion->current_price }}</td>
                                        <td>{{ $promotion->promotion_price }}</td>
                                        <td>{{ $promotion->current_price - $promotion->promotion_price }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="discounts">
                        <div class="col-md-12 mt-2 mb-3">
                            <table class="table table-bordered table-hover mt-2" id="create_datatable_50">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product</th>
                                    <th>From QTY</th>
                                    <th>To QTY</th>
                                    <th>Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($discounts as $discount)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $discount->inventoryItem-> title }}</td>
                                        <td>{{ $discount->from_quantity }}</td>
                                        <td>{{ $discount->to_quantity }}</td>
                                        <td>{{ $discount->discount_amount }}</td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>


@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <style>
        .modal .datepicker {
            z-index: 9999; /* Higher than Bootstrap modal z-index */
        }
        .error-message {
            color: red;
            font-size: 12px;
        }
    </style>
@endpush
@push('scripts')
    <div id="loader-on"
         style="position: fixed; top: 0; text-align: center; z-index: 999999;
                width: 100%;  height: 100%; background: #000000b8; display:none;"
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd'
            });
        });
    </script>

@endpush
