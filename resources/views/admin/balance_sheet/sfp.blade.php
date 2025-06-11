@extends('layouts.admin.admin')

@section('content')
    <style>
        .buttons-excel {
            background-color: #f39c12 !important;
            border-color: #e08e0b !important;
            border-radius: 3px !important;
            -webkit-box-shadow: none !important;
            box-shadow: none !important;
            border: 1px solid transparent !important;
            color: #fff !important;
            display: inline-block !important;
            padding: 6px 12px !important;
            margin-bottom: 0 !important;
            font-size: 14px !important;
            font-weight: 400 !important;
            line-height: 1.42857143 !important;
            text-align: center !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }

        .dt-buttons {
            width: 10% !important;
            position: relative !important;
            left: 90px !important;
            top: -42px !important;
        }

        .dataTables_wrapper table th,
        td {
            border-right: none !important;
        }

        .borders-op {
            border-color: #000000 !important;
            border-bottom: 1px solid #000000 !important;
            border-right: 1px solid !important;
            border-left: 1px solid !important;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Detailed Balance Sheet Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to General Ledger Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    <div class="card-header">
                        <a href="{{ route('statement-financical-position.excel') }}" class="btn btn-danger"><i
                                class="fa fa-file-excel" aria-hidden="true"></i></a>
                    </div>

                    <div>
                        <div class="col-md-12 no-padding-h">


                            <table class="table table-responsive table-bordered" style="margin-top:40px;"
                                id="create_datatable1">


                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>MTD</th>
                                        <th>YTD-1</th>
                                        <th>YTD-2</th>
                                        <th>YTD-3</th>
                                    </tr>
                                </thead>
                                <tbody>


                                    <tr>
                                        <th colspan="5">
                                            Capital Employed
                                        </th>
                                    </tr>
                                    @php
                                        $e_this_month = $e_this_year = $e_previous_year = $e_two_year_back = 0;
                                    @endphp
                                    @foreach ($EQUITY as $equity)
                                        @foreach ($equity->getWaAccountGroup as $group)
                                            @foreach ($group->getChartAccount as $account)
                                                <tr>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td style="text-align:right">{{ abs($account->this_month_amount) }}</td>
                                                    <td style="text-align:right">{{ abs($account->this_year_amount) }}</td>
                                                    <td style="text-align:right">{{ abs($account->previous_year_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->two_year_back_amount) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $e_this_month += abs($account->this_month_amount);
                                                    $e_this_year += abs($account->this_year_amount);
                                                    $e_previous_year += abs($account->previous_year_amount);
                                                    $e_two_year_back += abs($account->two_year_back_amount);
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <th class="borders-op">Total</th>
                                        <th class="borders-op" style="text-align:right">{{ $e_this_month }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $e_this_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $e_previous_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $e_two_year_back }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                    </tr>

                                    <tr>
                                        <th colspan="5">
                                            Non-Current Liabilities
                                        </th>
                                    </tr>
                                    @php
                                        $nl_this_month = $nl_this_year = $nl_previous_year = $nl_two_year_back = 0;
                                    @endphp
                                    @foreach ($NONCURRENTLIABILITIES as $nonliabi)
                                        @foreach ($nonliabi->getWaAccountGroup as $group)
                                            @foreach ($group->getChartAccount as $account)
                                                <tr>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td style="text-align:right">{{ abs($account->this_month_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->this_year_amount) }}</td>
                                                    <td style="text-align:right">{{ abs($account->previous_year_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->two_year_back_amount) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $nl_this_month += abs($account->this_month_amount);
                                                    $nl_this_year += abs($account->this_year_amount);
                                                    $nl_previous_year += abs($account->previous_year_amount);
                                                    $nl_two_year_back += abs($account->two_year_back_amount);
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <th class="borders-op">Total</th>
                                        <th class="borders-op" style="text-align:right">{{ $nl_this_month }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $nl_this_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $nl_previous_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $nl_two_year_back }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="5">
                                            Non-Current Assets
                                        </th>
                                    </tr>
                                    @php
                                        $na_this_month = $na_this_year = $na_previous_year = $na_two_year_back = 0;
                                    @endphp
                                    @foreach ($NONCURRENTASSESTS as $nonassets)
                                        @foreach ($nonassets->getWaAccountGroup as $group)
                                            @foreach ($group->getChartAccount as $account)
                                                <tr>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td style="text-align:right">{{ abs($account->this_month_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->this_year_amount) }}</td>
                                                    <td style="text-align:right">{{ abs($account->previous_year_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->two_year_back_amount) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $na_this_month += abs($account->this_month_amount);
                                                    $na_this_year += abs($account->this_year_amount);
                                                    $na_previous_year += abs($account->previous_year_amount);
                                                    $na_two_year_back += abs($account->two_year_back_amount);
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <th class="borders-op">Total</th>
                                        <th class="borders-op" style="text-align:right">{{ $na_this_month }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $na_this_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $na_previous_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $na_two_year_back }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                    </tr>


                                    <tr>
                                        <th colspan="5">
                                            Current Assets
                                        </th>
                                    </tr>
                                    @php
                                        $ca_this_month = $ca_this_year = $ca_previous_year = $ca_two_year_back = 0;
                                    @endphp
                                    @foreach ($ASSETS as $asset)
                                        @foreach ($asset->getWaAccountGroup as $group)
                                            @foreach ($group->getChartAccount as $account)
                                                <tr>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td style="text-align:right">{{ abs($account->this_month_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->this_year_amount) }}</td>
                                                    <td style="text-align:right">{{ abs($account->previous_year_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->two_year_back_amount) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $ca_this_month += abs($account->this_month_amount);
                                                    $ca_this_year += abs($account->this_year_amount);
                                                    $ca_previous_year += abs($account->previous_year_amount);
                                                    $ca_two_year_back += abs($account->two_year_back_amount);
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <th class="borders-op">Total</th>
                                        <th class="borders-op" style="text-align:right">{{ $ca_this_month }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $ca_this_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $ca_previous_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $ca_two_year_back }}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <th colspan="5">
                                            Liabilities
                                        </th>
                                    </tr>
                                    @php
                                        $li_this_month = $li_this_year = $li_previous_year = $li_two_year_back = 0;
                                    @endphp
                                    @foreach ($LIABILITIES as $liabi)
                                        @foreach ($liabi->getWaAccountGroup as $group)
                                            @foreach ($group->getChartAccount as $account)
                                                <tr>
                                                    <td>{{ $account->account_name }}</td>
                                                    <td style="text-align:right">{{ abs($account->this_month_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->this_year_amount) }}</td>
                                                    <td style="text-align:right">{{ abs($account->previous_year_amount) }}
                                                    </td>
                                                    <td style="text-align:right">{{ abs($account->two_year_back_amount) }}
                                                    </td>
                                                </tr>
                                                @php
                                                    $li_this_month += abs($account->this_month_amount);
                                                    $li_this_year += abs($account->this_year_amount);
                                                    $li_previous_year += abs($account->previous_year_amount);
                                                    $li_two_year_back += abs($account->two_year_back_amount);
                                                @endphp
                                            @endforeach
                                        @endforeach
                                    @endforeach
                                    <tr>
                                        <th class="borders-op">Total</th>
                                        <th class="borders-op" style="text-align:right">{{ $li_this_month }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $li_this_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $li_previous_year }}</th>
                                        <th class="borders-op" style="text-align:right">{{ $li_two_year_back }}</th>
                                    </tr>










                                </tbody>


                            </table>





                        </div>


                    </div>
                </div>

                </form>
            </div>

            <br>



        </div>
        </div>
    </section>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script type="text/javascript">
        $(function() {
            $(".mlselec6t").select2();
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            minuteStep: 1,
        });
    </script>


    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script type="text/javascript" language="javascript"
        src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script type="text/javascript" class="init">
        $(document).ready(function() {
            $('#create_datatable1').DataTable({
                searching: false,
                "paging": false,
                "ordering": false,
                "info": false,
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excelHtml5',
                    text: '<i class="fa fa-file-excel" aria-hidden="true">',
                    footer: true
                }, ]
            });
        });
    </script>
@endsection
