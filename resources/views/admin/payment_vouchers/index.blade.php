@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <h4 class="box-title">Payment Vouchers</h4>
            </div>
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-sm-3">
                        <input type="hidden" id="startDate">
                        <input type="hidden" id="endDate">
                        <div class="reportRange">
                            <i class="fa fa-calendar" style="padding:8px"></i>
                            <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                            <i class="fa fa-caret-down" style="padding:8px"></i>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <select name="supplier" id="supplier" class="form-control">
                            <option value="">Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <select name="status" id="status" class="form-control">
                                <option value="pending" @selected(request()->status == 'pending')>Pending</option>
                                <option value="approved" @selected(request()->status == 'approved')>Approved</option>
                                <option value="paid" @selected(request()->status == 'paid')>Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped" id="vouchersDataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Voucher No.</th>
                            <th>Date Created</th>
                            <th>Supplier</th>
                            <th>Account</th>
                            <th>Payment Mode</th>
                            <th class="bank-file">Bank File</th>
                            <th>Items</th>
                            <th>Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th colspan="8" class="text-right" id="totalLabel">Total</th>
                            <th class="text-right" id="totalAmount"></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
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
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {
            $("select.form-control").select2();

            $("#supplier, #status").change(function() {
                refreshTable();
            })

            $('.reportRange').daterangepicker({
                alwaysShowCalendars: true,
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

            var table = $("#vouchersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.index') !!}',
                    data: function(data) {
                        data.supplier = $("#supplier").val();
                        data.status = $("#status").val();
                        data.start_date = $("#startDate").val();
                        data.end_date = $("#endDate").val();
                    }
                },
                columns: [{
                        data: "id",
                        name: "id"
                    },
                    {
                        data: "number",
                        name: "number"
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        data: "supplier.name",
                        name: "supplier.name"
                    },
                    {
                        data: "account.account_name",
                        name: "account.account_name"
                    },
                    {
                        data: "payment_mode.mode",
                        name: "paymentMode.mode"
                    },
                    {
                        data: "file_no",
                        name: "bank_files.file_no",
                        className: "bank-file",
                    },
                    {
                        data: "voucher_items_count",
                        name: "voucher_items_count",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: "amount",
                        name: "amount",
                        className: "text-right"
                    },
                    {
                        data: "actions",
                        name: "actions",
                        searchable: false,
                        orderable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalAmount").html(json.total_amount);
                }
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $('#vouchersDataTable tbody').on('click', 'td.details-control', function() {
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

            $('#vouchersDataTable tbody').on('click', '[data-toggle="vouchers"]', function() {
                let action = $(this).data('action');
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to ' + action + ' voucher?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, I Confirm',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            });

            toggleColumns(table, $('#status').val())
            adjustFooterColspan(table);

            $('#status').on('change', function() {
                var status = $(this).val();

                toggleColumns(table, status)
                adjustFooterColspan(table);
            });
        });

        function toggleColumns(table, status) {
            if (status === 'paid') {
                table.column(6).visible(true);
            } else {
                table.column(6).visible(false);
            }
        }

        function adjustFooterColspan(table) {
            var visibleColumns = table.columns(':visible').count();
            $('#totalLabel').attr('colspan', visibleColumns - 2);
        }

        function refreshTable() {
            $("#vouchersDataTable").DataTable().ajax.reload();
        }

        function rowDetails(data) {
            let table = `<table class="table table-bordered table-striped" style="margin-top:10px">
                            <thead>
                                <tr>
                                    <th>Date Due</th>
                                    <th>Reference No.</th>
                                    <th>CU Invoice No.</th>
                                    <th>Tax</th>
                                    <th>Withholding Tax</th>
                                    <th>Amt to Pay</th>
                                </tr>
                            </thead>
                            <tbody>`;

            data.voucher_items.forEach(function(item) {
                table += `<tr>
                            <td>` + item.payable.due_date + `</td>
                            <td>` + item.payable?.suppreference + `</td>
                            <td>` + item.payable?.cu_invoice_number + `</td>
                            <td>` + Number(item.tax_amount).formatMoney() + `</td>
                            <td>` + Number(item.withholding_amount).formatMoney() + `</td>
                            <td>` + Number(item.amount).formatMoney() + `</td>
                          </tr>`;
            });

            return table += `</tbody>
                </table>`;
        }
    </script>
@endpush
