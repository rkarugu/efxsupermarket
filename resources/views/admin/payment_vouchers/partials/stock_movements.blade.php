<div style="padding: 10px">
    <div class="row" style="margin-bottom: 15px">
        <div class="col-sm-9">
            <h4>Stock Movements</h4>
        </div>
        <div class="col-sm-3">
            <div class="row" style="display: none">
                <label for="movements_location" class="col-sm-3">Location</label>
                <div class="col-sm-9">
                    <select name="location" id="movements_location" class="form-control" @disabled(true)>
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
    <table class="table table-striped" id="movementsDataTable">
        <thead>
            <tr>
                <th>GRN Number</th>
                <th>Item Code</th>
                <th>Item Desc</th>
                <th>Date</th>
                <th>User Name</th>
                <th>Store Location</th>
                <th>Qty In</th>
                <th>QOH</th>
                <th>Reference</th>
            </tr>
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#movements_location").change(function() {
                refreshMovementsTable()
            });

            $("#movementsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [3, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('payment-vouchers.stock-movements') !!}',
                    data: function(data) {
                        data.location = $("#movements_location").val();
                        data.items = {!! $items !!};
                    }
                },
                columns: [{
                        data: "document_no",
                        name: "document_no",
                    },{
                        data: "stock_id_code",
                        name: "stock_id_code",
                    },
                    {
                        data: "title",
                        name: "items.title",
                    },
                    {
                        data: "created_at",
                        name: "moves.created_at",
                    },
                    {
                        data: "user_name",
                        name: "users.name",
                    },
                    {
                        data: "location_name",
                        name: "locations.location_name",
                    },
                    {
                        data: "qauntity",
                        name: "qauntity",
                    },
                    {
                        data: "new_qoh",
                        name: "new_qoh",
                    },
                    {
                        data: "refrence",
                        name: "refrence",
                    }
                ],
            });
        })

        function refreshMovementsTable() {
            $("#movementsDataTable").DataTable().ajax.reload()
        }
    </script>
@endpush
