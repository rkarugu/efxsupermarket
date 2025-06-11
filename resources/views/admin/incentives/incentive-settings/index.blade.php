@extends('layouts.admin.admin')

@section('content')
    <section class="content">

        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Incentives</h3>
                    <div>
{{--                        <a href="#" class="btn btn-primary"  data-toggle="modal" data-target="#createModal">{{'+ '}}Create</a>--}}
                    </div>

                </div>
            </div>

            <div class="box-body">

                <hr>

                @include('message')


                <div class="col-md-12">
                    <table class="table table-bordered mt-3" id="create_datatable">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Incentive Name</th>

                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody id="incentive-settings-list">
                        @foreach ($incentiveSettings as $setting)
                            <tr id="setting-{{ $setting->id }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $setting->name }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit" data-id="{{ $setting->id }}"><i class="fa fa-edit"></i> </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


        <!-- Create Modal -->
        <div class="modal fade" id="createModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
{{--                        <div class="pull-left">--}}
{{--                            <i class="fas fa-money-bill"></i>--}}
{{--                        </div>--}}

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Add Incentive</h4>
                    </div>
                    <form id="incentive-form">
                    <div class="modal-body">
                        <div id="error-messages" class="alert alert-danger hidden"></div>
                            @csrf
                        <input type="hidden" id="incentive_id">
                        <div class="form-group">
                            <div class="mb-3">
                                <label for="name" class="form-label">Incentive Name</label>
                                <input type="text" class="form-control" id="name" name="name" required >
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="mb-3 mt-3">
                                <div id="target_reward-container">
                                    <div class="mb-3 row" id="row-0">
                                        <div class="col-md-5">
                                            <label for="type" class="form-label">Target Value</label>
                                            <input type="text" class="form-control mb-2" name="target[0]" placeholder="Value" value="" required>
                                        </div>
                                        <div class="col-md-5">
                                            <label for="type" class="form-label">Reward</label>
                                            <input type="text" class="form-control mb-2" name="reward[0]" placeholder="Reward" value="" required>
                                        </div>
                                    </div>
                                    <!-- Repeater fields will be appended here -->
                                </div>
                                <button type="button" class="btn btn-secondary btn-sm mt-2" id="add-row">  <i class="fa fa-plus"></i> Add Reward</button>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary pull-left" data-dismiss="modal" aria-label="Close"><i class="fa fa-close"></i>Close</button>
                        <button type="submit" class="btn btn-primary btn-sm " value="save"  id="save-incentive"> <i class="fa fa-save"></i> Save</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </section>


@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <style>
        .modal .datepicker {
            z-index: 9999; /* Higher than Bootstrap modal z-index */
        }
        .error-message {
            color: red;
            font-size: 12px;
        }
        .mb-3.row {
            margin-bottom: 15px; /* Adjust as needed */
        }
    </style>
