@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}</h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" id="status">
                                <option value="3">Pending</option>
                                <option value="1">Accepted</option>
                                <option value="2">Rejected</option>
                                <option value="">All</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">
                                Filter
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-striped mt-3" id="cashiersTable">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Cash Sale No.</th>
                        <th>Return No</th>
                        <th>Returned By</th>
                        <th>Bin</th>
                        <th>Sale Date</th>
                        <th>Return Date</th>
                        <th>Customer</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>

                    </tbody>

                </table>
            </div>
        </div>
    </section>

@endsection
@push('scripts')
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
    <script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
    <script>
        function printgrn(transfer_no) {
            var url = "{{url('admin/pos-cash-sales/invoice/return-print')}}/" + transfer_no;
            print_this(url);
        }
    </script>
    <script>
        var VForm = new Form();
        $(document).ready(function () {
            var table = $("#cashiersTable").DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('pos-cash-sales.returned_cash_sales_list_dispatcher') !!}',
                    data: function (data) {
                        // var payment_method = $('#payment_method').val();
                        // var restaurant_id = $('#restaurant_id').val();
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.from = from;
                        data.to = to;
                        data.status =  $('#status').val();
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        searchable: false,
                        sortable: false,
                        width: "70px"
                    },
                    {
                        data: "pos_cash_sale.sales_no",
                        name: "PosCashSale.sales_no"
                    },
                    {
                        data: "return_grn",
                        name: "return_grn",
                        searchable: false
                    },
                    {
                        data: "returned_by",
                        name: "returned_by",
                        searchable: false
                    },
                    {
                        data: "bin",
                        name: "bin",
                        searchable: false
                    },
                    {
                        data: "created_at",
                        name: "created_at",
                        searchable: false
                    },
                    {
                        data: "return_date",
                        name: "return_date",
                        searchable: false
                    },
                    {
                        data: "pos_cash_sale.customer",
                        name: "pos_cash_sale.customer",
                        searchable: false
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false
                    }
                ],
            });

            var orderId = 0;
            $('#cashiersTable tbody').on('click', '.show-details', function (e) {
                e.preventDefault();
                orderId = $(this).data('orderId');
                $('#staticBackdropModal').modal('show');
            });

            $('#filter').click(function (e) {
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();

            $('#saveReturn').click(function (e) {
                e.preventDefault();
                var csrfToken = $('meta[name="csrf-token"]').attr('content');
                var url = '{{ url('admin/pos-cash-sales/accept-return') }}' + '/' + orderId;
                var accept = $('#accept-checkbox').is(':checked') ? 1 : 0;
                var comment = $('#comment').val();
                var formData = new FormData();
                formData.append('comment', comment);
                formData.append('accept', accept);

                $.ajax({
                    type: 'POST',
                    url: url,
                    data:formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    success: function (response) {
                        VForm.successMessage('Return successful');
                        $('#accept-checkbox').val()
                        $('#comment').val()
                        $('#staticBackdropModal').modal('hide');
                        $('#cashiersTable').DataTable().draw();
                    },
                    error: function (xhr, status, error) {
                        console.error("Return failed:", error);

                    }
                });
            });
        })

    </script>
@endpush

