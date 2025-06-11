@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Fuel Statements </h3>

                    <a href="{{ route('fuel-statements.show-upload-page') }}" class="btn btn-primary">
                        <i class="fas fa-file-arrow-up btn-icon"></i> Upload
                    </a>
                </div>
            </div>

            <div class="box-body">
                <form action="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group col-md-3">
                                <label for="branch_id" class="control-label"> Branch </label>
                                <select id="branch_id" class="form-control mlselect" required name="branch_id">
                                    <option value="0" selected>All</option>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="fueling_date" class="control-label"> Date </label>
                                <input type="date" name="fueling_date" class="form-control" value="{{ request()->fueling_date }}">
                            </div>

                            <div class="form-group col-md-3">
                                <label for="status" class="control-label"> Status </label>
                                <select id="status" name="status" class="form-control mlselect" required>
                                    <option value="0" @if(request()->status == '0') selected @endif >All</option>
                                    @foreach(['Matched', 'Open'] as $status)
                                        <option value="{{ $status }}" @if(request()->status == $status) selected @endif> {{ $status }} </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-2">
                                <label class="control-label">&nbsp; </label>
                                <div class="d-flex">
                                    <button class="btn btn-primary" type="submit"><i class="fas fa-search btn-icon"></i> Search</button>
                                    <a class="btn btn-primary ml-12" href="{{ route('fuel-statements.listing') }}"><i class="fas fa-xmark btn-icon"></i> Clear Filters</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <table class="table table-hover table-bordered table-striped" id="create_datatable">
                    <thead>
                    <tr>
                        <th style="width: 3%;">#</th>
                        <th>Date</th>
                        <th>Receipt #</th>
                        <th>Fueled Quantity</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Matched LPO</th>
                        <th>LPO Date</th>
                        <th style="text-align: right;">Fuel Total</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($statements as $statement)
                        <tr>
                            <th style="width: 3%;">{{$loop->index + 1 }}</th>
                            <td>{{$statement->timestamp }}</td>
                            <td>{{$statement->receipt_number }}</td>
                            <td>{{$statement->quantity }}</td>
                            <td>{{$statement->narrative }}</td>
                            <td>{{$statement->status }}</td>
                            <td>{{$statement->lpo_number }}</td>
                            <td>{{$statement->fueling_time }}</td>
                            <td style="text-align: right;">{{ manageAmountFormat($statement->fuel_total) }}</td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tfoot>
                    <tr>
                        <th colspan="6"> STATEMENTS TOTAL</th>
                        <th style="text-align: right;">{{ manageAmountFormat($statements->sum('fuel_total')) }}</th>
                    </tr>
                    </tfoot>
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
            $("#branch_id").select2();
            $("#status").select2();
        });
    </script>
@endsection