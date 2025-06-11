@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Sales (Short) </h3>
                    <div class="d-flex">
                        @if (can('add', 'stock-processing-sales'))
                            <a href="{{route('stock-processing.sales.add')}}" class="btn btn-primary">Add Sales (Short)</a>
                        @endif
                    </div>
                </div>
            </div>
            
    <div class="box-body">
        @include('message')
        <form method="GET" action="{{route('stock-processing.sales')}}">
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
                        <label for="branch" class="control-label">Branch</label>
                        <select name="branch" id="branch" class="form-control select2" required>
                            <option value="">Choose Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{$branch->id}}" @if (request()->branch == $branch->id  || Auth::user()->wa_location_and_store_id == $branch->id)
                                    selected
                                @endif>{{$branch->name}}</option>
                            @endforeach
                        </select>
                      
                    </div>

                    <div class="col-md-4">
                        <label for="" style="display: block; color: white;">Action</label>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success" name="manage-request"
                                value="filter">FILTER</button>
                                @if (can('print', 'stock-processing-sales'))
                                    <button type="submit" class="btn btn-success ml-12" name="manage-request"
                                        value="PDF">PDF</button>
                                @endif
                            <a class="btn btn-success ml-12" href="{!! route('stock-processing.sales') . getReportDefaultFilterForTrialBalance() !!}"> CLEAR </a>
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
                    <th>Bin</th>
                    <th>Document No</th>
                    <th>Debtor Name</th>
                    <th>Store</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-right"><b>Total</b></td>
                    <td><b id="grandTotal"></b></td>
                    <td></td>
                </tr>
            </tfoot>
          
        </table>
    </div>
</div>
</section>

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
                url: '{!! route('stock-processing.sales') !!}',
                data: function(data) {
                    data.start_date= $('#start_date').val();
                    data.end_date= $('#end_date').val();
                    data.branch= $('#branch').val();
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
                    data: "stock_date",
                    name: "stock_date",
                },
                {
                    data: "uom",
                    name: "wa_unit_of_measures.title",
                },
                {
                    data: "document_no",
                    name: "document_no",
                },
                {
                    data: "name",
                    name: "users.name",
                },
                {
                    data: "location_name",
                    name: "wa_location_and_stores.location_name",
                },
                {
                    data: "total",
                    name: "total",
                    className: "text-right",
                },
                {
                    data: null,
                    orderable: false,
                    searchable: false
                }                
            ],
            columnDefs: [
                {
                        targets: 4,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                return `<a href="/admin/stock-processing/sales/show/`+row.id+`" title="view" target="_blank">`+row.document_no+`</a>`;
                            }
                            return data;
                        }
                },
                {
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '<div class="d-flex">';
                                    @if (can('show', 'stock-processing-sales'))
                                        actions += `<a href="/admin/stock-processing/sales/show/`+row.id+`" title="view"><i class="fa fa-solid fa-eye"></i></a>`;
                                    @endif
                                    
                                    if (row.esd_status  != 'Signed successfully.') {
                                        @if (can('resign_esd', 'stock-processing-sales'))
                                            actions += `
                                            <form action="/admin/stock-processing/sales/resign-esd/`+row.id+`" method="POST">
                                                @csrf
                                                        <button style="background:#fff; color:#337ab7; border:none;padding-right:0;"
                                                            class=""
                                                            type="submit"
                                                            title="Re-Sign ESD">
                                                            <i class="fa fa-repeat" aria-hidden="true"></i>
                                                        </button>
                                                        
                                                        `;
                                        @endif
                                    } else {
                                        @if (can('print', 'stock-processing-sales'))
                                            actions += `<a title="Print Invoice"
                                                        href="/admin/stock-processing/sales/file/PDF/`+row.id+`"
                                                        style="margin-left:5px;">
                                                        <i aria-hidden="true" class="fa fa-print"></i>
                                                    </a>`;
                                        @endif
                                    }
                                    
                                    actions +='</div>';
                                return actions;
                            }
                            return data;
                        }
                    }
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

    function printStock(format,id) {
            jQuery.ajax({
                    url: '/admin/stock-processing/sales/file/'+format+'/'+id,
                    async: false, //NOTE THIS
                    type: 'GET',
                    data: {},
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        var divContents = response;
                        var printWindow = window.open('', '', 'width=600');
                        printWindow.document.write(divContents);
                        printWindow.document.close();
                        printWindow.print();
                        printWindow.close();
                    }
                });
            
        }

</script>
@endpush
