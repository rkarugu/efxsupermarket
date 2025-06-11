@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">  {{ $issue->responseCustomer->contact_person }}
                        Customer Reported Issues </h3>

                    <a class="btn btn-primary" href="{{ route("salesman-shift.reported-issue", $shift->id) }}"
                    > Return Back
                    </a>
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                <div>
                    <div class="card-header">

                    </div>

                    <br>
                </div>
                <div class="box-body">
                    @include('message')
                    <div class="col-md-12 no-padding-h">
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                            <tr>

                                <th>Issue Category</th>
                                <th>Response Provided</th>
                                <th>Date Created</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($reports as $report)
                                <tr>
                                    <td>{{ $report['name'] }}</td>
                                    <td>
                                         @if($report['expected'] == "picture")
                                            <a href="#"><i class="fa fa-image"></i> </a>
                                        @else
                                            {{ $report['response'] }}
                                        @endif

                                    </td>
                                    <td>{{ $report['time']->format('M. d Y, h:i A') }}</td>
                                    <td>
                                        <div class="action-button-div">
                                            <a href="#"  title="Resolve ">
                                                <i class='fa fa-edit text-success fa-lg'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </section>
    <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
         aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-full">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="staticBackdropLabel"> Shift Reported Issues</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <div class="modal-body">
                    <div class="col-md-12 no-padding-h table-responsive">
                        <table class="table table-bordered table-hover" id="create_datatable1">
                            <thead>
                            <tr>

                                <th>Customer</th>
                                <th>Reported Issues</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>

                </div>

            </div>
        </div>
    </div>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function () {

            $(".mlselect").select2();
        });
    </script>

    <script>
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>


    <script type="text/javascript" class="init">
        $(document).ready(function () {
            $('#create_datatable1').DataTable({
                pageLength: "100",
                "order": [
                    [0, "desc"]
                ]
            });
        });
        $('.btn-summary').click(function () {
            var shiftId = $(this).data('id');
            var checkurl = '/admin/salesman-shift/reported-issues/' + shiftId;
            console.log(checkurl);

            $.ajax({
                url: '/admin/salesman-shift/reported-issues/' + shiftId,
                method: 'GET',
                success: function (data) {
                    console.log(data);
                    var tableBody = $('#create_datatable1 tbody');
                    tableBody.empty();
                    // $.each(data, function(index, item) {
                    //     var row = $('<tr>');
                    //     row.append('<td>' + item.get_inventory_item_detail.title + '</td>');
                    //     row.append('<td>' + item.quantity + '</td>');
                    //     row.append('<td>' + item.selling_price + '</td>');
                    //     row.append('<td>' + item.total_cost + '</td>');
                    //     row.append('<td>' + item.delivered + '</td>');
                    //     row.append('<td>' + item.is_returned + '</td>');
                    //     tableBody.append(row);
                    // });

                    // Open the modal
                    $('#create_datatable1').DataTable();
                },
                error: function (xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });

        });
    </script>
@endsection
