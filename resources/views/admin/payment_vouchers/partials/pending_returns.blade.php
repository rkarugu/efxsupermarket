<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>Pending Returns</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="returns_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="returns_location" class="form-control" @disabled(true)>
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
    <table class="table table-bordered" id="pendingReturnsDataTable">
        <thead>
            <tr>
                <th>Demand No.</th>
                <th>Date</th>
                <th>Return Type</th>
                <th>Document No.</th>
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
                <th class="text-right" colspan="8">Total</th>
                <th class="text-right" id="totalReturnAmount"></th>
                <th class="text-right" id="totalEditedAmount"></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            $("#returns_location").change(function() {
                refreshReturnsTable()
            });

            $("#pendingReturnsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.supplier-returns') !!}',
                    data: function(data) {
                        data.location = $("#returns_location").val();
                        data.supplier = "{{ $voucher->supplier->id }}";
                        data.items = {!! $items !!};
                    }
                },
                columns: [{
                    data: "demand_no",
                    name: "demand_no",
                }, {
                    data: "created_at",
                    name: "created_at",
                }, {
                    data: "return_type",
                    name: "return_type",
                    searchable: false,
                    orderable: false,
                }, {
                    data: "return_document_no",
                    name: "return_document_no",
                }, {
                    data: "return_demand_items_count",
                    name: "return_demand_items_count",
                    orderable: false,
                    searchable: false,
                }, {
                    data: "approved",
                    name: "approved",
                }, {
                    data: "credit_note_no",
                    name: "credit_note_no",
                }, {
                    data: "vat_amount",
                    name: "vat_amount",
                }, {
                    data: "demand_amount",
                    name: "demand_amount",
                }, {
                    data: "edited_demand_amount",
                    name: "edited_demand_amount",
                }, {
                    data: "action",
                    name: "action",
                    searchable: false,
                    orderable: false,
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#totalReturnAmount").html(json.total_amount);
                    $("#totalEditedAmount").html(json.total_edited_amount);
                }
            });
        })

        function refreshReturnsTable() {
            $("#pendingReturnsDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
