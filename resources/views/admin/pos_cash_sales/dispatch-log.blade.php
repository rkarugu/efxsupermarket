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
                                {!!Form::select('bin_id', $bins, null, ['placeholder'=>'Select Bin ', 'class' => 'form-control mlselec6t','required'=>true,'title'=>'Please select Bin','id'=>'bin_id'  ])!!}
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
                        <th>Date</th>
                        <th>Bin</th>
                        <th>Customer</th>
                        <th>Cash Sale</th>
                        <th>Dispatcher</th>
                        {{-- <th>Item</th>
                        <th>Quantity</th> --}}
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
                    url: '{!! route('pos-cash-sales.dispatch-logs') !!}',
                    data: function (data) {
                        var from = $('#start_date').val();
                        var to = $('#end_date').val();
                        data.bin_id = $('#bin_id').val();
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
                        data: "created_at",
                        name: "created_at",
                        searchable: false,
                        sortable: false,
                    },
                    {
                        data: "bin.title",
                        name: "bin.title",
                        // searchable: false,
                    },
                    {
                        data: "cash_sale_item.parent.customer",
                        name: "cashSaleItem.parent.customer",
                    },
                    {
                        data: "cash_sale_item.parent.sales_no",
                        name: "cashSaleItem.parent.sales_no",
                    },
                    {
                        data: "dispatch_user.name",
                        name: "dispatcher",
                        searchable: false,
                    },
                    {
                        data: "action",
                        name: "action",
                        searchable: false,
                    },
                    // {
                    //     data: "cash_sale_item.item.title",
                    //     name: "item",
                    //     searchable: false,
                    // },
                    // {
                    //     data: "cash_sale_item.qty",
                    //     name: "item",
                    //     searchable: false,
                    // }
                ],
            });
            $('#filter').click(function (e) {
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();
        });
    </script>
@endpush