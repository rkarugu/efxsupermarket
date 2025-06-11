@extends('layouts.admin.admin')


@section('content')
    <div id="app">

        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> End of Day Routine </h3>
                    </div>
                </div>
                <div class="box-body">
                    <div class="row">
                        <form action="{{ route('eod-routine.index') }}" method="get">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input name="date" type="date" value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                        class="form-control" id="date" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <input name="to_date" type="date" value="{{ \Carbon\Carbon::now()->toDateString() }}"
                                        class="form-control" id="to_date" max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <select name="select_branch" id="select_branch" class="form-control" required>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach

                                    </select>
                                </div>
                            </div>

                            <button class="btn btn-success" type="submit">
                                <i class="fas fa-filter"></i> Filter
                            </button>
                        </form>

                    </div>
                    @include('message')
                    <div class="col-md-12 table-responsive">
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>Day</th>
                                <th>Status</th>
                                <th>Returns</th>
                                <th>Splits</th>
                                <th>Bins</th>
                                <th>Sales Vs Stocks</th>
                                <th>Cash At Hand</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($eodRoutines as $routine)
                                <tr>
                                    <th>{{$loop->index+1}}</th>
                                    <td>{{$routine->day}}</td>
                                    <td>@if ($routine->status == 'Closed')
                                        <span  class="label label-success">
                                            {{$routine->status}}
                                        </span>
                                        
                                    @else
                                    <span  class="label label-danger">
                                        {{$routine->status}}
                                    </span>
                                        
                                    @endif</td>
                                    <td>{{$routine->returns_passed == 0 ? 'Pending' : 'Passed'}}</td>
                                    <td>{{$routine->splits_passed == 0 ? 'Pending' : 'Passed'}}</td>
                                    <td>{{$routine->binless_items_passed == 0 ? 'Pending' : 'Passed'}}</td>
                                    <td>{{$routine->unbalanced_transactions_passed == 0 ? 'Pending' : 'Passed'}}</td>
                                    <td>{{$routine->pos_cash_at_hand_passed == 0 ? 'Pending': 'Passed'}}</td>
                                    <td><a href="{{route('eod-routine.run-routine', ["date"=>$routine->day, "select_branch"=>$routine->branch_id])}}" target="_blank"><i class="fas fa-stopwatch-20" style="font-size: 25px;" title="Run EOD Routine"></i></a></td>
                                </tr>
                                    
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </section>
    </div>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <div id="loader-on"
        style="
            position: fixed;
            top: 0;
            text-align: center;
            display: block;
            z-index: 999999;
            width: 100%;
            height: 100%;
            background: #000000b8;
            display:none;
            "
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        a.btn.btn-default.btn-circle.step-button.active {
            background: #ff0000 !important;
            border: none;
            color: white;
            font-weight: bolder;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('#select_branch').select2();
        });
    </script>
@endsection
