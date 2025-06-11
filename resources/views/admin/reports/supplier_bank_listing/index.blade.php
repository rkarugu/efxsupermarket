@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Supplier Bank Listing Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Account Payables Rerports </a> --}}
                </div>
            </div>

            <div class="box-body">
                @include('message')
                <div class="row">
                    <div class="col-sm-7">
                        <form>
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <button type="submit" name="action" value="excel" class="btn btn-primary">
                                        Export Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-5">
                        <form method="post" enctype="multipart/form-data" class="form-horizontal"
                            action="{{ route('supplier-bank-listing.update') }}">
                            @csrf
                            <div class="row">
                                <div class="col-sm-8">
                                    <div class="form-group">
                                        <input type="file" class="form-control" name="file" id="file">
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <button type="submit" name="action" value="import" class="btn btn-primary">
                                            Import Data
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-hover" id="suppliersDataTable">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Supplier No</th>
                            <th>Supplier Name</th>
                            <th>Bank Name</th>
                            <th>Bank Account No</th>
                            <th>Bank Swift/Code</th>
                            <th>Bank Branch</th>
                            <th>KRA PIN</th>
                            <th>Withholding Tax</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#suppliersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [1, "asc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('supplier-bank-listing.index') !!}',
                    data: function(data) {

                    }
                },
                columns: [{
                    data: 'DT_RowIndex',
                    searchable: false,
                    sortable: false,
                }, {
                    data: 'supplier_code',
                    name: 'supplier_code',
                }, {
                    data: 'name',
                    name: 'name',
                }, {
                    data: 'bank_name',
                    name: 'bank_name',
                }, {
                    data: 'bank_account_no',
                    name: 'bank_account_no',
                }, {
                    data: 'bank_swift',
                    name: 'bank_swift',
                }, {
                    data: 'bank_branch',
                    name: 'bank_branch',
                }, {
                    data: 'kra_pin',
                    name: 'kra_pin',
                }, {
                    data: 'tax_withhold',
                    name: 'tax_withhold',
                }, ],
            })
        })

        function refreshTable() {
            $("#suppliersDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
