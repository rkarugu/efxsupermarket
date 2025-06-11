@extends('layouts.admin.admin')

@section('content')
    <style>
        .span-action {

            display: inline-block;
            margin: 0 3px;

        }
    </style>
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Sales Summary Report</h3>
                    <a href="{{ url()->previous() }}" class="btn btn-success" role="button">
                        Back</a>
                </div>
            </div>

            <div class="box-body">

                @include('message')
                <div class="col-md-12 no-padding-h table-responsive">
                    <form action="{{ route('summary_report.sales_summary') }}" method="GET">
                        <div class="row">
                            <div class="col-md-3 form-group">
                                <label for="">Choose From Date</label>
                                <input type="date" name="date" id="date" class="form-control"
                                    value="{{ request()->date }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Choose To Date</label>
                                <input type="date" name="todate" id="todate" class="form-control"
                                    value="{{ request()->todate }}">
                            </div>
                            <div class="col-md-3 form-group">
                                <label for="">Branch</label>
                                <select name="branch" id="mlselec6t" class="form-control mlselec6t" required>
                                    <option value="" selected disabled>--Select Branch--</option>
                                    @foreach (getBranchesDropdown() as $key => $branch)
                                        <option value="{{ $key }}"
                                            {{ request()->branch == $key ? 'selected' : '' }}>{{ $branch }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 ">
                                <br>
                                <input type="submit" name="filter" value="Filter" class="btn btn-success">
                                <input type="submit" name="download" value="Download" class="btn btn-success">

                                <button type="button" class="btn btn-danger" onclick="printgrn();return false;">Print
                                    Report</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    <h4>Sales</h4>
                    <table class="table table-bordered table-hover" id="create_datatable">
                        <thead>
                            <tr>
                                <td>DATE</td>
                                <td style="text-align:right">VATABLE SALES</td>
                                <td style="text-align:right">16% VAT</td>
                                <td style="text-align:right">ZERO RATED</td>
                                <td style="text-align:right">EXEMPT</td>
                                <td style="text-align:right">TOTAL SALES</td>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_sales_all = $total_vat_16 = $total_vat_0 = $total_vat_exempt = $total_tax = 0;
                            
                            ?>
                            @foreach ($salesData as $data)
                                @php
                                    $sales_date = \Carbon\Carbon::parse($data->sales_date)->toDateString();
                                @endphp
                                <tr class="item">
                                    <td>{{ \Carbon\Carbon::parse($data->sales_date)->toDateString() }}</td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report', ['date' => $sales_date, 'vat' => 1, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat($data->total_sale_16 - ($data->returns_16 ?? 0) - $data->total_vat_amount_16 + (16 * $data->returns_16) / 116) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report', ['date' => $sales_date, 'vat' => 1, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat($data->total_vat_amount_16 - (16 * $data->returns_16) / 116) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report', ['date' => $sales_date, 'vat' => 2, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat($data->total_sale_0 - ($data->returns_0 ?? 0)) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report', ['date' => $sales_date, 'vat' => 3, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat($data->total_sale_exempt - ($data->returns_exempt ?? 0)) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report', ['date' => $sales_date, 'vat' => 3, 'branch' => request()->branch, 'type' => 'all']) }}"
                                            target="_blank">{{ manageAmountFormat($data->total_sales - ($data->returns_16 ?? 0) - ($data->returns_0 ?? 0) - ($data->returns_exempt ?? 0)) }}</a>
                                    </td>

                                </tr>
                                <?php
                                $total_tax += $data->total_vat_amount_16 - (16 * $data->returns_16) / 116;
                                $total_sales_all += $data->total_sales - ($data->returns_16 ?? 0) - ($data->returns_0 ?? 0) - ($data->returns_exempt ?? 0);
                                $total_vat_16 += $data->total_sale_16 - ($data->returns_16 ?? 0) - $data->total_vat_amount_16 + (16 * $data->returns_16) / 116;
                                $total_vat_0 += $data->total_sale_0 - ($data->returns_0 ?? 0);
                                $total_vat_exempt += $data->total_sale_exempt - ($data->returns_exempt ?? 0);
                                ?>
                            @endforeach
                            <tr style="    border-top: 2px dashed #cecece;">
                                <td colspan="6"></td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_vat_16) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_tax) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_vat_0) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_vat_exempt) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_sales_all) }}</th>

                            </tr>
                            <tr style="border-top: 2px dashed #cecece;">
                                <td colspan="6"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-12 no-padding-h table-responsive">
                    <h4>Stock Take Sales</h4>
                    <table class="table table-bordered table-hover" id="create_datatable_25">
                        <thead>
                            <tr>
                                <td>DATE</td>
                                <td style="text-align:right">VATABLE SALES</td>
                                <td style="text-align:right">16% VAT</td>
                                <td style="text-align:right">ZERO RATED</td>
                                <td style="text-align:right">EXEMPT</td>
                                <td style="text-align:right">TOTAL SALES</td>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $total_sales_all = $total_vat_16 = $total_vat_0 = $total_vat_exempt = $total_tax = 0;
                            
                            ?>
                            @foreach ($stockSaleSummary as $data)
                                @php
                                    $sales_date = \Carbon\Carbon::parse($data->sales_date)->toDateString();
                                @endphp
                                <tr class="item">
                                    <td>{{ \Carbon\Carbon::parse($data->sales_date)->toDateString() }}</td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report.stock-sales', ['date' => $sales_date, 'vat' => 1, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat($data->stock_sale_16 - ($data->stock_return_16 ?? 0) - ($data->stock_sale_vat_16 ?? 0) + ($data->stock_return_vat_16 ?? 0)) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report.stock-sales', ['date' => $sales_date, 'vat' => 1, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat(($data->stock_sale_vat_16 ?? 0) - ($data->stock_return_vat_16 ?? 0)) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report.stock-sales', ['date' => $sales_date, 'vat' => 2, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat(($data->sales_zero_rated ?? 0) - ($data->returns_zero_rated ?? 0)) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report.stock-sales', ['date' => $sales_date, 'vat' => 3, 'branch' => request()->branch]) }}"
                                            target="_blank">{{ manageAmountFormat(($data->sales_exempt ?? 0) - ($data->returns_exempt ?? 0)) }}</a>
                                    </td>
                                    <td style="text-align:right"><a
                                            href="{{ route('detailed-sales-summary-report.stock-sales', ['date' => $sales_date, 'vat' => 3, 'branch' => request()->branch, 'type' => 'all']) }}"
                                            target="_blank">{{ manageAmountFormat(($data->total_sales ?? 0) - ($data->total_returns ?? 0)) }}</a>
                                    </td>

                                </tr>
                                <?php
                                $total_tax += ($data->stock_sale_vat_16 ?? 0) - ($data->stock_return_vat_16 ?? 0);
                                $total_sales_all += ($data->total_sales ?? 0) - ($data->total_returns ?? 0);
                                $total_vat_16 += $data->stock_sale_16 - ($data->stock_return_16 ?? 0) - ($data->stock_sale_vat_16 ?? 0) + ($data->stock_return_vat_16 ?? 0);
                                $total_vat_0 += ($data->sales_zero_rated ?? 0) - ($data->returns_zero_rated ?? 0);
                                $total_vat_exempt += ($data->sales_exempt ?? 0) - ($data->returns_exempt ?? 0);
                                ?>
                            @endforeach
                            <tr style="    border-top: 2px dashed #cecece;">
                                <td colspan="6"></td>
                            </tr>
                            <tr>
                                <th>Total</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_vat_16) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_tax) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_vat_0) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_vat_exempt) }}</th>
                                <th style="text-align:right">{{ manageAmountFormat($total_sales_all) }}</th>




                            </tr>
                            <tr style="border-top: 2px dashed #cecece;">
                                <td colspan="6"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>


    </section>
@endsection
@section('uniquepagescript')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        function printgrn() {
            jQuery.ajax({
                url: '{{ route('summary_report.sales_summary') }}',
                async: false, //NOTE THIS
                type: 'GET',
                data: {
                    'date': $('#date').val(),
                    'todate': $('#todate').val(),
                    'request_type': 'print'
                },
                success: function(response) {

                    var divContents = response;
                    //alert(divContents);
                    var printWindow = window.open('', '', 'width=600');
                    printWindow.document.write(divContents);
                    printWindow.document.close();
                    printWindow.print();
                    printWindow.close();
                }
            });
        }
        $(function() {
            $(".mlselec6t").select2();
        });
    </script>
@endsection
