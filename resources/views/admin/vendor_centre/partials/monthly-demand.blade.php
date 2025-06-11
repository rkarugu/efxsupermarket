<div style="padding:10px">
    <table class="table table-bordered" id="demandsDataTable">
        <thead>
            {{-- <tr>
                <th>Date</th>
                <th>Demand No.</th>
                <th>Created By</th>
                <th>Items</th>
                <th>Total Amount</th>
               
            </tr> --}}
        </thead>
    </table>
</div>
@push('scripts')
    <script>
        $(document).ready(function() {
            $("#demandsDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-suppliers.vendor_centre.monthly-demands', $supplier->id) !!}',
                },
                columns: [{
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'demand_no',
                        name: 'demand_no'
                    },
                    {
                        data: 'created_by',
                        name: 'created_by'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },
                    {
                        data: 'demand_amount',
                        name: 'demand_amount'
                    },
                    // {
                    //     data: 'valuation_before',
                    //     name: 'valuation_before'
                    // },
                    // {
                    //     data: 'new_cost',
                    //     name: 'new_cost'
                    // },
                    // {
                    //     data: 'valuation_after',
                    //     name: 'valuation_after',
                    //     className: "text-right"
                    // },
                    // {
                    //     data: 'demand',
                    //     name: 'demand',
                    //     className: "text-right"
                    // },
                ],
            });
        })
    </script>
@endpush
