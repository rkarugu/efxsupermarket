@extends('layouts.admin.admin')
@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border"><h3 class="box-title"> {!! $title !!}  </h3>
            </div>
            @include('message')
            <div class="box-body">
                <div class="row pb-4">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Bin</label>
                            <input type="text" name="bin" id="bin" class="form-control"
                                   value="{{$bin->title}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Sale</label>
                            <input type="text" name="sale_no" id="sale_no" class="form-control"
                                   value="{{$sale->sales_no}}" readonly>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Customer</label>
                            <input type="text" name="sale_no" id="sale_no" class="form-control"
                                   value="{{$sale->buyer->name}}" readonly>
                        </div>
                    </div>
                   

                </div>
                <table id="orderTable" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Stock Id Code</th>
                        <th>Title</th>
                        <th>Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach ($items as $item)
                            <tr>
                                <th>{{$loop->index + 1}}</th>
                                <td>{{$item->cashSaleItem->item->stock_id_code}}</td>
                                <td>{{$item->cashSaleItem->item->title}}</td>
                                <td>{{$item->cashSaleItem->qty}}</td>
                            </tr>
                            
                        @endforeach

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
            $('#filter').click(function (e) {
                e.preventDefault();
                table.draw();
            });
            $(".mlselec6t").select2();
        });
    </script>
@endpush