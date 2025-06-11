@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}  </h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    @if($user -> role_id == 1)
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="restaurant_id">Bin</label>
                                {!!Form::select('bin_id', $bins, null, ['placeholder'=>'Select Branch ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Branch','id'=>'bin_id'  ])!!}
                            </div>
                        </div>
                    @endif


                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">From</label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                   value="{{request()->input('start-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">To</label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                   value="{{request()->input('end-date') ?? date('Y-m-d')}}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <button type="submit" id="filter" class="btn btn-primary btn-sm" style="margin-top: 25px;">
                                Filter
                            </button>
                        </div>
                    </div>

                </div>
                <table id="orderTable" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Cash Sale</th>
                        <th>Items Count</th>
                        <th>Date</th>
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

        var VForm = new Form();

        function reloadTable() {
            $('#orderTable').DataTable().ajax.reload();
        }

        setInterval(reloadTable, 120000);

        function dispatchToItem(orderId, itemQuantities) {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var url = '{{ url('admin/pos-cash-sales/process_dispatch') }}' + '/' + orderId;
            var postData = {
                itemQuantities: itemQuantities,
            };
            $.ajax({
                type: 'POST',
                url: url,
                data: JSON.stringify(postData),
                contentType: "application/json",
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function (response) {
                    VForm.successMessage('Dispatch successful');
                    $('#orderTable').DataTable().ajax.reload();
                    console.log("Dispatch successful:", response);
                },
                error: function (xhr, status, error) {
                    console.error("Dispatch failed:", error);

                }
            });

        }

        $(document).ready(function () {
            var table = $('#orderTable').DataTable({
                processing: true,
                serverSide: true,
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('pos-cash-sales.dispatch') !!}',
                    data: function (data) {
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        var bin_id = $('#bin_id').val();
                        var status = $('.dispatching-status:checked').val();
                        data.status = status;
                        data.bin_id = bin_id;
                        data.from = from;
                        data.to = to;
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
                        data: "customer",
                        name: "customer"
                    },
                    {
                        data: "sales_no",
                        name: "sales_no"
                    },
                    {
                        data: "items_count",
                        name: "items_count",
                        searchable: false,
                    },
                    {
                        data: "created_at",
                        name: "created_at"
                    },
                    {
                        // Custom column for action button
                        data: null,
                        searchable: false,
                        sortable: false,
                        render: function (data, type, row) {
                            return '<button class="btn btn-sm btn-primary show-details" data-order-id="' + row.id + '"><i class="fa fa-eye"></i></button>';
                        }
                    }
                ],
            });
            $('#filter').click(function (e) {
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();
            // Handle click event on action button


            $('#orderTable tbody').on('click', '.show-details', function () {
                var tr = $(this).closest('tr');
                var row = table.row(tr);

                $('tr.shown').each(function () {
                    var row = $(this);
                    var table = row.closest('table').DataTable();
                    var rowData = table.row(row).data();
                    table.row(row).child.hide();
                    row.removeClass('shown');
                });
                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('shown');
                } else {
                    // Open this row
                    row.child(format(row.data())).show();
                    tr.addClass('shown');
                }
            });

            // Format the child row
            function format(data) {
                var items = data.items;

                var childTable = '<table class="table table-condensed child-table">';
                childTable += '<thead>' +
                    '<tr>' +
                    '<th>Product Name</th>' +
                    '<th>Quantity</th>' +
                    '<th>Disptach Quantity</th>' +
                    '<th>Selling Price</th>' +
                    '<th>Amount</th>' +
                    '</tr>' +
                    '</thead>';
                childTable += '<tbody>';
                // Loop through items and populate the table rows
                for (var i = 0; i < items.length; i++) {
                    console.log(data.items[i])
                    childTable += '<tr>';
                    childTable += '<td>' + items[i].item.title + '</td>';
                    childTable += '<td>' + items[i].qty + '</td>';
                    childTable += '<td><input type="number" class="form-control input-quantity" id="quantity_' + items[i].id + '" value="' + items[i].qty + '" readonly></td>';
                    childTable += '<td>' + items[i].selling_price + '</td>';
                    childTable += '<td>' + items[i].total + '</td>';
                    childTable += '</tr>';
                }
                childTable += '</tbody></table>';

                childTable += '<div class="row"><div class="col-xs-12 text-right"><button class="dispatch-button btn btn-primary" data-order-id="' + data.id + '">Dispatch</button></div></div>';
                return childTable;
            }

            $('#orderTable tbody').on('click', '.dispatch-button', function (event) {
                event.stopPropagation();
                var orderId = $(this).data('order-id');
                var itemQuantities = [];
                $('.input-quantity').each(function () {
                    var inputId = $(this).attr('id');
                    var itemId = inputId.split('_')[1];
                    var quantity = $(this).val();
                    itemQuantities.push({itemId: itemId, quantity: quantity});
                });
                dispatchToItem(orderId, itemQuantities);
            });
        });
    </script>
@endpush