@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="box-title">GRNs against Invoices Report</h4>
            </div>
            <div class="box-header with-border">
                <form>
                    <div class="row">
                        <div class="col-sm-3">
                            <label for="supplier">Supplier</label>
                            <select name="supplier" id="supplier" class="form-control">
                                <option value="">Select Option</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" @selected($supplier->id == request()->supplier)>
                                        {{ $supplier->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label style="display:block">&nbsp;</label>
                            <button class="btn btn-primary" type="submit" name="download" value="excel">
                                <i class="fa fa-file-excel"></i> Export
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <table class="table" id="grnsAgainstInvoicesTable">
                    <thead>
                        <tr>
                            <th>Supplier No</th>
                            <th>Supplier Name</th>
                            <th>GRN No.</th>
                            <th>Supplier Invoice No</th>
                            <th>GRN Date</th>
                            <th>GRN Vat Amount</th>
                            <th>GRN Total Amount</th>
                            <th>Invoice Date</th>
                            <th>Invoice Vat Amount</th>
                            <th>Invoice Total Amount</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("select.form-control").select2();

            $("#supplier").change(function() {
                refreshTable();
            });

            $("#grnsAgainstInvoicesTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('grns-against-invoices.index') !!}',
                    data: function(data) {
                        data.supplier = $("#supplier").val();
                    }
                },
                columns: [{
                        data: "supplier_code",
                        name: "suppliers.supplier_code",
                    },
                    {
                        data: "supplier_name",
                        name: "suppliers.name",
                    },
                    {
                        data: "grn_number",
                        name: "invoices.grn_number",
                    },
                    {
                        data: "supplier_invoice_number",
                        name: "invoices.supplier_invoice_number",
                    },
                    {
                        data: "delivery_date",
                        name: "grns.delivery_date"
                    },
                    {
                        data: "grn_vat_amount",
                        name: "grns.vat_amount",
                        className: "text-right"
                    },
                    {
                        data: "grn_total_amount",
                        name: "grns.total_amount",
                        className: "text-right"
                    },
                    {
                        data: "supplier_invoice_date",
                        name: "invoices.supplier_invoice_date",
                    },
                    {
                        data: "vat_amount",
                        name: "invoices.vat_amount",
                        className: "text-right"
                    },
                    {
                        data: "amount",
                        name: "invoices.amount",
                        className: "text-right"
                    },
                ]
            });
        });

        function refreshTable() {
            $("#grnsAgainstInvoicesTable").DataTable().ajax.reload();
        }
    </script>
@endpush
