@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Stock Expunged Entries </h3>
                    <div class="d-flex">
                        
                    </div>
                </div>
            </div>
            
    <div class="box-body">
        @include('message')
        <div class="row">
            <div class="form-group col-sm-2">
                <label for="">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control">
            </div>  
            <div class="form-group col-sm-2">
                <label for="">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control">
            </div>  
            <div class="form-group col-md-2">
                <label for="bin" class="control-label">Bin</label>
                <select name="bin" id="bin" class="form-control select2" required>
                    <option value="">Choose Bin</option>
                    @foreach ($bins as $bin)
                        <option value="{{$bin->id}}">{{$bin->title}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label for="" style="display: block; color: white;">Action</label>
                <div class="form-group">
                    @if (can('print', 'stock-expunged-entries'))
                        <button type="button" class="btn btn-success ml-12" id="generatePDFBtn"
                            value="PDF">PDF</button>
                    @endif
                </div>
            </div>
        </div>

        <table class="table table-bordered table-hover" id="debtorDataTable">
            <thead>
                <tr>
                    <th style="width: 3%;">#</th>
                    <th>Expunged Date</th>
                    <th>Expunged By</th>
                    <th>Date</th>
                    <th>Code</th>
                    <th>Description</th>
                    <th>Bin</th>
                    <th>Variation</th>
                </tr>
            </thead>
          
        </table>
    </div>
</div>
</section>

<div class="modal fade" id="confirmRestoreModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" id="approveRestoreTitle"> Restore</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="updateApproveForm" action="" method="POST">
                @csrf
                <div class="box-body">
                    Are you sure You want to Restore this Variation?
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="hidden" name="" id="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="confirmRestoreBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Restore</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmExpungeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title" id="approveExpungeTitle"> Expunge</h3>

                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <form id="updateApproveForm" action="" method="POST">
                @csrf
                <div class="box-body">
                    Are you sure You want to Expunge this Variation?
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <input type="hidden" name="" id="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="confirmExpungeBtn" class="btn btn-primary" data-id="0" data-dismiss="modal">Expunge</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
@section('uniquepagestyle')
    <link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet"/>
@endsection
@push('scripts')

<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script>
    $(function() {
        $('.select2').select2();
        $('#start_date, #end_date, #bin').on('change', function() {
            $("#debtorDataTable").DataTable().ajax.reload();
        });
        $('#generatePDFBtn').on('click',function(){
            location.href=`/admin/stock-expunged-entries?type=pdf&status=`+$("#status").val()+`&channel=`+$("#channel").val()+`&branch=`+$("#branch").val()+`&route=`+$("#route").val()+`&start_date=`+$("#start_date").val()+`&end_date=`+$("#end_date").val();
        });
        let table = $("#debtorDataTable").DataTable({
            processing: true,
            serverSide: true,
            order: [
                [0, "asc"]
            ],
            autoWidth: false,
            pageLength: '<?= Config::get('params.list_limit_admin') ?>',
            ajax: {
                url: '{!! route('stock-expunged-entries.index') !!}',
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
                    data: 'created_at', 
                    name: 'created_at', 
                },
                { 
                    data: 'expunged_by.name', 
                    name: 'expungedBy.name', 
                },
                { 
                    data: 'created_on', 
                    name: 'created_on', 
                },
                { 
                    data: 'get_inventory_item_detail.stock_id_code', 
                    name: 'getInventoryItemDetail.stock_id_code', 
                },
                { 
                    data: 'get_inventory_item_detail.description', 
                    name: 'getInventoryItemDetail.description', 
                },
                { 
                    data: 'get_uom_detail.title', 
                    name: 'getUomDetail.title', 
                },
                { 
                    data: 'variation', 
                    name: 'variation', 
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

    function restore(id)
    {
        $('#confirmRestoreBtn').data('id',id);
        $('#confirmRestoreModal').modal();
    }

    function expunge(id)
    {
        $('#confirmExpungeBtn').data('id',id);
        $('#confirmExpungeModal').modal();
    }

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
