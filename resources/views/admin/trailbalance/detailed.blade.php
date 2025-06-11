@extends('layouts.admin.admin')

@section('content')
    <style>
        .bg-grey td {
            background: #ddd;
        }
    </style>
    @php
        $to = request()->get('start-date');
        $from = request()->get('end-date');
    @endphp
    <!-- Main content -->
    <section class="content">
        <!-- Small boxes (Stat box) -->
        <div class="box box-primary">

            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">Detailed Trial Balance Report</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to General Ledger Reports --}}
                    </a>
                </div>
            </div>

            <div class="box-header with-border no-padding-h-b">
                <div style="height: 150px ! important;">
                    <div class="card-header">
                        <i class="fa fa-filter"></i> Filter
                    </div><br>
                    {!! Form::open(['route' => 'trial-balances.detailed', 'method' => 'get']) !!}

                    <div>
                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('start-date', null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'Start Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::text('end-date', null, [
                                        'class' => 'datepicker form-control',
                                        'placeholder' => 'End Date',
                                        'readonly' => true,
                                    ]) !!}
                                </div>
                            </div>

                            <div class="col-sm-3">
                                <div class="form-group">
                                    {!! Form::select('restaurant', $restroList, null, [
                                        'placeholder' => 'Select TB Branch Code',
                                        'class' => 'form-control',
                                    ]) !!}
                                </div>
                            </div>

                        </div>

                        <div class="col-md-12 no-padding-h">
                            <div class="col-sm-1"><button type="submit" class="btn btn-success" name="manage-request"
                                    value="new-filter">Filter</button></div>

                            <div class="col-sm-1">
                                <button title="Export In Excel" type="submit" class="btn btn-warning" name="manage-request"
                                    value="xls"><i class="fa fa-file-excel" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="col-sm-1">
                                <button title="Export In PDF" type="submit" class="btn btn-warning" name="manage-request"
                                    value="pdf"><i class="fa fa-file-pdf" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="col-sm-1">
                                <a class="btn btn-info" href="{!! route('trial-balances.detailed') . getReportDefaultFilterForTrialBalance() !!}">Clear</a>
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
                                <th width="30%">Account Name</th>
                                <th style="text-align:right;">Opening Balance</th>
                                <th style="text-align:right;">Period Debits</th>
                                <th style="text-align:right;">Period Credits</th>
                                <th style="text-align:right;">Period Balance</th>
                                <th style="text-align:right;">Closing Balance</th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php $counter = 1;
                            $openingBalanceAmount = [];
                            $periodDebit = [];
                            $periodCredit = [];
                            $periodBalance = [];
                            $closingBalance = [];
                            
                            ?>
                            @foreach ($detail as $account_name => $itemArray)
                                <tr @if ($counter % 2 == 0) class="bg-grey" @endif>
                                    <td style="font-weight:700;font-size:16px" colspan="7">{{ $account_name }}</td>
                                </tr>
                                <?php
                                
                                $subopeningBalanceAmount = [];
                                $subperiodDebit = [];
                                $subperiodCredit = [];
                                $subperiodBalance = [];
                                $subclosingBalance = [];
                                
                                ?>
                                @foreach ($itemArray as $itemData)
                                    <tr @if ($counter % 2 == 0) class="bg-grey" @endif>
                                        <td><a onclick="openThisModel(this); return false;"
                                                href="{{ route('trial-balances.accountPayablesDetails', [
                                                    'to' => $from,
                                                    'from' => $to,
                                                    'restaurant' => request()->restaurant,
                                                    'gl_account' => $itemData['gl_account'],
                                                ]) }}">{{ $itemData['gl_account'] }}</a>
                                        </td>
                                        <td><a
                                                href="{{ route('profit-and-loss.gl-entries', ['account_code' => $itemData['gl_account'], 'id' => $itemData['gl_account'], 'to' => $to, 'from' => $from]) }}">{{ $itemData['gl_account_name'] }}
                                        </td>
                                        <td style="text-align:right;">
                                            {{ manageAmountFormat($itemData['openingBalanceAmount']) }}</td>
                                        <td style="text-align:right;">
                                            {{ manageAmountFormat(abs($itemData['periodDebit'])) == '0.00' ? ' - ' : manageAmountFormat(abs($itemData['periodDebit'])) }}
                                        </td>
                                        <td style="text-align:right;">
                                            {{ manageAmountFormat(abs($itemData['periodCredit'])) == '0.00' ? ' - ' : manageAmountFormat(abs($itemData['periodCredit'])) }}
                                        </td>
                                        <td style="text-align:right;">{{ manageAmountFormat($itemData['periodBalance']) }}
                                        </td>
                                        <td style="text-align:right;">{{ manageAmountFormat($itemData['closingBalance']) }}
                                        </td>
                                    </tr>
                                    <?php
                                    $openingBalanceAmount[] = $itemData['openingBalanceAmount'];
                                    $periodDebit[] = $itemData['periodDebit'];
                                    $periodCredit[] = $itemData['periodCredit'];
                                    $periodBalance[] = $itemData['periodBalance'];
                                    $closingBalance[] = $itemData['closingBalance'];
                                    $subopeningBalanceAmount[] = $itemData['openingBalanceAmount'];
                                    $subperiodDebit[] = $itemData['periodDebit'];
                                    $subperiodCredit[] = $itemData['periodCredit'];
                                    $subperiodBalance[] = $itemData['periodBalance'];
                                    $subclosingBalance[] = $itemData['closingBalance'];
                                    ?>
                                @endforeach
                                <tr @if ($counter % 2 == 0) class="bg-grey" @endif>
                                    <td></td>
                                    <td>Sub Total</td>
                                    <td style="text-align:right;">
                                        {{ manageAmountFormat(array_sum($subopeningBalanceAmount)) }}</td>
                                    <td style="text-align:right;">{{ manageAmountFormat(array_sum($periodDebit)) }}</td>
                                    <td style="text-align:right;">{{ manageAmountFormat(array_sum($subperiodCredit)) }}
                                    </td>
                                    <td style="text-align:right;">{{ manageAmountFormat(array_sum($subperiodBalance)) }}
                                    </td>
                                    <td style="text-align:right;">{{ manageAmountFormat(array_sum($subclosingBalance)) }}
                                    </td>
                                </tr>
                                <?php $counter++; ?>
                            @endforeach

                            <tr style ="font-weight: bold;">
                                <td></td>
                                <td>Total</td>
                                <td style="text-align:right;">{{ manageAmountFormat(array_sum($openingBalanceAmount)) }}
                                </td>
                                <td style="text-align:right;">{{ manageAmountFormat(array_sum($periodDebit)) }}</td>
                                <td style="text-align:right;">{{ manageAmountFormat(array_sum($periodCredit)) }}</td>
                                <td style="text-align:right;">{{ manageAmountFormat(array_sum($periodBalance)) }}</td>
                                <td style="text-align:right;">{{ manageAmountFormat(array_sum($closingBalance)) }}</td>
                            </tr>




                        </tbody>
                    </table>
                </div>


            </div>
        </div>
    </section>



    <div class="modal fade" id="AccountDetailModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="accountId"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('assets/admin/dist/datepicker.css') }}">
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/dist/bootstrap-datepicker.js') }}"></script>
    <script>
        function openThisModel(a) {
            $('#AccountDetailModal #accountId').html($(a).html());
            $("#AccountDetailModal .modal-body").html('');
            $("#AccountDetailModal .modal-body").load($(a).attr('href'));
            $("#AccountDetailModal").modal('show');
        }



        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection
