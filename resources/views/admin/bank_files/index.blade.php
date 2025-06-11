@extends('layouts.admin.admin')

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header">
                @include('message')
                <div class="d-flex">
                    <h4 class="flex-grow-1">Bank Files</h4>
                    <div class="text-right">
                        <a href="{{ route('bank-files.create') }}" class="btn btn-primary">
                            Generate Bank File
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-striped" id="bankFilesDataTable">
                    <thead>
                        <tr>
                            <th></th>
                            <th>File No.</th>
                            <th>Date</th>
                            <th>Account</th>
                            <th>Vouchers</th>
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
                    url: '{!! route('bank-files.index') !!}',
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
                        name: "wa_bank_files.created_at"
                    },
                    {
                        data: "account.account_name",
                        name: "account.account_name"
                    },
                    {
                        data: "items_count",
                        name: "items_count",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: "prepared_by",
                        name: "users.name"
                    },
                    {
                        data: "amount",
                        name: "amount",
                        className: "text-right",
                    },
                    {
                        data: "actions",
                        name: "actions",
                        className: "text-center",
                        searchable: false,
                        orderable: false,
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
                    $.ajax({
                        url: "{{ route('bank-files.items') }}",
                        data: {
                            file: row.data().id
                        },
                        success: function(result) {
                            row.child(rowDetails(result.items)).show();
                            tr.addClass('shown');
                            icon.addClass('fa-minus-circle').removeClass('fa-plus-circle');
                        }
                    });
                }
            });
        });

        function refreshTable() {
            $("#bankFilesDataTable").DataTable().ajax.reload();
        }

        function rowDetails(items) {
            let table = `<table class="table table-bordered table-striped" style="margin-top:10px">
                            <thead>
                                <tr>
                                    <th>Voucher No</th>
                                    <th>Date</th>
                                    <th>Supplier</th>
                                    <th>Payment Mode</th>
                                    <th>Prepared By</th>
                                    <th class="text-right">Withholding</th>
                                    <th class="text-right">Amount</th>
                                    <th>Remittance</th>
                                </tr>
                            </thead>
                            <tbody>`;

            items.forEach(function(item) {
                table += `<tr>
                            <td>` + item.voucher.number + `</td>
                            <td>` + moment(item.voucher.created_at).format('YYYY-MM-DD') + `</td>
                            <td>` + item.voucher.supplier.name + `</td>
                            <td>` + item.voucher.payment_mode.mode + `</td>
                            <td>` + item.voucher.prepared_by.name + `</td>
                            <td class="text-right">` + Number(item.voucher.withholdingAmount).formatMoney() + `</td>
                            <td class="text-right">` + Number(item.amount).formatMoney() + `</td>
                            <td><a href="` + item.voucher.remittanceUrl + `" target="_blank">
                                <i class="fa fa-print"></i>
                            </td>
                          </tr>`;
            });

            return table += `</tbody>
                </table>`;
        }
    </script>
@endpush
