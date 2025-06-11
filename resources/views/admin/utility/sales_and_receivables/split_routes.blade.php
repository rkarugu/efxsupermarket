@extends('layouts.admin.admin')
<?php
$logged_user_info = getLoggeduserProfile();
$my_permissions = $logged_user_info->permissions;
$route_name = \Route::currentRouteName();
?>
@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title">Split Route</h3>
                </div>
            </div>

            <div class="box-body">
                @if (session('error'))
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: '{{ session('error') }}',
                            confirmButtonText: 'OK'
                        });
                    </script>
                @endif
                <form id="split-routes-form" action="{{ route('route-split.process') }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-group col-md-3">
                        <label for="route">Select a route to split</label>
                        <select name="route" id="route" class="form-control mlselec6t">
                            <option value="" selected disabled>--Select Route--</option>
                            @foreach ($routes as $index => $route)
                                <option value="{{ $route->id }}" {{ request()->route == $route->id ? 'selected' : '' }}>
                                    {{ $route->route_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3" id="file-input-group">
                        <label for="new_route_name" class="control-label">New route name</label>
                        <input type="text" class="form-control" name="new_route_name" id="new_route_name" disabled>
                    </div>

                    <input type="hidden" name="selected_centers" id="selected_centers">

                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['route-split___add']))
                        <div class="col-md-2" id="update-button-group">
                            <div class="form-group">
                                <label style="display: block;">&nbsp;</label>
                                <input type="submit" class="btn btn-primary" name="intent" id="update" value="Update"
                                    disabled>
                                <input type="button" class="btn btn-primary" name="clear" id="clear_btn" value="Clear"
                                    disabled>
                            </div>
                        </div>
                    @endif


                </form>
            </div>
        </div>

        <div class="box" id="split_route_centres" style="display: none;">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <p class="box-title">Delivery Centres</p>
                </div>
            </div>

            <div class="box-body">
                <table class="table table-bordered" id="split_route_centres_table">
                    <thead>
                        <tr style="font-size: 12px">
                            <th>ROUTE</th>
                            <th>DELIVERY CENTRES</th>
                            <th>TOTAL ROUTE CUSTOMERS</th>
                            <th>
                                <input type="checkbox" name="all_centres" id="select-centre-all-checkbox">
                            </th>
                        </tr>
                    </thead>
                    <tbody id="split_route_centres_table_body">

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('styles')
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

        .select2 {
            width: 100% !important;
        }
    </style>
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('/js/form.js') }}"></script>

    <script>
        $(document).ready(function() {

            let all_routes_data = @json($routes);
            let tableInitialized = false;

            function initializeDataTable() {
                $('#split_route_centres_table').DataTable({
                    "paging": true,
                    "pageLength": 100,
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
            }

            $(".mlselec6t").select2();

            let routeSelected = false;
            let fileUploaded = '';

            $('#clear_btn').prop('disabled', false);

            function updateButtonStates() {
                if (routeSelected && fileUploaded) {
                    $('#update').prop('disabled', false);
                    $('#download').prop('disabled', true);
                    $('#clear_btn').prop('disabled', false);
                } else if (routeSelected) {
                    $('#download').prop('disabled', false);
                    $('#update').prop('disabled', true);
                    $('#clear_btn').prop('disabled', false);
                } else {
                    $('#download').prop('disabled', true);
                    $('#update').prop('disabled', true);
                    $('#clear_btn').prop('disabled', false);
                }
            }

            $('#route').change(function() {
                routeSelected = $(this).val() !== "";
                fileUploaded = $('#new_route_name').val() !== "";
                updateButtonStates();
                $('#new_route_name').prop('disabled', !routeSelected);
                if (routeSelected) {
                    $('#split_route_centres').show();
                    let selectedRouteId = $(this).val();
                    populateTable(selectedRouteId);
                } else {
                    $('#split_route_centres').hide();
                }
            });

            $('#new_route_name').on('change', function() {
                fileUploaded = $(this).val() !== "";
                updateButtonStates();
            });

            let intentValue = '';
            let submitButton = '';

            $('input[type="submit"]').click(function() {
                intentValue = $(this).val();
                submitButton = $(this);
            });

            $('#clear_btn').on('click', function() {
                location.reload()
            })

            $('#select-centre-all-checkbox').on('change', function() {
                var checked = $(this).is(':checked');
                $('.select-centre-checkbox').prop('checked', checked);
            });

            $('.select-centre-checkbox').on('change', function() {
                var allChecked = $('.select-centre-checkbox').length === $(
                    '.select-centre-checkbox:checked').length;
                $('#select-centre-all-checkbox').prop('checked', allChecked);
            });

            $('#split-routes-form').on('submit', function(e) {
                e.preventDefault();

                var selected_centers = [];
                $('.select-centre-checkbox:checked').each(function() {
                    selected_centers.push($(this).val());
                });

                $('#selected_centers').val(selected_centers.join(','));

                var selected_centres_array = $('#selected_centers').val();

                submitButton.prop('disabled', true).val('Processing...');

                var formData = new FormData(this);
                formData.append('intent', intentValue);
                formData.append('selected_centres', selected_centres_array);

                var xhr = new XMLHttpRequest();
                xhr.open('POST', $(this).attr('action'), true);

                xhr.responseType = intentValue === 'Download Prices' ? 'blob' : 'json';

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        if (intentValue === 'Download Prices') {
                            var link = document.createElement('a');
                            link.href = window.URL.createObjectURL(xhr.response);
                            link.download = `Item_price_template.xlsx`;
                            link.click();
                        } else {
                            var response = xhr.response;
                            form.successMessage('Route split complete');
                        }
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        var errorMessage = null;
                        try {
                            errorMessage = xhr.response.error;
                        } catch (err) {
                            errorMessage = 'There was an error processing your request.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                    submitButton.prop('disabled', false).val(intentValue);
                    $('#new_route_name').val('');
                };

                xhr.onerror = function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred during the request.',
                    });
                    submitButton.prop('disabled', false).val(intentValue);
                    $('#new_route_name').val('');
                };

                xhr.send(formData);
            });

            function populateTable(routeId) {
                $('#split_route_centres_table').DataTable().destroy();
                let route = all_routes_data.find(route => route.id == routeId);
                let tableBody = $('#split_route_centres_table_body');
                tableBody.empty();

                if (route?.centers?.length > 0) {
                    route.centers.forEach(center => {
                        tableBody.append(`
                                <tr style="font-size: 12px">
                                    <td>${center.name}</td>
                                    <td>${center.name}</td>
                                    <td>${center.wa_route_customers_count}</td>
                                    <td><input type="checkbox" name="selected_centres[]" value="${center.id}" class="select-centre-checkbox"></td>
                                </tr>
                            `);
                    });
                } else {
                    tableBody.append(
                        '<tr><td colspan="4" style="text-align: center;">No centers available for this route.</td></tr>'
                    );
                }
                $('#split_route_centres_table').DataTable().destroy();
                initializeDataTable();
            }
        });
    </script>
@endpush
