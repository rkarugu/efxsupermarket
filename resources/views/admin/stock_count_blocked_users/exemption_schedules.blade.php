@extends('layouts.admin.admin')

@section('content')
    <section class="content">
    
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Exempted Stock Take Users ({{$tomorrow}})</h3>
                    <div class="btn-group">
                        @if  (isset($permission[$pmodule . '___add']) || $permission == 'superadmin')
                            <button id="addUserBtn" class="btn btn-success btn-sm"> <i class="fas fa-add"></i> Add</button>

                        @endif
                    </div>
                </div>
            </div>

            <div class="box-body">
                @include('message')
                <div class="col-md-12">
                        <table class="table table-bordered table-hover" id="create_datatable">
                            <thead>
                            <tr>
                                <th>#</th>
                                <th>User</th>
                                <th>Branch</th>
                                <th>Bin</th>
                                <th>Exempted By</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @foreach ($exemptedUsers as $record)
                                    <tr>
                                        <th>{{$loop->index+1}}</th>
                                        <td>{{$record->storekeeper}}</td>
                                        <td>{{$record->store}}</td>
                                        <td>{{$record->bin}}</td>   
                                        <td>{{$record->creator}}</td>
                                        <td>
                                            @if  (isset($permission[$pmodule . '___delete']) || $permission == 'superadmin')
                                                <a href="javascript:void(0)" class="removeUser" data-id="{{ $record->id }}" title="remove">
                                                    <i class="fas fa-user-minus"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                
                                    
                                @endforeach
                            </tbody>
                        </table>
                </div>
            </div>
        </div>

        {{-- add users modal --}}
        <div class="modal fade" id="addUsersModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title">Add Users </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                   
                    <div class="box-body">
                        <label for="usersSelect">Select Users</label>
                        <select id="usersSelect" name="users[]" class="form-control select2" multiple="multiple" style="width: 100%;">
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button id="submitUsersBtn" class="btn btn-success btn-sm"><i class="fas fa-paper-plane"></i> Submit</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- remove users modal --}}
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-danger">
                    <div class="box-header with-border">
                        <h3 class="box-title">Confirm Deletion</h3>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="box-body">
                        <p>Are you sure you want to remove this record?</p>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <button id="confirmDeleteBtn" class="btn btn-success btn-sm"><i class="fas fa-trash"></i> Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
    <style>
        .box-header-flex .btn-group {
            display: flex;
            gap: 15px;
        }
    </style>
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script>
        $(document).ready(function() {
            $('#usersSelect').select2({
                placeholder: "Select users",
                allowClear: true,
                dropdownParent: $('#addUsersModal')

            });
            $('#addUserBtn').click(function() {
                $('#addUsersModal').modal('show');
            });

            $('#submitUsersBtn').click(function() {
                let selectedUsers = $('#usersSelect').val();
                let form = new Form();


                if (selectedUsers.length > 0) {
                    $.ajax({
                        url: '{{ route("admin.stock-count-blocked-users.exemption-schedules.add-users") }}', 
                        method: 'POST',
                        data: {
                            users: selectedUsers,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $('#addUsersModal').modal('hide');
                            form.successMessage('Batch price change processed successfully.');
                            location.reload(); 
                        },
                        error: function(xhr) {
                            form.errorMessage(xhr.responseJSON.message);   

                        }
                    });
                } else {
                    form.errorMessage('Please select at least one user.');   
                }
            });
        });
        $(document).ready(function() {
        let deleteUserId = null;

        $('.removeUser').click(function() {
            deleteUserId = $(this).data('id'); 
            $('#confirmDeleteModal').modal('show');
        });

        $('#confirmDeleteBtn').click(function() {
            if (deleteUserId) {
                let form = new Form();
                $.ajax({
                    url: '{{ route("admin.stock-count-blocked-users.exemption-schedules.delete-user") }}',
                    method: 'DELETE',
                    data: {
                        id: deleteUserId,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#confirmDeleteModal').modal('hide');
                        form.successMessage('User removed successfully.');
                        location.reload(); 
                    },
                    error: function(xhr) {
                        form.errorMessage(xhr.responseJSON.message);
                    }
                });
            }
        });
    });

    </script>
@endsection

