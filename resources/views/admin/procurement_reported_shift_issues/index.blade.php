@extends('layouts.admin.admin')

<?php
$logged_user_info = getLoggeduserProfile();
$my_permissions = $logged_user_info->permissions;
?>

@section('content')
    <section class="content">
        <div class="modal fade" id="view-issue-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Salesman Shift Issue </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row w-100">
                            <div class="col-md-8" id="issue-info">
                                <table>
                                    <tr>
                                        <th class="text-left" id="product-name-title">Product Name:</th>
                                        <td style="padding-left:20px" id="product-name"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="issue-scenario-title">Reported Issue:</th>
                                        <td style="padding-left:20px" id="issue-scenario"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="issue-route-title">Route:</th>
                                        <td style="padding-left:20px" id="issue-route"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="issue-salesman-title">Salesman:</th>
                                        <td style="padding-left:20px" id="issue-salesman"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="issue-customer-title">Customer:</th>
                                        <td style="padding-left:20px" id="issue-customer"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="item-description-title">Item Description:</th>
                                        <td style="padding-left:20px" id="item-description"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="new-price-title">Competitor Price:</th>
                                        <td style="padding-left:20px" id="new-price"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="selling-price-title">Selling Price:</th>
                                        <td style="padding-left:20px" id="selling-price"></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <img id="issue-image" src="" alt="Issue Image" width="100%;">
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resolve-issue-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Resolve Salesman Shift Issue </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row w-100">
                            <div class="col-md-12" id="issue-info">
                                <table>
                                    <input type="hidden" id="reported-issue-id" name="issue_id" value="">

                                    <tr>
                                        <th class="text-left" id="reported-product-name-title">Product Name:</th>
                                        <td style="padding-left:20px" id="reported-product-name"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-issue-scenario-title">Reported Issue:</th>
                                        <td style="padding-left:20px" id="reported-issue-scenario"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-issue-route-title">Route:</th>
                                        <td style="padding-left:20px" id="reported-issue-route"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-issue-salesman-title">Salesman:</th>
                                        <td style="padding-left:20px" id="reported-issue-salesman"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-issue-customer-title">Customer:</th>
                                        <td style="padding-left:20px" id="reported-issue-customer"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-item-description-title">Item Description:</th>
                                        <td style="padding-left:20px" id="reported-item-description"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-new-price-title">Competitor Price:</th>
                                        <td style="padding-left:20px" id="reported-new-price"></td>
                                    </tr>
                                    <tr>
                                        <th class="text-left" id="reported-selling-price-title">Selling Price:</th>
                                        <td style="padding-left:20px" id="reported-selling-price"></td>
                                    </tr>
                                </table>

                            </div>

                        </div>
                        <hr>
                        <div id="show-shop-closed-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <p style="font-weight:bolder">Onboarded Photo</p>
                                    <img id="reported-issue-original-image" src="" alt="Issue Image"
                                        width="200px;height:200px;object-fit:cover">
                                </div>
                                <div class="col-md-6">
                                    <p style="font-weight:bolder">Taken Photo</p>
                                    <img id="reported-issue-image" src="" alt="Issue Image"
                                        width="200px;height:200px;object-fit:cover">
                                </div>
                            </div>
                            <hr>
                        </div>
                        <div class="row" style="margin-top: 10px">
                            <div class="col-md-12">
                                <label id="reported-resolved-description-title" style="width: 100%">Enter Comment</label>
                                <textarea name="reported-resolved-description" id="reported-resolved-description" style="width: 100%"
                                    rows="5"></textarea>
                            </div>
                        </div>
                        <input type="hidden" id="product_id" name="product_id" value="">
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" name="save" value="save" class="btn btn-primary"
                                id="submit_resolution">Submit</button>
                            <span id="loader" class="spinner-border spinner-border-sm" role="status"
                                aria-hidden="true" style="display: none;"></span>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Salesman Shift Reported Issues Report</h3>
                </div>
            </div>

            <div class="box-body">

                <form action="{{ route('procurement-reported-shift-issues.index') }}" method="get">

                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">From</label>-
                                <input type="date" name="start_date" id="start_date" class="form-control"
                                    value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">To</label>
                                <input type="date" name="end_date" id="end_date" class="form-control"
                                    value="{{ request()->end_date ?? \Carbon\Carbon::now()->toDateString() }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Select Route</label>
                                <select name="route_name" id="route_id" class="form-control">
                                    <option value="" selected disabled>Select route</option>
                                    @foreach ($routes as $route)
                                        <option value="{{ $route->id }}"
                                            @if (request()->route_name == $route->id) selected @endif>
                                            {{ $route->route_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="group_name" class="control-label">Select Group</label>
                                <select name="group_name" id="group_name" class="form-control">
                                    <option value="" selected disabled> Select a group</option>
                                    <option value="A" @if (request()->group_name == 'A') selected @endif>A</option>
                                    <option value="B" @if (request()->group_name == 'B') selected @endif>B</option>
                                    <option value="C" @if (request()->group_name == 'C') selected @endif>C</option>
                                    <option value="D" @if (request()->group_name == 'D') selected @endif>D</option>
                                    <option value="E" @if (request()->group_name == 'E') selected @endif>E</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="" class="control-label">Select Issue</label>
                                <select name="display_scenario" id="display_scenario" class="form-control">
                                    <option value="" selected disabled>Select Issue</option>
                                    @foreach ($uniqueScenarios as $uniqueScenario)
                                        @php
                                            $optionValue = str_replace(' ', '_', strtolower($uniqueScenario));
                                        @endphp
                                        <option value="{{ $optionValue }}"
                                            @if (request()->display_scenario == $optionValue) selected @endif>
                                            {{ ucwords(str_replace('_', ' ', $uniqueScenario)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group" style="margin-top: 25px; ">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <button type="submit" name="intent" value="Excel"
                                    class="btn btn-primary">Excel</button>
                                <button type="button" name="clear" value="clear"
                                    class="btn btn-primary">Clear</button>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table" id="create_datatable_10">
                        <thead>
                            <tr>
                                <th style="width: 3%;">#</th>
                                <th> Date </th>
                                <th> Reported Issue </th>
                                <th> Route </th>
                                <th> Salesman </th>
                                <th> Customer </th>
                                <th> Resolved Status </th>
                                <th> Actions </th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($issues as $index => $issue)
                                <tr>
                                    <th scope="row" style="width: 3%;">{{ $index + 1 }}</th>
                                    <td>{{ $issue->created_at }}</td>
                                    <td>{{ $issue->display_scenario }}</td>
                                    <td>{{ $issue->route }}</td>
                                    <td>{{ $issue->salesman }}</td>
                                    <td>{{ $issue->customer }}</td>
                                    <td>
                                        @if ($issue->resolved_status == 1)
                                            Resolved
                                        @else
                                            Pending
                                        @endif
                                    </td>
                                    <td>
                                        <div class="action-buttons-div">
                                            <button title="View Issue" data-toggle="modal"
                                                data-target="#view-issue-modal" data-backdrop="static" class="btn"
                                                data-issue="{{ json_encode($issue) }}">
                                                <i class="fa fa-eye text-primary fa-lg"></i>
                                            </button>
                                            @if (
                                                $logged_user_info->role_id == 1 ||
                                                    isset($my_permissions['reported-shift-issues___resolve-salesman-reported-issues']))
                                                {{-- @if ($issue->resolved_status != 1) --}}
                                                <button title="Resolve Issue" data-toggle="modal"
                                                    data-target="#resolve-issue-modal" data-backdrop="static"
                                                    class="btn" data-issue="{{ json_encode($issue) }}">
                                                    <i class="fab fa-resolving"></i>
                                                </button>
                                                {{-- @endif --}}
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </section>
@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .reported-resolved-description {
            vertical-align: top;
            padding: 5px;
            box-sizing: border-box;
            overflow: auto;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            $("#route_id").select2();
            $("#group_name").select2();
            $("#display_scenario").select2();
        });

        const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.onmouseenter = Swal.stopTimer;
                toast.onmouseleave = Swal.resumeTimer;
            }
        });

        document.addEventListener('DOMContentLoaded', function() {

            sessionStorage.removeItem('start_date');
            sessionStorage.removeItem('end_date');
            sessionStorage.removeItem('route_name');
            sessionStorage.removeItem('group_name');
            sessionStorage.removeItem('display_scenario');

            const form = document.querySelector('form');

            const today = new Date().toISOString().slice(0, 10);

            form.addEventListener('submit', function() {
                const formData = new FormData(form);
                formData.forEach((value, key) => {
                    sessionStorage.setItem(key, value);
                });
            });

            const storedStartDate = sessionStorage.getItem('start_date');
            if (storedStartDate) {
                document.getElementById('start_date').value = storedStartDate;
            }
            const storedEndDate = sessionStorage.getItem('end_date');
            if (storedEndDate) {
                document.getElementById('end_date').value = storedEndDate;
            }
            const storedRouteName = sessionStorage.getItem('route_name');
            if (storedRouteName) {
                document.getElementById('route_id').value = storedRouteName;
            }

            const storedGroupName = sessionStorage.getItem('group_name');
            if (storedGroupName) {
                document.getElementById('group_name').value = storedGroupName;
            }

            const storedDisplayScenario = sessionStorage.getItem('display_scenario');
            if (storedDisplayScenario) {
                document.getElementById('display_scenario').value = storedDisplayScenario;
            }

            const clearButton = document.querySelector('button[name="clear"]');
            clearButton.addEventListener('click', function(event) {
                sessionStorage.removeItem('start_date');
                sessionStorage.removeItem('end_date');
                sessionStorage.removeItem('route_name');
                sessionStorage.removeItem('group_name');
                sessionStorage.removeItem('display_scenario');

                document.getElementById('start_date').value = '';
                document.getElementById('end_date').value = '';
                document.getElementById('route_id').value = '';
                document.getElementById('group_name').value = '';
                document.getElementById('display_scenario').value = '';

                window.location.href = window.location.pathname;

            });
        });

        function approveShop() {
            let subjectShopId = $("#subject-shop").val();
            $(`#source-${subjectShopId}`).val('approval_requests');

            $(`#approve-shop-form-${subjectShopId}`).submit();
        }

        function approveAllShops() {
            $("#approve-all-shops-form").submit();
        }

        function numberWithCommas(x) {
            return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        $('#view-issue-modal').on('show.bs.modal', function(event) {

            let triggeringButton = $(event.relatedTarget);
            let dataValue = triggeringButton.data('issue');

            let date = new Date();
            date.setTime(date.getTime() + (2 * 60 * 1000));
            let expires = "; expires=" + date.toGMTString();

            document.cookie = 'issue' + "=" + dataValue + expires + "; path=/";

            if (dataValue.display_scenario == 'No Order') {
                $('#issue-scenario').text(dataValue.display_scenario);
                $('#issue-route').text(dataValue.route);
                $('#issue-salesman').text(dataValue.salesman);
                $('#issue-customer').text(dataValue.customer);
                $('#issue-scenario, #issue-route, #issue-salesman, #issue-customer, #issue-scenario-title, #issue-route-title, #issue-salesman-title, #issue-customer-title')
                    .show();
                $('#product-name, #new-price, #selling-price, #issue-image, #item-description, #product-name-title, #new-price-title, #selling-price-title, #issue-image-title, #item-description-title')
                    .hide();
            } else if (dataValue.display_scenario == 'Price Conflict') {
                $('#issue-scenario').text(dataValue.display_scenario);
                $('#item-description').text(dataValue.wainventoryitem.description);
                $('#new-price').text(numberWithCommas(dataValue.new_price));
                $('#selling-price').text(numberWithCommas(dataValue.wainventoryitem.selling_price));
                $('#product-name, #issue-route, #issue-salesman, #issue-customer, #issue-route-title, #issue-salesman-title, #issue-customer-title, #product-name-title')
                    .hide();
                if (dataValue.image == null) {
                    $('#issue-image').hide()
                        .hide();
                } else {
                    $('#issue-image').attr('src', '{{ asset('uploads/shift_issues/') }}/' + dataValue.image)
                        .show();
                }
                $('#issue-scenario, #item-description, #new-price, #selling-price, #issue-scenario-title, #item-description-title, #new-price-title, #selling-price-title')
                    .show();
            } else if (dataValue.display_scenario == 'Shop Closed') {
                $('#issue-scenario').text(dataValue.display_scenario);
                $('#issue-route').text(dataValue.route);
                $('#issue-salesman').text(dataValue.salesman);
                $('#issue-customer').text(dataValue.customer);
                $('#item-description').text(dataValue.description);
                if (dataValue.image == null) {
                    $('#issue-image').hide()
                        .hide();
                } else {
                    $('#issue-image').attr('src', '{{ asset('uploads/shift_issues/') }}/' + dataValue.image)
                        .show();
                }
                $('#product-name, #new-price, #selling-price, #item-description, #new-price-title, #selling-price-title, #item-description-title, #product-name-title')
                    .hide();
                $('#issue-scenario, #issue-route, #issue-salesman, #issue-customer, #issue-scenario-title, #issue-route-title, #issue-salesman-title, #issue-customer-title')
                    .show();
            } else if (dataValue.display_scenario == 'New Product') {
                $('#issue-scenario').text(dataValue.display_scenario);
                $('#product-name').text(dataValue.product_name);
                $('#issue-route').text(dataValue.route);
                $('#issue-salesman').text(dataValue.salesman);
                $('#issue-customer').text(dataValue.customer);
                $('#new-price, #selling-price, #item-description, #new-price-title, #selling-price-title, #item-description-title')
                    .hide();
                if (dataValue.image == null) {
                    $('#issue-image').hide()
                        .hide();
                } else {
                    $('#issue-image').attr('src', '{{ asset('uploads/shift_issues/') }}/' + dataValue.image)
                        .show();
                }
                $('#product-name, #issue-route, #issue-salesman, #issue-customer, #product-name-title, #issue-route-title, #issue-salesman-title, #issue-customer-title')
                    .show();
            }

            let imageUrl = '{{ asset('uploads/shift_issues/') }}/' + dataValue.image;
            checkImageExists(imageUrl, function(exists) {
                if (dataValue.image === null || !exists) {
                    $('#issue-image').hide();
                } else {
                    $('#issue-image').attr('src', imageUrl).show();
                }
            })

        })

        function checkImageExists(imageUrl, callback) {
            var img = new Image();
            img.onload = function() {
                callback(true);
            };
            img.onerror = function() {
                callback(false);
            };
            img.src = imageUrl;
        }

        $(document).ready(function() {

            $('#resolve-issue-modal').on('show.bs.modal', function(event) {

                let triggeringButton = $(event.relatedTarget);
                let dataValue = triggeringButton.data('issue');
                console.log('dataValue', dataValue)

                let date = new Date();
                date.setTime(date.getTime() + (2 * 60 * 1000));
                let expires = "; expires=" + date.toGMTString();

                document.cookie = 'issue' + "=" + dataValue + expires + "; path=/";
                // console.log('dataValue', dataValue.wainventoryitem.id)

                $('#reported-issue-id').val(dataValue.id);
                $('#product_id').val(dataValue.wainventoryitem.id);

                if (dataValue.display_scenario == 'No Order') {
                    console.log(dataValue.display_scenario)
                    $('#reported-issue-scenario').text(dataValue.display_scenario);
                    $('#reported-issue-route').text(dataValue.route);
                    $('#reported-issue-salesman').text(dataValue.salesman);
                    $('#reported-issue-customer').text(dataValue.customer);
                    $('#reported-resolved-description').text(dataValue.resolvedDescription);
                    $('#reported-issue-scenario, #reported-issue-route, #reported-issue-salesman, #reported-issue-customer, #reported-resolved-description, #reported-resolved-description-title, #reported-issue-scenario-title, #reported-issue-route-title, #reported-issue-salesman-title, #reported-issue-customer-title')
                        .show();
                    $('#show-shop-closed-data, #reported-product-name, #reported-issue-original-image, #reported-new-price, #reported-selling-price, #reported-issue-image, #reported-item-description, #reported-product-name-title, #reported-new-price-title, #reported-selling-price-title, #reported-issue-image-title, #reported-item-description-title')
                        .hide();
                } else if (dataValue.display_scenario == 'Price Conflict') {
                    $('#reported-issue-scenario').text(dataValue.display_scenario);
                    $('#reported-item-description').text(dataValue.wainventoryitem.description);
                    $('#reported-new-price').text(numberWithCommas(dataValue.new_price));
                    $('#reported-selling-price').text(numberWithCommas(dataValue.wainventoryitem
                        .selling_price));
                    $('#reported-resolved-description').text(dataValue.resolvedDescription);
                    $('#show-shop-closed-data, #reported-product-name, #reported-issue-original-image, #reported-issue-route, #reported-issue-salesman, #reported-issue-customer, #reported-issue-route-title, #reported-issue-salesman-title, #reported-issue-customer-title, #reported-product-name-title')
                        .hide();
                    if (dataValue.image == null) {
                        $('#reported-issue-image').hide()
                            .hide();
                    } else {
                        $('#reported-issue-image').attr('src', '{{ asset('uploads/shift_issues/') }}/' +
                                dataValue
                                .image)
                            .show();
                    }
                    $('#reported-issue-scenario, #reported-resolved-description, #reported-resolved-description-title, #reported-item-description, #reported-new-price, #reported-selling-price, #reported-issue-scenario-title, #reported-item-description-title, #reported-new-price-title, #reported-selling-price-title')
                        .show();
                } else if (dataValue.display_scenario == 'Shop Closed') {
                    $('#reported-issue-scenario').text(dataValue.display_scenario);
                    $('#reported-issue-route').text(dataValue.route);
                    $('#reported-issue-salesman').text(dataValue.salesman);
                    $('#reported-issue-customer').text(dataValue.customer);
                    $('#reported-item-description').text(dataValue.description);
                    $('#reported-resolved-description').text(dataValue.resolvedDescription);
                    if (dataValue.image == null) {
                        $('#reported-issue-image, #reported-issue-original-image').hide()
                            .hide();
                    } else {
                        $('#reported-issue-image').attr('src', '{{ asset('uploads/shift_issues/') }}/' +
                                dataValue
                                .image)
                            .show();
                        $('#reported-issue-original-image').attr('src', '{{ asset('uploads/shops/') }}/' +
                                dataValue
                                .original_image)
                            .show();
                    }
                    $('#reported-product-name, #reported-new-price, #reported-selling-price, #reported-item-description, #reported-new-price-title, #reported-selling-price-title, #reported-item-description-title, #reported-product-name-title')
                        .hide();
                    $('#reported-issue-scenario, #reported-resolved-description, #reported-resolved-description-title, #reported-issue-route, #reported-issue-salesman, #reported-issue-customer, #reported-issue-scenario-title, #reported-issue-route-title, #reported-issue-salesman-title, #reported-issue-customer-title')
                        .show();
                } else if (dataValue.display_scenario == 'New Product') {
                    $('#reported-issue-scenario').text(dataValue.display_scenario);
                    $('#reported-product-name').text(dataValue.product_name);
                    $('#reported-issue-route').text(dataValue.route);
                    $('#reported-issue-salesman').text(dataValue.salesman);
                    $('#reported-issue-customer').text(dataValue.customer);
                    $('#reported-resolved-description').text(dataValue.resolvedDescription);
                    $('#show-shop-closed-data, #reported-new-price, #reported-issue-original-image, #reported-selling-price, #reported-item-description, #reported-new-price-title, #reported-selling-price-title, #reported-item-description-title')
                        .hide();
                    if (dataValue.image == null) {
                        $('#reported-issue-image').hide()
                            .hide();
                    } else {
                        $('#reported-issue-image').attr('src', '{{ asset('uploads/shift_issues/') }}/' +
                                dataValue
                                .image)
                            .show();
                    }
                    $('#reported-product-name, #reported-resolved-description, #reported-resolved-description-title, #reported-issue-route, #reported-issue-salesman, #reported-issue-customer, #reported-product-name-title, #issue-route-title, #reported-issue-salesman-title, #reported-issue-customer-title')
                        .show();
                }

                let imageUrl = '{{ asset('uploads/shift_issues/') }}/' + dataValue.image;
                checkImageExists(imageUrl, function(exists) {
                    if (dataValue.image === null || !exists) {
                        $('#reported-issue-image').hide();
                    } else {
                        $('#reported-issue-image').attr('src', imageUrl).show();
                    }
                })

            })

            $(document).on('click', '#submit_resolution', function(event) {
                event.preventDefault()

                let $button = $(this);
                let originalText = $button.text();


                let issueData = {
                    productName: $('#reported-product-name').text(),
                    issueScenario: $('#reported-issue-scenario').text(),
                    route: $('#reported-issue-route').text(),
                    salesman: $('#reported-issue-salesman').text(),
                    customer: $('#reported-issue-customer').text(),
                    itemDescription: $('#reported-item-description').text(),
                    competitorPrice: $('#reported-new-price').text(),
                    sellingPrice: $('#reported-selling-price').text(),
                    resolvedDescription: $('#reported-resolved-description').val(),
                    issueId: $('#reported-issue-id').val(),
                    productId: $('#product_id').val()
                };
                $button.prop('disabled', true).text('Loading...');

                $.ajax({
                    url: '{{ route('resolve.salesman.reported.issue') }}',
                    type: 'POST',
                    data: issueData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        const successMessage = response.message ||
                            "Issue Resolved";
                        Toast.fire({
                            icon: "success",
                            title: successMessage
                        });
                        if (issueData.issueScenario === 'Price Conflict') {
                            // window.location.replace('{{ route('maintain-items.index') }}');
                            $('#view-issue-modal').modal('hide');
                            $('#resolve-issue-modal').modal('hide');
                            const newWindow = window.open('about:blank', '_blank');
                            if (newWindow) {
                                const maintainItemsRoute =
                                    '{{ route('maintain-items.index') }}';
                                const productId = $('#product_id').val();
                                const redirectUrl = productId ?
                                    `${maintainItemsRoute}?productId=${productId}` :
                                    maintainItemsRoute;
                                newWindow.location.href = redirectUrl;
                            }

                            // if (newWindow) {
                            //     newWindow.location.href =
                            //         '{{ route('maintain-items.index') }}';
                            // }
                        } else {
                            $('#view-issue-modal').modal('hide');
                            $('#resolve-issue-modal').modal('hide');
                            location.reload();
                        }
                    },
                    error: function(xhr) {
                        const errorMessage = xhr.responseText.message ||
                            "An error occurred. Please try again.";
                        const errorTitle = "Error";
                        Toast.fire({
                            icon: "error",
                            title: errorTitle + ": " + errorMessage
                        });
                        console.error(xhr.responseText);
                    },
                    complete: function() {
                        $button.prop('disabled', false).text(originalText);
                    }
                });
            });

            $('#resolve-issue-modal').on('hidden.bs.modal', function() {
                $('#reported-resolved-description').val('');
            });
        });
    </script>
@endsection
