@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between">
                    <h3 class="box-title">{{ $title }}</h3>
                    {{-- <a href="{{ url()->previous() }}" class="btn btn-primary" role="button">
                        << Back to Sales and Receivables Reports </a> --}}
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>
                <form action="{{route('pos-cash-sales.overview')}}" method="get">
                    <div class="row">
                        <div class="form-group col-md-2">
                            <label for="branch">Branch</label>
                            <select name="branch" id="branch" class="form-control new_filters">
                                <option value="">select branch</option>
                                @foreach ($branches as $branchValue)
                                    <option value="{{$branchValue->id}}" {{ $branch == $branchValue->id ? 'selected' : ''}}>{{$branchValue->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2 form-group">
                            <label for="branch">From</label>
                            <input type="date" name="from_date" id="from" class="form-control" value="{{ request()->get('from_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>
    
                        <div class="col-md-2 form-group">
                            <label for="branch">To</label>
                            <input type="date" name="to_date" id="to" class="form-control" value="{{ request()->get('to_date') ?? \Carbon\Carbon::now()->toDateString() }}">
                        </div>
                        <div class="form-group col-md-3">
                            <label class="text-white" style="display: block; color: white!important;"> Action </label>
                            <button type="submit" name="intent" value="FILTER" class="btn btn-success">
                                <i class="fas fa-filter"></i> FILTER
                            </button>
                            <button type="submit" name="download" value="PDF" class="btn btn-success">
                                <i class="fas fa-file-pdf"></i> PDF
                            </button>
                           
                            <a href="{{ route('pos-cash-sales.overview') }}"
                               class="btn btn-success"> Clear </a>
                        </div>
                    </div>
                </form>
                <hr>
                <div id="top-cards"  class="row d-flex justify-content-between">
             
                        <div class="major-detail d-flex flex-column justify-content-between border-primary col-md-3 col-sm-6 col-xs-12">
                            <div class="d-flex">
                                <i class="ion ion-stats-bars" style="color: #0d6efd; font-size:30px;"></i>
                                <a href="{{route('pos-cash-sales.overview.posSales', ['startDate'=>$from_date, 'endDate'=>$to_date, 'branch'=>$branch])}}"><span class="major-detail-title">Total Sales </span></a>
                            </div>

                            <a href="{{route('pos-cash-sales.overview.posSales', ['startDate'=>$from_date, 'endDate'=>$to_date, 'branch'=>$branch])}}"><span class="major-detail-value"> {{ number_format($total_sales, 2) }} </span></a>
                        </div>
             
                    <div class="major-detail d-flex flex-column justify-content-between border-danger col-md-3 col-sm-6 col-xs-12">
                        <div class="d-flex">
                            <i class="fas fa-recycle " style="color: #dc3545; font-size:30px;"></i>
                            <a href="{{route('pos-cash-sales.overview.posReturns', ['startDate'=>$from_date, 'endDate'=>$to_date, 'branch'=>$branch])}}"><span class="major-detail-title">Returns </span></a>
                        </div>

                        <a href="{{route('pos-cash-sales.overview.posReturns', ['startDate'=>$from_date, 'endDate'=>$to_date, 'branch'=>$branch])}}"><span class="major-detail-value"> {{ number_format($total_returns, 2) }} </span></a>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-warning col-md-3 col-sm-6 col-xs-12">
                        <div class="d-flex">
                            <i class="fas fa-shopping-cart" style="color:#a4970d; font-size:30px;"></i>
                            <span class="major-detail-title">Transactions</span>
                        </div>

                        <span class="major-detail-value"> {{ $total_transaction }}</span>
                    </div>
                    <div class="major-detail d-flex flex-column justify-content-between border-success col-md-3 col-sm-6 col-xs-12">
                        <div class="d-flex">
                            <i class="fas fa-users major-detail-icon" style="color: #198754; font-size:30px;"></i>
                            <span class="major-detail-title">Customers </span>
                        </div>

                        <span class="major-detail-value"> {{ $customers }} </span>
                    </div>

               
                </div>
                <hr>
                <div class="table-responsive">
                    <h5>Sales Vs Stocks</h5>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th style="text-align: right;">Sales</th>
                                <th style="text-align: right;">Returns</th>
                                <th style="text-align: right;">Net Sales</th>
                                <th style="text-align: right;">Stocks Value</th>
                                <th  style="text-align: right;">Variance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th style="text-align: right;">{{manageAmountFormat($total_sales)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_returns)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_sales - $total_returns)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($stockMoves)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_sales - $total_returns - $stockMoves)}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="table-responsive">
                    <h5>Summary</h5>


                    <table class="table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>DATE</th>
                            <th c>TOTAL PAYMENT</th>
                            <th style="text-align: right;">TOTAL RETURNS</th>
                            <th style="text-align: right;">PENDING RETURNS</th>
                            <th style="text-align: right;">ACCEPTED RETURNS</th>
                            <th style="text-align: right;">EAZZY</th>
                            <th style="text-align: right;">VOOMA</th>
                            <th style="text-align: right;">MPESA</th>
                            <th style="text-align: right;">CASH</th>
                        </tr>
                        </thead>
                        <tbody>
                                @php
                                    $total_total_payments = $total_all_returns = $total_pending_returns = $total_accepted_returns = $total_eazzy = $total_vooma = $total_mpesa = $total_cash = 0
                                @endphp
                                @foreach ($payments as $row)
                                    <tr>
                                        <th>{{$loop->index+1}}</th>
                                        <td>{{$row->date}}</td>
                                        <td style="text-align: right;">{{ '('.$row->total_count.")\t". manageAmountFormat($row->total_payments)}}</td>
                                        <td style="text-align: right;">{{'('.$row->pending_returns_count+$row->accepted_returns_count.")\t".manageAmountFormat($row->pending_returns + $row->accepted_returns)}}</td>
                                        <td style="text-align: right;">{{'('.$row->pending_returns_count.")\t".manageAmountFormat($row->pending_returns)}}</td>
                                        <td style="text-align: right;">{{'('.$row->accepted_returns_count.")\t".manageAmountFormat($row->accepted_returns)}}</td>
                                        <td style="text-align: right;">{{'('.$row->EazzyCount.")\t".manageAmountFormat($row->Eazzy)}}</td>
                                        <td style="text-align: right;">{{'('.$row->VoomaCount.")\t".manageAmountFormat($row->Vooma)}}</td>
                                        <td style="text-align: right;">{{'('.$row->MpesaCount.")\t".manageAmountFormat($row->Mpesa)}}</td>
                                        <td style="text-align: right;">{{'('.$row->CashCount.")\t".manageAmountFormat($row->Cash)}}</td>
                                    </tr>
                                    @php
                                        $total_total_payments += $row->total_payments;
                                        $total_all_returns += ($row->pending_returns + $row->accepted_returns);
                                        $total_pending_returns += $row->pending_returns;
                                        $total_accepted_returns += $row->accepted_returns;
                                        $total_eazzy += $row->Eazzy;
                                        $total_vooma += $row->Vooma;
                                        $total_mpesa += $row->Mpesa;
                                        $total_cash += $row->Cash;
                                    @endphp
                                    
                                @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_total_payments)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_all_returns)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_pending_returns)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_accepted_returns)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_eazzy)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_vooma)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_mpesa)}}</th>
                                <th style="text-align: right;">{{manageAmountFormat($total_cash)}}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/material_red.css">
    <style>
        .major-detail {
            border: 2px solid;
            border-radius: 15px;
            padding: 10px 15px;
            height: 80px;
            flex-grow: 1 !important;
            margin-right: 10px;
            margin-left: 10px;
        }

        .major-detail.border-primary {
            border-color: #0d6efd;
        }

        .major-detail.border-success {
            border-color: #198754;
        }

        .major-detail.border-danger {
            border-color: #dc3545;
        }

        .major-detail.border-warning {
            border-color: #a4970d;
        }

        .major-detail-icon {
            font-size: 20px;
        }

        .major-detail-title {
            font-size: 18px;
            font-weight: 500;
            margin-left: 12px;
            margin-top: -5px;
        }

        .major-detail-value {
            font-size: 20px;
            font-weight: 600;
            padding-left: 30px; 
        }

        #activity {
            position: relative;
            width: 40%;
        }

        .mt-20 {
            margin-top: 30px !important;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script> --}}


    <script type="text/javascript">
        $(function() {
            $('body').addClass('sidebar-collapse');
            $(".new_filters").select2();
        });
    </script>
          
@endsection
