<div class="box-body">
    <div class="filters">
        <form action="">
            {{ @csrf_field() }}

            <div class="row">
                <div class="col-md-2 form-group">
                    <input type="date" name="date" id="date" class="form-control"
                        value="{{ request()->get('date') ?? \Carbon\Carbon::yesterday()->toDateString() }}">
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
                            <option value="{{ $route->id }}" {{ $route->id == request()->route ? 'selected' : '' }}>
                                {{ $route->route_name }}</option>
                        @endforeach
                    </select>

                </div>

                <div class="col-md-3 form-group">
                    <button type="submit" class="btn btn-success" name="manage-request"
                        value="order-taking-filter">Filter
                    </button>
                    <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.initial') !!}">Clear </a>
                </div>
            </div>
        </form>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-12">
            <form id="refresh_form"
                action="{{ route($source == 'initial' ? 'petty-cash-approvals.initial-approve' : 'petty-cash-approvals.final-approve') }}"
                method="post">
                {{ @csrf_field() }}

                <input type="hidden" name="petty_cash_type" value="travel-order-taking">

                <input type="hidden" name="approval_ids"
                    value="{{ json_encode($salesmanAllocations->pluck('id')->toArray()) }}">
                <table class="table table-bordered table-hover" id="incetive_datatable">

                    <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th> Date</th>
                            <th> Route</th>
                            <th> Salesman</th>
                            <th> Loading Schedule</th>
                            <th> Status</th>
                            <th> Customers</th>
                            <th> Met</th>
                            <th> Variance</th>
                            <th> Tonnage</th>
                            <th> Sales</th>
                            <th> Onsite</th>
                            <th> Offsite</th>
                            <th> Onsite Amount</th>
                            <th> Offsite Amount</th>
                            <th> Earned Amount</th>
                            <th>
                                <input type="checkbox" name="approve_all" id="salesman-approval-all-checkbox" checked>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $totalIncentiveAmount = 0; 
                        @endphp
                        @foreach ($salesmanAllocations as $record)
                            @php
                                $routeoffsiteallowance =
                                    floatval($record->offsite_allowance) / floatval($record->total_customers);
                                $routeonsiteallowance =
                                    floatval($record->travel_expense) / floatval($record->total_customers);
                                $incentiveAmount = ceil(
                                    $record->onsitecount * $routeonsiteallowance +
                                        $record->offsitecount * $routeoffsiteallowance,
                                );
                                $totalIncentiveAmount += $incentiveAmount;
                            @endphp
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ \Carbon\Carbon::parse($record->created_at)->format('d-m-Y H:i:s') }}
                                </td>
                                <td> {{ $record->route }} </td>
                                <td> {{ $record->salesman }} </td>
                                <td><a href="{{ route('salesman-shift-details', $record->shift_id) }}" target="_blank">
                                        {{ $record->delivery_number ?? '' }} </a>
                                </td>
                                <td> {{ ucfirst($record->status) }} </td>
                                <td> {{ $record->total_customers }} </td>
                                <td> {{ $record->met_customers }} </td>
                                <td> {{ $record->total_customers - $record->met_customers }} </td>
                                <td> {{ number_format($record->tonnage, 2) }} </td>
                                <td style="text-align: right;">
                                    {{ manageAmountFormat($record->gross_sales - $record->returns) }}</td>
                                <td style="text-align: right;"> {{ $record->onsitecount }}</td>
                                <td style="text-align: right;"> {{ $record->offsitecount }}</td>
                                <td style="text-align: right;"> {{ manageAmountFormat($record->travel_expense) }} </td>
                                <td style="text-align: right;">
                                {{manageAmountFormat($record->offsite_allowance)}}
                                    {{-- @if ($record->onsitecount != 0 && $record->offsitecount != 0)
                                        {{ manageAmountFormat($record->travel_expense + $record->offsite_allowance) }}
                                    @elseif ($record->onsitecount != 0 && $record->offsitecount == 0)
                                        {{ manageAmountFormat($record->travel_expense) }}
                                    @elseif ($record->offsitecount != 0 && $record->onsitecount == 0)
                                        {{ manageAmountFormat($record->offsite_allowance) }}
                                    @else
                                        0
                                    @endif --}}
                                </td>
                                <td>
                                    <input type="text" class="form-control order-taking-earned-amount"
                                        style="width: 100px;" value="{{ $record->amount }}"
                                        name="amount_{{ $record->id }}">
                                </td>
                                <td>
                                    <input type="checkbox" name="approve_{{ $record->id }}"
                                        class="salesman-approval-checkbox" checked>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <th class="hover-th">l</th>
                            <th class="hover-th">l</th>
                            <th class="hover-th">l</th>
                            <th class="hover-th">l</th>
                            <th class="hover-th">l</th>
                            <th class="hover-th">l</th>
                            <th>{{ $salesmanAllocations->sum('total_customers') }}</th>
                            <th>{{ $salesmanAllocations->sum('met_customers') }}</th>
                            <th>{{ $salesmanAllocations->sum('total_customers') - $salesmanAllocations->sum('met_customers') }}
                            </th>
                            <th>{{ number_format($salesmanAllocations->sum('tonnage'), 2) }}</th>
                            <th>
                                {{ manageAmountFormat($salesmanAllocations->sum('gross_sales') - $salesmanAllocations->sum('returns')) }}
                            </th>
                            <th class="hover-th">l</th>
                            <th class="hover-th">l</th>
                            <th>
                                {{ manageAmountFormat($salesmanAllocations->sum('travel_expense')) }}</td>
                            </th>
                            <th class="hover-th">l</th>
                            <th>{{ manageAmountFormat($salesmanAllocations->sum('amount')) }}</th>
                            <th class="hover-th">l</th>
                        </tr>
                    </tbody>

                    {{-- <tfoot>
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td> {{ $salesmanAllocations->sum('total_customers') }} </td>
                            <td> {{ $salesmanAllocations->sum('met_customers') }} </td>
                            <td> {{ $salesmanAllocations->sum('total_customers') - $salesmanAllocations->sum('met_customers') }}
                            </td>
                            <td> {{ number_format($salesmanAllocations->sum('tonnage'), 2) }} </td>
                            <td style="text-align: right;">
                                {{ manageAmountFormat($salesmanAllocations->sum('gross_sales') - $salesmanAllocations->sum('returns')) }}
                            </td>
                            <td colspan="2"></td>
                            <td style="text-align: right;">
                                {{ manageAmountFormat($salesmanAllocations->sum('travel_expense')) }}</td>
                            <td></td>
                            <td></td>

                        </tr>
                        <tr>
                            <th colspan="15" style="text-align: right;"></th>
                            <th id="order-taking-total-amount">
                                {{ manageAmountFormat($salesmanAllocations->sum('amount')) }} </th>
                            <td></td>
                            <td></td>
                        </tr>

                        @if (count($salesmanAllocations) > 0)
                            <tr>
                                <th style="text-align: right;" colspan="16">
                                    <input type="submit" id="refresh-recalculate-btn" value="Refresh & Recalculate"
                                        class="btn btn-primary">
                                    <input type="submit" value="Confirm Approval" class="btn btn-primary">
                                </th>
                            </tr>
                        @endif
                    </tfoot> --}}

                </table>
                @if (count($salesmanAllocations) > 0)
                    {{-- <tr>
                        <th style="text-align: right;" colspan="16">
                            <input type="submit" id="refresh-recalculate-btn" value="Refresh & Recalculate"
                                class="btn btn-primary">
                            <input type="submit" value="Confirm Approval" class="btn btn-primary">
                        </th>
                    </tr> --}}
                    <div style="text-align: right;">
                        <input type="submit" id="refresh-recalculate-btn" value="Refresh & Recalculate"
                            class="btn btn-primary">
                        <input type="submit" value="Confirm Approval" class="btn btn-primary">
                    </div>
                @endif
            </form>
        </div>
    </div>



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
                <th>Salesman</th>
                <th>Phone Number</th>
                <th>Customer Count</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($unVisitedRoutes as $route)
                <tr>
                    <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                    <td> {{ \Carbon\Carbon::parse($route->created_at)->format('d-m-Y') }} </td>
                    <td> {{ $route->route_name }} </td>
                    <td> {{ $route->salesman }} </td>
                    <td> {{ $route->phone_number }} </td>
                    <td> {{ $route->total_customers }} </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
