@extends('layouts.admin.admin')

@section('content')


    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">
            <div class="box-header with-border no-padding-h-b">

                <form action="" method="get">

                    <div class="form-group col-sm-4">
                        <select name="item" class="form-control destination_items">
                            <option value="" disabled selected>Select Inventory Item</option>
                            @if (request()->item)
                                <option value="{{ request()->item }}" selected>
                                    {{ @\App\Model\WaInventoryItem::find(request()->item)->title }}</option>
                            @endif
                        </select>
                    </div>
                    <div class="form-group col-sm-4">
                        <select name="supplier" class="form-control select-suppliers" id=supplier-id>
                            <option value="" selected disabled>Select Supplier</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @if (request()->supplier == $supplier->id) selected @endif>
                                    {{ $supplier->name }} </option>
                            @endforeach
                        </select>
                    </div>



                    <button type="submit" class="btn btn-primary col-sm-2">Filter</button>
                    {{-- <a class="btn btn-info ml-12" href="{!! route('maintain-items.item_price_history_list') !!}">Clear </a> --}}

                </form>

                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover" id="price_change_history_table">
                        <thead>
                            <tr>
                                <th>S.No.</th>
                                <th>Stock ID Code</th>
                                <th>Title</th>
                                <th>Old Standard Cost</th>
                                <th>Standard Cost</th>
                                <th>Old Selling Price</th>
                                <th>Selling Price</th>
                                <th>Old Price List Cost</th>
                                <th>Price List Cost</th>
                                <th>Old Weighted Cost</th>
                                <th>Weighted  Cost</th>
                                <th>Block?</th>
                                <th>Status</th>
                                <th>Initiator</th>
                                <th>Approver</th>
                                <th>Updated At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($list) && !empty($list))
                                <?php $b = 1; ?>
                                @foreach ($list as $item)
                                    <tr>
                                        <td>{!! $b !!}</td>
                                        <td>{!! @$item->item->stock_id_code !!}</td>
                                        <td>{!! @$item->item->title !!}</td>
                                        <td>{!! @manageAmountFormat($item->old_standard_cost) !!}</td>
                                        <td>{!! @manageAmountFormat($item->standard_cost) !!}</td>
                                        <td>{!! @manageAmountFormat($item->old_selling_price) !!}</td>
                                        <td>{!! @manageAmountFormat($item->selling_price) !!}</td>
                                        <td>{!! @manageAmountFormat($item->old_price_list_cost) !!}</td>
                                        <td>{!! @manageAmountFormat($item->price_list_cost) !!}</td>
                                        <td>{{manageAmountFormat($item->old_weighted_cost)}}</td>
                                        <td>{{manageAmountFormat($item->weighted_cost)}}</td>
                                        <td>{!! $item->block_this ? 'Yes' : 'No' !!}</td>
                                        <td>{!! @$item->status !!}</td>
                                        <td>{!! @$item->creator->name !!}</td>
                                        <td>{!! @$item->approver->name !!}</td>
                                        <td>{!! date('d/M/Y H:i A', strtotime(@$item->updated_at)) !!}</td>



                                    </tr>
                                    <?php $b++; ?>
                                @endforeach
                            @endif


                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </section>

@endsection
@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection
@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('body').addClass('sidebar-collapse');
            $('#price_change_history_table').DataTable({
                "paging": true,
                "pageLength": 50,
                "searching": true,
                "lengthChange": true,
                "lengthMenu": [10, 20, 50, 100],
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "order": [
                    [0, "asc"]
                ]
            });
        });

        var destinated_item = function() {
            $(".destination_items").select2({
                ajax: {
                    url: "{{ route('maintain-items.inventoryDropdown') }}",
                    dataType: 'json',
                    type: "GET",
                    data: function(term) {
                        return {
                            q: term.term
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: response
                        };
                    },
                    cache: true
                }
            });
        }
        destinated_item();
    </script>
    <script type="text/javascript">
        $('.select-suppliers').select2();
    </script>
@endsection
