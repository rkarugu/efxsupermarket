@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Production Work Orders </h3>

                    <a href="{{ route("$base_route_name.create") }}" role="button" class="btn btn-primary">
                        Add Work Order
                    </a>
                </div>
            </div>

            <div class="box-body">
                <div style="margin-bottom: 10px;">
                    @include('message')
                </div>

                <div class="table-responsive">
                    <table class="table" id="work-orders">
                        <thead>
                        <tr>
                            <th scope="col"> Order Reference</th>
                            <th scope="col"> Production Plant</th>
                            <th scope="col"> Production Item</th>
                            <th scope="col"> Order Date</th>
                            <th scope="col"> Description</th>
                            <th scope="col"> Availability of BOM</th>
                            <th scope="col"> Status</th>
                            <th scope="col"> Actions</th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script type="text/javascript">
        function startWorkOrder() {
            $("#start-work-form").submit();
        }
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('#work-orders').DataTable({
                "processing": true,
                "serverSide": true,
                "order": [[0, "desc"]],
                "pageLength": '<?= Config::get('params.list_limit_admin') ?>',
                "ajax": {
                    "url": '{!! route('work-orders.datatable') !!}',
                    "dataType": "json",
                    "type": "GET",
                    "data": {_token: "{{csrf_token()}}"}
                },
                "columns": [
                    {data: 'order_reference', name: 'order_reference', orderable: false},
                    {data: 'production_plant', name: 'production_plant', orderable: false},
                    {data: 'production_item', name: 'production_item', orderable: false},
                    {data: 'order_date', name: 'order_date', orderable: false},
                    {data: 'description', name: 'description', orderable: false},
                    {data: 'bom_availability', name: 'bom_availability', orderable: false},
                    {data: 'status', name: 'status', orderable: false},
                    {data: 'actions', name: 'actions', orderable: false}
                ]
            });
        });
    </script>
@endsection
