@extends('layouts.admin.admin')

@section('content')
    @php
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        $route_name = \Route::currentRouteName();

        $inventoryreports = [
            [
                'category_title' => 'Stock Summary Reports',
                'reports' => [
                    [
                        'title' => 'Valuation Report',
                        'route' => route('summary_report.inventory_sales_report'),
                        'model' => 'inventory_sales_report',
                        'permission' => 'inventory-reports___inventory-valuation-report',
                    ],
                    [
                        'title' => 'Suggested Order Report',
                        'route' => route('reports.suggested_order_report'),
                        'model' => 'maintain-items-suggested-order-report',
                        'permission' => 'maintain-items___suggested_order_report',
                    ],
                    [
                        'title' => 'Inventory -Ve Stock Report',
                        'route' => route('reports.items_negetive_listing'),
                        'model' => 'items_negetive_listing',
                        'permission' => 'maintain-items___negetive_stock_report',
                    ],
                    [
                        'title' => 'Average Sales Vs Max Stock Report',
                        'route' => route('inventory-reports.average-sales-report.index'),
                        'model' => 'average-sales-report',
                        'permission' => 'inventory-reports___average-sales-report',
                    ],
                    [
                        'title' => 'Max Stock Report',
                        'route' => route('inventory-reports.max-stock-report.index'),
                        'model' => 'max-stock-report',
                        'permission' => 'inventory-reports___max-stock-report',
                    ],
                    [
                        'title' => 'Missing Items Report',
                        'route' => route('inventory-reports.missing-items-report.index'),
                        'model' => 'missing-items-report',
                        'permission' => 'inventory-reports___missing-items-report',
                    ],
                    [
                        'title' => 'Discount Items Report',
                        'route' => route('items-with-discounts-reports'),
                        'model' => 'discount-items-report',
                        'permission' => 'inventory-reports___discount-items',
                    ],
                    [
                        'title' => 'Promotion Items Report',
                        'route' => route('items-with-promotions-reports'),
                        'model' => 'promotion-items-report',
                        'permission' => 'inventory-reports___promotion-items',
                    ],
                    [
                        'title' => 'Overstock Report',
                        'route' => route('inventory-reports.overstock-report.index'),
                        'model' => 'overstock-report',
                        'permission' => 'inventory-reports___overstock-report',
                    ],
                    [
                        'title' => 'Inactive Report',
                        'route' => route('inventory-reports.inactive-stock-report.index'),
                        'model' => 'inactive-stock-report',
                        'permission' => 'inventory-reports___inactive-stock-report',
                    ],
                    [
                        'title' => 'Dead Stock Report',
                        'route' => route('inventory-reports.dead-stock-report.index'),
                        'model' => 'dead-stock-report',
                        'permission' => 'inventory-reports___dead-stock-report',
                    ],
                    [
                        'title' => 'Slow Moving Items Report',
                        'route' => route('reports.slow_moving_items_report'),
                        'model' => 'slow-moving-items-report',
                        'permission' => 'inventory-reports___slow-moving-items-report',
                    ],
                    [
                        'title' => 'Child Vs Mother Qoh Report',
                        'route' => route('child-vs-mother-qoh'),
                        'model' => 'child-vs-mother-qoh-report',
                        'permission' => 'inventory-reports___child-vs-mother-qoh',
                    ],
                    [
                        'title' => 'Missing Split Report',
                        'route' => route('report.missingsplit-report'),
                        'model' => 'missing-split-report',
                        'permission' => 'inventory-reports___missing-split-report',
                    ],
                    [
                        'title' => 'CTN without Children Report',
                        'route' => route('report.ctn-no-child'),
                        'model' => 'CTN-without-children',
                        'permission' => 'inventory-reports___CTN-without-children',
                    ],
                    [
                        'title' => 'Price Timeline Report',
                        'route' => route('reports.price_timeline_report'),
                        'model' => 'price-timeline-report',
                        'permission' => 'inventory-reports___price-timeline-report',
                    ],
                    [
                        'title' => 'Items List Report',
                        'route' => route('reports.items_list_report'),
                        'model' => 'items-list-report',
                        'permission' => 'inventory-reports___items-list-report',
                    ],
                    [
                        'title' => 'Reported Issues Report',
                        'route' => route('procurement-reported-shift-issues.index'),
                        'model' => 'procurement-reported-shift-issues',
                        'permission' => 'reported-shift-issues___view',
                    ],
                ],
            ],
            [
                'category_title' => 'Supplier Summary Reports',
                'reports' => [
                    [
                        'title' => 'Supplier Product Report',
                        'route' => route('inventory-reports.supplier-product-reports'),
                        'model' => null,
                        'permission' => 'inventory-reports___supplier-product-reports',
                    ],
                    [
                        'title' => 'GRN Summary by Supplier Report',
                        'route' => route('inventory-reports.grn-summary-by-supplier-report.index'),
                        'model' => 'grn-summary-by-supplier-report',
                        'permission' => 'inventory-reports___grn-summary-by-supplier-report',
                    ],
                    [
                        'title' => 'No Supplier Items Report',
                        'route' => route('reports.no_supplier_items_report'),
                        'model' => 'no-supplier-items-report',
                        'permission' => 'inventory-reports___no-supplier-items-report',
                    ],
                    [
                        'title' => 'Supplier User Report',
                        'route' => route('reports.supplier_user_report'),
                        'model' => 'supplier-user-report',
                        'permission' => 'inventory-reports___supplier-user-report',
                    ],
                    [
                        'title' => 'Sub Distributor Suppliers Report',
                        'route' => route('reports.sub_distributor_report'),
                        'model' => 'sub-distributor-suppliers-report',
                        'permission' => 'inventory-reports___sub-distributor-suppliers-report',
                    ],
                ],
            ],
            [
                'category_title' => 'Location Summary Reports',
                'reports' => [
                    [
                        'title' => 'Location Stock Report',
                        'route' => route('reports.inventory_location_stock_summary'),
                        'model' => 'inventory_location_stock_summary',
                        'permission' => 'inventory-reports___inventory-location-stock-report',
                    ],
                    [
                        'title' => 'Location Stock As At Report',
                        'route' => route('reports.inventory_location_as_at'),
                        'model' => 'inventory_location_as_at',
                        'permission' => 'inventory-reports___inventory-location-as-at',
                    ],
                    [
                        'title' => 'Items Data Sales Report',
                        'route' => route('reports.items-data-sales'),
                        'model' => 'items-data-sales',
                        'permission' => 'inventory-reports___items-data-sales',
                    ],
                    [
                        'title' => 'Items Data Purchases Report',
                        'route' => route('reports.items_data_purchase_report'),
                        'model' => 'promotion-items-report',
                        'permission' => 'inventory-reports___promotion-items',
                    ],
                    [
                        'title' => 'Transfers Inwards Report',
                        'route' => route('reports.transfer_inwards_report'),
                        'model' => 'transfer-inwards-report',
                        'permission' => 'inventory-reports___transfer-inwards-report',
                    ],
                    [
                        'title' => 'Item Sales Route Performance Report',
                        'route' => route('reports.route_performance_report'),
                        'model' => 'item-sales-route-performance-report',
                        'permission' => 'inventory-reports___item-sales-route-performance-report',
                    ],
                ],
            ],
        ];
    @endphp

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border  no-padding-h-b">
                <h3 class="box-title report-main-title">Inventory Reports</h3>
                </p>
                @include('message')
            </div>
            <div class="box-body">
                <div class="row">

                    @foreach ($inventoryreports as $category)
                        <div class="col-md-4">
                            <ul class="list-group" style="cursor: pointer">
                                <li class="list-group-item">
                                    <span class="report-main-title-black">
                                        {{-- <div class="d-flex"> --}}
                                        <p>
                                            {{ $category['category_title'] }}
                                        </p>
                                        <form class="form-inline search-form">
                                            <input style="width: 100%" type="text" class="form-control search-input"
                                                placeholder="Search reports...">
                                        </form>
                                        {{-- </div> --}}
                                    </span>
                                </li>
                                @foreach ($category['reports'] as $item)
                                    @if ($item['permission'])
                                        @if ($logged_user_info->role_id == 1 || isset($my_permissions[$item['permission']]))
                                            <li class="list-group-item @if (isset($model) && $model == $item['model']) active @endif">
                                                <a href="{!! $item['route'] !!}">
                                                    <div class="d-flex justify-content-between">
                                                        <span class="report-title">{{ $item['title'] }}</span>
                                                        <span class="report-title"><i class="fas fa-angle-right"></i></span>
                                                    </div>
                                                </a>
                                            </li>
                                        @endif
                                    @else
                                        <li class="list-group-item @if (isset($model) && $model == $item['model']) active @endif">
                                            <a href="{!! $item['route'] !!}">
                                                <div class="d-flex justify-content-between">
                                                    <span class="report-title">{{ $item['title'] }}</span>
                                                    <span class="report-title"><i class="fas fa-angle-right"></i></span>
                                                </div>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </section>
@endsection
@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        .reportRange {
            display: flex;
            align-content: center;
            justify-content: stretch;
            border: 1px solid #eee;
            cursor: pointer;
            height: 35px;
        }

        .report-title {
            color: black;
            font-weight: normal;
        }

        .report-main-title {
            font-weight: bolder;
            font-size: 14px;
            color: black;
        }

        .report-main-title-black {
            color: black;
            font-weight: bolder
        }
    </style>
@endpush
@push('scripts')
    <script type="text/javascript" src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}">
    </script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.search-form .search-input').on('keyup', function() {
                var query = $(this).val().toLowerCase();
                $(this).closest('.list-group').find('li').each(function() {
                    var $this = $(this);
                    if ($this.hasClass('search-form') || $this.find('form').length > 0) {
                        return; // Skip the search form li
                    }
                    var title = $this.find('.report-title').text().toLowerCase();
                    if (title.indexOf(query) > -1) {
                        $this.show();
                    } else {
                        $this.hide();
                    }
                });
            });
        });
    </script>
@endpush
