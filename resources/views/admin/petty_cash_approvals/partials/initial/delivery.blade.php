<div class="box-body">
    <div class="filters">
        <form action="{{ route('petty-cash-approvals.initial') }}" method="get">
            {{ @csrf_field() }}

            <div class="row">
                <div class="col-md-2 form-group">
                    <input type="date" name="delivery_date" id="date" class="form-control"
                           value="{{ request()->get('delivery_date') ?? \Carbon\Carbon::today()->toDateString() }}">
                </div>

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
                    <select name="route" id="route" class="mlselect form-control">
                        <option value="" selected disabled>Select Route</option>
                        @foreach ($routes as $route)
                            <option value="{{ $route->id }}"
                                    {{ $route->id == request()->route ? 'selected' : '' }}>
                                {{ $route->route_name }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="col-md-3 form-group">
                    <button type="submit" class="btn btn-success" name="manage-request"
                            value="delivery-filter">Filter
                    </button>
                    <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.initial') !!}">Clear </a>
                </div>
            </div>
        </form>
    </div>

    <hr>

    <form action="{{ route($source == 'initial' ? 'petty-cash-approvals.initial-approve' : 'petty-cash-approvals.final-approve') }}" method="post">
        {{ @csrf_field() }}

        <input type="hidden" name="petty_cash_type" value="travel-delivery">
        
        <input type="hidden" name="approval_ids" value="{{ json_encode($driverAllocations->pluck('id')->toArray()) }}">

        <table class="table table-bordered table-hover" id="create_datatable_50">

            <thead>
            <tr>
                <th style="width: 3%;"> #</th>
                <th> Date</th>
                <th> Route</th>
                <th> Driver</th>
                <th> Loading Schedule</th>
                <th> Customers</th>
                <th> Met</th>
                <th> Variance</th>
                <th> Tonnage</th>
                <th> Sales</th>
                <th> Travel Amount</th>
                <th> Earned Amount</th>
                <th>
                    <input type="checkbox" name="approve_all" id="approval-all-checkbox"
                           checked>
                </th>
            </tr>
            </thead>

            <tbody>
            @foreach ($driverAllocations as $record)
                <tr>
                    <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                    <td> {{ \Carbon\Carbon::parse($record->created_at)->format('d-m-Y H:i:s') }}
                    </td>
                    <td> {{ $record->route }} </td>
                    <td> {{ $record->salesman }} </td>
                    <td><a href="{{ route('salesman-shift-details', $record->shift_id) }}" target="_blank"> {{ $record->delivery_number }}
                        </a></td>
                    <td> {{ $record->total_customers }} </td>
                    <td> {{ $record->met_customers }} </td>
                    <td> {{ $record->total_customers - $record->met_customers }} </td>
                    <td> {{ number_format($record->tonnage, 2) }} </td>
                    <td style="text-align: right;"> {{ manageAmountFormat($record->gross_sales - $record->returns) }}
                    </td>
                    <td style="text-align: right;"> {{ manageAmountFormat($record->travel_expense) }} </td>
                    <td>
                        {{-- <input type="text" class="form-control delivery-earned-amount" style="width: 100px;" value="{{ $record->driver_allocation }}" name="amount_{{ $record->id }}"> --}}
                        <input type="text" class="form-control delivery-earned-amount" style="width: 100px;" value="{{ $record->amount }}" name="amount_{{ $record->id }}">
                    </td>
                    <td>
                        <input type="checkbox" name="approve_{{ $record->id }}" class="approval-checkbox" checked>
                    </td>
                </tr>
            @endforeach
            </tbody>

            <tfoot>
            <tr>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td> {{ $driverAllocations->sum('total_customers') }} </td>
                <td> {{ $driverAllocations->sum('met_customers') }} </td>
                <td> {{ $driverAllocations->sum('total_customers') - $driverAllocations->sum('met_customers') }}
                </td>
                <td> {{ number_format( $driverAllocations->sum('tonnage'), 2) }} </td>
                <td style="text-align: right;"> {{ manageAmountFormat($driverAllocations->sum('gross_sales') - $driverAllocations->sum('returns')) }}</td>
                <td style="text-align: right;"> {{ manageAmountFormat($driverAllocations->sum('travel_expense')) }}</td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <th colspan="11" style="text-align: right;"></th>
                <th id="delivery-total-amount"> {{ manageAmountFormat($driverAllocations->sum('amount')) }} </th>
                <td></td>
            </tr>

            @if(count($driverAllocations) > 0)
                <tr>
                    <th style="text-align: right;" colspan="13">
                        <input type="submit" value="Confirm Approval" class="btn btn-primary">
                    </th>
                </tr>
            @endif
            </tfoot>
        </table>
    </form>

    <hr>

    <div class="box-header">
        <h3 class="box-title"> Unvisited Routes </h3>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th>Date</th>
            <th>Route</th>
            <th>Driver</th>
            <th>Phone Number</th>
            <th>Customer Count</th>
        </tr>
        </thead>

        <tbody>
        @foreach($unVisitedDeliveries as $route)
            <tr>
                <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                <td> {{ \Carbon\Carbon::parse($route->created_at)->format('d-m-Y') }} </td>
                <td> {{ $route->route_name }} </td>
                <td> {{ $route->driver }} </td>
                <td> {{ $route->phone_number }} </td>
                <td> {{ $route->total_customers }} </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>