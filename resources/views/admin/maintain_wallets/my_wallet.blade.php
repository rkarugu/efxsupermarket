@extends('layouts.admin.admin')

@section('content')
    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> My Wallet </h3> 
                    <a href="#" class="btn btn-success withdraw-btn" >Withdraw</a>       
                </div>
            </div>
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title">  Current  Balance {{manageAmountFormat($currentBalance)}} </h3> 
                    <h3 class="box-title">  Available Balance {{manageAmountFormat($availableBalance)}} </h3> 

                </div>
            </div>

            <div class="box-body">
             
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="col-md-12">
                    <table class="table table-bordered table-hover" id="my_wallet_datatable">
                        <thead>
                            <tr>
                                <th >Transaction Date</th>
                                <th >Transaction Type</th>
                                <th >Narration</th>
                                <th >Credit</th>
                                <th >Debit</th>
                                <th >Balance</th>
                            </tr>
                        </thead>

                        <tbody>
                            
                                <?php 
                                    $b = 1;
                                    $currentBalance = 0;
                                    $totalDebit = 0;
                                    $totalCredit = 0;

                                 ?>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{!! $transaction->created_at !!}</td>
                                        <td>{!! ucfirst($transaction->transaction_type ?? '-' )!!}</td>
                                        <td>{!! $transaction->narration?? '-' !!}</td>
                                        <td style="text-align: right;">
                                            @if ($transaction->amount >= 0)
                                            @php
                                             $currentBalance += $transaction->amount ;
                                             $totalDebit += $transaction->amount ;
                                                
                                            @endphp
                                            {{ manageAmountFormat($transaction->amount) }}
                                                
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            @if ($transaction->amount < 0)
                                            @php
                                            $currentBalance += $transaction->amount ;
                                            $totalCredit += $transaction->amount ;
                                               
                                           @endphp
                                            {{ manageAmountFormat($transaction->amount * -1) }}
                                                
                                            @endif
                                        </td>
                                        <td style="text-align: right;">
                                            {{ manageAmountFormat($currentBalance)}}
                                        </td>
                                       


                                    </tr>
                                 
                                @endforeach
                                <tfoot>
                                    <tr>
                                        <th colspan="3">Total</th>
                                        <th style="text-align: right;">{{ manageAmountFormat($totalDebit) }}</th>
                                        <th style="text-align: right;">{{ manageAmountFormat($totalCredit) }}</th>
                                        <th style="text-align: right;">{{ manageAmountFormat($currentBalance) }}</th>

                                    </tr>
                                </tfoot>
                              
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="withdraw-funds" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="box-title"> Withdraw Funds</h3>

                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                </div>
                <form action="{{route('withdraw-from-web')}}" method="post"  name="withdraw-form" class="withdraw-form">
                    @csrf
                    <div class="box-body">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="text" class="form-control" name="phone_number" id="phone_number" required>

                            </div>
                            <div class="form-group">
                                <label for="phone">Amount</label>
                                <input type="number" class="form-control" name="amount" id="amount" required>

                            </div>
                        
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <input type="submit" class="btn btn-success" name="submit" id="submit">
                            {{-- <button type="submit" class="btn btn-success" data-dismiss="modal">Withdraw</button> --}}

                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
   


@endsection

@section('uniquepagescript')
<link rel="stylesheet" href="{{asset('css/multistep-form.css')}}">
<link href="{{asset('assets/admin/bower_components/select2/dist/css/select2.min.css')}}" rel="stylesheet" />

    <style type="text/css">
        .select2 {
            width: 100% !important;
        }

        #note {
            height: 80px !important;
        }

        .align_float_right {
            text-align: right;
        }

        .textData table tr:hover {
            background: #000 !important;
            color: white !important;
            cursor: pointer !important;
        }


        /* ALL LOADERS */

        .loader {
            width: 100px;
            height: 100px;
            border-radius: 100%;
            position: relative;
            margin: 0 auto;
            top: 35%;
        }

        /* LOADER 1 */

        #loader-1:before,
        #loader-1:after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border-radius: 100%;
            border: 10px solid transparent;
            border-top-color: #3498db;
        }

        #loader-1:before {
            z-index: 100;
            animation: spin 1s infinite;
        }

        #loader-1:after {
            border: 10px solid #ccc;
        }

        @keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
                -ms-transform: rotate(0deg);
                -o-transform: rotate(0deg);
                transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
                -ms-transform: rotate(360deg);
                -o-transform: rotate(360deg);
                transform: rotate(360deg);
            }
        }
    </style>
    <div id="loader-on"
        style="
position: absolute;
top: 0;
text-align: center;
display: block;
z-index: 999999;
width: 100%;
height: 100%;
background: #000000b8;
display:none;
"
        class="loder">
        <div class="loader" id="loader-1"></div>
    </div> 
 
 <script src="{{asset('js/sweetalert.js')}}"></script>
<script src="{{ asset('js/form.js') }}"></script>
<script src="{{asset('js/multistep-form.js')}}"></script>
<script src="{{asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.mlselec6t').select2();

        $('.withdraw-btn').on('click', function (event) {
                event.preventDefault();
                $('#withdraw-funds').modal('show');
            });

    });
    $('#my_wallet_datatable').DataTable({
        'paging': false,
        'lengthChange': true,
        'searching': false,
        'ordering': false,
        'info': true,
        'autoWidth': false,
        'pageLength': 1000,
        'scrollX': true,
        'initComplete': function (settings, json) {
            var info = this.api().page.info();
            var total_record = info.recordsTotal;
            if (total_record < 101) {

                $('.dataTables_paginate').hide();
            }
        },
        'aoColumnDefs': [{
            'bSortable': false,
            'aTargets': 'noneedtoshort'
        }],
        //"aaSorting": [ [0,'desc'] ]

    });

    </script>
@endsection
