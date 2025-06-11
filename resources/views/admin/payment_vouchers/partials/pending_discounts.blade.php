<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>Pending Discounts</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="discounts_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="discounts_location" class="form-control" @disabled(true)>
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
    <table class="table table-bordered" id="pendingDiscountsDataTable">
        <thead>
            <tr>
                <th>Ref</th>
                <th>Discount Type</th>
                <th>Invoice No.</th>
                <th>Invoice Date</th>
                <th>Demand No.</th>
                <th>Description</th>
                <th>Prepared By</th>
                <th>Approval</th>
                <th>Invoice Amount</th>
                <th>Disc. Amount</th>
                <th>Approved Amount</th>
                <th></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th class="text-right" colspan="8">Total</th>
                <th class="text-right" id="totalInvoiceAmount"></th>
                <th class="text-right" id="totalAmount"></th>
                <th class="text-right" id="totalApprovedAmount"></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#discounts_location").change(function() {
                refreshDiscountsTable()
            });

            $("#pendingDiscountsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-discounts') !!}',
                    data: function(data) {
                        data.location = $("#discounts_location").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                        data.invoices = {!! $invoices !!};
                    }
                },
                columns: [{
                    data: "id",
                    name: "id",
                }, {
                    data: "discount_type",
                    name: "agreements.discount_type",
                }, {
                    data: "supplier_invoice_number",
                    name: "supplier_invoice_number",
                }, {
                    data: "invoice_date",
                    name: "invoice_date",
                }, {
                    data: "demand_no",
                    name: "demands.demand_no",
                }, {
                    data: "description",
                    name: "description",
                }, {
                    data: "prepared_by.name",
                    name: "preparedBy.name",
                }, {
                    data: "status",
                    name: "status",
                }, {
                    data: "invoice_amount",
                    name: "trade_discounts.invoice_amount",
                    className: "text-right",
                }, {
                    data: "amount",
                    name: "trade_discounts.amount",
                    className: "text-right",
                }, {
                    data: "approved_amount",
                    name: "approved_amount",
                    className: "text-right",
                }, {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalAmount").html(json.total_amount);
                    $("#totalInvoiceAmount").html(json.total_invoice_amount);
                    $("#totalApprovedAmount").html(json.total_approved_amount);
                }
            });
        })

        function refreshDiscountsTable() {
            $("#pendingDiscountsDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
