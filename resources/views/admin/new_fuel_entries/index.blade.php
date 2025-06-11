@extends('layouts.admin.admin')

@section('content')
<section class="content">
    <div class="box box-primary">
        <div class="box-header with-border">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="box-title"> Fuel History </h3>
            </div>
        </div>

        <div class="box-body">
            <div class="session-message-container">
                @include('message')
            </div>

            <div class="table-responsive">
                <table class="table" id="create_datatable">
                    <thead>
                    <tr>
                        <th scope="col"> #</th>
                        <th scope="col"> LPO </th>
                        <th scope="col"> Date</th>
                        <th scope="col"> Vehicle</th>
                        <th scope="col"> Route</th>
                        <th scope="col"> Pre Mileage</th>
                        <th scope="col"> Current Mileage</th>
                        <th scope="col"> System Mileage</th>
                        <th scope="col"> Distance Estimate</th>
                        <th scope="col"> Distance Covered</th>
                        <th scope="col"> Variance</th>
                        <th scope="col"> Fuel Estimate</th>
                        <th scope="col"> Fuel Consumed</th>
                        <th scope="col"> Variance</th>
                        <th scope="col"> Fuel Price</th>
                        <th scope="col"> Fuel Total</th>
                        <th scope="col"> Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($entries as $index => $entry)
                    <tr>
                        <th scope="row"> {{ $index + 1 }}</th>
                        <td> {{ $entry->lpo_number }}</td>
                        <td> {{ $entry->date }}</td>
                        <td> {{ $entry->license_plate }}</td>
                        <td> {{ $entry->route_name }}</td>
                        <td> {{ $entry->pre_mileage }} Km</td>
                        <td> {{ $entry->current_mileage }} Km</td>
                        <td> {{ $entry->system_mileage }} Km</td>
                        <td> {{ $entry->distance_estimate }} Km</td>
                        <td> {{ $entry->distance_covered }} Km</td>
                        <td> {{ $entry->distance_variance }} Km</td>
                        <td> {{ $entry->fuel_estimate }} L</td>
                        <td> {{ $entry->fuel_consumed }} L</td>
                        <td> {{ $entry->fuel_variance}} L</td>
                        <td> {{ format_amount_with_currency($entry->fuel_price) }} </td>
                        <td> {{ format_amount_with_currency($entry->fuel_price * $entry->fuel_consumed) }} </td>
                        <td>
                            <div class="action-button-div">
                                 <a href="javascript:void(0);" title="Approve">
                                 <i class="fa fa-check-square text-success fa-lg" aria-hidden="true"></i>
                                 </a>

                                {{-- <a href="javascript:void(0);" title="Remove Station" onclick="confirmStationDeletion()">--}}
                                    {{-- <i class="fa fa-trash text-danger fa-lg" aria-hidden="true"></i>--}}
                                    {{--
                                    <form action="{{ route(" $base_route.destroy
                                    ", $station->id) }}" method="post" id="delete-station-form"--}}
                                    {{--style = "display: inline-block;" > --}}
                                    {{-- <input type="hidden" name="_method" value="DELETE">--}}
                                    {{--{{@csrf_field()}}--}}
                                    {{-- </form>--}}
                                    {{--                                        </a>--}}
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
@endsection