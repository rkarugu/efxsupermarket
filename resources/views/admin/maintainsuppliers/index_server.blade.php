@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Suppliers </h3>
                    <div class="d-flex">
                        @if (can('add', 'maintain-suppliers'))
                            <button data-toggle="modal" data-target="#my-suppliers-modal" data-backdrop="static"
                                class="btn btn-primary">
                                My Suppliers
                            </button>

                            <a href="{!! route($model . '.create') !!}" class="btn btn-success ml-12">Add {!! $title !!}</a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="box-body">
                @include('message')
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#goods" data-toggle="tab">Goods Suppliers</a></li>
                    <li><a href="#service" data-toggle="tab">Service Suppliers</a></li>
                    <li><a href="#dormant" data-toggle="tab">Dormant Suppliers</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="goods">
                        @include('admin.maintainsuppliers.partials.goodsTable')
                    </div>
                    <div class="tab-pane" id="service">
                        @include('admin.maintainsuppliers.partials.serviceTable')
                    </div>
                    <div class="tab-pane" id="dormant">
                        @include('admin.maintainsuppliers.partials.dormantTable')
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="my-suppliers-modal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> My Suppliers </h3>

                            <div>
                                <button type="button" class="close ml-12" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="box-body">
                        <table class="table" id="user-suppliers-table">
                            <thead>
                                <tr>
                                    <th style="width: 1%;">#</th>
                                    <th>Supplier </th>
                                    <th>Listed Items </th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="user-suppliers">
                            </tbody>
                        </table>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <x-suppliers.email-supplier-form />
    </section>
@endsection

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .span-action {
            display: inline-block;
            margin-right: 5px;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('js/form.js') }}"></script>
    <script type="text/javascript">
        window.user = {!! json_encode(getLoggeduserProfile()) !!}
        $(document).ready(function() {
            fetchUserSuppliers();
        });

        function fetchUserSuppliers() {
            let form = new Form();
            $("#user-suppliers").html('');
            window.userSuppliersTable?.destroy();
            $.ajax({
                url: "/api/user-suppliers",
                type: "get",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: window.user.id,
                },
                success: function(response) {
                    let suppliers = response.data;
                    let string = '';
                    for (let counter = 0; counter < suppliers.length; counter++) {
                        string += '<tr>' +
                            '<td style="width: 1%;">' + (counter + 1) + '</td>' +
                            '<td>' + suppliers[counter].supplier + '(' + suppliers[counter].supplier_code +
                            ')' + '</td>' +
                            '<td><a title="Open" target="_blank" href="/admin/maintain-suppliers/trade-agreement/' +
                            suppliers[counter].supplier_code + '">' + suppliers[counter].listed_items +
                            '</td>' +
                            '<td>' +
                            '<button onclick="removeSupplier(' + suppliers[counter].supplier_id +
                            ')" title="De-allocate" style="background-color: transparent !important; border: none !important"><i class="fas fa-user-times text-danger"></i></button>' +
                            '<a title="Stock report" href="/admin/reports/inventory-location-stock-report?supplier=' +
                            suppliers[counter].supplier_id +
                            '" target="_blank" class="ml-12"><i class="fas fa-file-pdf"></i></a>' +
                            '<a title="Inventory List" href="/admin/maintain-items" target="_blank" class="ml-12"><i class="fas fa-list"></i></a>' +
                            '</td>' +
                            '</tr>';
                    }

                    $("#user-suppliers").html(string);

                    window.userSuppliersTable = $('#user-suppliers-table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 10,
                        'initComplete': function(settings, json) {
                            let info = this.api().page.info();
                            let total_record = info.recordsTotal;
                            if (total_record < 11) {
                                $('#user-suppliers-table_paginate').hide();
                            }
                        },
                        'aoColumnDefs': [{
                            'bSortable': false,
                            'aTargets': 'noneedtoshort'
                        }],
                    });
                },
                error: function(response) {
                    // form.errorMessage(response.responseJSON?.message ?? response.responseJSON);
                }
            });
        }

        function addUserSupplier() {
            let form = new Form();
            let supplierId = $("#supplier-id").val();
            if (!supplierId) {
                return form.errorMessage('Select a supplier to add');
            }

            $("#loader-on").show();
            $.ajax({
                url: "/api/user-suppliers/add",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: window.user.id,
                    supplier_id: supplierId
                },
                success: function(response) {
                    $("#loader-on").hide();
                    form.successMessage('Supplier added successfully');
                    $("#supplier-id").val('');
                    fetchUserSuppliers();
                },
                error: function(response) {
                    $("#loader-on").hide();
                    form.errorMessage(response.responseJSON?.message ?? response.responseJSON);
                }
            });
        }

        function removeSupplier(supplierId) {
            let form = new Form();
            $("#loader-on").show();
            $.ajax({
                url: "/api/user-suppliers/remove",
                type: "post",
                data: {
                    _token: "{{ csrf_token() }}",
                    user_id: window.user.id,
                    supplier_id: supplierId
                },
                success: function(response) {
                    $("#loader-on").hide();
                    form.successMessage('Supplier de-allocated successfully');
                    fetchUserSuppliers();
                },
                error: function(response) {
                    $("#loader-on").hide();
                    form.errorMessage(response.responseJSON?.message ?? response.responseJSON);
                }
            });
        }
    </script>
@endpush
