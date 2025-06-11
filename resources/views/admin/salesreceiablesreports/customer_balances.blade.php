@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Customer Balance Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="">
                    <div class="row">
                        <div class="form-group col-md-3">
                            <label for="route" class="control-label">Select branch</label>
                            <select name="branch_id" id="branch_id" class="form-control" required>
                                @foreach ($branches as $branch)
                                    @php
                                        $branchIsSelected = false;
                                        if (isset(request()->branch_id)) {
                                            if (request()->branch_id == $branch->id) {
                                                $branchIsSelected = true;
                                            }
                                        } else {
                                            if ($branch->id == $user->restaurant_id) {
                                                $branchIsSelected = true;
                                            }
                                        }
                                    @endphp
                                    <option value="{{ $branch->id }}" @if ($branchIsSelected) selected @endif>
                                        {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="date" class="control-label">Pick a date</label>
                            <input type="date" name="date" id="date" class="form-control" required
                                value="{{ request()->date }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label for="date" class="control-label"
                                style="color: white !important; display: block;">Actions</label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary">
                            <input type="submit" name="intent" value="PDF" class="btn btn-primary ml-12">
                        </div>
                    </div>
                </form>

                <hr>

                @if (request()->branch_id && request()->date)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Customer</th>
                                    <th>Balance B/f</th>
                                    <th>Debits</th>
                                    <th>Credits</th>
                                    <th>Lastrans</th>
                                    <th>Pd Chqs</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $balanceBfTotal = 0;
                                    $debitsTotal = 0;
                                    $creditsTotal = 0;
                                    $pdChqsTotal = 0;
                                    $balanceTotal = 0;
                                @endphp

                                @foreach ($records as $index => $record)
                                    <tr>
                                        <th scope="row" style="width: 3%;">{{ $loop->index + 1 }}</th>
                                        <td> {{ $record['customer'] }} </td>
                                        <td> {{ number_format($record['balance_bf'], 2) }} </td>
                                        <td> {{ number_format($record['debits'], 2) }} </td>
                                        <td> {{ number_format($record['credits'], 2) }} </td>
                                        <td>{{ $record['last_trans_time'] }}</td>
                                        <td> {{ number_format($record['pd_cheques'], 2) }} </td>
                                        <td> {{ number_format($record['balance'], 2) }} </td>
                                    </tr>

                                    @php
                                        $balanceBfTotal += $record['balance_bf'];
                                        $debitsTotal += $record['debits'];
                                        $creditsTotal += $record['credits'];
                                        $pdChqsTotal += $record['pd_cheques'];
                                        $balanceTotal += $record['balance'];
                                    @endphp
                                @endforeach
                                <tr>
                                    <td style="text-align: center;" colspan="2"><strong>TOTALS</strong></td>
                                    <td><strong>{{ number_format($balanceBfTotal, 2) }}</strong></td>
                                    <td><strong>{{ number_format($debitsTotal, 2) }}</strong></td>
                                    <td><strong>{{ number_format($creditsTotal, 2) }}</strong></td>
                                    <td></td>
                                    <td><strong>{{ number_format($pdChqsTotal, 2) }}</strong></td>
                                    <td><strong>{{ number_format($balanceTotal, 2) }}</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p> Select a branch and a date to continue. </p>
                @endif
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
            $("#branch_id").select2();
        });
    </script>
@endsection
