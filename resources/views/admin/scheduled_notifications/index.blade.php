@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            @include('message')
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h4 class="box-title">Scheduled Notifications</h4>
                    @if (can('add', 'scheduled-notifications'))
                        <button class="btn btn-primary" data-toggle="modal" data-target="#createNotificationScheduleModal">
                            <i class="fa fa-plus"></i> Create Notification
                        </button>
                    @endif
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered" id="scheduledNotificationsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Frequency</th>
                            <th>Time</th>
                            <th>Roles</th>
                            <th>Users</th>
                            <th>Emails</th>
                            <th>Phone Numbers</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    @include('admin.scheduled_notifications.create')
    @include('admin.scheduled_notifications.edit')
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}" rel="stylesheet" />
    <style>
        .bootstrap-tagsinput {
            width: 100%;
            font-size: 18px;
            padding: 7px 6px;
            border-radius: 0;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('[name="notification"], [name="frequency"]').select2();
            $('.multiselect').select2({
                placeholder: "Select Option"
            });

            $('[name="emails"]').on('beforeItemAdd', function(event) {
                const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
                event.cancel = !emailRegex.test(event.item);
            });

            $("#scheduledNotificationsTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('scheduled-notifications.index') !!}',
                    data: function(data) {
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                    },{
                        data: 'class_name',
                        name: 'class_name',
                    },
                    {
                        data: 'frequency',
                        name: 'frequency',
                    },
                    {
                        data: 'time',
                        name: 'time',
                    },
                    {
                        data: 'roles',
                        name: 'roles',
                    },
                    {
                        data: 'users',
                        name: 'users',
                    },
                    {
                        data: 'emails',
                        name: 'emails',
                    },
                    {
                        data: 'phone_numbers',
                        name: 'phone_numbers',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        className: "text-center"
                    },
                ]
            });
        })
    </script>
@endpush
