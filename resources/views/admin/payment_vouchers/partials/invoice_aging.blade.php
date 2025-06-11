<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>Invoice Aging</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="invoice_aging_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="invoice_aging_location" class="form-control"
                        @disabled(true)>
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
    <table class="table table-striped" id="invoiceAgingDataTable">
        <thead>
            <tr>
                <th>Invoice Date</th>
                <th>GRN No.</th>
                <th>CU Invoice No</th>
                <th>Supplier Reference</th>
                <th>VAT Amount</th>
                <th>Total Amount</th>
                <th>Withholding Amount</th>
                <th>Credit Amount</th>
                <th>Payable Amount</th>
                <th>Days Pending</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th colspan="8" class="text-right">Total:</th>
                <th id="invoiceAgingBalance"></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {

            $("#invoice_aging_location").change(function() {
                refreshInvoiceAgingTable();
            });

            $("#invoiceAgingDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [9, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-invoice-aging') !!}',
                    data: function(data) {
                        data.location = $("#grn_invoice_location").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                        data.items = {!! $items !!};
                    }
                },
                columns: [{
                        data: 'supplier_invoice_date',
                        name: 'supplier_invoice_date'
                    }, {
                        data: 'grn_number',
                        name: 'grn_number'
                    },
                    {
                        data: 'supplier_invoice_number',
                        name: 'supplier_invoice_number'
                    },
                    {
                        data: 'cu_invoice_number',
                        name: 'cu_invoice_number'
                    },
                    {
                        data: 'vat_amount',
                        name: 'trans.vat_amount',
                        className: "text-right"
                    },
                    {
                        data: 'total_amount_inc_vat',
                        name: 'trans.total_amount_inc_vat',
                        className: "text-right"
                    },
                    {
                        data: 'withholding_amount',
                        name: 'trans.withholding_amount',
                        className: "text-right"
                    },
                    {
                        data: 'note_amount',
                        name: 'notes.note_amount',
                        className: "text-right"
                    },
                    {
                        data: 'payable_amount',
                        name: 'payable_amount',
                        className: "text-right",
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'days_pending',
                        name: 'days_pending',
                        searchable: false,
                    },
                ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#invoiceAgingBalance").html(json.total_payable);
                }
            });
        })

        function refreshInvoiceAgingTable() {
            $("#invoiceAgingDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
