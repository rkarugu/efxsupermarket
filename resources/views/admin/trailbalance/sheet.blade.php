@extends('layouts.admin.admin')

@section('content')
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Trial Balance Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to General Ledger Reports </a> --}}
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    {{-- <div class="d-flex justify-content-between align-items-center">
                        <div class="card-header">
                            <i class="fa fa-filter"></i> Filter
                        </div>
                    </div> --}}

                    <br>
                    {!! Form::open(['route' => 'trial-balances.index', 'method' => 'get']) !!}

                    <div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">Start Date</label>
                                    {!! Form::text('start-date', request()->get('start-date') ?? null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'Start Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">End Date</label>
                                    {!! Form::text('end-date', request()->get('end-date') ?? null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'End Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    <label for="">TB Branch Code</label>
                                    {!! Form::select('restaurant[]', $restroList, request()->get('restaurant') ?? null, [
                                        'class' => 'form-control mlselec6t multiselect',
                                        'multiple' => 'multiple',
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3" style="padding-left: 0; padding-right: 0;">
                                <div class="col-md-12" style="padding-left: 0; padding-right: 0; padding-top:20px;">
                                    <div class="col-sm-3" style="padding-left: 0px;">
                                        <button type="submit" class="btn btn-success" name="manage-request"
                                            value="filter">Filter</button>
                                    </div>
                                    <div class="col-sm-3">
                                        <button title="Export In Excel" type="submit" class="btn btn-warning"
                                            name="manage-request" value="xls"><i class="fa fa-file-excel"
                                                aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <div class="col-sm-3">
                                        <button title="Export In PDF" type="submit" class="btn btn-warning"
                                            name="manage-request" value="pdf"><i class="fa fa-file-pdf"
                                                aria-hidden="true"></i>
                                        </button>
                                    </div>

                                    <div class="col-sm-3">
                                        <a class="btn btn-info" href="{!! route('trial-balances.index') . getReportDefaultFilterForTrialBalance() !!}">Clear</a>
                                    </div>


                                </div>
                            </div>

                        </div>
                    </div>

                    </form>
                </div>

                <br>
                @include('message')
                <div class="col-md-12 no-padding-h">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Account Code</th>
                                <th>Account Name</th>
                                <th>Account Group</th>
                                <th>Period Debits</th>
                                <th>Period Credits</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($all_item as $item)
                                <tr>
                                    <td>
                                        <a href="{{ route('trial-balance.account', [$item->account, 'start-date' => request()->get('start-date'), 'end-date' => request()->get('end-date')]) }}"
                                            target="_blank">{{ $item->account }}</a>
                                    </td>
                                    <td>{{ @$item->getAccountDetail->account_name }}</td>
                                    <td>{{ $item->getAccountDetail->getRelatedGroup->group_name }}</td>
                                    <td>{{ $item->sm > 0 ? manageAmountFormat($item->sm) : '' }}</td>
                                    <td>{{ $item->sm < 0 ? manageAmountFormat($item->sm) : '' }}</td>
                                </tr>
                            @endforeach
                            <tr style="font-weight: bold;">
                                <td></td>
                                <td></td>
                                <td>Total</td>
                                <td>{{ manageAmountFormat($all_item->where('sm', '>', 0)->sum('sm')) }}</td>
                                <td>{{ manageAmountFormat($all_item->where('sm', '<', 0)->sum('sm')) }}</td>

                            </tr>
                            {{--
                                                            @php
                                                            // $openingBalanceAmount = [];
                                                              $periodDebit = [];
                                                                $periodCredit = [];
                                                                  // $periodBalance = [];
                                                                    // $closingBalance = [];
                                                            @endphp

                                                          @foreach ($detail as $itemData)
                                                          <tr>
                                                            <td>{{ $itemData['gl_account'] }}</td>
                                                            <td>{{ $itemData['gl_account_name'] }}</td>
                                                            <td>{{ $itemData['account_group'] }}</td>
                                                            <td>{{ (manageAmountFormat($itemData['periodDebit'])=="0.00" ? '-' : manageAmountFormat($itemData['periodDebit']) ) }}</td>
                                                            <td>{{  (manageAmountFormat(abs($itemData['periodCredit']))=="0.00" ? ' - ' : manageAmountFormat(abs($itemData['periodCredit']))) }}</td>

                                                        </tr>

                                                             @php
                                                            // $openingBalanceAmount[]= $itemData['openingBalanceAmount'];
                                                              $periodDebit[] = $itemData['periodDebit'];
                                                                $periodCredit[] =$itemData['periodCredit'];
                                                                  // $periodBalance[] = $itemData['periodBalance'];
                                                                    // $closingBalance[]= $itemData['closingBalance'];
                                                                    @endphp
                                                        @endforeach


                                                         <tr style ="font-weight: bold;">
                                                               <td></td>
                                                               <td></td>
                                                               <td>Total</td>
                                                                <td>{{ manageAmountFormat(array_sum($periodDebit))}}</td>
                                                               <td>{{ manageAmountFormat(abs(array_sum($periodCredit)))}}</td>

                                                            </tr>




                                                            --}}
                        </tbody>
                    </table>
                </div>


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
    <script>
        $(function() {
            $(".mlselec6t").select2();
        });


        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
