@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Approved Fuel Lpos </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form action="">
                        {{ @csrf_field() }}
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <select name="branch" id="branch" class="mlselect form-control">
                                    <option value="" selected disabled>Select branch</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                                {{ $branch->id == request()->branch ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="date" name="date" id="date" class="form-control" value="{{ request()->date ?? \Carbon\Carbon::now()->toDateString() }}">
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request()->to_date ?? \Carbon\Carbon::now()->toDateString() }}">
                            </div>

                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                                <a class="btn btn-success" href="{!! route('fuel-lpos.processed') !!}"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <table class="table" id="create_datatable">
                    <thead>
                        <tr>
                            <th scope="col"> #</th>
                            <th scope="col"> Posting Date</th>
                            <th scope="col"> LPO Number</th>
                            <th scope="col"> Shift Type</th>
                            <th scope="col"> Shift Description</th>
                            <th scope="col"> Document Number</th>
                            <th scope="col"> Vehicle</th>
                            <th scope="col"> Fueling Branch</th>
                            <th scope="col">Fueled Qty - Lts</th>
                            <th scope="col">Total</th>
                            {{-- <th scope="col"> Action</th> --}}
                  
                        </tr>
                        </thead>
                        <tbody>
                            @php
                                $totalQuantity = $totalAmount = 0;
                            @endphp
                            @foreach($lpos as $index => $lpo)
                                <tr>
                                    <th scope="row"> {{ $index + 1 }}</th>
                                    <td> {{ \Carbon\Carbon::parse($lpo->created_at)->toDateString() }} </td>
                                    <td style="text-align: center;"> {{ $lpo->lpo_number }} </td>
                                    <td> {{ $lpo->shift_type }} </td>
                                    @if ($lpo->shift_type == 'Miscellaneous')
                                        <td> {{ $lpo->comments ?? '' }} </td>

                                    @else
                                        <td> {{ $lpo->getRelatedShift?->route?->route_name }} </td>
                                            
                                    @endif
                                    @if($lpo->getRelatedShift)
                                            <td style="text-align: center;"> <a href="{{route('salesman-shift-details', $lpo->getRelatedShift?->shift_id)}}" target="_blank"> {{ $lpo->getRelatedShift?->delivery_number }}</a></td>
                                    @else
                                        <td style="text-align: center;"> - </td>
                                    @endif
                                    <td> {{ $lpo->getRelatedVehicle?->license_plate_number. ' ('.$lpo->getRelatedVehicle?->driver?->name . ')' }} </td>
                                    <td>{{ $lpo->getRelatedShift?->route?->branch->name }}</td>
                                    <td style="text-align: center;">{{$lpo->actual_fuel_quantity}} </td>
                                    <td style="text-align: right;">{{manageAmountFormat($lpo->actual_fuel_quantity * $lpo->fuel_price)}}</td>
                                    {{-- <td>
                                        <div class="action-button-div">
                                            @if ($permission == 'superadmin' || isset($permission['approved-fuel-lpos___view']))
                                                <a href="{{route('fuel-lpos.approved.details', $lpo->id)}}" title="view details"><li class="fas fa-eye"></li></a>
                                            @endif
                                        </div>
                                        <input type="checkbox" name="approved_lpos[]" value="{{$lpo->id}}" form="unblock_selected_form">

                                    </td> --}}
                                </tr>
                                @php
                                    $totalQuantity += $lpo->actual_fuel_quantity;
                                    $totalAmount += ($lpo->actual_fuel_quantity * $lpo->fuel_price);
                                @endphp
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="8">Total</th>
                                <th style="text-align: center;">{{ manageAmountFormat($totalQuantity) }} Lts</th>
                                <th style="text-align: right;">{{ manageAmountFormat($totalAmount) }}</th>
                            </tr>
                            {{-- <tr>
                                <td colspan="11" class="text-right">
                                    <form id="unblock_selected_form" action="{{ route('fuel-lpos.confirm-selected') }}" method="POST">
                                        @csrf
                                        <button type="submit" id="approve_button" class="btn btn-success btn-sm" disabled><i class="fas fa-thumbs-up"></i> Approve Selected</button>
                                    </form>
                                </td>
                            </tr> --}}
                        </tfoot>
                        
                 
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $(".mlselect").select2();
            // $("body").addClass('sidebar-collapse');
        });
        $(document).ready(function() {
    $('#approve_button').prop('disabled', true);

    $('input[name="approved_lpos[]"]').on('change', function() {
        if ($('input[name="approved_lpos[]"]:checked').length > 0) {
            $('#approve_button').prop('disabled', false);
        } else {
            $('#approve_button').prop('disabled', true);
        }
    });
});
    </script>
@endsection