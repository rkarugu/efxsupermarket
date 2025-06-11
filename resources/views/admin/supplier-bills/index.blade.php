@extends('layouts.admin.admin')

@section('content')
    <div class="box box-primary">
        <div class="box-header with-border">
            @include('message')
            <div class="d-flex">
                <h4 class="text-left flex-grow-1">Supplier Bills</h4>
                @if (can('add', 'supplier-bills'))
                    <div class="text-right">
                        <a href = "{!! route('supplier-bills.create') !!}" class = "btn btn-success">
                            <i class="fa fa-plus"></i>
                            Add Bill
                        </a>
                    </div>
                @endif
            </div>
        </div>
        <div class="box-header with-border">
            <form>
                <input type="hidden" id="startDate" name="from">
                <input type="hidden" id="endDate" name="to">
                <div class="row">
                    <div class="col-sm-3">
                        <div id="reportRange" class="reportRange">
                            <i class="fa fa-calendar" style="padding:8px"></i>
                            <span class="flex-grow-1" style="padding:8px">Select Dates</span>
                            <i class="fa fa-caret-down" style="padding:8px"></i>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="supplier" id="supplier" class="form-control select2">
                            <option value="" selected>Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @selected(request()->supplier == $supplier->id)>
                                    {{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="form-group">
                            <select name="payment" id="payment" class="form-control">
                                <option value="">Select Payment Status</option>
                                <option value="pending">Pending</option>
                                <option value="paid" @selected(request()->payment == 'paid')>Paid</option>
                            </select>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="box-body">
            <table class="table table-bordered" id="billsDataTable">
                <thead>
                    <tr>
                        <th></th>
                        <th>Bill No.</th>
                        <th>Cu Invoice No.</th>
                        <th>Supplier Invoice No.</th>
                        <th>Bill Date</th>
                        <th>Supplier</th>
                        <th>Location</th>
                        <th>Memo</th>
                        <th>Vat Tax</th>
                        <th>Withholding Tax</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Voucher No</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
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
            margin-left: 5px;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $("body").addClass('sidebar-collapse');

        $(document).ready(function() {
            $("select.form-control").select2();

            $("#supplier, #payment").change(function() {
                refreshTable();
            });

            let start = moment().subtract(30, 'days');
            let end = moment();
            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
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

            var table = $("#billsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('supplier-bills.index') !!}',
                    data: function(data) {
                        data.from = $("#startDate").val()
                        data.to = $("#endDate").val()
                        data.supplier = $("#supplier").val()
                        data.payment = $("#payment").val()
                    },
                },
                columns: [{
                    className: 'details-control',
                    orderable: false,
                    searchable: false,
                    data: null,
                    defaultContent: '<i class="fa fa-plus-circle" style="cursor: pointer; font-size: 16px;"></i>',
                    title: '',
                    width: '20px',
                }, {
                    data: 'bill_no',
                    name: 'bill_no'
                }, {
                    data: 'cu_invoice_number',
                    name: 'cu_invoice_number'
                }, {
                    data: 'supplier_invoice_number',
                    name: 'supplier_invoice_number'
                }, {
                    data: 'bill_date',
                    name: 'bill_date'
                }, {
                    data: 'supplier.name',
                    name: 'supplier.name'
                }, {
                    data: 'location.name',
                    name: 'location.name'
                }, {
                    data: 'memo',
                    name: 'memo'
                }, {
                    data: 'tax_amount',
                    name: 'tax_amount',
                    className: 'text-right',
                }, {
                    data: 'withholding_amount',
                    name: 'withholding_amount',
                    className: 'text-right',
                }, {
                    data: 'amount',
                    name: 'amount',
                    className: 'text-right',
                }, {
                    data: 'status',
                    name: 'status',
                }, {
                    data: 'voucher_no',
                    name: 'vouchers.number',
                    className: 'text-right',
                }, {
                    data: 'action',
                    name: 'action',
                    className: 'text-center',
                }],
            });

            table.on('draw', function() {
                $('[data-toggle="tooltip"]').tooltip();
            });

            $('#billsDataTable tbody').on('click', 'td.details-control', function() {
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

        function rowDetails(data) {
            let table = `<table class="table table-bordered table-striped" style="margin-top:10px">
                            <thead>
                                <tr>
                                    <th>Account Name</th>
                                    <th>Memo</th>
                                    <th>Tax Rate</th>
                                    <th>Tax Amount</th>
                                    <th>Withholding Tax</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>`;

            data.items.forEach(function(item) {
                table += `<tr>
                            <td>` + item.account.account_name + `</td>
                            <td>` + item.memo + `</td>
                            <td class="text-right">` + Number(item.tax_rate).formatMoney() + `</td>
                            <td class="text-right">` + Number(item.tax_amount).formatMoney() + `</td>
                            <td class="text-right">` + Number(item.withholding_amount).formatMoney() + `</td>
                            <td class="text-right">` + Number(item.amount).formatMoney() + `</td>
                          </tr>`;
            });

            return table += `</tbody>
                </table>`;
        }

        function refreshTable() {
            $("#billsDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
