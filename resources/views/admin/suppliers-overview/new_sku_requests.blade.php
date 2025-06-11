@extends('layouts.admin.admin')

@php
    $authuser = Auth::user();
    $hasPermission = isset($permission['maintain-items___view-all-stocks']);
@endphp

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border" style="margin-bottom: 10px">
                @include('message')
                <div class="box-header-flex">
                    <h3 class="box-title">{{ $title }}</h3>
                </div>
            </div>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#new_requests_tab" data-toggle="tab">New Requests</a></li>
                <li><a href="#approved_requests_tab" data-toggle="tab">Approved Requests</a></li>
                <li><a href="#rejected_requests_tab" data-toggle="tab">Rejected Requests</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="new_requests_tab">
                    @include('admin.suppliers-overview.new_sku_requests_includes.new_requests')
                </div>
                <div class="tab-pane" id="approved_requests_tab">
                    @include('admin.suppliers-overview.new_sku_requests_includes.approved_requests')
                </div>
                <div class="tab-pane" id="rejected_requests_tab">
                    @include('admin.suppliers-overview.new_sku_requests_includes.rejected_requests')
                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
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
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#new_requests_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#approved_requests_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });

            $('#rejected_requests_table').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
            
        });
    </script>
@endpush
