@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="d-flex align-items-center">
                    <h4 class="box-title flex-grow-1">Withholding Tax Files</h4>
                    <div class="text-right">
                        <a href="{{ route('withholding-files.create') }}" class="btn btn-primary">
                            <i class="fa fa-file-excel"></i>
                            Generate Withholding File
                        </a>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="bankFilesDataTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>File No.</th>
                            <th>Date</th>
                            <th>Prepared By</th>
                            <th>Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
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

        .row-action {
            margin-right: 5px
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#account").select2()

            $("#account").change(function() {
                refreshTable();
            })

            let start = moment().subtract(30, 'days');
            let end = moment();
            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'Last 30 Days': [moment().subtract(30, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                }
            });

            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));

                refreshTable();
            });

            var table = $("#bankFilesDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('withholding-files.index') !!}',
                    data: function(data) {
                        data.account = $("#account").val();
                        data.from = $("#startDate").val();
                        data.to = $("#endDate").val();
                    }
                },
                columns: [{
                        className: 'details-control',
                        orderable: false,
                        searchable: false,
                        data: null,
                        defaultContent: '<i class="fa fa-plus-circle" style="cursor: pointer; font-size: 16px;"></i>',
                        title: '',
                        width: '20px',
                    },
                    {
                        data: "file_no",
                        name: "file_no"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "prepared_by.name",
                        name: "prepared_by.name"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "actions",
                        name: "actions",
                        searchable: false,
                        orderable: false,
                        className: "text-center"
                    },
                ],
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $('#bankFilesDataTable tbody').on('click', 'td.details-control', function() {
                let tr = $(this).closest('tr');
                let row = table.row(tr);
                let icon = $(this).find('i');

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                    icon.removeClass('fa-minus-circle').addClass('fa-plus-circle');
                } else {
                    row.child(rowDetails(row.data())).show();
                    tr.addClass('shown');
                    icon.addClass('fa-minus-circle').removeClass('fa-plus-circle');
                }
            });
        });

        function refreshTable() {
            $("#bankFilesDataTable").DataTable().ajax.reload();
        }

        function rowDetails(data) {
            console.log(data)
            let table = `<table class="table table-bordered table-striped" style="margin-top:10px">
                            <thead>
                                <tr>
                                    <th>File No</th>
                                    <th>Date</th>
                                    <th>Account</th>
                                    <th>Prepared By</th>
                                    <th>Amount</th>
                                    <th>Download</th>
                                </tr>
                            </thead>
                            <tbody>`;

            data.items.forEach(function(item) {
                table += `<tr>
                            <td>` + item.bank_file.file_no + `</td>
                            <td>` + item.bank_file.created_at + `</td>
                            <td>` + item.bank_file.account.account_name + `</td>
                            <td>` + item.bank_file.prepared_by.name + `</td>
                            <td>` + Number(item.amount).formatMoney() + `</td>
                            <td><a href="` + item.bank_file.fileUrl + `" target="_blank">
                                <i class="fa fa-print"></i>
                            </td>
                          </tr>`;
            });

            return table += `</tbody>
                </table>`;
        }
    </script>
@endpush
