<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>GRN/Invoice Variance</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="grn_invoice_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="grn_invoice_location" class="form-control" @disabled(true)>
                        <option value="">Select Option</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected($location->id == 46)>
                                {{ $location->location_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <table class="table" id="grnsAgainstInvoicesTable">
        <thead>
            <tr>
                <th>GRN No.</th>
                <th>Supplier Invoice No</th>
                <th>GRN Date</th>
                <th>Invoice Date</th>
                <th>GRN Total Amount</th>
                <th>Invoice Total Amount</th>
                <th>Variance</th>
            </tr>
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#grn_invoice_location").change(function() {
                refreshInvoiceVarianceTable();
            });

            $("#grnsAgainstInvoicesTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-invoice-variance') !!}',
                    data: function(data) {
                        data.location = $("#grn_invoice_location").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                        data.items = {!! $items !!};
                    }
                },
                columns: [{
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
                        data: "supplier_invoice_date",
                        name: "invoices.supplier_invoice_date",
                    },
                    {
                        data: "grn_total_amount",
                        name: "grns.total_amount",
                        className: "text-right"
                    },
                    {
                        data: "amount",
                        name: "invoices.amount",
                        className: "text-right"
                    },
                    {
                        data: "variance",
                        name: "variance",
                        className: "text-right",
                        orderable: false,
                        searchable: false,
                    },
                ]
            });
        });

        function refreshInvoiceVarianceTable() {
            $("#grnsAgainstInvoicesTable").DataTable().ajax.reload();
        }
    </script>
@endpush
