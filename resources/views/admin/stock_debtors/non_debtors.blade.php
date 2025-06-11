@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" style="font-weight:500 !important;"> Non Stock Debtors </h3>
                    <div class="d-flex">
                        {{-- <div>
                            <select name="role" id="role" class="mtselect form-control">
                                <option value="">Choose Role</option>
                                @foreach (getRoles() as $key => $item)
                                    <option value="{{$key}}">{{$item}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="button" class="btn btn-primary" name="action" id="generatePDFBtn" style="margin:0 10px;height: 35px;">
                            <i class="fa fa-file"></i> PDF
                        </button>
                        <a type="button" href="{{ route('stock-debtors.index') }}" class="btn btn-primary" style="margin-right:10px;height: 35px;">
                            <i class="fa fa-file"></i> Clear
                        </a> --}}
                       
                    </div>
                </div>
            </div>
            {{-- <div class="box-header with-border">
               
                <div class="box-header-flex">
                    <h3 class="box-title"></h3>
                </div>
            </div> --}}
    <div class="box-body">
        @include('message')
        <table class="table table-bordered table-hover" id="debtorDataTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone Number</th>
                    <th>Role</th>
                    <th class="text-right">Amount</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan="3" class="text-right"><b>Total</b></td>
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
<script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{asset('js/form.js')}}"></script>
<script>
    $(function() {
        
        $('#generatePDFBtn').on('click',function(){
            var rol = $("#role").val();
            location.href=`{!! route('stock-debtors.index', ['print' => 'pdf']) !!}&role=` + rol;
        });

        $('.select2').select2({
            dropdownParent: $('#createModal')
        });
        $('.select22').select2({
            dropdownParent: $('.splitModal')
        });
       
        $('.mtselect').select2();
        $('#role').on('change', function() {
            $("#debtorDataTable").DataTable().ajax.reload();
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
                url: '{!! route('stock-non-debtors.index') !!}',
                data: function(data) {
                    data.role = $("#role").val();
                }
            },
            columns: [{
                    data: "non_debtor.name",
                    name: "nonDebtor.name",
                },
                {
                    data: "non_debtor.phone_number",
                    name: "nonDebtor.phone_number",
                },
                {
                    data: "non_debtor.user_role.title",
                    name: "nonDebtor.userRole.title"
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
                        targets: -1,
                        render: function (data, type, row, meta) {
                            if (type === 'display') {
                                var actions = '<div class="d-flex">';
                                @if (can('view-details', 'stock-non-debtors'))
                                    actions += `<a href="/admin/stock-non-debtors/view/`+row.stock_non_debtor_id+`" role="button" title="View"><i class="fa fa-solid fa-eye"></i></a>`;
                                @endif
                                
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

</script>
@endpush
