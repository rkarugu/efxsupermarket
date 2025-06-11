@extends('layouts.admin.admin')

@section('content')
    <div class="content">

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Update Max Stock </h3>
                    <form action="{{ route('utility.generate_sample_excel') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary" name="action" value="download max stock">
                            <i class="fa fa-download"></i> Download Sample Max Stock .xlsx
                        </button>
                    </form>
                </div>
            </div>

            <div class="box-body">
                <form id="uploadMaxStockForm" method="post" enctype="multipart/form-data">

                    <div class="form-group col-md-3">
                        <label for="">Branch</label>
                        <select name="branch" id="mlselec6t" class="form-control mlselec6t">
                            <option value="" selected disabled>--Select Branch--</option>
                            @foreach (getStoreLocationDropdown() as $index => $store)
                                <option value="{{ $index }}" {{ request()->store == $index ? 'selected' : '' }}>
                                    {{ $store }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="upload_file" class="control-label"> Upload Excel File (.xlsx) </label>
                        <input type="file" class="form-control" name="max_stock_file" id="max_stock_file" accept=".xlsx"
                            disabled>
                        <label class="custom-file-label" id="maxStockReorderLevel"></label>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="Process Max Stock" id="process-max-stock"
                                value="Process Max Stock">
                                <i class="fa fa-cogs"></i> Process Max Stock
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2" id="update-button">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="Update Max Stock"
                                id="updateMaxStockDataBtn" value="Update Max Stock">
                                <i class="fa-solid fa-pen-to-square"></i> Update Max Stock
                            </button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <div class="box" id="tableMaxStockContainer" style="display: none;">
            <div class="box-body">
                <table class="table table-bordered" id="maxStockDataTable">
                    <thead>
                        <tr style="font-size: 12px">
                            <th>ITEM ID</th>
                            <th>STOCK ID CODE</th>
                            <th>DESCRIPTION</th>
                            <th>CURRENT MAX STOCK</th>
                            <th>SUGGESTED MAX STOCK</th>
                            <th>CURRENT RE-ORDER LEVEL</th>
                            {{-- <th>SUGGESTED REORDER LEVEL</th> --}}
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Update Reorder Level </h3>
                    <form action="{{ route('utility.generate_sample_excel') }}" method="post">
                        @csrf
                        <button type="submit" class="btn btn-primary" name="action" value="download reorder level">
                            <i class="fa fa-download"></i> Download Sample Reorder Level .xlsx
                        </button>
                    </form>
                </div>
            </div>

            <div class="box-body">
                <form id="uploadReorderLevelForm" method="post" enctype="multipart/form-data">

                    <div class="form-group col-md-3">
                        <label for="">Branch</label>
                        <select name="branch" id="mlselec7t" class="form-control mlselec7t">
                            <option value="" selected disabled>--Select Branch--</option>
                            @foreach (getStoreLocationDropdown() as $index => $store)
                                <option value="{{ $index }}" {{ request()->store == $index ? 'selected' : '' }}>
                                    {{ $store }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group col-md-3">
                        <label for="upload_file" class="control-label"> Upload Excel File (.xlsx) </label>
                        <input type="file" class="form-control" name="reorder_level_file" id="reorder_level_file"
                            accept=".xlsx" disabled>
                        <label class="custom-file-label" id="maxStockReorderLevel"></label>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="Process Reorder Level"
                                id="process-reorder-level" value="Process Reorder Level">
                                <i class="fa fa-cogs"></i> Process Reorder Level
                            </button>
                        </div>
                    </div>

                    <div class="col-md-2" id="update-button">
                        <div class="form-group">
                            <label style="display: block;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary" name="Update Reorder Level"
                                id="updateReorderLevelDataBtn" value="Update Reorder Level">
                                <i class="fa-solid fa-pen-to-square"></i> Update Reorder Level
                            </button>
                        </div>
                    </div>

                </form>
            </div>

        </div>

        <div class="box" id="tableReorderLevelContainer" style="display: none;">
            <div class="box-body">
                <table class="table table-bordered" id="reorderLevelDataTable">
                    <thead>
                        <tr style="font-size: 12px">
                            <th>ITEM ID</th>
                            <th>STOCK ID CODE</th>
                            <th>DESCRIPTION</th>
                            <th>CURRENT MAX STOCK</th>
                            <th>CURRENT RE-ORDER LEVEL</th>
                            {{-- <th>SUGGESTED MAX STOCK</th> --}}
                            <th>SUGGESTED REORDER LEVEL</th>
                        </tr>
                    </thead>
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

    <script>
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

        $(document).ready(function() {
            // $("body").addClass('sidebar-collapse');
            $(".mlselec6t").select2();
            $(".mlselec7t").select2();

            function updateMaxStockButtonState(form) {
                var selectedBranch = form.find("#mlselec6t").val();
                var fileSelected = form.find("#max_stock_file")[0].files[0];
                var button = form.find("input[name=intent]");

                if (selectedBranch && fileSelected) {
                    button.prop('disabled', false);
                } else {
                    button.prop('disabled', true);
                }
            }

            function updateReorderLevelButtonState(form) {
                var selectedBranch = form.find("#mlselec7t").val();
                var fileSelected = form.find("#reorder_level_file")[0].files[0];
                var button = form.find("input[name=intent]");

                if (selectedBranch && fileSelected) {
                    button.prop('disabled', false);
                } else {
                    button.prop('disabled', true);
                }
            }


            $("#mlselec6t").change(function() {
                var form = $(this).closest('form');
                updateMaxStockButtonState(form);
                var selectedBranch = $(this).val();
                if (selectedBranch) {
                    form.find("#max_stock_file").prop('disabled', false);
                } else {
                    form.find("#max_stock_file").prop('disabled', true);
                }
            });

            $("#mlselec7t").change(function() {
                var form = $(this).closest('form');
                updateReorderLevelButtonState(form);
                var selectedBranch = $(this).val();
                if (selectedBranch) {
                    form.find("#reorder_level_file").prop('disabled', false);
                } else {
                    form.find("#reorder_level_file").prop('disabled', true);
                }
            });

            $("#max_stock_file").change(function() {
                var form = $(this).closest('form');
                updateMaxStockButtonState(form);
            });

            $("#reorder_level_file").change(function() {
                var form = $(this).closest('form');
                updateReorderLevelButtonState(form);
            });

            $("#uploadMaxStockForm").submit(function(event) {
                event.preventDefault();

                var form = $(this);
                var file = form.find("#max_stock_file")[0].files[0];
                var selectedBranch = form.find("#mlselec6t").val();

                if (file && selectedBranch) {
                    var fileName = file.name;
                    if (fileName.endsWith(".xlsx")) {

                        let originalHtmlUpdate = $('#process-max-stock').html();
                        let buttonintent = $('#process-max-stock').val();

                        $('#process-max-stock').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Processing...');

                        var formData = new FormData();
                        formData.append('file', file);
                        formData.append('branch', selectedBranch);
                        formData.append('intent', buttonintent)

                        var token = "{{ csrf_token() }}";
                        $.ajax({
                            url: '{{ route('update-max-stock-reorder-level') }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (buttonintent === "Process Max Stock") {
                                    initializeMaxStockDataTable(response.data);
                                }
                                const successMessage = response.message ||
                                    "Inventory data updated successfully";
                                Toast.fire({
                                    icon: "success",
                                    title: successMessage
                                });
                                if (buttonintent === "Process Max Stock") {
                                    $("#tableMaxStockContainer").show();
                                    $('#process-max-stock').prop('disabled', false).html(
                                        originalHtmlUpdate);
                                }
                            },
                            error: function(xhr, status, error) {
                                const errorMessage = xhr.responseJSON.message ||
                                    "An error occurred. Please try again.";
                                const errorTitle = xhr.responseJSON.title || "Error";
                                Toast.fire({
                                    icon: "error",
                                    title: errorTitle + ": " + errorMessage
                                });
                                if (buttonintent === "Process Max Stock") {
                                    $("#tableMaxStockContainer").hide();
                                    $('#process-max-stock').prop('disabled', false).html(
                                        originalHtmlUpdate);
                                }
                            },
                            complete: function() {
                                if (buttonintent === "Process Max Stock") {
                                    $('#process-max-stock').prop('disabled', false).html(
                                        originalHtmlUpdate);
                                }
                                updateMaxStockButtonState(form);
                            }
                        });
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "Please upload a .xlsx file."
                        });
                        if (buttonintent === "Process Max Stock") {
                            $("#tableMaxStockContainer").hide();
                        }
                    }
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Please add a branch and a file"
                    });
                    if (buttonintent === "Process Max Stock") {
                        $("#tableMaxStockContainer").hide();
                    }
                }
            });

            $("#uploadReorderLevelForm").submit(function(event) {
                event.preventDefault();

                var form = $(this);
                var file = form.find("#reorder_level_file")[0].files[0];
                var selectedBranch = form.find("#mlselec7t").val();

                if (file && selectedBranch) {
                    var fileName = file.name;
                    if (fileName.endsWith(".xlsx")) {

                        let originalHtmlUpdate = $('#process-reorder-level').html();
                        let buttonintent = $('#process-reorder-level').val();

                        $('#process-reorder-level').prop('disabled', true).html(
                            '<i class="fas fa-spinner fa-spin"></i> Processing...');

                        var formData = new FormData();
                        formData.append('file', file);
                        formData.append('branch', selectedBranch);
                        formData.append('intent', buttonintent)

                        var token = "{{ csrf_token() }}";
                        $.ajax({
                            url: '{{ route('update-max-stock-reorder-level') }}',
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token
                            },
                            data: formData,
                            processData: false,
                            contentType: false,
                            success: function(response) {
                                if (buttonintent === "Process Reorder Level") {
                                    initializeReorderLevelDataTable(response.data);
                                }
                                const successMessage = response.message ||
                                    "Inventory data updated successfully";
                                Toast.fire({
                                    icon: "success",
                                    title: successMessage
                                });
                                if (buttonintent === "Process Reorder Level") {
                                    $('#process-reorder-level').prop('disabled', false).html(
                                        originalHtmlUpdate);
                                    $("#tableReorderLevelContainer").show();
                                    $("#tableMaxStockContainer").hide();
                                }
                            },
                            error: function(xhr, status, error) {
                                const errorMessage = xhr.responseJSON.message ||
                                    "An error occurred. Please try again.";
                                const errorTitle = xhr.responseJSON.title || "Error";
                                Toast.fire({
                                    icon: "error",
                                    title: errorTitle + ": " + errorMessage
                                });
                                if (buttonintent === "Process Reorder Level") {
                                    $('#process-reorder-level').prop('disabled', false).html(
                                        originalHtmlUpdate);
                                    $("#tableReorderLevelContainer").hide();
                                    $("#tableMaxStockContainer").hide();
                                }
                            },
                            complete: function() {
                                if (buttonintent === "Process Reorder Level") {
                                    $('#process-reorder-level').prop('disabled', false).html(
                                        originalHtmlUpdate);
                                    form.find("input[name=intent]").val(
                                        "Process Reorder Level");
                                }
                                updateReorderLevelButtonState(form);
                            }
                        });
                    } else {
                        Toast.fire({
                            icon: "error",
                            title: "Please upload a .xlsx file."
                        });
                        if (buttonintent === "Process Reorder Level") {
                            $("#tableReorderLevelContainer").hide();
                            $("#tableMaxStockContainer").hide();
                        }
                    }
                } else {
                    Toast.fire({
                        icon: "error",
                        title: "Please add a branch and a file"
                    });
                    if (buttonintent === "Process Reorder Level") {
                        $("#tableReorderLevelContainer").hide();
                        $("#tableMaxStockContainer").hide();
                    }
                }
            });

            $('#updateMaxStockDataBtn').on('click', function() {
                $("#max_stock_file").prop('disabled', true);
                $("#mlselec6t").prop('disabled', true);
                $("#mlselec7t").prop('disabled', true);
                $("input[name=intent]").prop('disabled', true);

                let originalHtmlUpdate = $('#updateMaxStockDataBtn').html();
                let buttonintent = $('#updateMaxStockDataBtn').val();

                $('#updateMaxStockDataBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var tableData = $('#maxStockDataTable').DataTable().data().toArray();
                $.ajax({
                    url: '{{ route('update-max-stock-reorder-level-data') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        data: JSON.stringify(tableData),
                        intent: buttonintent
                    },
                    success: function(response) {
                        const successMessage = response.message ||
                            "Inventory data updated. Redirecting...";
                        if (response) {
                            Toast.fire({
                                icon: "success",
                                title: successMessage
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        const errorMessage = xhr.responseJSON.message ||
                            "An error occurred. Please try again.";
                        const errorTitle = xhr.responseJSON.title || "Error";
                        Toast.fire({
                            icon: "error",
                            title: errorTitle + ": " + errorMessage
                        });
                    },
                    complete: function() {
                        $('#updateMaxStockDataBtn').prop('disabled', false).html(
                            originalHtmlUpdate);
                        location.reload()
                    }
                });
            });

            $('#updateReorderLevelDataBtn').on('click', function() {
                $("#reorder_level_file").prop('disabled', true);
                $("#mlselec6t").prop('disabled', true);
                $("#mlselec7t").prop('disabled', true);
                $("input[name=intent]").prop('disabled', true);

                let originalHtmlUpdate = $('#updateReorderLevelDataBtn').html();
                let buttonintent = $('#updateReorderLevelDataBtn').val();

                $('#updateReorderLevelDataBtn').prop('disabled', true).html(
                    '<i class="fas fa-spinner fa-spin"></i> Processing...');

                var tableData = $('#reorderLevelDataTable').DataTable().data().toArray();
                $.ajax({
                    url: '{{ route('update-max-stock-reorder-level-data') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        data: JSON.stringify(tableData),
                        intent: buttonintent
                    },
                    success: function(response) {
                        const successMessage = response.message ||
                            "Inventory data updated. Redirecting...";
                        if (response) {
                            Toast.fire({
                                icon: "success",
                                title: successMessage
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        const errorMessage = xhr.responseJSON.message ||
                            "An error occurred. Please try again.";
                        const errorTitle = xhr.responseJSON.title || "Error";
                        Toast.fire({
                            icon: "error",
                            title: errorTitle + ": " + errorMessage
                        });
                    },
                    complete: function() {
                        $('#updateReorderLevelDataBtn').prop('disabled', false).html(
                            originalHtmlUpdate);
                        location.reload()
                    }
                });
            });

        });

        function initializeMaxStockDataTable(data) {
            var table = $("#maxStockDataTable").DataTable({
                autoWidth: false,
                pageLength: 10,
                destroy: true,
                data: data,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'max_stock',
                        name: 'max_stock',
                        defaultContent: '0'
                    },
                    {
                        data: 'suggested_max_stock',
                        name: 'suggested_max_stock',
                        defaultContent: '0'
                    },
                    {
                        data: 're_order_level',
                        name: 're_order_level',
                        defaultContent: '0'
                    },
                ]

            });
        }

        function initializeReorderLevelDataTable(data) {
            var table = $("#reorderLevelDataTable").DataTable({
                autoWidth: false,
                pageLength: 10,
                destroy: true,
                data: data,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'stock_id_code',
                        name: 'stock_id_code'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'max_stock',
                        name: 'max_stock',
                        defaultContent: '0'
                    },
                    {
                        data: 're_order_level',
                        name: 're_order_level',
                        defaultContent: '0'
                    },
                    {
                        data: 'suggested_reorder_level',
                        name: 'suggested_reorder_level',
                        defaultContent: '0'
                    }
                ]

            });
        }
    </script>
@endpush
