@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Abnormal Returns </h3>
                </div>
            </div>

            <div class="box-body">
                <form action="" method="get">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="start_date">Start</label>
                            <input type="datetime-local" name="start" id="start" value="{{ request()->start ?? \Carbon\Carbon::now()->startOfDay() }}" class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label for="start_date">End</label>
                            <input type="datetime-local" name="end" id="end" value="{{ request()->end ?? \Carbon\Carbon::now() }}" class="form-control">
                        </div>

                        <div class="form-group col-md-3">
                            <label style="display: block;">&nbsp;</label>
                            <input type="submit" name="intent" value="Filter" class="btn btn-primary">
                            <input type="submit" name="intent" value="Excel" class="btn btn-primary ml-12">
                            <a href="{{ route('sales-invoice.returns.abnormal') }}" class="btn btn-primary ml-12">Clear</a>
                        </div>
                    </div>
                </form>

                <hr>
                
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Return Number</th>
                            <th>Route</th>
                            <th>Salesman</th>
                            <th>Return Time</th>
                            <th>Processed Time</th>
                            <th>Processed By</th>
                            <th>Item Count</th>
                            <th>Total Returns</th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach($returns as $return)
                            <tr>
                                <th style="width: 3%;">{{ $loop->index + 1 }}</th>
                                <td>{{ $return->return_number }}</td>
                                <td>{{ $return->route }}</td>
                                <td>{{ $return->salesman }}</td>
                                <td>{{ $return->return_time }}</td>
                                <td>{{ $return->receive_time }}</td>
                                <td>{{ $return->receiver }}</td>
                                <td>{{ $return->count }}</td>
                                <td>{{ manageAmountFormat($return->total_returns) }}</td>
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
    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');
        });
    </script>
@endsection
