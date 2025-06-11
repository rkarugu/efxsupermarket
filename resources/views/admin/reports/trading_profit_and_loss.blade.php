@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h4 class="box-title">Trading Profit & Loss</h4>
            </div>
            <div class="box-header with-border">
                <form>
                    <div class="row">
                        <div class="col-sm-3">
                            <div class="form-group">
                                <input type="hidden" id="startDate" name="from">
                                <input type="hidden" id="endDate" name="to">
                                <label>Select Period</label>
                                <div class="reportRange">
                                    <i class="fa fa-calendar" style="padding:8px"></i>
                                    <span class="flex-grow-1" style="padding:8px"></span>
                                    <i class="fa fa-caret-down" style="padding:8px"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label for="branch">Branch</label>
                            <select name="branch" id="branch" class="form-control">
                                <option value="">All Branches</option>
                                @foreach ($locations as $location)
                                    <option value="{{ $location->id }}" @selected($location->id == request()->branch)>
                                        {{ $location->location_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label style="display:block">&nbsp;</label>
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-filter"></i>
                                Filter
                            </button>
                            <button type="submit" name="download" value="pdf" class="btn btn-primary">
                                <i class="fa fa-file-pdf"></i>
                                PDF
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="box-body">
                <table class="table no-border">
                    <tr>
                        <th>Sales</th>
                        <th style="width: 180px" class="text-right"></th>
                        <th style="width: 180px" class="text-right">
                            <a href="{{ route('trial-balance.account', ['account' => '56002-003', 'start-date' => request()->from, 'end-date' => request()->to]) }}"
                                target="_blank">
                                {{ manageAmountFormat($sales) }}
                            </a>
                        </th>
                    </tr>
                    <tr>
                        <th>Cost of Goods Sold</th>
                        <th colspan="2"></th>
                    </tr>
                    <tr>
                        <td>Opening Stock</td>
                        <td class="text-right">
                            <a href="{{ route('summary_report.inventory_sales_report', ['date' => request()->from]) }}"
                                target="_blank">{{ manageAmountFormat($openingStock) }}
                            </a>
                        </td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <th>Purchases</th>
                        <td class="text-right"></td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td>Goods Received</td>
                        <td class="text-right">
                            <a href="{{ route('trading-profit-and-loss.download', ['transactions' => 'grns', 'from' => request()->from, 'to' => request()->to]) }}"
                                target="_blank">
                                {{ manageAmountFormat($grns_total) }}
                            </a>
                        </td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td>Transfers In</td>
                        <td class="text-right">
                            <a href="{{ route('trading-profit-and-loss.download', ['transactions' => 'transfers-in', 'from' => request()->from, 'to' => request()->to]) }}"
                                target="_blank">
                                {{ manageAmountFormat($transfers_in_total) }}
                            </a>
                        </td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td>Transfers Out</td>
                        <td class="text-right">
                            <a href="{{ route('trading-profit-and-loss.download', ['transactions' => 'transfers-out', 'from' => request()->from, 'to' => request()->to]) }}"
                                target="_blank">
                                ({{ manageAmountFormat($transfers_out_total) }})
                            </a>
                        </td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td>Store Returns</td>
                        <td class="text-right">
                            <a href="{{ route('trading-profit-and-loss.download', ['transactions' => 'returns', 'from' => request()->from, 'to' => request()->to]) }}"
                                target="_blank">
                                ({{ manageAmountFormat($returns_total) }})
                            </a>
                        </td>
                        <td class="text-right"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td class="text-right border-top">
                            {{ manageAmountFormat($cog = $openingStock + $grns_total + $transfers_in_total - $transfers_out_total - $returns_total) }}
                        </td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Closing Stock</td>
                        <td class="text-right">
                            <a href="{{ route('summary_report.inventory_sales_report', ['date' => request()->to]) }}"
                                target="_blank">
                                {{ manageAmountFormat($closingStock) }}
                            </a>
                        </td>
                        <td class="text-right">{{ manageAmountFormat($netCost = $cog - $closingStock) }}</td>
                    </tr>
                    <tr>
                        <th>Gross Profit</th>
                        <td class="text-right border-top"></td>
                        <td class="text-right border-top">
                            {{ manageAmountFormat($grossProfit = $sales - $netCost) }}</td>
                    </tr>
                    <tr>
                        <th>Expenses</th>
                        <td colspan="2"></td>
                    </tr>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->getAccountDetail->account_name }}</td>
                            <td class="text-right">
                                <a href="{{ route('trial-balance.account', ['account' => $expense->account, 'start-date' => request()->from, 'end-date' => request()->to]) }}"
                                    target="_blank">
                                    {{ manageAmountFormat($expense->amount) }}
                                </a>
                            </td>
                            <td></td>
                        </tr>
                        @if ($loop->last)
                            <tr>
                                <td></td>
                                <td class="text-right">
                                    <a href="{{ route('trial-balance.account', ['account' => $expense->account, 'start-date' => request()->from, 'end-date' => request()->to]) }}"
                                        target="_blank">
                                        {{ manageAmountFormat($expense->amount) }}
                                    </a>
                                </td>
                                <td class="text-right">{{ manageAmountFormat($expenses->sum('amount')) }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <th>Net Profit</th>
                        <th class="text-right"></th>
                        <th class="text-right double-underline border-top">
                            {{ manageAmountFormat($grossProfit - $expenses->sum('amount')) }}</th>
                    </tr>
                </table>
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

        .border-top {
            border-top: 2px solid #111 !important;
            border-left: 10px solid #fff !important;
        }

        .double-underline {
            border-bottom: 6px double #000 !important;
            padding-bottom: 5px;
        }
    </style>
@endpush
@push('scripts')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(document).ready(function() {
            $("#branch").select2();

            var startDate = "{{ request()->from }}";
            var endDate = "{{ request()->to }}";

            let start = startDate ? moment(startDate) : moment().startOf('month');
            let end = endDate ? moment(endDate) : moment().endOf('month');

            $('.reportRange span').html(start.format('MMM D, YYYY') + ' - ' + end.format('MMM D, YYYY'));
            $("#startDate").val(start.format('YYYY-MM-DD'));
            $("#endDate").val(end.format('YYYY-MM-DD'));

            $('.reportRange').daterangepicker({
                startDate: start,
                endDate: end,
                alwaysShowCalendars: true,
                ranges: {
                    'Today': [moment(), moment()],
                    'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Last 7 Days': [moment().subtract(7, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')],
                    'This Year': [moment().startOf('year'), moment().endOf('year')],
                }
            });

            $('.reportRange').on('apply.daterangepicker', function(ev, picker) {
                $("#startDate").val(picker.startDate.format('YYYY-MM-DD'));
                $("#endDate").val(picker.endDate.format('YYYY-MM-DD'));

                $('.reportRange span').html(picker.startDate.format('MMM D, YYYY') + ' - ' + picker.endDate
                    .format('MMM D, YYYY'));
            });
        });
    </script>
@endpush
