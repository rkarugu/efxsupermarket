@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Petty Cash Final Approvals - {{ ucwords(str_replace('_', ' ', request()->type)) }} {{ request()->date }} </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="filters">
                    <form action="">
                        {{ @csrf_field() }}

                        <div class="row">
                            <div class="col-md-2 form-group">
                                <input type="date" name="date" id="date" class="form-control" value="{{ request()->get('date') }}" readonly>
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
                                <button type="submit" class="btn btn-success" name="manage-request" value="filter">Filter
                                </button>
                                <a class="btn btn-success ml-12" href="{!! route('petty-cash-approvals.initial') !!}">Clear </a>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <form action="{{ route('petty-cash-approvals.final-approve') }}" method="post">
                    {{ @csrf_field() }}

                    <input type="hidden" name="date_time" value="{{ request()->date }}">

                    <input type="hidden" name="approval_ids" value="{{ json_encode($salesmanAllocations->pluck('id')->toArray()) }}">
                    <table class="table table-bordered table-hover" id="create_datatable">

                        <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th> Date</th>
                            <th> Route</th>
                            <th> User</th>
                            <th> Loading Schedule</th>
                            <th> Customers</th>
                            <th> Met</th>
                            <th> Variance</th>
                            <th> Tonnage</th>
                            <th> Sales</th>
                            <th> Travel Amount</th>
                            <th> Earned Amount</th>
                            <th> Edited Amount</th>
                            <th>
                                <input type="checkbox" name="approve_all" id="salesman-approval-all-checkbox" checked>
                            </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($salesmanAllocations as $record)
                            <tr>
                                <th style="width: 3%;" scope="row"> {{ $loop->index + 1 }} </th>
                                <td> {{ \Carbon\Carbon::parse($record->created_at)->format('d-m-Y') }}
                                </td>
                                <td> {{ $record->route }} </td>
                                <td> {{ $record->salesman }} </td>
                                <td><a href="{{ route('salesman-shift-details', $record->shift_id) }}" target="_blank"> {{ $record->delivery_number }} </a>
                                </td>
                                <td> {{ $record->total_customers }} </td>
                                <td> {{ $record->met_customers }} </td>
                                <td> {{ $record->total_customers - $record->met_customers }} </td>
                                <td> {{ number_format($record->tonnage, 2) }} </td>
                                <td style="text-align: right;"> {{ manageAmountFormat($record->gross_sales - $record->returns) }}</td>
                                <td style="text-align: right;"> 
                                    @if (request()->type == 'delivery')
                                    {{ manageAmountFormat($record->driver_allocation) }}
                                    @elseif (request()->type == 'order_taking')
                                    {{ manageAmountFormat($record->travel_expense) }}
                                    @else
                                    0
                                    @endif
                                 </td>
                                <td style="text-align: right;">
                                    {{ manageAmountFormat($record->old_amount) }}
                                    {{--                                    <input type="text" class="form-control" style="width: 100px;" value="{{ $record->amount }}" name="amount_{{ $record->id }}">--}}
                                </td>
                                <td style="text-align: right;">
                                    {{ manageAmountFormat($record->amount) }}
                                    {{--                                    <input type="text" class="form-control" style="width: 100px;" value="{{ $record->amount }}" name="amount_{{ $record->id }}">--}}
                                </td>
                                <td>
                                    <input type="checkbox" name="approve_{{ $record->id }}" class="salesman-approval-checkbox" checked>
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
                            <td> {{ $salesmanAllocations->sum('total_customers') }} </td>
                            <td> {{ $salesmanAllocations->sum('met_customers') }} </td>
                            <td> {{ $salesmanAllocations->sum('total_customers') - $salesmanAllocations->sum('met_customers') }}
                            </td>
                            <td> {{ number_format( $salesmanAllocations->sum('tonnage'), 2) }} </td>
                            <td style="text-align: right;"> {{ manageAmountFormat($salesmanAllocations->sum('gross_sales') - $salesmanAllocations->sum('returns')) }}</td>
                            <td style="text-align: right;"> {{ manageAmountFormat($salesmanAllocations->sum('travel_expense')) }}</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th colspan="12" style="text-align: right;"></th>
                            <th style="text-align: right;"> {{ manageAmountFormat($salesmanAllocations->sum('amount')) }} </th>
                            <td></td>
                        </tr>

                        @if(count($salesmanAllocations) > 0)
                            <tr>
                                <th style="text-align: right;" colspan="14">
                                    <input type="submit" name="submit" value="Reject" class="btn btn-secondary" style="margin-right: 10px">
                                    <input type="submit" name="submit" value="Confirm Approval" class="btn btn-primary">
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
                        <th>User</th>
                        <th>Phone Number</th>
                        <th>Customer Count</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($unVisitedRoutes as $route)
                        <tr>
                            <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                            <td> {{ \Carbon\Carbon::parse($route->created_at)->format('d-m-Y') }} </td>
                            <td> {{ $route->route_name }} </td>
                            <td> {{ $route->user }} </td>
                            <td> {{ $route->phone_number }} </td>
                            <td> {{ $route->total_customers }} </td>
                        </tr>
                    @endforeach
                    </tbody>
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
            $("body").addClass('sidebar-collapse');
            $(".mlselect").select2();

            $("#salesman-approval-all-checkbox").change(function () {
                if ($(this).prop('checked')) {
                    $(".salesman-approval-checkbox").attr('checked', true);
                } else {
                    $(".salesman-approval-checkbox").attr('checked', false);
                }
            });
        });
    </script>
@endsection
