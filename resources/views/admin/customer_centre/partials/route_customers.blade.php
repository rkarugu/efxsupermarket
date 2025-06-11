<div class="text-right" style="padding: 10px">
    @if (can('export', 'customer-centre'))
    <a href="/api/route-customer-export-all?route_id={{ $customer->route->id }}" class="btn btn-success">
        <i class="fa fa-file-excel"></i>
        Export
    </a>
    @endif
    @if (can('add', 'customer-centre'))
        <a href="{!! route($model . '.route_customer_add', $customer->id) !!}" class="btn btn-success ml-12">
            <i class="fa fa-plus"></i>
            Add Route Customer
        </a>
    @endif
</div>
<div style="padding: 10px">
    <table class="table table-bordered" id="routeCustomersDataTable">
        <thead>
            <tr>
                <th>Center</th>
                <th>Shop Owner</th>
                <th>Phone No.</th>
                <th>Business Name</th>
                <th>Mapped</th>
                <th>Action</th>
            </tr>
        </thead>
    </table>
    <div class="modal fade" id="delete-customer-modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Remove Customer </h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>

                <div class="box-body">
                    <p style="font-size: 16px;"> Are you sure you want to remove <span id="shop-name"></span> from the
                        system? </p>
                    <form action="{!! route('route-customers.remove') !!} " method="post" id="delete-customet-form">
                        @csrf
                        <input type="hidden" id="shop-id" name="shop_id">
                    </form>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="confirmDeleteCustomer();">Yes,
                            Remove</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#routeCustomersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('customer-centre.route-customers', $customer->id) !!}',
                    data: function(data) {

                    }
                },
                columns: [{
                        data: 'center',
                        name: 'center.name',
                    },
                    {
                        data: 'name',
                        name: 'name',
                    },
                    {
                        data: 'phone',
                        name: 'phone',
                    },
                    {
                        data: 'bussiness_name',
                        name: 'bussiness_name',
                    },
                    {
                        data: 'image_url',
                        name: 'image_url',
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                    },
                ],
            });



            $('#delete-customer-modal').on('show.bs.modal', function(event) {
                let triggeringButton = $(event.relatedTarget);
                let idValue = triggeringButton.data('id');
                let nameValue = triggeringButton.data('name');

                $("#shop-id").val(idValue);
                $("#shop-name").text(nameValue);
            })
        });

        function confirmDeleteCustomer() {
            $("#delete-customet-form").submit();
        }

        function refreshTable() {
            $("#routeCustomersDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
