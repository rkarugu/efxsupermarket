@extends('layouts.admin.admin')
@push('styles')
{{--    <style>--}}
{{--        th, td {--}}
{{--            text-align: right;--}}
{{--        }--}}
{{--    </style>--}}
@endpush
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> Cashier Summary.</h3>
            </div>
            @include('message')
            <div class="box-body">
                <form action="" method="get">
                    <div class="row pb-4">
                        @if($permission == 'superadmin')
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="restaurant_id">Branch</label>
                                    {!!Form::select('restaurant_id', $branches, request()->input('restaurant_id') ?? null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','title'=>'Please select Branch','id'=>'restaurant_id'  ])!!}
                                </div>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="submit" name="intent" value="FILTER" class="btn btn-primary btn-sm" style="margin-top: 25px;"/>
                                <input type="submit" name="intent" value="PDF" class="btn btn-primary btn-sm" style="margin-top: 25px;"/>
                            </div>
                        </div>
                    </div>
                </form>
                <table class="table table-striped mt-3" id="cashiersTable">
                    <div class="d-flex justify-content-end mb-3" style="margin-bottom: 20px">
                        <button id="bulkUpdateBtn" class="btn btn-primary" style="display:none;">
                            <i class="fa fa-edit"></i> Update Drop Limit for Selected
                        </button>
                    </div>
                    <thead>
                    <tr class="text-right">
                        <th><input type="checkbox" id="selectAll"></th> <!-- Select All Checkbox -->
                        <th>#</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Branch</th>
                        <th>Drop Limit</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($branch_cashiers as $cashier)
                        <tr class="parent-row">
                            <td><input type="checkbox" class="cashier-checkbox" value="{{ $cashier->id }}"></td> <!-- Checkbox for single row -->
                            <td>{{ $loop -> iteration }}</td>
                            <td>{{ $cashier->name }}</td>
                            <td>{{ $cashier->phone_number }}</td>
                            <td>{{ $cashier->email }}</td>
                            <td>{{@$cashier->branch ->name }}</td>
                            <td>{{number_format( $cashier->drop_limit, 2) ?? 0.00 }}</td>
                            <td>
                                <a href="#" class="edit-drop-limit" data-id="{{ $cashier->id }}"><i class="fa fa-pencil"></i> </a>
{{--                                <a href="{{ route('cashier-management.cashier', base64_encode($cashier->id)) }}"><i class="fa fa-eye"></i></a>--}}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="dropLimitModal" tabindex="-1" role="dialog" aria-labelledby="dropLimitModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="dropLimitModalLabel">Update Drop Limit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="updateDropLimitForm">
                        @csrf
                        <input type="hidden" name="user_ids" id="userIds"> <!-- Hidden field to store user IDs -->
                        <div class="form-group">
                            <label for="drop_limit">Drop Limit</label>
                            <input type="number" class="form-control" id="drop_limit" name="drop_limit" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        var VForm = new Form();
        $(document).ready(function() {
            var table = $("#cashiersTable").DataTable();

            $('#filter').click(function(e){
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();
        })

        $(document).ready(function() {
            $('#selectAll').click(function() {
                $('.cashier-checkbox').prop('checked', this.checked);
                toggleBulkUpdateButton();
            });

            $('.cashier-checkbox').change(function() {
                toggleBulkUpdateButton();
            });

            function toggleBulkUpdateButton() {
                let selected = $('.cashier-checkbox:checked').length > 0;
                $('#bulkUpdateBtn').toggle(selected);
            }

            $('.edit-drop-limit').click(function(e) {
                e.preventDefault();
                let userId = $(this).data('id');
                $('#userIds').val(userId);
                $('#drop_limit').val('');
                $('#dropLimitModal').modal('show');
            });

            // Open modal for bulk update
            $('#bulkUpdateBtn').click(function() {
                let selectedIds = $('.cashier-checkbox:checked').map(function() {
                    return this.value;
                }).get();

                if (selectedIds.length > 0) {
                    $('#userIds').val(selectedIds.join(','));
                    $('#drop_limit').val('');
                    $('#dropLimitModal').modal('show');
                }
            });

            // Form submission for updating drop limit
            $('#updateDropLimitForm').submit(function(e) {
                e.preventDefault();

                let formData = $(this).serialize();
                $.ajax({
                    url: '{{ route('cashier-management.updateDropLimit') }}', // Define route in your web.php
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        $('#dropLimitModal').modal('hide');
                        $('#drop_limit').val('');
                        $('.cashier-checkbox').prop('checked', false);
                        $('#selectAll').prop('checked', false);
                        $('#bulkUpdateBtn').hide();
                        location.reload();
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('An error occurred while updating the drop limit.');
                    }
                });
            });
        });


    </script>
@endpush