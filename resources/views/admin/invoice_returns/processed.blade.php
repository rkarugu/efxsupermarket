@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Processed Returns </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <form action="" method="get">
                    <div class="row">
                    <div class="col-md-3">
                     <div class="form-group">
                        <label for="">From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request()->start_date ?? \Carbon\Carbon::now()->toDateString() }}">
                    </div>
                    </div>
                       <div class="col-md-3">
                       <div class="form-group">
                        <label for="">To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{request()->end_date ?? \Carbon\Carbon::now()->toDateString()}}">
                                             </div>
                        </div>


                       <div class="col-md-3">
                            <div class="form-group">
                                <label for="">Route</label>
                                <select name="route_id" id="route_id" class="form-control">
                                    <option value="Select Route" selected disabled></option>
                                    @foreach($routes as $route)
                                        <option value="{{ $route->route_name }}" {{ $route->route_name == request()->route_id ? 'selected' : '' }}> {{ $route->route_name }} </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group" style="margin-top: 25px; ">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ route('transfers.processed-returns') }}" class="btn btn-primary ml-12"> Clear </a>
                            </div>
                        </div>
                    </div>
                </form>

                <hr>

                <div class="table-responsive">
                    <table class="table table-bordered" id="processedDatatable">
                        <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th> Item</th>
                            <th> Bin</th>
                            <th> Return No</th>
                            <th> Return Date</th>
                            <th> Processed On</th>
                            <th> Returned By</th>
                            <th> Invoice No</th>
                            <th> Invoice Date</th>
                            <th> Route</th>
                            <th> Customer</th>
                            <th> Confirmed By</th>
                            <th> Received By</th>
                            <th> Returned Qty</th>
                            <th> Qty Received</th>
                            <th> Amount </th>
                        </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection



@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection


@section('uniquepagescript')
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('body').addClass('sidebar-collapse');

            var table = $("#processedDatatable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [2, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('transfers.processed-returns-datatable') !!}',
                    data: function(data) {
                        data.start_date = $('#start_date').val();
                        data.end_date = $('#end_date').val();;
                        data.route_id = $('#route_id').val();


                    }
                },
                columns: [{
                        orderable: false,
                        searchable: false,
                        data: 'DT_RowIndex',
                        name: null
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'bin',
                        name: 'wa_unit_of_measures.title'
                    },
                    {
                        data: 'return_number',
                        name: 'return_number'
                    },
                    {
                        data: 'return_date',
                        name: 'wa_inventory_location_transfer_item_returns.created_at'
                    },
                    {
                        data: 'approved_date',
                        name: 'wa_inventory_location_transfer_item_returns.updated_at'
                    },
                    {
                        data: 'initiator',
                        name: 'initiators.name'
                    },
                    {
                        data: 'invoice_number',
                        name: 'wa_inventory_location_transfers.transfer_no'
                    },
                    {
                        data: 'invoice_date',
                        name: 'wa_inventory_location_transfers.created_at'
                    },
                    {
                        data: 'route',
                        name: 'wa_inventory_location_transfers.route'
                    },
                    {
                        data: 'customer',
                        name: 'wa_inventory_location_transfers.name'
                    },
                    {
                        data: 'confirmer',
                        name: 'wa_inventory_location_transfer_item_returns.confirmed_by'
                    },
                    {
                        data: 'receiver',
                        name: 'receivers.name'
                    },
                    {
                        data: 'return_quantity',
                        name: 'return_quantity'
                    },
                    {
                        data: 'received_quantity',
                        name: 'received_quantity'
                    },
                    {
                        data: 'total_value',
                        name: 'total_value',
                        searchable: false,
                    },                 
                ],
            });
        });
    </script>
    <script>
        function printgrn(transfer_no) {
            jQuery.ajax({
                url: '{{route('transfers.print-return')}}',
                async: false,   //NOTE THIS
                type: 'POST',
                data: {transfer_no: transfer_no},
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    var divContents = response;
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                }
            });
        }

        $(function () {
            $("#route_id").select2();
            $(".mlselec6t").select2();
        });

    $(document).on("click", ".open-confirmDialog", function () {
     var return_number = $(this).data('id');
     $(".modal-body #return_number").val(return_number );
     // As pointed out in comments,
     // it is unnecessary to have to manually call the modal.
      $('#approve').modal('show');
    });
    </script>
@endsection
