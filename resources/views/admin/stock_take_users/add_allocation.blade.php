@extends('layouts.admin.admin')
@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Add User Item Allocation </h3>
                </div>
            </div>
            @include('message')
            <div class="box-body">
                <form id="update-item-form" action="{{ route('admin.stock-count.user-item-assignments.store-allocation') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-3">
                        <label for="user">User</label>
                        <select name="user" id="user" class="form-control mlselec6t" required>
                            <option value="">--Select User--</option>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="item">Item</label>
                        <select name="item" id="item" class="form-control mlselec6t" required>
                            <option value="">--Select Item--</option>
                            @foreach ($items as $item)
                                <option value="{{$item->id}}">{{$item->stock_id_code . ' - ' . $item->title}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <input type="submit" class="btn btn-success btn-sm" name="intent" id="process" value="Add">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endpush
@push('scripts')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.mlselec6t').select2();
            $('#branch').change(function() {
                var branchId = $(this).val();
                var url = $(this).data('url');
                $.ajax({
                    url: url,
                    type: 'GET',
                    data: { branch_id: branchId },
                    success: function(data) {
                        $('#route').empty();
                        $('#route').append('<option value="" selected disabled>Select Route</option>');
    
                        $.each(data.routes, function(key, value) {
                            $('#route').append('<option value="' + value.id + '">' + value.route_name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            });
        });
    </script>
    <script>
        document.getElementById('upload_file').addEventListener('change', function() {
            var fileName = this.files[0].name;
            document.getElementById('file-name').textContent = 'Selected file: ' + fileName;
        });
    </script>
@endpush
