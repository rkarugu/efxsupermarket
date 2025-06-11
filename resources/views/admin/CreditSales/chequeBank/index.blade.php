
@extends('layouts.admin.admin')
@section('content')
    @include('message')

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Banks </h3>
                <button class="btn btn-primary pull-right"  data-toggle="modal" data-target="#createModal">Add Bank</button>
            </div>
            <div class="box-body">

                <table class="table table-bordered table-hover" id="create_datatable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Unpaid Cheque Charges</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($chequeBanks as $chequeBank)
                        <tr>
                            <td>{{ $chequeBank->bank }}</td>
                            <td>{{ $chequeBank->bounce_penalty }}</td>
                            <td>
                                <button class="btn btn-sm btn-info edit-btn" data-id="{{ $chequeBank->id }}" data-data="{{ $chequeBank }}" data-toggle="modal" data-target="#editModal">
                                    <i class="glyphicon glyphicon-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $chequeBank->id }}" data-name="{{ $chequeBank->bank }}" data-toggle="modal" data-target="#deleteModal">
                                    <i class="glyphicon glyphicon-trash"></i>
                                </button>

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>


    <!-- Modal -->

    <!-- Create Modal -->
    <div class="modal fade" id="createModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Add Bank</h4>
                </div>
                <div class="modal-body">
                    <div id="error-message" style="display: none; color: red;"></div>
                    <form class="validate" id="createForm">
                        <div class="form-group">
                            <label for="name">Bank Name</label>
                            <input type="text" class="form-control" id="bank" name="bank" required>
                        </div>
                        <div class="form-group">
                            <label for="bounce_penalty">Bounce Penalty</label>
                            <input type="text" class="form-control" id="bounce_penalty" name="bounce_penalty" required>
                            @error('bounce_penalty') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Edit</h4>
                </div>
                <div class="modal-body">
                    <div id="error-message" style="display: none; color: red;"></div>
                    <form class="validate" id="editForm">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="form-group">
                            <label for="edit-bank">Bank Name</label>
                            <input type="text" class="form-control" id="edit-bank" name="bank" required>
                        </div>
                        <div class="form-group">
                            <label for="edit_bounce_penalty">Bounce Penalty</label>
                            <input type="text" class="form-control" id="edit_bounce_penalty" name="bounce_penalty" required >
                            @error('edit_bounce_penalty') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Delete Bank</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong id="delete-name"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">Delete</button>
                </div>
            </div>
        </div>
    </div>

{{--    <livewire:cheque-bank-component/>--}}
@endsection

@push('scripts')
    <div id="loader-on"
         style="position: fixed; top: 0; text-align: center; z-index: 999999;
                width: 100%;  height: 100%; background: #000000b8; display:none;"
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $(document).ready(function() {
            $(function () {

                $(".mlselect").select2();
            });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            // Create Promotion Type
            $('#createForm').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('cheque-banks.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#createModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        // Check if the error response has a specific message
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage = xhr.responseText;
                        }

                        // Display the error message
                        $('#error-message').text(errorMessage).show();
                    }
                });
            });

            // Edit Promotion Type
            $('.edit-btn').click(function() {
                var id = $(this).data('id');
                var data = $(this).data('data');
                console.log(data);
                $('#edit-id').val(id);
                $('#edit-bank').val(data.bank);
                $('#edit_bounce_penalty').val(data.bounce_penalty);
            });

            $('#editForm').submit(function(e) {
                e.preventDefault();
                var id = $('#edit-id').val();
                $.ajax({
                    url: "/admin/cheque-banks/" + id,
                    method: "PUT",
                    data: $(this).serialize(),
                    success: function(response) {
                        $('#editModal').modal('hide');
                        location.reload();
                    },
                    error: function(xhr) {
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMessage = xhr.responseText;
                        }

                        // Display the error message
                        $('#error-message').text(errorMessage).show();
                    }
                });
            });
            // Delete Promotion Type
            var deleteId;
            $('.delete-btn').click(function() {
                deleteId = $(this).data('id');
                var bank = $(this).data('bank');
                $('#delete-name').text(bank);
            });

            $('#confirm-delete').click(function() {
                $.ajax({
                    url: "/admin/cheque-banks/" + deleteId,
                    method: "DELETE",
                    success: function(response) {
                        $('#deleteModal').modal('hide');
                        location.reload();
                    },
                    error: function(response) {
                        // Handle error
                    }
                });
            });
        });
    </script>

@endpush

