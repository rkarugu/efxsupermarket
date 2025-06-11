@extends('layouts.admin.admin')

@section('content')
    @php
        $logged_user_info = getLoggeduserProfile();
        $my_permissions = $logged_user_info->permissions;
        $route_name = \Route::currentRouteName();

        $salesandreceivablesreports = [
            [
                'category_title' => 'Sales Summary Reports',
                'reports' => [
                    [
                        'title' => 'Sales Summary Report',
                        'route' => route('summary_report.sales_summary'),
                        'model' => 'summary_report',
                        'permission' => 'summary-report___sales_summary',
                    ],
                    [
                        'title' => 'Sales of Product by Date Report',
                        'route' => route('summary_report.detailed_sales_report'),
                        'model' => 'detailed_sales_report',
                        'permission' => 'summary-report___detailed_sales_report',
                    ],
                    [
                        'title' => 'Sales by Date Report',
                        'route' => route('summary_report.sales_by_date_report'),
                        'model' => 'sales_by_date_report',
                        'permission' => 'summary-report___sales_by_date_report',
                    ],
                    [
                        'title' => 'Daily Sales Report',
                        'route' => route('route-reports.daily-sales'),
                        'model' => 'route-daily-sales-report',
                        'permission' => 'route-reports___weekly-sales-report',
                    ],
                    [
                        'title' => 'Promotion Sales Report',
                        'route' => route('sales-and-receivables-reports.promotion-sales-report'),
                        'model' => 'promotion-sales-report',
                        'permission' => 'sales-and-receivables-reports___promotion-sales',
                    ],
                    [
                        'title' => 'Discount Sales Report',
                        'route' => route('discount-sales-report'),
                        'model' => 'discount-sales-report',
                        'permission' => 'sales-and-receivables-reports___discount-sales',
                    ],
                    [
                        'title' => 'Sales Per Supplier Per Route Report',
                        'route' => route('sales-per-supplier-per-route'),
                        'model' => 'sales-per-supplier-per-route',
                        'permission' => 'sales-and-receivables-reports___sales-per-supplier-per-route',
                    ],
                    [
                        'title' => 'Sales Analysis Report',
                        'route' => route('sales-analysis-report'),
                        'model' => 'sales-analysis-report',
                        'permission' => 'sales-and-receivables-reports___sales-analysis',
                    ],
                    [
                        'title' => 'Shift Summary Report',
                        'route' => route('sales-and-receivables-reports.shift-summary'),
                        'model' => null,
                        'permission' => 'sales-and-receivables-reports___shift-summary',
                    ],
                    [
                        'title' => 'Salesman Trip Summary Report',
                        'route' => route('sales-and-receivables-reports.salesman-trip-summary'),
                        'model' => null,
                        'permission' => 'sales-and-receivables-reports___salesman-trip-summary',
                    ],
                    [
                        'title' => 'Route Profitibility Report',
                        'route' => route('gross-profit.route-profitibility-report'),
                        'model' => 'route-profitibility-report',
                        'permission' => 'route-profitibility-report___view',
                    ],
                    [
                        'title' => 'Dispatch Items Report',
                        'route' => route('dispatched_items.report'),
                        'model' => 'dispatched_items_report',
                        'permission' => 'dispatch-pos-invoice-sales___dispatch-report',
                    ],
                    [
                        'title' => 'Inventory Valuation Report',
                        'route' => route('summary_report.inventory_sales_report'),
                        'model' => 'dispatched_items_report',
                        'permission' => 'summary-report___inventory_sales_report',
                    ],
                    [
                        'title' => 'Customer Aging Analysis Report',
                        'route' => route('customer-aging-analysis.index'),
                        'model' => 'customer-aging-analysis',
                        'permission' => 'sales-and-receivables-reports___customer-aging-analysis',
                    ],
                    [
                        'title' => 'Customer Statement Report',
                        'route' => route('sales-and-receivables-reports.customer_statement'),
                        'model' => 'customer-statement',
                        'permission' => 'sales-and-receivables-reports___customer-statement',
                    ],
                    [
                        'title' => 'Loading Schedule vs Stocks Report',
                        'route' => route('sales-and-receivables-reports.loading-schedule-vs-sales-report'),
                        'model' => 'loading-schedule-vs-sales-report',
                        'permission' => 'sales-and-receivables-reports___loading-schedule-vs-stock-report',
                    ],
                    [
                        'title' => 'Delivery Schedule Report',
                        'route' => route('sales-and-receivables-reports.delivery-schedule-report'),
                        'model' => 'delivery-schedule-report',
                        'permission' => 'sales-and-receivables-reports___delivery-schedule-report',
                    ],
                    [
                        'title' => 'Customer Balances Report',
                        'route' => route('sales-and-receivables-reports.customer-balances-report'),
                        'model' => 'customer-balances-report',
                        'permission' => 'sales-and-receivables-reports___customer-balances-report',
                    ],
                    [
                        'title' => 'Route Performance Report',
                        'route' => route('sales-and-receivables-reports.route-performance-report'),
                        'model' => 'route-performance-report',
                        'permission' => 'sales-and-receivables-reports___route-performance-report',
                    ],
                    [
                        'title' => 'Group Performance Report',
                        'route' => route('sales-and-receivables-reports.group-performance-report'),
                        'model' => 'group-performance-report',
                        'permission' => 'sales-and-receivables-reports___group-performance-report',
                    ],
                    [
                        'title' => 'Customer Invoices Report',
                        'route' =>
                            route('sales-and-receivables-reports.customer_invoices') .
                            getReportDefaultFilterForTrialBalance(),
                        'model' => null,
                        'permission' => 'sales-and-receivables-reports___customer_invoices',
                    ],
                    [
                        'title' => 'Unsigned Invoices Report',
                        'route' =>
                            route('sales-and-receivables-reports.unassigned_invoices') .
                            getReportDefaultFilterForTrialBalance(),
                        'model' => null,
                        'permission' => 'sales-and-receivables-reports___customer_invoices',
                    ],
                    [
                        'title' => 'Daily Cash Receipt Summary Report',
                        'route' =>
                            route('sales-and-receivables-reports.daily-cash-receipt-summary') .
                            getReportDefaultFilterForTrialBalance(),
                        'model' => null,
                        'permission' => 'sales-and-receivables-reports___daily-cash-receipt-summary',
                    ],
                    [
                        'title' => 'Vat Report',
                        'route' => route('customer-aging-analysis.vatReport'),
                        'model' => 'total-vat-report',
                        'permission' => 'sales-and-receivables-reports___vat-report',
                    ],
                    [
                        'title' => 'ESD Vat Report',
                        'route' => route('customer-aging-analysis.esdVatReport'),
                        'model' => 'esd-vat-report',
                        'permission' => 'sales-and-receivables-reports___vat-report',
                    ],
                    [
                        'title' => 'Till Direct Banking Report',
                        'route' => route('sales-and-receivables-reports.till-direct-banking-report'),
                        'model' => 'till-direct-banking-report',
                        'permission' => 'sales-and-receivables-reports___till-direct-banking-report',
                    ],
                    [
                        'title' => 'Sales Vs Stocks Ledger Report',
                        'route' => route('summary_report.sales_vs_stocks_ledger'),
                        'model' => 'till-direct-banking-report',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Gross Profit Summary Report',
                        'route' => route('gross-profit.inventory-valuation-report'),
                        'model' => 'dispatched_items_report',
                        'permission' => 'sales-and-receivables-reports___gross-profit',
                    ],
                    [
                        'title' => 'EOD Detailed Report',
                        'route' => route('summary_report.index'),
                        'model' => 'summary_report',
                        'permission' => 'summary-report___detailed',
                    ],
                    [
                        'title' => 'EOD Summary Report',
                        'route' => route('summary_report.summaryindex'),
                        'model' => 'summary_report',
                        'permission' => 'summary-report___summary',
                    ],
                    [
                        'title' => 'Dashboard Report',
                        'route' => route('dashboard_report.index', ['show_dashboard' => 1]),
                        'model' => 'dashboard_report',
                        'permission' => 'sales-and-receivables-reports___dashboard-report',
                    ],
                ],
            ],
        ];
    @endphp

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border  no-padding-h-b">
                <h3 class="box-title report-main-title">Sales and Receivables Reports</h3>
                </p>
                @include('message')
            </div>
            <div class="box-body">
                <div class="row">

                    @foreach ($salesandreceivablesreports as $category)
                        <div class="col-md-4">
                            <ul class="list-group" style="cursor: pointer">
                                <li class="list-group-item">
                                    <span class="report-main-title-black">
                                        {{-- <div class="d-flex justify-content-between"> --}}
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
                        return;
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
