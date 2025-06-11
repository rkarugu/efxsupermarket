@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Daily Sales Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>
            
            <div class="box-body">
                <form action="" method="get" id="filter-form">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="date"> Filter by date </label>
                            <input type="date" name="date" id="date" class="form-control"
                                value="{{ request()->date ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="branch"> Filter by branch </label>
                            <select name="branch" id="branch" class="form-control">
                                <option value="" @if (!request()->branch) selected @endif disabled>Select
                                    branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if (request()->branch == $branch->id) selected @endif>
                                        {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="route"> Filter by route </label>
                            <select name="route" id="route" class="form-control">
                                <option value="" @if (!request()->route) selected @endif disabled>Select
                                    route</option>
                                @foreach ($routes as $route)
                                    <option value="{{ $route->id }}" @if (request()->route == $route->id) selected @endif>
                                        {{ $route->route_name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label style="display: block;"> &nbsp; </label>
                            <button class="btn btn-primary" id="filter-btn" value="filter"> Filter </button>
                            <button class="btn btn-primary" id="download-btn" value="download"> Download </button>
                            <a href="{{ route('route-reports.daily-sales') }}" class="btn btn-primary" id="reset-btn"> Reset
                            </a>
                        </div>
                    </div>
                </form>

                <div style="margin-top: 20px;" class="table-responsive">
                    <table class="table" id="create_datatable_50">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th>ROUTE</th>
                                <th>SALESMAN</th>
                                <th>TON</th>
                                <th>TARG</th>
                                <th>PER%</th>
                                <th>AMOUNT</th>
                                <th>TARG</th>
                                <th>PER%</th>
                                <th>CTN</th>
                                <th>TARG</th>
                                <th>PER%</th>
                                <th>DZN</th>
                                <th>TARG</th>
                                <th>PER%</th>
                                {{-- <th>FREQ</th> --}}
                                <th>CUST</th>
                                <th>MET</th>
                                <th>UNMET</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($data as $index => $entry)
                                <tr>
                                    <th scope="row" style="width: 3%;"> {{ $index + 1 }} </th>
                                    <td> {{ $entry['route'] }} </td>
                                    <td> {{ $entry['salesman'] ?? '-' }} </td>
                                    <td> {{ $entry['tonnage']['value'] }} </td>
                                    <td> {{ $entry['tonnage']['target'] }} </td>
                                    <td> {{ $entry['tonnage']['percentage'] }} </td>
                                    <td> {{ $entry['sales']['value'] }} </td>
                                    <td> {{ $entry['sales']['target'] }} </td>
                                    <td> {{ $entry['sales']['percentage'] }} </td>
                                    <td> {{ $entry['ctns']['value'] }} </td>
                                    <td> {{ $entry['ctns']['target'] }} </td>
                                    <td> {{ $entry['ctns']['percentage'] }} </td>
                                    <td> {{ $entry['dzns']['value'] }} </td>
                                    <td> {{ $entry['dzns']['target'] }} </td>
                                    <td> {{ $entry['dzns']['percentage'] }} </td>
                                    {{-- <td> {{ $entry['freq'] }} </td> --}}
                                    <td>{{ $entry['custs']['total'] }}</td>
                                    <td>{{ $entry['custs']['met'] }}</td>
                                    <td>{{ $entry['custs']['unmet'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#branch").select2();
            $("#route").select2();
        });

        function resetFilters(e) {
            e.preventDefault();

            $("#date").val();
            $("#branch").val();
            $("#route").val();

            //$("#filter-form").submit();
            //window.location.assign('/admin/routes/reports/daily-sales-report');
        }


        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filter-form');
            const filterButton = form.querySelector('button[value="filter"]');
            const downloadButton = form.querySelector('button[value="download"]');

            filterButton.addEventListener('click', function() {
                form.action = "{{ route('route-reports.daily-sales') }}";
            });

            downloadButton.addEventListener('click', function() {
                form.action = "{{ route('route-reports.daily-sales.download') }}";
            });
        });
    </script>
@endsection
