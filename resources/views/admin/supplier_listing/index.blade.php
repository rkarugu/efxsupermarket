@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Supplier Listing Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Account Payables Rerports </a> --}}
                </div>
            </div>

            <div class="box-body">
                @include('message')
                <div class="row">
                    <form>
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label for="balance">Supplier Balance</label>
                                <select name="balance" id="balance" class="form-control">
                                    <option value="all" selected>All</option>
                                    <option value="zero" {{ request()->balance == 'zero' ? 'selected' : '' }}> Supplier
                                        with Zero Balance</option>
                                    <option value="less" {{ request()->balance == 'less' ? 'selected' : '' }}> Supplier
                                        with Less than zero Balance</option>
                                    <option value="more" {{ request()->balance == 'more' ? 'selected' : '' }}> Supplier
                                        with Greater than zero Balance</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 d-flex">
                            <div class="form-group">
                                <label style="display:block">&nbsp;</label>
                                <button type="submit" name="action" value="pdf" class="btn btn-primary">
                                    Print PDF
                                </button>
                            </div>
                            <div class="form-group" style="margin-left: 10px;">
                                <label style="display:block">&nbsp;</label>
                                <button type="submit" name="action" value="excel" class="btn btn-primary">
                                    Print Excel
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <table class="table table-bordered table-hover" id="suppliersDataTable">
                    <thead>
                        <tr>
                            <th width="5%">S.No.</th>
                            <th width="5%">Supplier Code</th>
                            <th width="10%">Supplier Name</th>
                            <th width="10%">Address</th>
                            <th width="10%">Telephone</th>
                            <th width="10%">Email</th>
                            <th width="10%">Supllier Since</th>
                            <th width="10%">Payment Terms</th>
                            <th width="10%">Total Balance</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="8">Grand Total </th>
                            <th class="text-right" id="total"></th>
                        </tr>
                    </tfoot>
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
            $("#balance").select2();

            $("#suppliersDataTable").DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [8, "desc"]
                ],
                autoWidth: false,
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('supplier-listing.index') !!}',
                    data: function(data) {
                        data.balance = $("#balance").val();
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
                    data: 'address',
                    name: 'address',
                }, {
                    data: 'telephone',
                    name: 'telephone',
                }, {
                    data: 'email',
                    name: 'email',
                }, {
                    data: 'supplier_since',
                    name: 'supplier_since',
                }, {
                    data: 'payment_term.term_description',
                    name: 'paymentTerm.term_description',
                }, {
                    data: 'supplier_balance',
                    name: 'supplier_balance',
                    className: "text-right",
                    searchable: false,
                }],
                footerCallback: function(row, data, start, end, display) {
                    var api = this.api();
                    var json = api.ajax.json();

                    $("#total").html(Number(json.total).formatMoney());
                }
            })

            $("#balance").change(function() {
                refreshTable();
            })
        })

        function refreshTable() {
            $("#suppliersDataTable").DataTable().ajax.reload();
        }
    </script>
@endpush
