@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Confirmed Fuel Lpos </h3>
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
                                                {{ $branch->id == $selectedBranch ? 'selected' : '' }}>
                                            {{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 form-group">
                                <input type="date" name="date" id="date" class="form-control" value="{{ request()->date ?? \Carbon\Carbon::now()->toDateString() }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter"><i class="fas fa-filter"></i> Filter</button>
                                <a class="btn btn-success" href="{!! route('fuel-lpos.confirmed') !!}"><i class="fas fa-eraser"></i> Clear</a>
                            </div>
                            <div class="col-md-4 col-sm-6 col-xs-12" style="padding-top: 0px; padding-right:60px;">
                                <div class="" style="text-align: right; width:100%;">
                                    <span style="font-size: 20px; ">Fuel Saved</span>
                                    <br>
                                 <span id="saved-fuel-span" style="font-size: 25px; font-weight:bold; color:#0070E0;">{{manageAmountFormat($savedFuelValue)}}</span>

                                </div>

                            </div> 
                        </div>
                    </form>
                </div>
                <hr>
                <div class="row">
                        <div class="col-lg-3 col-xs-12">
                            <div class="small-box bg-aqua">
                                <div class="inner">
                                    <h4>Deliveries</h4>
                                    <span style="margin-top:2px; ">
                                        <table  style="border: none !important; width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="table-data">Actual</th>
                                                    <th class="table-data">Expected</th>
                                                    <th class="table-data">Variance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="table-data">{{$actualDeliveries}}</th>
                                                    <th class="table-data">{{$expectedDeliveries}}</th>
                                                    <th class="table-data">{{$actualDeliveries  - $expectedDeliveries}}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </span>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-route"></i>
                                </div>
                                <a href="{{route('fuel_lpos.deliveries', [ 'date' => $date, 'branch'=>$selectedBranch ])}}" class="small-box-footer" target="_blank">Show More
                                    <i class="fa fa-arrow-circle-right"></i></a>
                               
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-12">
                            <div class="small-box bg-red">
                                <div class="inner">
                                    <h4>Fillings</h4>
                                    <span style="margin-top:2px; ">
                                        <table  style="border: none !important; width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="table-data" >Actual</th>
                                                    <th class="table-data">Expected</th>
                                                    <th class="table-data">Variance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="table-data">{{$fueledEntries->count()}}</th>
                                                    <th class="table-data">{{$actualDeliveries}}</th>
                                                    <th class="table-data">{{$fueledEntries->count() - $actualDeliveries}}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </span>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-gas-pump"></i>
                                </div>
                                <a href="javascript:void(0)" class="small-box-footer">Show More
                                    <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-xs-12">
                            <div class="small-box bg-green">
                                <div class="inner">
                                    <h4>Fuel</h4>
                                    <span style="margin-top:2px; ">
                                        <table  style="border: none !important; width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="table-data">Actual</th>
                                                    <th class="table-data">Expected</th>
                                                    <th class="table-data">Variance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="table-data">{{$fueledEntries->sum('actual_fuel_quantity') . 'lts'}}</th>
                                                    <th class="table-data">{{$expectedFuelEntries->sum('shift_fuel_estimate') . ' lts'}}</th>
                                                    <th class="table-data">{{($fueledEntries->sum('actual_fuel_quantity') - $expectedFuelEntries->sum('shift_fuel_estimate'))  . ' lts'}}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </span>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-gauge-high"></i>
                                </div>
                                <a href="javascript:void(0)" class="small-box-footer">Show More
                                    <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                        <div class="col-lg-3 col-xs-12">
                            <div class="small-box bg-blue">
                                <div class="inner">
                                    <h4> Cost</h4>
                                    
                                    <span style="margin-top:2px; ">
                                        <table  style="border: none !important; width:100%;">
                                            <thead>
                                                <tr>
                                                    <th class="table-data">Actual</th>
                                                    <th class="table-data">Expected</th>
                                                    <th class="table-data">Variance</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <th class="table-data">{{manageAmountFormat($actualCost)}}</th>
                                                    <th class="table-data">{{manageAmountFormat($expectedFuelEntries->sum('shift_fuel_estimate') *  $stationFuelPrice)}}</th>
                                                    <th class="table-data">{{(manageAmountFormat($actualCost - ($expectedFuelEntries->sum('shift_fuel_estimate') *  $stationFuelPrice) ))  . ' lts'}}</th>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </span>
                                </div>
                                <div class="icon" style="font-size: 50px;margin-top:10px">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                                <a href="javascript:void(0)" class="small-box-footer">Show More
                                    <i class="fa fa-arrow-circle-right"></i></a>
                            </div>
                        </div>

                </div>

                
            </div>
        </div>
        <div class="box box-primary">
    

            <div class="box-body">
                <ul class="nav nav-tabs" style="margin-bottom:5px; ">
                    <li class="nav-item">
                        <a class="nav-link active" id="pending-tab" data-toggle="tab" href="#pending" role="tab">Pending</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="approved-tab" data-toggle="tab" href="#approved" role="tab">Approved</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="saved-fuel-tab" data-toggle="tab" href="#saved-fuel" role="tab">Saved Fuel</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="pending" role="tabpanel">
                        <table class="table" id="create_datatable">
                            <thead>
                                <tr>
                                    <th scope="col"> #</th>
                                    <th scope="col"> Posting Date</th>
                                    <th scope="col"> LPO Number</th>
                                    <th scope="col"> Receipt No </th>
                                    <th scope="col"> Shift Description</th>
                                    <th scope="col"> Document Number</th>
                                    <th scope="col"> Vehicle</th>
                                    <th scope="col">System - Lts</th>
                                    <th scope="col">Dashboard - Lts</th>
                                    <th scope="col">Variance</th>
                                    <th scope="col">Total</th>
                                    <th scope="col"> Action</th>
                          
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalQuantity = $totalAmount = $dashboardQuantity = 0;
                                    @endphp
                                    @foreach($lpos as $index => $lpo)
                                        <tr>
                                            <th scope="row"> {{ $index + 1 }}</th>
                                            <td> {{ \Carbon\Carbon::parse($lpo->created_at)->toDateString() }} </td>
                                            <td style="text-align: center;"> {{ $lpo->lpo_number }} </td>
                                            <td style="text-align: center;">{{$lpo->receipt_number}}</td>
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
                                            <td style="text-align: center;">{{$lpo->actual_fuel_quantity}} </td>
                                            <td style="text-align: center;">{{number_format($lpo->manual_distance_covered / $lpo->manual_consumption_rate,2)}}</td>
                                            <td style="text-align: center;">{{number_format($lpo->actual_fuel_quantity - ($lpo->manual_distance_covered / $lpo->manual_consumption_rate),2)}}</td>
                                            <td style="text-align: right;">{{manageAmountFormat($lpo->actual_fuel_quantity * $lpo->fuel_price)}}</td>
                                            <td>
                                                <div class="action-button-div">
                                                    @if ($permission == 'superadmin' || isset($permission['pending-fuel-lpos___view']))
                                                        <a href="{{route('fuel-lpos.approved.details', $lpo->id)}}" title="view details"><li class="fas fa-eye"></li></a>
                                                    @endif
                                                </div>
                                                <input type="checkbox" name="approved_lpos[]" value="{{$lpo->id}}" form="unblock_selected_form">
        
                                            </td>
                                        </tr>
                                        @php
                                            $totalQuantity += $lpo->actual_fuel_quantity;
                                            $dashboardQuantity += number_format($lpo->manual_distance_covered / $lpo->manual_consumption_rate,2);
                                            $totalAmount += ($lpo->actual_fuel_quantity * $lpo->fuel_price);
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7">Total</th>
                                        <th style="text-align: center;">{{ manageAmountFormat($totalQuantity) }} lts</th>
                                        <th style="text-align: center;">{{$dashboardQuantity}} lts</th>
                                        <th style="text-align: center;">{{manageAmountFormat($totalQuantity - $dashboardQuantity)}} lts</th>
                                        <th style="text-align: right;">{{ manageAmountFormat($totalAmount) }}</th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td colspan="12" class="text-right">
                                            <form id="unblock_selected_form" action="{{ route('fuel-lpos.confirm-selected') }}" method="POST">
                                                @csrf
                                                <button type="submit" id="approve_button" class="btn btn-success btn-sm" disabled><i class="fas fa-thumbs-up"></i> Approve Selected</button>
                                            </form>
                                        </td>
                                    </tr>
                                </tfoot>
                                
                         
                        </table>
                    </div>
                    <div class="tab-pane fade" id="approved" role="tabpanel">
                        <table class="table" id="create_datatable_10">
                            <thead>
                                <tr>
                                    <th scope="col"> #</th>
                                    <th scope="col"> Posting Date</th>
                                    <th scope="col"> LPO Number</th>
                                    <th scope="col"> Receipt No </th>
                                    <th scope="col"> Shift Description</th>
                                    <th scope="col"> Document Number</th>
                                    <th scope="col"> Vehicle</th>
                                    <th scope="col">System - Lts</th>
                                    <th scope="col">Dashboard - Lts</th>
                                    <th scope="col">Variance</th>
                                    <th scope="col">Total</th>
                                    <th scope="col"> Action</th>
                          
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalQuantity = $totalAmount = $dashboardQuantity = 0;
                                    @endphp
                                    @foreach($processedLpos as $index => $lpo)
                                        <tr>
                                            <th scope="row"> {{ $index + 1 }}</th>
                                            <td> {{ \Carbon\Carbon::parse($lpo->created_at)->toDateString() }} </td>
                                            <td style="text-align: center;"> {{ $lpo->lpo_number }} </td>
                                            <td style="text-align: center;">{{$lpo->receipt_number}}</td>
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
                                            <td style="text-align: center;">{{$lpo->actual_fuel_quantity}} </td>
                                            <td style="text-align: center;">{{number_format($lpo->manual_distance_covered / $lpo->manual_consumption_rate,2)}}</td>
                                            <td style="text-align: center;">{{number_format($lpo->actual_fuel_quantity - ($lpo->manual_distance_covered / $lpo->manual_consumption_rate),2)}}</td>                                            <td style="text-align: right;">{{manageAmountFormat($lpo->actual_fuel_quantity * $lpo->fuel_price)}}</td>
                                            <td>
                                                <div class="action-button-div">
                                                    @if ($permission == 'superadmin' || isset($permission['pending-fuel-lpos___view']))
                                                        <a href="{{route('fuel-lpos.approved.details', $lpo->id)}}" title="view details"><li class="fas fa-eye"></li></a>
                                                    @endif
                                                </div>        
                                            </td>
                                        </tr>
                                        @php
                                            $totalQuantity += $lpo->actual_fuel_quantity;
                                            $dashboardQuantity += number_format($lpo->manual_distance_covered / $lpo->manual_consumption_rate,2);
                                            $totalAmount += ($lpo->actual_fuel_quantity * $lpo->fuel_price);
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7">Total</th>
                                        <th style="text-align: center;">{{ manageAmountFormat($totalQuantity) }} Lts</th>
                                        <th style="text-align: center;">{{$dashboardQuantity}} lts</th>
                                        <th style="text-align: center;">{{manageAmountFormat($totalQuantity - $dashboardQuantity)}} lts</th>
                                        <th style="text-align: right;">{{ manageAmountFormat($totalAmount) }}</th>
                                        <th></th>
                                    </tr>
                                   
                                </tfoot>
                                
                         
                        </table>
                    </div>
                    <div class="tab-pane fade" id="saved-fuel" role="tabpanel">
                        <table class="table" id="create_datatable_10">
                            <thead>
                                <tr>
                                    <th scope="col"> #</th>
                                    <th scope="col"> Posting Date</th>
                                    <th scope="col"> LPO Number</th>
                                    <th scope="col"> Receipt No </th>
                                    <th scope="col"> Shift Description</th>
                                    <th scope="col"> Document Number</th>
                                    <th scope="col"> Vehicle</th>
                                    <th scope="col">System - Lts</th>
                                    <th scope="col">Dashboard - Lts</th>
                                    <th scope="col">Variance</th>
                                    <th scope="col">Total</th>
                                    <th scope="col"> Action</th>
                          
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalQuantity = $totalAmount = $dashboardQuantity = 0;
                                    @endphp
                                    @foreach($savedFuelEntries as $index => $lpo)
                                        <tr>
                                            <th scope="row"> {{ $index + 1 }}</th>
                                            <td> {{ \Carbon\Carbon::parse($lpo->created_at)->toDateString() }} </td>
                                            <td style="text-align: center;"> {{ $lpo->lpo_number }} </td>
                                            <td style="text-align: center;">{{$lpo->receipt_number}}</td>
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
                                            <td style="text-align: center;">{{$lpo->actual_fuel_quantity}} </td>
                                            <td style="text-align: center;">{{number_format($lpo->manual_distance_covered / $lpo->manual_consumption_rate,2)}}</td>
                                            <td style="text-align: center;">{{number_format($lpo->actual_fuel_quantity - ($lpo->manual_distance_covered / $lpo->manual_consumption_rate),2)}}</td>                                            <td style="text-align: right;">{{manageAmountFormat($lpo->actual_fuel_quantity * $lpo->fuel_price)}}</td>
                                            <td>
                                                <div class="action-button-div">
                                                    @if ($permission == 'superadmin' || isset($permission['pending-fuel-lpos___view']))
                                                        <a href="{{route('fuel-lpos.approved.details', $lpo->id)}}" title="view details"><li class="fas fa-eye"></li></a>
                                                    @endif
                                                </div>        
                                            </td>
                                        </tr>
                                        @php
                                            $totalQuantity += $lpo->actual_fuel_quantity;
                                            $dashboardQuantity += number_format($lpo->manual_distance_covered / $lpo->manual_consumption_rate,2);
                                            $totalAmount += ($lpo->actual_fuel_quantity * $lpo->fuel_price);
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="7">Total</th>
                                        <th style="text-align: center;">{{ manageAmountFormat($totalQuantity) }} Lts</th>
                                        <th style="text-align: center;">{{$dashboardQuantity}} lts</th>
                                        <th style="text-align: center;">{{manageAmountFormat($totalQuantity - $dashboardQuantity)}} lts</th>
                                        <th style="text-align: right;">{{ manageAmountFormat($totalAmount) }}</th>
                                        <th></th>
                                    </tr>
                                   
                                </tfoot>
                                
                         
                        </table>
                    </div>
                </div>
                
                
                
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
   
    <style>
        #saved-fuel-span {
            cursor: pointer;
        }
        

        .table-data{
            text-align: center;
        }
        .hidden {
            display: none !important;
        }
        .visible {
            /* display: block !important; */
        }
        .major-detail {
            border: 2px solid;
            border-radius: 15px;
            padding: 10px 15px;
            height: 80px;
            flex-grow: 1 !important;
            margin-right: 20px;
        }

        .major-detail.border-primary {
            border-color: #0d6efd;
        }

        .major-detail.border-success {
            border-color: #198754;
        }

        .major-detail.border-danger {
            border-color: #dc3545;
        }

        .major-detail.border-info {
            border-color: #0dcaf0;
        }

        .major-detail-icon {
            font-size: 20px;
        }

        .major-detail-title {
            font-size: 18px;
            font-weight: 500;
            margin-left: 12px;
            margin-top: -5px;
        }

        .major-detail-value {
            font-size: 20px;
            font-weight: 600;
        }

        #activity {
            position: relative;
            width: 40%;
        }

        .mt-20 {
            margin-top: 30px !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".mlselect").select2();
            $("body").addClass('sidebar-collapse');

            $('#approve_button').prop('disabled', true);

            $('input[name="approved_lpos[]"]').on('change', function() {
                if ($('input[name="approved_lpos[]"]:checked').length > 0) {
                    $('#approve_button').prop('disabled', false);
                } else {
                    $('#approve_button').prop('disabled', true);
                }
            });
            $('#pending-tab').trigger('click');
            
            $('#approved-tab').on('click', function(e) {
                $('#pending').addClass('hidden'); 
                $('#saved-fuel').addClass('hidden');
                $('#approved').removeClass('hidden').addClass('visible'); 

             });

            $('#pending-tab').on('click', function(e) {
                $('#approved').addClass('hidden'); 
                $('#saved-fuel').addClass('hidden');
                $('#pending').removeClass('hidden').addClass('visible'); 

            });
            $('#saved-fuel-tab').on('click', function(e) {
                $('#pending').addClass('hidden'); 
                $('#approved').addClass('hidden'); 
                $('#saved-fuel').removeClass('hidden').addClass('visible'); 

 
            });
            $('#saved-fuel-span').on('click', function() {
            $('#saved-fuel-tab').trigger('click');
        });

        });
   
    </script>
@endsection