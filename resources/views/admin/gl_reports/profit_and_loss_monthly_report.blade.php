@extends('layouts.admin.admin')

@section('content')
    <style>
        th {
            font-weight: 600
        }

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

        #bgGreen th {
            background-color: green;
            color: #fff;
            font-weight: bold;
        }

        .bgGreen th {
            background-color: #86ea86;
            color: #000;
            font-weight: 600;
        }
    </style>

    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Profit & Loss Monthly Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to General Ledger Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    <div class="card-header">
                    </div>

                    <div>
                        <div class="col-md-12 no-padding-h" style="overflow:auto">
                            <div style="height: 150px ! important;">
                                <div class="card-header">
                                    <i class="fa fa-filter"></i> Filter
                                </div><br>
                                {!! Form::open(['route' => 'profit-and-loss.monthlyProfitSummary', 'method' => 'post', 'id' => 'thisfilterOk']) !!}
                                <!-- <input type="hidden" name="manage-request" value="" id="manage_request"> -->
                                <div>
                                    <div class="col-md-12 no-padding-h">
                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="">Start Date</label>
                                                {!! Form::text('start-date', null, [
                                                    'class' => 'datepicker form-control',
                                                    'placeholder' => 'Start Date',
                                                    'readonly' => true,
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-3">
                                            <div class="form-group">
                                                <label for="">End Date</label>
                                                {!! Form::text('end-date', null, [
                                                    'class' => 'datepicker form-control',
                                                    'placeholder' => 'End Date',
                                                    'readonly' => true,
                                                ]) !!}
                                            </div>
                                        </div>

                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label for="">TB Branch Code</label>
                                                {!! Form::select('restaurant[]', $restroList, null, [
                                                    'class' => 'form-control mlselec6t multiselect',
                                                    'id' => 'restaurant_id_set',
                                                    'multiple' => 'multiple',
                                                    'disabled' => request()->all_branches ? true : false,
                                                ]) !!}
                                                <label for="">Select all branches <input type="checkbox"
                                                        name="all_branches" {{ request()->all_branches ? 'checked' : '' }}
                                                        value="all" onchange="branch_disable(this)"></label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-12 no-padding-h">
                                        <div class="col-sm-1"><button type="submit" class="btn btn-success manage_request"
                                                name="manage-request" value="new-filter">Filter</button></div>

                                        <!-- <div class="col-sm-1"><button type="submit" class="btn btn-success manage_request" value="filter"  >Filter</button></div> -->
                                        <div class="col-sm-1"><button type="submit" class="btn btn-success manage_request"
                                                name="manage-request" value="excel"><i class="fa fa-file-excel"
                                                    aria-hidden="true"></i></button></div>




                                    </div>
                                </div>

                                </form>


                            </div>

                            <table class="table table-responsive" style="border:1px solid #ddd; margin-top:40px;"
                                id="create_datatable1">

                                @if (count($lists) > 0)

                                    <thead>
                                        <tr>
                                            <th>{{ @$restuarantname }}</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr>
                                            <th>Profit & Loss Report</th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                        <tr id="bgGreen">
                                            <th>Main Category</th>
                                            <th>Sub-Category</th>
                                            <th>Account Name</th>
                                            <th>Account ID</th>
                                            @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                                @foreach ($selectedMonthArr['m'] as $key => $month)
                                                    @php
                                                        $year = $selectedMonthArr['y'][$key];
                                                    @endphp
                                                    <th>{{ date('F', strtotime(date($year . '-' . $month . '-01'))) }}</th>
                                                @endforeach
                                            @endif
                                            <th>Grand Total</th>

                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $a = [];
                                            $b = [];
                                            $c = [];
                                            $d = [];
                                        @endphp
                                        @foreach ($lists as $key => $val)
                                            @if (count($val->getWaAccountGroup) > 0)
                                                @php
                                                    //$totalqty = 0;
                                                    $totalcost = [];
                                                    $grandtotalcost = 0;
                                                @endphp

                                                @foreach ($val->getWaAccountGroup as $key => $groupacount)
                                                    @php
                                                        $dataChartAccount = $groupacount->getChartAccountMontly;
                                                    @endphp
                                                    @if (count($dataChartAccount) > 0)
                                                        <tr>
                                                            <th width="15%">{{ $val->section_name }}</th>
                                                            <th>{{ $groupacount->group_name }}</th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                            <th></th>
                                                        </tr>
                                                        @foreach ($dataChartAccount as $key => $value)
                                                            @php
                                                                //$totalqty += 0;
                                                            @endphp
                                                            <tr>
                                                                <th width="2%"></th>
                                                                <td></td>
                                                                <th><a target="_blank"
                                                                        href="{{ route('profit-and-loss.gl-entries', [$value->account_code]) }}?to={{ request()->get('start-date') }}&from={{ request()->get('end-date') }}">{{ $value->account_name }}</a>
                                                                </th>
                                                                <th>{{ $value->account_code }}</th>
                                                                @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                                                    @foreach ($selectedMonthArr['m'] as $key => $month)
                                                                        @php
                                                                            $year = $selectedMonthArr['y'][$key];
                                                                            $am = 'amount_' . $month . '_' . $year;
                                                                            $totalcost[$year][$month][] = abs(
                                                                                $value->$am,
                                                                            );
                                                                        @endphp
                                                                        <th>{{ manageAmountFormat(abs($value->$am)) }}</th>
                                                                    @endforeach
                                                                @endif
                                                                <th>{{ manageAmountFormat(abs($value->amount_total)) }}
                                                                </th>
                                                                @php
                                                                    $grandtotalcost += abs($value->amount_total);
                                                                @endphp
                                                            </tr>
                                                        @endforeach
                                                    @endif
                                                @endforeach



                                                <tr class="bgGreen">
                                                    <th width="2%"></th>
                                                    <th>Total</th>
                                                    <th></th>
                                                    <th></th>
                                                    @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                                        @foreach ($selectedMonthArr['m'] as $key => $month)
                                                            @php
                                                                $year = $selectedMonthArr['y'][$key];
                                                            @endphp
                                                            @if ($val->section_name == 'INCOME')
                                                                @php
                                                                    $a[$year][$month] = array_sum(
                                                                        @$totalcost[$year][$month] ?? [],
                                                                    );
                                                                @endphp
                                                            @endif

                                                            @if ($val->section_name == 'COST OF SALES')
                                                                @php
                                                                    $b[$year][$month] = array_sum(
                                                                        @$totalcost[$year][$month] ?? [],
                                                                    );
                                                                @endphp
                                                            @endif

                                                            @if ($val->section_name == 'OVERHEADS')
                                                                @php
                                                                    $d[$year][$month] = array_sum(
                                                                        @$totalcost[$year][$month] ?? [],
                                                                    );

                                                                @endphp
                                                            @endif
                                                            @php
                                                                $c[$year][$month] =
                                                                    (@$a[$year][$month] ?? 0) -
                                                                    (@$b[$year][$month] ?? 0);
                                                                $e[$year][$month] =
                                                                    (@$c[$year][$month] ?? 0) -
                                                                    (@$d[$year][$month] ?? 0);
                                                            @endphp
                                                            <th style="text-align:right">
                                                                {{ number_format(abs(array_sum(@$totalcost[$year][$month] ?? [])), 2) }}
                                                            </th>
                                                        @endforeach
                                                    @endif
                                                    <th style="text-align:right">{{ number_format($grandtotalcost, 2) }}
                                                    </th>

                                                </tr>
                                                @php
                                                @endphp

                                                @if ($val->section_name == 'COST OF SALES')
                                                    <tr style="background:#ddd;">
                                                        <th>GROSS PROFIT </th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        @php
                                                            $gtSUm = 0;
                                                        @endphp
                                                        @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                                            @foreach ($selectedMonthArr['m'] as $key => $month)
                                                                @php
                                                                    $year = $selectedMonthArr['y'][$key];
                                                                @endphp
                                                                @php
                                                                    $gtSUm += @$c[$year][$month] ?? 0;
                                                                @endphp
                                                                <th style="text-align:right">
                                                                    {{ number_format(@$c[$year][$month] ?? 0, 2) }}</th>
                                                            @endforeach
                                                        @endif
                                                        <th style="text-align:right">{{ number_format($gtSUm, 2) }}</th>

                                                    </tr>
                                                @endif
                                                @if ($val->section_name == 'OVERHEADS')
                                                    @php
                                                    @endphp
                                                    <tr style="background:#ddd;">
                                                        <th>NET PROFIT </th>
                                                        <th></th>
                                                        <th></th>
                                                        <th></th>
                                                        @php
                                                            $gtSUm = 0;
                                                        @endphp
                                                        @if (isset($selectedMonthArr['m']) && !is_null($selectedMonthArr['m']))
                                                            @foreach ($selectedMonthArr['m'] as $key => $month)
                                                                @php
                                                                    $year = $selectedMonthArr['y'][$key];
                                                                @endphp
                                                                @php
                                                                    $gtSUm += @$e[$year][$month] ?? 0;
                                                                @endphp
                                                                <th style="text-align:right">
                                                                    {{ number_format(@$e[$year][$month] ?? 0, 2) }}</th>
                                                            @endforeach
                                                        @endif
                                                        <th style="text-align:right">{{ number_format($gtSUm, 2) }}</th>


                                                    </tr>
                                                @endif
                                                <tr>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                            @endif
                                        @endforeach
                                        <tr>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                            <th></th>
                                        </tr>

                                    </tbody>


                                @endif
                            </table>





                        </div>


                    </div>
                </div>

            </div>

            <br>



        </div>
        </div>
    </section>



@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>

    </style>
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

        function branch_disable(input) {
            if ($(input).is(":checked")) {
                $('#restaurant_id_set').val([]).change();
                $('#restaurant_id_set').attr('disabled', true);
            } else {
                $('#restaurant_id_set').attr('disabled', false);
            }
            return false;
        }
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
                dom: 'frtip',
                // buttons: [
                // 	{ extend: 'excelHtml5', text: '<i class="fa fa-file-excel" aria-hidden="true">', footer: true },
                // ]
            });
            // $('.manage_request').click(function(e){
            //     e.preventDefault();
            //     var va = $(this).val();
            //     var url1 = '{{ route('profit-and-loss.monthlyProfitSummary') }}';
            //     // var url2 = '{{ route('profit-and-loss.detailsAll.excel') }}';
            //     if(va == 'excel')
            //     {
            //         $('#thisfilterOk').attr('action',url1);
            //     }else{
            //         $('#thisfilterOk').attr('action',url1);
            //     }
            //     if($(this).val() == 'filter'){
            //         $("#restaurant_id_set").val('');
            //     }
            //     $("#manage_request").val($(this).val());
            //     $('#thisfilterOk').submit();
            // });
        });
    </script>
@endsection
