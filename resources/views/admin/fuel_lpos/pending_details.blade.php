@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="box-header-flex">
                    <h3 class="box-title">{{ $lpo->getRelatedVehicle?->license_plate_number }} - ({{ $lpo->getRelatedVehicle?->driver?->name }})</h3>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-success btn-sm">Back</a>
                        @if ($user->role_id == 1 || isset($permission['pending-fuel-lpos___approve']))
                            <a href="{{ route('fuel-lpos.approveLpo', $lpo->id) }}" class="btn btn-success btn-sm">Approve</a>
                        @endif

                        


                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                
                    <div class="col-md-3">
                        <label for="">Date</label>
                        <p>{{\Carbon\Carbon::parse($lpo->created_at)->toDateString()}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">LPO No.</label>
                        <p>{{$lpo->lpo_number}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Shift Type</label>
                        <p>{{$lpo->shift_type}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Delivery Schedule</label>
                        @if ($lpo->shift_type == 'Miscellaneous')
                            <p> - </p>
                        @else
                            <p>{{$lpo->getRelatedShift?->deliveryNumber}}</p>   
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="">Route</label>
                        @if ($lpo->shift_type == 'Miscellaneous')
                            <p> {{ $lpo->comments ?? '-' }} </p>
                        @else
                            <p> {{ $lpo->getRelatedShift?->route?->route_name }} </p>
                                
                        @endif
                    </div>
                    <div class="col-md-3">
                        <label for="">Prev Mileage</label>
                        <p>{{$lpo->last_fuel_entry_mileage . ' Km'}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Current Mileage</label>
                        <p>{{ceil($lpo->end_shift_mileage). ' Km'}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Fuel Price</label>
                        <p>{{manageAmountFormat($lpo->fuel_price)}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Standard Distance</label>
                        <p>-</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Dashboard Distance</label>
                        <p>{{ceil($lpo->manual_distance_covered) . ' Km'}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Actual Distance</label>
                        <p>{{ceil($lpo->end_shift_mileage - $lpo->last_fuel_entry_mileage). ' Km'}}</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Distance Variance(actual vs standard)</label>
                        <p>-</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Standard  Fuel</label>
                        <p>-</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Dashboard Fuel</label>
                        <p>{{ceil($lpo->manual_distance_covered / $lpo->manual_consumption_rate)}}Lts</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Actual Fuel</label>
                        <p>{{ceil($lpo->actual_fuel_quantity)}}Lts</p>
                    </div>
                    <div class="col-md-3">
                        <label for="">Fuel Variance(actual vs standard)</label>
                        <p>-</p>
                    </div>
                    
                   
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <h4  style="margin-left: 15px;">Support Documents</h4>
            <div class="row" >
                <div class="col-md-6">
                    <label for="" style="margin-left: 15px;">Dashboard</label>
                    <div class="image">
                        <img src="{{ asset('uploads/dashboard_photos/' . $lpo->dashboard_photo) }}" alt="{{$lpo->dashboard_photo}}">
                    </div>

                </div>
                <div class="col-md-6">
                    <label for="">Receipt</label>
                    <div class="image">
                        <img src="{{ asset('uploads/dashboard_photos/' .$lpo->receipt_photo) }}" alt="{{$lpo->receipt_photo}}">

                    </div>


                </div>
             
            </div>
           
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }
        .image{
            margin-left:4px;
            border-radius: 10px;
            align-content: center;
            display: flex;
            justify-content: center; 
            align-items: center;  
            margin-bottom: 10px;
            
        }
        .image img{
            max-width: 500px;
            max-height: 300px;
            border-radius: 10px;
        }
        .col-md-6{
            height: 400px;
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
    {{-- <script src="{{ asset('js/form.js') }}"></script> --}}
    {{-- <script>
        function refreshTable(table) {
            table.DataTable().ajax.reload();
        }

        function printStockCard(input) {
            var url = "{{ route('maintain-items.stock-movements', ['stockIdCode' => $item->stock_id_code]) }}?" + $(input)
                .parents(
                    'form').serialize() + '&type=print';
            print_this(url);

        }
    </script> --}}
@endpush
