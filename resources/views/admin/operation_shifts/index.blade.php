@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">End of Day Operation Shifts </h3>
                    <a href="#" class="btn btn-primary"> Back </a>

                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>


                <form action="" method="get">

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">From</label>
                                <input type="date" name="date" id="start_date" class="form-control" value="{{request()->input('date') ?? date('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <input type="submit" name="intent" value="FILTER" class="btn btn-primary" />
                            <a href="{{ route('operation_shifts.index') }}"
                               class="btn btn-primary ml-12"> Clear </a>
                        </div>
                    </div>

                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered" id="create_datatable">
                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Restaurant ID</th>
                            <th>Balanced</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($operationalShifts as $shift)
                            <tr data-toggle="collapse" data-target="#shift{{ $shift->id }}" class="clickable">
                                <td>{{ $shift->date }}</td>
                                <td>{{ $shift->branch -> name }}</td>
                                <td>{{ $shift->balanced ? 'Yes' : 'No' }}</td>
                                <td>
                                    <a href="{{route('operation_shifts.show', $shift->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="Show Details"><i class="fa fa-eye"></i></a>
                                    <form action="{{ route('operation_shifts.override', $shift->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" data-toggle="tooltip" data-placement="top" title="Manual Override"> <i class="fa fa-step-forward"></i></button>
                                    </form>
                                    <form action="{{ route('operation_shifts.rerun', $shift->id) }}" method="POST" class="d-inline" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-warning btn-sm" title="Re-run Shift" data-toggle="tooltip" data-placement="top" ><i class="fa fa-refresh"></i></button>
                                    </form>
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
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>


    <script type="text/javascript">
        $(function() {
            $('body').addClass('sidebar-collapse');
            $("#route").select2();
            $("#filter").select2();
            $(".new_filters").select2();
            $("#frequency_filter").select2();
            $("#group").select2();
            $("#frequency_filter").val("1").trigger("change");
            $("#filter").val("sales").trigger("change");
        });

        $(document).ready(function() {
            $('#create_datatable').DataTable().destroy();
            $('#create_datatable').DataTable({
                "paging": true,
                "pageLength": 100,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": false,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });
    </script>

    <script type="text/javascript">

    </script>
@endsection