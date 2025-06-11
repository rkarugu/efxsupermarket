@extends('layouts.admin.admin')
@section('content')
    <style>
        .mr-12 {
            margin-right: 12px;
        }

        .has-pointer-cursor {
            cursor: pointer;
        }

        .d-flex {
            display: flex !important;
        }

        .justify-content-end {
            justify-content: end !important;
        }
    </style>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title"> Production Processes </h3>
                    <a href="{{ route('processes.create') }}" class="btn btn-primary"> Add Process </a>
                </div>
            </div>

            <div class="box-body">
                @include('message')

                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="processes">
                        <thead>
                        <tr>
                            <th> Operation</th>
                            <th> Description</th>
                            <th> Notes</th>
                            <th> Status</th>
                            <th class="noneedtoshort">Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#processes').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[0, "desc"]],
                "pageLength": '<?= Config::get('params.list_limit_admin') ?>',
                "ajax": {
                    "url": '{!! route('processes.datatable') !!}',
                    "dataType": "json",
                    "type": "GET",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {data: 'operation', name: 'operation'},
                    {data: 'description', name: 'description', orderable: false},
                    {data: 'notes', name: 'notes', orderable: false},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'actions', name: 'actions', orderable: false}
                ]
            });
        });
    </script>
@endsection