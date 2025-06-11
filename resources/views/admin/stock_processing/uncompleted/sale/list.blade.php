@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Stock Uncompleted Sales (Short) </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>
            
    <div class="box-body">
        @include('message')
        <form method="GET" action="{{route('stock-uncompleted-sales.index')}}">
                <div class="row">
                    <div class="form-group col-md-2">
                        <label for="start_date" class="control-label">From Date</label>
                        <input type="date" class="form-control" name="start_date" id="start_date"
                            value="{{ request()->get('start_date') }}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="end_date" class="control-label">To Date</label>
                        <input type="date" class="form-control" name="end_date" id="end_date" value="{{ request()->get('end_date') }}">
                    </div>

                    <div class="form-group col-md-2">
                        <label for="bin" class="control-label">Bin</label>
                        <select name="bin" id="bin" class="form-control select2" required>
                            <option value="">Choose Bin</option>
                            @foreach ($bins as $bin)
                                <option value="{{$bin->id}}" @if (request()->bin == $bin->id)
                                    selected
                                @endif>{{$bin->title}}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="" style="display: block; color: white;">Action</label>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" name="manage-request"
                                value="filter">FILTER</button>
                                @if (can('print', 'stock-uncompleted-sales'))
                                    <button type="submit" class="btn btn-success ml-12" name="manage-request"
                                        value="PDF">PDF</button>
                                @endif
                                @if (can('process', 'stock-uncompleted-sales'))
                                    <button type="button" class="btn btn-success ml-12" onclick="processItems()"
                                        value="Process">Process</button>
                                @endif
                            <a class="btn btn-success ml-12" href="{!! route('stock-uncompleted-sales.index') . getReportDefaultFilterForTrialBalance() !!}"> CLEAR </a>
                        </div>
                    </div>
                </div>
            </form>
        <table class="table table-bordered table-hover" id="debtorDataTable">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th>Date</th>
                    <th>Stock Date</th>
                    <th>Document No</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Store</th>
                    <th>Bin</th>
                    <th>Qty</th>
                    <th>Qty Pending</th>
                    <th>Total</th>
                </tr>
            </thead>
            {{-- <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><b>Total</b></td>
                    <td><b id="grandTotal"></b></td>
                    <td></td>
                </tr>
            </tfoot> --}}
          
        </table>
    </div>
</div>
</section>
<span class="btn-loader" style="display:none;z-index:9999;">
    <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
</span>
@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection
@push('scripts')

<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(function() {
        $('.select2').select2();
        
        let table = $("#debtorDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('stock-uncompleted-sales.index') !!}',
                data: function(data) {
                    data.start_date= $('#start_date').val();
                    data.end_date= $('#end_date').val();
                    data.bin= $('#bin').val();
                }
            },
            columns: [
                { 
                    data: 'DT_RowIndex', 
                    name: 'DT_RowIndex', 
                    orderable: false, 
                    searchable: false 
                },
                {
                    data: "created_at",
                    name: "created_at",
                },
                {
                    data: 'stock_date',
                    name: 'stock_date'
                },
                {
                    data: "document_no",
                    name: "document_no",
                },
                {
                    data: "stock_id_code",
                    name: "wa_inventory_items.stock_id_code",
                },
                {
                    data: "item_title",
                    name: "wa_inventory_items.title",
                },
                {
                    data: "location_name",
                    name: "wa_location_and_stores.location_name",
                },
                {
                    data: "uom_title",
                    name: "wa_unit_of_measures.title",
                },
                {
                    data: "quantity",
                    name: "quantity",
                },
                {
                    data: "quantity_pending",
                    name: "quantity_pending",
                },
                {
                    data: "total",
                    name: "total",
                    className: "text-right",
                },               
            ],
            columnDefs: [
                {
                        targets: 3,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                let documentNo = row.document_no;
                                    if (documentNo.includes("SAS")) {
                                        return `<a href="/admin/stock-processing/sales/show/`+row.trans_id+`" title="view" target="_blank">`+documentNo+`</a>`;
                                    }
                                    if (documentNo.includes("SAR")) {
                                        return `<a href="/admin/stock-processing/return/show/`+row.trans_id+`" title="view" target="_blank">`+documentNo+`</a>`;
                                    }
                            }
                            return data;
                        }
                },
                
            ],
            footerCallback: function(row, data, start, end, display) {
                var api = this.api();
                var json = api.ajax.json();

                $("#grandTotal").text(json.grand_total);
            }
        });

        table.on('draw', function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    });

    function processItems(format,id) {
        $('.btn-loader').show();
            jQuery.ajax({
                    url: "{{route('stock-uncompleted.process')}}",
                    async: false, //NOTE THIS
                    type: 'POST',
                    data: {},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('.btn-loader').hide();
                        location.reload();
                    }
                });
            
        }

</script>
@endpush
