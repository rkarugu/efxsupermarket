@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Field Visit Schedule </h3>
                    <div>
                        <a href="#" class="btn btn-primary"> Download Report </a>
                        <a href="{{ route("$base_route.create") }}" class="btn btn-primary" style="margin-left: 12px;"> Create Schedule </a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th colspan="10" style="text-align: center; font-size: 22px;">
                                KANINI HARAKA ONBOARDING EXERCISE
                            </th>
                        </tr>
                        <tr>
                            <th>Route</th>
                            <th>Sales Rep</th>
                            <th>HQ Rep</th>
                            <th>System Rep</th>
                            <th>Initial Customers</th>
                            <th>Visited</th>
                            <th>Not Visited</th>
                            <th>New</th>
                            <th>Total</th>
                            <th></th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($list as $day)
                            <tr>
                                <th scope="row" colspan="10" style="font-size: 18px;">{{ $day['date'] }}</th>
                            </tr>
                            @foreach($day['routes'] as $route)
                                <tr>
                                    <td> {{ $route['route_name'] }} </td>
                                    <td> {{ $route['salesman'] }} </td>
                                    <td> {{ $route['hq_rep'] }} </td>
                                    <td> {{ $route['bw_rep'] ?? '-' }} </td>
                                    <td> {{ $route['initial_customers'] }} </td>
                                    <td> {{ $route['visited'] }} </td>
                                    <td> {{ $route['not_visited'] }} </td>
                                    <td> {{ $route['new'] }} </td>
                                    <td> {{ $route['initial_customers'] + $route['new'] }} </td>
                                    <td>
                                        <div class="action-button-div">
                                            <a href="#" title="Detailed Report"><i class="fas fa-eye fa-lg text-primary"></i></a>
                                            <a href="#" title="Comments"><i class="fas fa-comment fa-lg text-primary"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
