@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Upload User Items </h3>
                    <div>
                        <a href="{{route('admin.stock-count.user-item-assignments.all')}}"  class="btn btn-success  btn-sm">Back</a>
                    </div>
                </div>
            </div>
            @include('message')
            <div class="box-body">
                <form id="update-item-form" action="{{ route('admin.stock-counts.user-item-allocations.upload') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-3">
                        <label for="user">User</label>
                        <select name="user" id="user" class="form-control mlselec6t" required>
                            @foreach ($users as $user)
                                <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="upload_file" class="control-label">Upload Excel File (.xlsx)</label>
                        <input type="file" class="form-control" name="file" id="upload_file"
                            accept=".xlsx">
                            <span id="file-name" style="margin-top: 10px; display: block;"></span>

                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <input type="submit" class="btn btn-success btn-sm" name="intent" id="template" value="Template">
                            <input type="submit" class="btn btn-success btn-sm" name="intent" id="process" value="Process">
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

