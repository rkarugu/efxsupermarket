@extends('layouts.admin.admin')

@php
    $user = request()->user();

    $isAdmin = $user->role_id == 1;
@endphp

@section('content')
    <div class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                @include('message')
                <div class="row">
                    <div class="col-sm-9">
                        <form action="{{ route('admin.downloadExcel') }}" method="get">
                            <div class="row">
                                <div class="col-sm-3">
                                    <select name="branch" id="branch" class="form-control" @if (!$isAdmin && !isset($user->permissions['maintain-items___view-per-branch'])) disabled @endif>
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option 
                                                value="{{ $branch->id }}"
                                                @if ($branchId == $branch->id) selected @endif
                                            >
                                                {{ $branch->location_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <select name="bin" id="bin" class="form-control" @if (!$isAdmin && !isset($user->permissions['maintain-items___view-per-branch'])) disabled @endif>
                                        <option value="">Select bin</option>
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name="category" id="category" class="form-control">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->category_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-3">
                                    <select name="supplier" id="supplier" class="form-control">
                                        <option value="">Select Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <input type="hidden" name="productId" id="productId" value="{{ request()->productId }}"
                                    class="form-control" placeholder="Enter Product ID">
                                <div class="col-sm-1">
                                    <button type="submit" class="btn btn-primary" name="action" value="excel">
                                        <i class="fa fa-file-excel"></i>
                                        Excel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-sm-3">
                        <div class="text-right">
                            @if (can('add', $model))
                                <a href="{!! route($model . '.create') !!}" class="btn btn-success">
                                    <i class="fa fa-plus"></i>
                                    Add Item</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-hover" id="inventoryItemsDataTable">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Stock ID Code</th>
                            <th>Title</th>
                            <th>Item Category</th>
                            <th>Pack Size</th>
                            @if (can('price_list_cost', 'maintain-items'))
                                <th>Price List Cost</th>
                            @endif
                            @if (can('last_grn_cost', 'maintain-items'))
                                <th>Last GRN Cost</th>
                            @endif
                            @if (can('weighted_average_cost', 'maintain-items'))
                                <th>Weighted Cost</th>
                            @endif
                            @if (can('standard_cost', 'maintain-items'))
                                <th>Standard Cost</th>
                            @endif
                            <th>Selling Price</th>
                            <th>QOH</th>
                            <th>QOO</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <div class="modal fade" role="dialog" id="stockStatusModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="statusItemCode"></h4>
                </div>
                <div class="modal-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Store</th>
                                <th>Quantity On Hand</th>
                                <th>Max Stock</th>
                                <th>Reorder Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4">Loading...</td>
                            </tr>
                        </tbody>
                        <tfoot>

                        </tfoot>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .span-action {
            display: inline-block;
            margin: 0 3px;
        }
    </style>
@endpush
@push('scripts')
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script>
        const bins = @json($bins);
        const user = @json($user);
        const binId = @json($binId);

        $(function() {
            $("#category, #supplier, #branch", "#bin").select2();
            $("#category, #supplier").change(function() {
                refreshTable();
            });

            let branch = $('#branch').val();
            
            if (branch) {
                let select = $('#bin');

                let branchBins = bins.filter(bin => bin.branch_id == branch);
                
                branchBins.forEach(function (bin) {
                    let option = $('<option></option>', {
                        value: bin.id,
                        text: bin.title,
                        selected: user.role_id == 152 && bin.id == binId
                    });
                    
                    select.append(option)
                })
            }

            var table = $('#inventoryItemsDataTable').DataTable({
                processing: true,
                serverSide: true,
                order: [
                    [0, "desc"]
                ],
                pageLength: '<?= Config::get('params.list_limit_admin') ?>',
                ajax: {
                    url: '{!! route('maintain-items.index') !!}',
                    data: function(data) {
                        data.branch = $("#branch").val();
                        data.bin = $("#bin").val();
                        data.category = $("#category").val();
                        data.supplier = $("#supplier").val();
                        data.productId = $("#productId").val();
                    }
                },
                columns: [{
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'stock_id_code',
                        name: 'stock_id_code',
                    },
                    {
                        data: 'title',
                        name: 'title',
                    },
                    {
                        data: 'category.category_description',
                        name: 'category.category_description',
                        searchable: false,
                    },
                    {
                        data: 'pack_size.title',
                        name: 'packSize.title',
                        searchable: false,
                    },
                    @if (can('price_list_cost', 'maintain-items'))
                        {
                            data: 'price_list_cost',
                            name: 'price_list_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    @endif
                    @if (can('last_grn_cost', 'maintain-items'))
                        {
                            data: 'last_grn_cost',
                            name: 'last_grn_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    @endif
                    @if (can('weighted_average_cost', 'maintain-items'))
                        {
                            data: 'weighted_average_cost',
                            name: 'weighted_average_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    @endif
                    @if (can('standard_cost', 'maintain-items'))
                        {
                            data: 'standard_cost',
                            name: 'standard_cost',
                            className: 'text-right',
                            searchable: false,
                        },
                    @endif {
                        data: 'selling_price',
                        name: 'selling_price',
                        className: 'text-right',
                        searchable: false,
                    },
                    {
                        data: 'qty_on_hand',
                        name: 'items.qty_on_hand',
                        className: 'text-right',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'qty_on_order',
                        name: 'orders.qty_on_order',
                        className: 'text-right',
                        searchable: false,
                        orderable: false,
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false,
                        orderable: false,
                        width: "100px",
                        className: 'text-center'
                    },
                ],
            });

            $('.table tbody').on('click', '[data-toggle="delete"]', function() {
                let target = $(this).data('target');

                Swal.fire({
                    title: 'Confirm',
                    text: 'Are you sure want to delete the item?',
                    showCancelButton: true,
                    confirmButtonColor: '#252525',
                    cancelButtonColor: 'red',
                    confirmButtonText: 'Yes, Delete It',
                    cancelButtonText: `No, Cancel It`,
                }).then((result) => {
                    if (result.isConfirmed) {
                        $(target).submit();
                    }
                })
            });

            $('.table tbody').on('click', '[data-toggle="item"]', function(e) {
                e.preventDefault();
                let itemCode = $(this).data('item-code');
                let itemTitle = $(this).data('item-title');
                $("#statusItemCode").text(itemCode + ' - ' + itemTitle);
                $("#stockStatusModal").modal('show')

                $.ajax({
                    url: "{{ route('maintain-items.item-stock-status') }}",
                    method: "GET",
                    data: {
                        "item_code": itemCode
                    },
                    success: function(response) {
                        let tbody = $("#stockStatusModal table tbody");
                        let tfoot = $("#stockStatusModal table tfoot");
                        let rows = '';
                        response.locations.forEach(function(location) {
                            rows += `
                            <tr>
                                <td>${location.location_name}</td>
                                <td>${location.qty_on_hand}</td>
                                <td>${location.max_stock}</td>
                                <td>${location.re_order_level}</td>
                            </tr>`;
                        });

                        tbody.html(rows);

                        tfoot.html(`
                            <tr>
                                <th>Total</th>
                                <th>${response.total_qty_on_hand}</th>
                                <td></td>
                                <td></td>
                            </tr>
                        `)
                    },
                    error: function(error) {

                    }
                });
            });

            $("#stockStatusModal").on('hide.bs.modal', function() {
                let tbody = $("#stockStatusModal table tbody");
                tbody.html('<tr><td colspan="4">Loading...</td></tr>')
            })
        });

        function refreshTable() {
            $("#inventoryItemsDataTable").DataTable().ajax.reload();
        }

        function fetchCompetingBrands(itemId) {
            $.ajax({
                url: 'fetch-competing-brands/' + itemId,
                method: 'GET',
                success: function(data) {
                    let tableBody = $('#competingBrandsTableBody');
                    $('#itemName').text(data.itemName);
                    tableBody.empty();
                    data.competingBrands.forEach(function(brand, index) {
                        let qoh = brand.qoh !== null ? brand.qoh : 0;
                        tableBody.append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${brand.stock_id_code}</td>
                                <td>${brand.title}</td>
                                <td>${brand.standard_cost}</td>
                                <td>${brand.selling_price}</td>
                                <td>${qoh}</td>
                            </tr>
                        `);
                    });
                },
                error: function() {
                    alert('Failed to fetch competing brands. Please try again later.');
                }
            });
        }

        function showCloneModal(itemId) {
            $('#cloneItemId').val(itemId);
            $('#cloneItemModal').modal('show');
        }

        $('#branch').on('change', function () {
            let branch = $(this).val();
            
            let select = $('#bin');

            select.empty();
            select.append(`
                <option value="">Select Bin</option>
            `)

            if (branch) {
                let branchBins = bins.filter(bin => bin.branch_id == $('#branch').val());
                
                branchBins.forEach(function (bin) {
                    let option = $('<option></option>', {
                        value: bin.id,
                        text: bin.title,
                    });
                    
                    select.append(option)
                })
            }
        })

        $('#bin').on('change', function () {
            refreshTable()
        })
    </script>
@endpush
