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
                    <h3 class="box-title">Inventory Utility Logs</h3>
                </div>
            </div>
            <ul class="nav nav-tabs">
                <li class="active"><a href="#update_bin_logs" data-toggle="tab">Bin Logs</a></li>
                <li><a href="#upload_new_items_logs" data-toggle="tab">New Items Logs</a></li>
                <li><a href="#update_item_prices_logs" data-toggle="tab">Prices per Branch Logs</a></li>
                <li><a href="#selling_price" data-toggle="tab">Selling Price Logs</a></li>
                <li><a href="#standard_cost" data-toggle="tab">Standard Cost Logs</a></li>
                <li><a href="#verify_stocks" data-toggle="tab">Verify Stocks Logs</a></li>
                <li><a href="#update_item_bin_logs" data-toggle="tab">Update Item Bin Logs</a></li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane active" id="update_bin_logs">
                    @include('admin.utility.inventory.includes.pending_approvals')
                </div>
                <div class="tab-pane" id="upload_new_items_logs">
                    @include('admin.utility.inventory.includes.upload_new_item_logs')
                </div>
                <div class="tab-pane" id="update_item_prices_logs">
                    @include('admin.utility.inventory.includes.update_item_prices_logs')
                </div>
                <div class="tab-pane" id="selling_price">
                    @include('admin.utility.inventory.includes.selling_price')
                </div>
                <div class="tab-pane" id="standard_cost">
                    @include('admin.utility.inventory.includes.standard_cost')
                </div>
                <div class="tab-pane" id="verify_stocks">
                    @include('admin.utility.inventory.includes.verify_stocks')
                </div>
                <div class="tab-pane" id="update_item_bin_logs">
                    @include('admin.utility.inventory.includes.update_item_bins')
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
    {{-- <script src="{{ asset('js/form.js') }}"></script> --}}
    <script>
        $(document).ready(function() {
            $("#locationselect").select2();
            $("#binselect").select2();
            $("#initiatedbyselect").select2();
            $("#approvedbyselect").select2();

            $("#locationselect_2").select2();
            $("#initiatedbyselect_2").select2();
            $("#approvedbyselect_2").select2();
            $("#statusselect_2").select2();

            $("#locationselect_3").select2();
            $("#initiatedbyselect_3").select2();
            $("#approvedbyselect_3").select2();
            $("#statusselect_3").select2();

            $('#inventory_utility_table').DataTable({
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
            $('#inventory_utility_item_prices_table').DataTable({
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
            $('#inventory_utility_new_items_table').DataTable({
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
            $('#inventory_selling_price_table').DataTable({
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
            $('#inventory_standard_cost_table').DataTable({
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
            $('#inventory_verify_stocks_table').DataTable({
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
            $('#update_item_bins_table').DataTable({
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
