@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Route Targets Summary</h3>
                    <form action="">
                        <input type="submit" class="btn btn-primary" value="Download" name="Download">


                    </form>

                    {{-- <a href="{{ route("$base_route.create") }}" role="button" class="btn btn-primary"> Add Route </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="create_datatable_25">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th scope="col"> NAME</th>
                            <th scope="col"> SALES TARGET</th>
                            <th scope="col"> TONNAGE</th>
                            <th scope="col"> CTNS</th>
                            <th scope="col"> DZNS</th>
                            <th scope="col"> FUEL EST.</th>
                            <th scope="col"> TRAVEL EXPENSE</th>
                            <th scope="col"> CURRENT SALESMAN</th>
                        </tr>
                        </thead>

                        <tbody>
                            @foreach ($routes as $route)
                            <tr>
                                <th>{{$loop->index+1}}</th>
                                <td>{{ $route['route_name'] }}</td>
                                <td style="text-align: right;">{{ $route['sales_target']}}</td>
                                <td>{{ $route['tonnage_target'] }}</td>
                                <td>{{ $route['ctns_target'] }}</td>
                                <td>{{ $route['dzns_target'] }}</td>
                                <td>{{ $route['fuel_est'] }}</td>
                                <td style="text-align: right;">{{ $route['travel_expense'] }}</td>
                                <td>{{ $route['salesman'] }}</td>






                            </tr>
                                
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script type="text/javascript">
        function confirmRouteDeletion() {
            let userHasConfirmed = confirm(`Are you sure you want to remove this route?`);
            if (userHasConfirmed) {
                $("#delete-route-form").submit();
            }
        }
    </script>

    
@endsection