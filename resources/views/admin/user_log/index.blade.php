@extends('layouts.admin.admin')

@section('content')

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">

                <form action="" method="GET">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from"> From Date</label>
                            <input type="datetime-local" name="date" id="from" class="form-control" placeholder="From">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="from"> To Date</label>
                            <input type="datetime-local" name="todate" id="to" class="form-control" placeholder="to">
                        </div>
                    </div>
                    <br>
                    <div class="col-md-2">
                        <button type="button" id="filterMe" class="btn-inline btn btn-danger  mt-4 float-right">Apply Filter</button>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" name="manage" value="pdf" class="btn-inline btn btn-danger  mt-4 float-right">Pdf Logs</button>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" name="manage" value="excel" class="btn-inline btn btn-danger  mt-4 float-right">Excel</button>
                    </div>

                </form>


                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="dataTable">
                        <thead>
                        <tr>
                            <th width="4%">S.No.</th>

                            <th width="8%">user_name</th>
                            <th width="10%">user_ip</th>
                            <th width="15%">User agent</th>
                            <th width="12%">created_at</th>

                        </tr>
                        </thead>
                        <tbody>
                        {{--  @if(isset($lists) && !empty($lists))
                             @php $b = 1;@endphp
                             @foreach($lists as $list)

                                 <tr>
                                     <td>{!! $b !!}</td>

                                    <td>{!! ucfirst($list->user_name) !!}</td>
                                    <td>{!! $list->user_ip  !!}</td>
                                    <td>{!! $list->user_agent!!}</td>
                                   <td>{!! $list->created_at  !!}</td>
                                 </tr>
                               @php $b++; @endphp
                             @endforeach
                         @endif --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
    <style>
        .modal-header .close {
            margin-top: -24px !important;
        }

        #dataTable tr td, #dataTable tr th {
            text-align: left !important;
        }
    </style>

    </style>
@endsection
@section('uniquepagescript')

    <script type="text/javascript">
        $(function () {
            var table = $('#dataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [[0, "desc"]],
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                "ajax": {
                    "url": '{!! route('userlog.index') !!}',
                    "dataType": "json",
                    "type": "GET",
                    data: function (data) {
                        var date = $('#from').val();
                        var todate = $('#to').val();

                        data.date = date;
                        data.todate = todate;

                    },
                },
                columns: [
                    {
                        data: 'id', name: 'id', orderable: true, render: function (data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {data: 'user_name', name: 'user_name', orderable: true},
                    {data: 'user_ip', name: 'user_ip', orderable: true},
                    {data: 'user_agent', name: 'user_agent', orderable: false},
                    {data: 'created_at', name: 'created_at', orderable: true},

                ],
                "columnDefs": [
                    {"searchable": false, "targets": 0},
                    {className: 'text-center', targets: [1]},
                ]
                , language: {
                    searchPlaceholder: "Search"
                },
            });

            $('#filterMe').click(function (e) {
                e.preventDefault();
                table.draw();

            });
        });


    </script>

@endsection
