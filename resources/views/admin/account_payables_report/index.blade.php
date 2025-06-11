@extends('layouts.admin.admin')

@section('content')
    @php
        $accountpayablesreports = [
            [
                'category_title' => 'Account Payables Summary Reports',
                'reports' => [
                    [
                        'title' => 'Supplier Ageing Analysis Report',
                        'route' => route('supplier-aging-analysis.index'),
                        'model' => 'supplier-aging-analysis',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Vat Report',
                        'route' => route('vat-report.index'),
                        'model' => 'vat-report',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Supplier Listing Report',
                        'route' => route('supplier-listing.index'),
                        'model' => 'supplier-listing',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Supplier Bank Listing Report',
                        'route' => route('supplier-bank-listing.index'),
                        'model' => 'supplier-bank-listing',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Supplier Statement Report',
                        'route' => route('maintain-suppliers.supplier-statement'),
                        'model' => 'supplier-statement',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Supplier Ledger Report',
                        'route' => route('maintain-suppliers.supplier-ledger-report'),
                        'model' => 'supplier-ledger-report',
                        'permission' => null,
                    ],
                    [
                        'title' => 'Approved Pending Voucher Report',
                        'route' => route('payment-vouchers.approved.pending.report'),
                        'model' => 'approved-pending-payments-voucher-report',
                        'permission' => null,
                    ],
                ],
            ],
        ];
    @endphp

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border  no-padding-h-b">
                <h3 class="box-title report-main-title">Account Payables Reports</h3>
                </p>
                @include('message')
            </div>

            <div class="box-body">
                <div class="row">

                    @foreach ($accountpayablesreports as $category)
                        <div class="col-md-4">
                            <ul class="list-group" style="cursor: pointer">
                                <li class="list-group-item">
                                    <span class="report-main-title-black">
                                        {{-- <div class="d-flex justify-content-between"> --}}
                                        <p>
                                            {{ $category['category_title'] }}
                                        </p>
                                        <form class="form-inline search-form">
                                            <input style="width:100%" type="text" class="form-control search-input"
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
