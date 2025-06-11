@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Salesman Reporting Scenarios </h3>

                    <button class="btn btn-primary" data-toggle="modal" data-target="#assign-vehicle-modal"
                            data-backdrop="static"> Add Reporting Scenario
                    </button>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered " id="create_datatable">
                        <thead>
                        <tr>
                            <td>#</td>
                            <th>Name</th>
                            <th>Reason Options</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($reports as $key=>$report)
                            <tr>
                                <td>{{++$key}}</td>
                                <td>{{ $report->name }}</td>
                                <td>@foreach($report->salesReportingReasons as $option)
                                        {{$option->reason_option}},
                                    @endforeach
                                </td>
                                <td>
                                    <div class="action-button-div">

                                        <button type="button" class="mr-1 btn-edit" title="Edit"
                                                data-toggle="modal" data-target="#edit-reporting-modal"
                                                data-id="{{ $report->id }}"><i
                                                    class='fa fa-edit text-primary fa-lg'></i></button>

                                        <button type="button" class="text-primary mr-1 btn-delete" title="Delete"
                                                data-toggle="modal" data-target="#delete-reporting-modal"
                                                data-id="{{ $report->id }}"><i
                                                    class='fa fa-times-rectangle text-danger fa-lg'></i></button>
                                        <a href="{{ route('salesman-reporting.manage-reporting-scenarios',$report->id) }}" title="Manage Reporting Options" class="btn"><i class='fa fa-list-alt text-danger fa-lg'></i></a>

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div class="modal fade" id="assign-vehicle-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form method="POST" action="{{ route('salesman-reporting.add-reporting-scenarios') }}"
                      style="border-top: 2px solid red;">
                    @csrf
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="box-title"> Add New Reporting Scenario</h3>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <div class="form-group">
                                <label class="control-label"> Reporting Scenario </label>
                                <input type="text" name="reporting_scenario" value="{{ old('reporting_scenario') }}"
                                       class="form-control">
                            </div>
                        </div>

                        <div class="box-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
        <div class="modal fade" id="edit-reporting-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form method="POST" action="{{ route('salesman-reporting.update-reporting-scenarios') }}"
                      style="border-top: 2px solid red;">
                    @csrf
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="box-title"> Update Reporting Scenario</h3>

                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="box-body edit_reasons_box_body">
                            <div class="form-group">
                                <label class="control-label"> Reporting Scenarios </label>
                                <input type="text" name="name" id="name" class="form-control">
                                <input type="hidden" name="reporting_scenario_id" id="reporting_scenario_id"/>
                            </div>
                        </div>



                        <div class="box-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
        <div class="modal fade" id="delete-reporting-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <form method="POST" action="{{ route('salesman-reporting.delete-reporting-scenarios') }}"
                      style="border-top: 2px solid red;">
                    @csrf
                    <div class="box">
                        <div class="box-header with-border">
                            <div class="d-flex justify-content-between align-items-center">
                                <h3 class="box-title"> Delete Reporting Scenario?</h3>
                                <br/>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="box-body">
                            <p>All linked options will also be deleted. Are you sure to delete? </p>

                            <input type="hidden" name="delete_reporting_scenario_id" id="delete_reporting_scenario_id"/>

                        </div>


                        <div class="box-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>

                                <button type="submit" class="btn btn-primary">Yes, Delete</button>
                            </div>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </section>
@endsection
@section('uniquepagescript')

    <script type="text/javascript">

        $(document).ready(function () {

            $('.btn-edit').click(function () {
                var scenarioId = $(this).data('id');
                $.ajax({
                    url: '/admin/salesman-reporting/reporting-scenarios/' + scenarioId,
                    method: 'GET',
                    success: function (data) {
                        $('#name').val(data.name);
                        $('#reporting_scenario_id').val(data.id);

                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
            $('.btn-delete').click(function () {
                var scenarioId = $(this).data('id');
                $.ajax({
                    url: '/admin/salesman-reporting/reporting-scenarios/' + scenarioId,
                    method: 'GET',
                    success: function (data) {
                        console.log(data);
                        $('#delete_reporting_scenario_id').val(data.id);

                    },
                    error: function (xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });

    </script>
@endsection