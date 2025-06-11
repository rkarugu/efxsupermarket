@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <h4 class="box-title">Trade Discount Demands Report</h4>
            </div>
            <div class="box-header with-border">
                <div class="row">
                    <form>
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
                                <option value="">All Suppliers</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select name="status" id="status" class="form-control">
                                <option value="">All</option>
                                <option value="pending">Pending</option>
                                <option value="processed">Processed</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary" name="download" value="excel">
                                    <i class="fa fa-file-excel"></i> Excel
                                </button>
                                <button type="submit" class="btn btn-primary" name="download" value="pdf">
                                    <i class="fa fa-file-pdf"></i> PDF
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="box-body">
                <table class="table" id="tradeDiscountDemandsDataTable">
                    <thead>
                        <tr>
                            <th>Demand No</th>
                            <th>Supplier</th>
                            <th>Reference</th>
                            <th>CU Invoice No.</th>
                            <th>Note Date</th>
                            <th>Memo</th>
                            <th>Processed</th>
                            <th>Date Processed</th>
                            <th>Credit Note No.</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="9">Total</th>
                            <th class="text-right" id="totalDemandAmount"></th>
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
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            var form = new Form();

            $('select.form-control').select2();

            $("#supplier, #status").change(function() {
                refreshTable();
            });

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

            let demands = $("#tradeDiscountDemandsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: "{{ route('trade-discount-demands-report.index') }}",
                    data: function(data) {
                        data.from = $("#startDate").val()
                        data.to = $("#endDate").val()
                        data.supplier = $("#supplier").val()
                        data.status = $("#status").val()
                    }
                },
                columns: [{
                    data: "demand_no",
                    name: "demand_no",
                }, {
                    data: "supplier_name",
                    name: "suppliers.name",
                }, {
                    data: "supplier_reference",
                    name: "supplier_reference",
                }, {
                    data: "cu_invoice_number",
                    name: "cu_invoice_number",
                }, {
                    data: "note_date",
                    name: "note_date",
                }, {
                    data: "memo",
                    name: "memo",
                }, {
                    data: "status",
                    name: "status",
                }, {
                    data: "processed_at",
                    name: "processed_at",
                }, {
                    data: "credit_note_no",
                    name: "credit_note_no",
                }, {
                    data: "amount",
                    name: "trade_discounts.amount",
                    className: "text-right",
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalDemandAmount").html(json.total_amount);
                }
            })
        })

        function refreshTable() {
            $("#tradeDiscountDemandsDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
