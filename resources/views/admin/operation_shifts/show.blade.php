@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">End Of Day Operation Shifts </h3>
                    <a href="{{ route('operation_shifts.index') }}" class="btn btn-primary"> Back </a>

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
                            <th>Date</th>
                            <th>Restaurant ID</th>
                            <th>Balanced</th>
                            <th>Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>{{ $operationalShift->date }}</td>
                            <td>{{ $operationalShift->branch->name }}</td>
                            <td>{{ $operationalShift->balanced ? 'Yes' : 'No' }}</td>
                            <td>{{ $operationalShift->status ? 'Open' : 'Closed' }}</td>
                        </tr>
                        </tbody>
                    </table>

                    <h2>Shift Checks</h2>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>Check Name</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($operationalShift->shiftChecks as $check)

                            <tr class="clickable {{ $check->status ? '' : 'bg-danger' }}" data-toggle="collapse" data-target="#check{{ $check->id }}">
                            <td>{{ ucfirst(str_replace('_', ' ', $check->check_name))  }}</td>
                                <td>{{ $check->status ? 'Passed' : 'Failed' }}</td>
                                <td>
                                    <button class="btn btn-outline-light btn-sm" type="button" data-toggle="collapse" data-target="#details{{ $check->id }}" aria-expanded="false" aria-controls="details{{ $check->id }}">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr class="collapse" id="details{{ $check->id }}">
                                <td colspan="3">
                                    <table class="table table-sm">
                                        <thead>
                                        <tr>
                                            <th>Detail </th>
                                            <th>Value</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($check->checkDetails as $detail)
                                            <tr>
                                                <td>{{ ucfirst(str_replace('_', ' ', $detail->detail_name)) }}</td>
                                                <td>
                                                    @if($detail->detail_name == 'status')
                                                        {{ $detail->detail_value ? 'Passed' : 'Failed' }}
                                                    @else
                                                        {{ $detail->detail_value }}
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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