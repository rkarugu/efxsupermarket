<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>Price Drop Demands</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="pricedrop_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="pricedrop_location" class="form-control" @disabled(true)>
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
    <table class="table table-bordered" id="pricedropDemandsDataTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Demand No.</th>
                <th>Items</th>
                <th>Approved</th>
                <th>Credit Note</th>
                <th>Vat Amount</th>
                <th>Total Amount</th>
                <th>New Amount</th>
                <th></th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <th class="text-right" colspan="6">Total</th>
                <th class="text-right" id="totalDropAmount"></th>
                <th class="text-right" id="totalNewDropAmount"></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#pricedrop_location").change(function() {
                refreshPriceDropTable()
            });

            $("#pricedropDemandsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-pricedrops') !!}',
                    data: function(data) {
                        data.location = $("#pricedrop_location").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                    }
                },
                columns: [{
                    data: "created_at",
                    name: "created_at",
                }, {
                    data: "demand_no",
                    name: "demand_no",
                }, {
                    data: "demand_items_count",
                    name: "demand_items_count",
                }, {
                    data: "approved",
                    name: "approved",
                }, {
                    data: "credit_note_no",
                    name: "credit_note_no",
                }, {
                    data: "vat_amount",
                    name: "vat_amount",
                    className: "text-right",
                }, {
                    data: "demand_amount",
                    name: "demand_amount",
                    className: "text-right",
                }, {
                    data: "edited_demand_amount",
                    name: "edited_demand_amount",
                    className: "text-right",
                }, {
                    data: "action",
                    name: "action",
                    orderable: false,
                    searchable: false,
                }, ],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalDropAmount").html(json.total_amount);
                    $("#totalNewDropAmount").html(json.total_edited_amount);
                }
            });
        })

        function refreshPriceDropTable() {
            $("#pricedropDemandsDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
