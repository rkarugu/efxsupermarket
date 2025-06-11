@extends('layouts.admin.admin')

<?php
$logged_user_info = getLoggeduserProfile();
$my_permissions = $logged_user_info->permissions;
$route_name = \Route::currentRouteName();
$can_sort_reports = $logged_user_info->role_id == 1 || isset($my_permissions['reports-category___sort-report']);
$can_show_fields = $logged_user_info->role_id == 1 || isset($my_permissions['reports-category___show-fields']);
$mainpermission = $mainpermission;
?>

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="row">
                <div class="col-md-6">
                    <a href="{!! route('trial-balances.detailed') !!}"></a>
                </div>
            </div>
            <div class="box-header with-border no-padding-h-b">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title report-main-title">{{ $reports->module_title }} Reports</h3>
                    @if ($logged_user_info->role_id == 1 || isset($my_permissions['reports-category___create-category']))
                        <div id="buttonGroup" class="d-flex justify-content-end">
                            <input type="submit" class="btn btn-primary" id="createCategory" value="Create Category">
                            <input type="text" class="form-control" id="categoryInput" style="display:none;margin-right:5px">
                            <input type="submit" class="btn btn-success" id="submitCategory" value="Submit"
                                style="display:none;margin-right:5px">
                            <input type="button" class="btn btn-success" id="clearCategory" value="Clear"
                                style="display:none;">
                        </div>
                    @endif
                </div>
                @include('message')
            </div>

            <div class="box-body">
                <div class="row" id="new-category">
                    @foreach ($reports->modulereportcategories as $category)
                        <div class="col-md-4" data-category-id="{{ $category->id }}">
                            <ul class="list-group" style="cursor: pointer" id="route-data">
                                <li class="list-group-item">
                                    <span class="report-main-title-black">
                                        <div class="category-container" style="margin-bottom: 5px">
                                            <div class="d-flex justify-content-between">
                                                <p class="category-title"
                                                    data-category-title="{{ $category->category_title }}">
                                                    {{ $category->category_title }}
                                                </p>
                                                @if ($logged_user_info->role_id == 1 || isset($my_permissions['reports-category___create-report']))
                                                    <button class="btn btn-primary btn-sm add-report-btn"
                                                        data-category-id="{{ $category->id }}">+</button>
                                                    <input type="text" class="form-control category-input"
                                                        style="display: none;" name="category_title">
                                                @endif
                                            </div>
                                        </div>
                                        <form class="form-inline search-form">
                                            <input style="width:100%" type="text" class="form-control search-input"
                                                placeholder="Search reports...">
                                        </form>
                                    </span>
                                </li>
                                <ul class="sortable-list">
                                    @foreach ($category->modulereports as $item)
                                        <?php
                                        $report_permission = $item->report_permission ?? $mainpermission;
                                        ?>
                                        @if ($logged_user_info->role_id == 1 || isset($my_permissions[$report_permission]))
                                            <li class="list-group-item @if (isset($model) && $model == $item->report_model) activvve @endif"
                                                data-report-id="{{ $item->id }}">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex justify-content-start">
                                                        <a href="{{ route($item->report_route) }}" class="report-link" target="_blank">
                                                            <span
                                                                class="report-title has-permission">{{ $item->report_title }}</span>
                                                        </a>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                    </div>
                                                </div>
                                            </li>
                                        @else
                                            <li class="list-group-item @if (isset($model) && $model == $item->report_model) activvve @endif"
                                                data-report-id="{{ $item->id }}">
                                                <div class="d-flex justify-content-between">
                                                    <div class="d-flex justify-content-start">
                                                        <span
                                                            class="report-title no-permission">{{ $item->report_title }}</span>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                    </div>
                                                </div>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </ul>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="modal fade" id="addReportModal" tabindex="-1" role="dialog" aria-labelledby="addReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addReportModalLabel">Edit Category</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="addReportForm">
                            <div class="form-group">
                                <label for="categoryTitleData">Category Title</label>
                                <input type="text" class="form-control" id="categoryTitleData" name="category_title">
                            </div>
                            {{-- <div class="form-group">
                                <label for="reportTitle">Report Title</label>
                                <input type="text" class="form-control" id="reportTitle" name="report_title">
                            </div>
                            @if ($can_show_fields)
                                <div class="form-group">
                                    <label for="reportModel">Report Model</label>
                                    <input type="text" class="form-control" id="reportModel" name="report_model">
                                </div>
                                <div class="form-group">
                                    <label for="reportPermission">Report Permission</label>
                                    <input type="text" class="form-control" id="reportPermission"
                                        name="report_permission">
                                </div>
                                <div class="form-group">
                                    <label for="reportRoute">Report Route</label>
                                    <input type="text" class="form-control" id="reportRoute" name="report_route">
                                </div>
                            @endif --}}
                            <input type="hidden" id="categoryID" name="module_report_category_id">
                            <input type="hidden" id="reportId" name="report_id">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="submitReportBtn">Submit</button>
                        <button type="button" class="btn btn-primary" id="updateReportBtn">Update</button>
                    </div>
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

        .report-title {
            color: black;
            font-weight: normal;
        }

        .report-main-title {
            font-weight: bolder;
            font-size: 14px;
            color: black;
        }

        .report-main-title-black {
            color: black;
            font-weight: bolder
        }

        .category-input {
            display: none;
        }

        .category-title {
            opacity: 1;
        }

        .category-title+.category-input:visible {
            opacity: 0;
        }

        .list-group .sortable-list {
            padding-left: 0;
            margin-left: 0;
            width: 100%;
            list-style: none;
        }

        .list-group .sortable-list .list-group-item {
            padding-left: 15px;
            margin-bottom: 5px;
        }

        .list-group .sortable-list>li {
            margin-bottom: 5px;
        }

        .list-group .sortable-list {
            border: none;
            background-color: transparent;
        }

        .has-permission {
            color: #1B75D0;
            cursor: pointer;
        }

        .has-permission:hover {
            text-decoration: underline;
        }

        .no-permission {
            color: gray;
            cursor: not-allowed;
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>

    <script>
        function showDataResponse(response) {
            location.reload()
            // var divContent =
            //     '<div class="col-md-4" data-category-id="' + response.modeulereportcategory.id +
            //     '"><ul class="list-group" style="cursor: pointer"><li class="list-group-item"><span class="report-main-title-black"><div class="category-container" style="margin-bottom: 5px"><div class="d-flex justify-content-between"><p class="category-title">' +
            //     response.modeulereportcategory.category_title +
            //     '</p><button class="btn btn-primary btn-sm add-report-btn" data-category-id="' + response
            //     .modeulereportcategory.id + '" onClick="openModal(' + response.modeulereportcategory.id +
            //     ')">+</button><input type="text" class="form-control category-input" style="display: none;" name="category_title"></div></div><form class="form-inline search-form"><input style="width:100%" type="text" class="form-control search-input" placeholder="Search reports..."></form></span></li></ul></div>';
            // $('#new-category').append(divContent);
        }

        function showReportDataResponse() {
            location.reload();
        }

        function clearFormFields() {
            $('#addReportForm input[type="text"]').val('');
            $('#categoryID').val('');
        }

        function openModal(id, categoryTitle) {
            clearFormFields();
            $('#categoryID').val(id);
            $('#categoryTitle').val(categoryTitle);
            $('#categoryTitleData').val(categoryTitle)
            $('#addReportModal').modal('show');
        }

        $(document).ready(function() {
            var mainpermission = "{{ $mainpermission }}";

            function extractCategoryTitle(fullTitle) {
                var parts = fullTitle.split(' ');
                if (parts.length > 1) {
                    parts.shift();
                    return parts.join(' ');
                }
                return fullTitle;
            }

            $('#createCategory').click(function() {
                $('#categoryInput, #submitCategory, #clearCategory').toggle();
                $(this).hide();
            });

            $('#clearCategory').click(function() {
                $('#categoryInput').val('');
                $(this).hide();
                $('#categoryInput').hide();
                $('#submitCategory').hide();
                $('#createCategory').show();
            });

            $('.add-report-btn').click(function() {
                var categoryId = $(this).data('category-id');
                var categoryTitle = $(this).closest('.category-container').find('.category-title').text()
                    .trim();
                $('#categoryTitleData').val(categoryTitle);
                $('#submitReportBtn').show();
                $('#updateReportBtn').hide();
                openModal(categoryId, categoryTitle);
            });

            $('#submitCategory').click(function() {
                var categoryTitle = $('#categoryInput').val();
                var sidebarTitleData = @json($sidebartitle);

                $.ajax({
                    url: "{{ route('create-purchaes-reports-category') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        category_title: categoryTitle,
                        sidebar_title: sidebarTitleData
                    },
                    success: function(response) {
                        showDataResponse(response);
                    },
                    error: function(xhr, status, error) {}
                });

                $('#categoryInput, #submitCategory').toggle();
                $('#createCategory').show();
            });

            $('#submitReportBtn').click(function() {
                var formData = $('#addReportForm').serialize();
                $.ajax({
                    url: "{{ route('create-purchases-reports') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData,
                    success: function(response) {
                        showReportDataResponse();
                        $('#addReportModal').modal('hide');
                    },
                    error: function(xhr, status, error) {}
                });
            });

            $('.search-form .search-input').on('keyup', function() {
                var query = $(this).val().toLowerCase();
                $(this).closest('.list-group').find('li').each(function() {
                    var $this = $(this);
                    if ($this.hasClass('search-form') || $this.find('form').length > 0) {
                        return;
                    }
                    var title = $this.find('.report-title').text().toLowerCase();
                    if (title.indexOf(query) > -1) {
                        $this.show();
                    } else {
                        $this.hide();
                    }
                });
            });

            $('.category-input').on('blur keydown', function(e) {
                if (e.type === 'keydown' && e.keyCode === 13) {
                    e.preventDefault();
                    $(this).siblings('.category-title').css('opacity', 1);
                    $(this).toggle();
                }
            });

            @if ($can_sort_reports || $logged_user_info->role_id == 1)
                $('.sortable-list').each(function() {
                    new Sortable($(this)[0], {
                        group: 'reports',
                        sort: true,
                        animation: 150,
                        onEnd: function(evt) {
                            var newCategoryId = $(evt.to).closest('.col-md-4').data(
                                'category-id');
                            var reportId = $(evt.item).data('report-id');
                            var newIndex = evt.newIndex;

                            $.ajax({
                                url: "{{ route('update-purchaes-reports-position') }}",
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                        'content')
                                },
                                data: {
                                    category_id: newCategoryId,
                                    report_id: reportId,
                                    new_index: newIndex
                                },
                                success: function(response) {
                                    console.log(
                                        'Report position updated successfully.');
                                },
                                error: function(xhr, status, error) {
                                    console.error(
                                        'Error updating report position.');
                                }
                            });
                        }
                    });
                });
            @endif


            $('.edit-report-btn').click(function() {
                var reportId = $(this).data('report-id');
                $('#submitReportBtn').hide()
                $('#updateReportBtn').show()

                $.ajax({
                    url: "{{ route('get-report-details') }}",
                    method: 'GET',
                    data: {
                        report_id: reportId
                    },
                    success: function(response) {
                        $('#reportTitle').val(response.report_title);
                        $('#reportModel').val(response.report_model);
                        var reportPermission = response.report_permission ? response
                            .report_permission : mainpermission;
                        $('#reportPermission').val(reportPermission)
                        $('#reportRoute').val(response.report_route);
                        $('#categoryID').val(response.module_report_category_id);
                        $('#reportId').val(response.id)
                        $('#categoryTitleData').val(response.modulereportcategory
                            .category_title)

                        $('#addReportModal').modal('show');
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching report details.');
                    }
                });
            });

            $('#updateReportBtn').click(function() {
                event.preventDefault();
                var formData = $('#addReportForm').serialize();
                var reportId = $('#reportId')
                    .val();
                $.ajax({
                    url: "{{ route('update-purchaes-reports') }}",
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: formData + '&report_id=' +
                        reportId,
                    success: function(response) {
                        showReportDataResponse();
                        $('#addReportModal').modal('hide');
                    },
                    error: function(xhr, status, error) {}
                });
            });

            $('.delete-report-btn').click(function() {
                var reportId = $(this).data('report-id');

                $.ajax({
                    url: "{{ route('delete-report-details') }}",
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        report_id: reportId
                    },
                    success: function(response) {
                        showReportDataResponse();
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching report details.');
                    }
                });
            });
        });
    </script>
@endpush