@endpush
@push('scripts')
    <div id="loader-on"
         style="position: fixed; top: 0; text-align: center; z-index: 999999;
                width: 100%;  height: 100%; background: #000000b8; display:none;"
         class="loder">
        <div class="loader" id="loader-1"></div>
    </div>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>

    <script>
        $(document).ready(function() {
            let rowIndex = 1;

            function renderRow(index, value = '', reward = '', operation='') {
                return `
            <div class="mb-3 row" id="row-${index}">
                <div class="col-md-5">
                     <label for="type" class="form-label">Target Value (%)</label>
                    <input type="text" class="form-control mb-2" name="target[${index}]" placeholder="Target" value="${value}" required>
                </div>

                <div class="col-md-5">
                     <label for="type" class="form-label">Reward</label>
                    <input type="text" class="form-control mb-2" name="reward[${index}]" placeholder="Reward" value="${reward}" required>
                </div>
                <div class="col-md-2">
                     <label for="type" class="form-label"></label>
<!--                     <button type="button" class="btn btn-primary btn-sm  remove-row"  data-index="${index}">  <i class="fa fa-trash"></i> </button>-->
                    <button type="button" class="btn btn-primary btn-sm remove-row" data-index="${index}"> <i class="fas fa-trash"></i> Remove</button>
                </div>
            </div>
        `;}

            $('#create-new').click(function() {
                $('#incentiveModalLabel').text('Create Incentive');
                $('#incentive-form')[0].reset();
                $('#incentive_id').val('');
                $('#target_reward-container').html(renderRow(rowIndex++));
                $('#incentiveModal').modal('show');
            });

            $('#add-row').click(function() {
                $('#target_reward-container').append(renderRow(rowIndex++));
            });

            $('#incentive-form').on('click', '.remove-row', function() {
                let index = $(this).data('index');
                $(`#row-${index}`).remove();
            });


            $('#save-incentive').click(function(e) {
                e.preventDefault();

                // Reset previous error messages
                $('.error').remove();

                let isValid = true;

                // Check if all required fields are filled
                let incentiveName = $('#name').val();
                if (!incentiveName) {
                    $('#name').after('<span class="text-danger error">This field is required</span>');
                    isValid = false;
                }


                let targets = $('input[name^="target"]').map(function() { return $(this).val().trim(); }).get();
                let rewards = $('input[name^="reward"]').map(function() { return $(this).val().trim(); }).get();
                let operation = $('input[name^="operation"]').map(function() { return $(this).val().trim(); }).get();

                // Check if value and reward fields are filled
                targets.forEach((value, index) => {
                    if (!value) {
                        $(`input[name="target[${index}]"]`).after('<span class="text-danger error">This field is required</span>');
                        isValid = false;
                    }
                });
                rewards.forEach((reward, index) => {
                    if (!reward || isNaN(reward)) {
                        $(`input[name="reward[${index}]"]`).after('<span class="text-danger error">Enter a valid number</span>');
                        isValid = false;
                    }
                });
                operation.forEach((operation, index) => {
                    if (!operation) {
                        $(`input[name="reward[${index}]"]`).after('<span class="text-danger error">Select Operation</span>');
                        isValid = false;
                    }
                });

                if (isValid) {
                    let id = $('#incentive_id').val();
                    let url = id ? `/admin/incentive-settings/${id}` : '/admin/incentive-settings';
                    let type = id ? 'PUT' : 'POST';

                    $.ajax({
                        url: url,
                        type: type,
                        data: $('#incentive-form').serialize(),
                        success: function(response) {

                            $('#incentive-form')[0].reset(); // Clear the form fields
                            $('#target_reward-container').empty();
                            $('#incentiveModal').modal('hide');
                            location.reload(); // Reload the page to reflect changes
                        },
                        error: function(xhr) {
                            // Display server-side validation errors
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';
                            $.each(errors, function(field, messages) {
                                errorMessages += '<p>' + messages.join('<br>') + '</p>';
                            });
                            $('#error-messages').html(errorMessages).removeClass('hidden');
                        }
                    });
                }
            });


            $('.edit').click(function() {
                let id = $(this).data('id');
                $.get(`/admin/incentive-settings/${id}`, function(data) {
                    $('#incentiveModalLabel').text('Edit Incentive');
                    $('#incentive_id').val(data.id);
                    $('#name').val(data.name);
                    // $('#target').val(data.target);
                    // $('#type').val(data.type);
                    $('#target_reward-container').empty();

                    // Parse JSON and render rows
                    if (Array.isArray(data.target_reward)) {
                        data.target_reward.forEach((item, index) => {
                            $('#target_reward-container').append(renderRow(index, item.target, item.reward, item.operation));
                        });
                        rowIndex = data.target_reward.length;
                    } else {
                        $('#target_reward-container').append(renderRow(rowIndex++));
                    }

                    $('#createModal').modal('show');
                });
            });

            $('.delete').click(function() {
                let id = $(this).data('id');
                if (confirm('Are you sure you want to delete this incentive?')) {
                    $.ajax({
                        url: `/admin/incentive-settings/${id}`,
                        type: 'DELETE',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            $(`#setting-${id}`).remove();
                        },
                        error: function(response) {
                            alert('Something went wrong!');
                        }
                    });
                }
            });
        });
    </script>


@endpush
