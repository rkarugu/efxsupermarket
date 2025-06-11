@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Users Access Requests</h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                @if (session('request_not_found'))
                    <div class="alert alert-danger" style="margin:10px;">
                        {{ session('request_not_found') }}
                    </div>
                @endif
                @if (session('request_deleted_successfully'))
                    <div class="alert alert-success" style="margin:10px;">
                        {{ session('request_deleted_successfully') }}
                    </div>
                @endif
                <div class="table-responsive">
                    <table class="table" id="users-denied-access-table">
                        <thead>
                            <tr>
                                <th scope="col"> User </th>
                                <th scope="col"> Phone Number</th>
                                <th scope="col"> Email Address</th>
                                <th scope="col">Role</th>
                                <th scope="col"> Reason </th> 
                                <th scope="col"> Submitted At</th>
                                <th scope="col"> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key => $user)
                                <tr>
                                    <td>{{ $user->user->name }}</td>
                                    <td>{{ $user->user->phone_number }}</td>
                                    <td>{{ $user->user->email }}</td>
                                    <td>{{$user->user?->userRole?->title}}</td>

                                    <td>{{ $user->reason }}</td> 
                                    <td>{{ $user->created_at->format('d/m/y h:i: A') }}</td>
                                    <td>
                                        <div class="action-button-div">
                                        <button type="button" class="text-primary  btn-approve" style="margin-right: 10px;" data-toggle="modal" title="Approve Request"
                                            data-target="#staticBackdrop" data-id="{{ $user->id }}">
                                            <i class="fa fa-check-square text-success fa-lg"></i>
                                            </button> 
                                        <button type="button" class="text-primary mr-2 btn-decline" data-toggle="modal" title="Decline Request"
                                            data-target="#staticDeleteBackdrop" data-id="{{ $user->id }}"> <i class="fa fa-times-rectangle text-danger fa-lg"></i>
                                            </button>
                                             
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- approve request --}}

        <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="staticBackdropLabel">Are you sure you want to approve this access request?</h4>
                       
                    </div>
                    <form method="POST" action="{{ route('admin.approve-user-denied-access') }}">
                        @csrf
                        
                        <input name="user_requested_access" type="hidden" id="user_requested_access"
                                value="{{ old('user_requested_access') }}" required />
                       
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Approve Access</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="staticDeleteBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="staticBackdropLabel">Decline User Request Access</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="POST" action="{{ route('admin.decline-user-denied-access') }}">
                        @csrf
                        <div style="padding:10px;">
                            <p>Are you sure you want to decline this user's access request? This message will be shared with the user.</p>
                        </div>
                        <div class="modal-body">
                            <h4>User Request</h4>
                            <p id="declined_user_request_message" class="text-primary font-weight-bold"></p>
                            <textarea name="decline_request_response" id="" cols="30" rows="2" class="form-control"
                                placeholder="Write decline message here with at least 10 characters"></textarea>
                            <input name="declined_user_requested_access" type="hidden" id="declined_user_requested_access"
                                value="{{ old('declined_user_requested_access') }}" required />
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary btn-submit-updated-center">Yes, Decline Access</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </section>
@endsection
@section('uniquepagescript')
    <script type="text/javascript">
        $('#users-denied-access-table').DataTable();
        $('.btn-approve').click(function() {
            var requestId = $(this).data('id');
            $.ajax({
                url: '/admin/get-user-request-access-details/' + requestId,
                method: 'GET',
                success: function(data) {

                    $('#user_requested_access').val(data.id);
                    $('#user_request_message').text(data.reason);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
        $('.btn-decline').click(function() {
            var requestId = $(this).data('id');
            $.ajax({
                url: '/admin/get-user-request-access-details/' + requestId,
                method: 'GET',
                success: function(data) {

                    $('#declined_user_requested_access').val(data.id);
                    $('#declined_user_request_message').text(data.reason);
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                }
            });
        });
        //   function confirmApproval() {
        //     return confirm('Are you sure to approve this request?');
        //   }

        //   function confirmDecline() {
        //     return confirm('Are you sure to decline this request?');
        //   }

        // $(document).ready(function () {
        //     $('#users-denied-access-table').DataTable({
        //         "processing": true,
        //         "serverSide": true,
        //         'searching': true,
        //         "order": [[0, "desc"]],
        //         "pageLength": '<?= Config::get('params.list_limit_admin') ?>',
        //         "ajax": {
        //             "url": '{!! route('admin.fetch.users-access-denied', ['all' => 1]) !!}',
        //             "dataType": "json",
        //             "type": "GET",
        //             "data": {_token: "{{ csrf_token() }}"}
        //         },
        //         "columns": [
        //             {data: 'user_name', name: 'user_name', orderable: true},
        //             {data: 'user_phone', name: 'user_phone', orderable: true}, 
        //             {data: 'user_email', name: 'user_email', orderable: true},
        //             {data: 'reason_submitted', name: 'reason_submitted', orderable: false},
        //             {data: 'access_level', name: 'access_level', orderable: false},
        //             {data: 'onboarding_date', name: 'onboarding_date', orderable: false}, 
        //             {data: 'action_links', name: 'action_links', orderable: false}
        //         ],
        //         "columnDefs": [
        //             {"searchable": false, "targets": 0},
        //         ]
        //     });
        // });
    </script>
@endsection
