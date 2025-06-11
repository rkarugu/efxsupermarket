@extends('layouts.admin.admin')

@section('content')

    <script>
        window.eodRoutineDetails = {!! $eodRoutineDetails !!};
    </script>
    <section class="content" id="vue-mount">
        <div class="modal fade" id="cashBankingConfirmation" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Confirm Cash At Hand </h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">

                        <div class="form-group">
                            <label for="system_cash_at_hand">Cash At Hand</label>
                            <input type="text" class="form-control" name="system_cash_at_hand" id="system_cash_at_hand"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="cashiers_amount"><small>Enter amount to confirm cash at hand</small></label>
                            <input type="number" class="form-control" name="cashiers_amount" id="cashiers_amount"
                                required>
                        </div>

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <a id="confirmBtnEdit" href="#" class="btn btn-success btn-sm"
                                @click="verifyCashBanking"> Verify</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $branchDetails->name . ' ' .$day }}</h3>
                    <div>
                        <a href="{{ route('eod-routine.index') }}" class="btn btn-success btn-sm"> <i class="fas fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <h4 class="text-center" style="margin-bottom:30px;margin-top:-20px;font-weight:bolder">
                    END OF DAY ROUTINE
                </h4>
                <div class=" multistep">
                    <div class="container">
                        <div class="stepwizard">
                            <div class="stepwizard-row setup-panel">
                                <div class="stepwizard-step col-xs-2">
                                    <a href="#step-1" type="button" class="btn btn-success btn-circle step-buttons step-buttons1">1</a>
                                    <p><b>Returns </b></p>
                                </div>
                                <div class="stepwizard-step col-xs-2">
                                    <a href="#step-2" type="button" class="btn btn-default btn-circle step-buttons step-buttons2" disabled="disabled">2</a>
                                    <p><b>Splits</b></p>
                                </div>
                                <div class="stepwizard-step col-xs-4">
                                    <a href="#step-3" type="button" class="btn btn-default btn-circle step-buttons step-buttons3" disabled="disabled">3</a>
                                    <p><b>Binless Items</b></p>
                                </div>
                                <div class="stepwizard-step col-xs-2">
                                    <a href="#step-4" type="button" class="btn btn-default btn-circle step-buttons step-buttons4" disabled="disabled">4</a>
                                    <p><b>Sales Vs Stock</b></p>
                                </div>
                                <div class="stepwizard-step col-xs-2">
                                    <a href="#step-5" type="button" class="btn btn-default btn-circle step-buttons step-buttons5" disabled="disabled">5</a>
                                    <p><b> Cash At Hand</b></p>
                                </div>
                                {{-- <div class="stepwizard-step col-xs-2">
                                    <a href="#step-5" type="button" class="btn btn-default btn-circle step-buttons step-buttons5" disabled="disabled">5</a>
                                    <p><b>Number Series</b></p>
                                </div> --}}
                            
                            </div>
                        </div>
                    </div>
                    <form class="validate" id="eodRoutineForm" role="form" method="POST" action="{!! route('banking.reconcile.daily.transactions.store') !!}" enctype = "multipart/form-data">
                        @csrf
                        @include('admin.eod_routine.partials.cash_at_hand')
                        @include('admin.eod_routine.partials.returns')
                        @include('admin.eod_routine.partials.splits')
                        @include('admin.eod_routine.partials.items_with_no_bins')
                        @include('admin.eod_routine.partials.unbalanced_transactions')


                        {{-- @include('admin.eod_routine.partials.number_series') --}}
                    </form>
                </div>
            </div>
        </div>
        

    </section>
@endsection

@section('uniquepagestyle')
    <link rel="stylesheet" href="{{ asset('css/multistep-form.css') }}">
    <div id="loader-on"
         style="
            position: fixed;
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
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
    <style>
        a.btn.btn-default.btn-circle.step-button.active {
            background: #ff0000 !important;
            border: none;
            color: white;
            font-weight: bolder;
        }
    </style>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/form-eod.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');

        $(document).ready(function() {
            $('select_branch').select2();
        });
    </script>

<script type="importmap">
    {
      "imports": {
        "vue": "/js/vue.esm-browser.js"
      }
    }
</script>

<script type="module">

    import {
        createApp
    } from 'vue';
        const app = createApp({
        data() {
            return {
               returnsPassed:false,
               splitsPassed:false,
               binlessItemsPassed:false,
               unbalancedTransactionsPassed:false,
               numberSeriesPassed:false,
               cashAtHandPassed:false,

               returnSummary:{},
               pendingReturns:[],

               loadingReturns:true,
               loadingSplits:true,
               loadingBinlessItems:true,
               loadingUnbalancedTransactions:true,
               loadingNumberSeries:true,
               loadingCashAtHand:true,

               pendingSplits:[],
               missingOnSplit:[],
               binlessItems:[],
               stocksVsSales:{},
               salesSummary:{},
               posCashAtHand:{},
               cashBankingBalance:0,
               unbalancedInvoicesExist:false,
               unbalanced_invoices:{},
               unbalancedInvoiceIds:null,
               dayClosed:false,

                }
        },

        mounted() {
                this.returnsPassed = this.eodRoutineDetails.returns_passed === 0 ? false : true;
            this.splitsPassed = this.eodRoutineDetails.splits_passed === 0? false : true;
            this.binlessItemsPassed = this.eodRoutineDetails.binless_items_passed === 0? false : true;
            this.unbalancedTransactionsPassed = this.eodRoutineDetails.unbalanced_transactions_passed === 0? false : true;
            this.numberSeriesPassed = this.eodRoutineDetails.number_series_passed === 0? false : true;
            this.cashAtHandPassed = this.eodRoutineDetails.pos_cash_at_hand_passed === 0? false : true;
            this.dayClosed = this.eodRoutineDetails.status === 'Closed'? true : false;

            this.fetchReturnSummary();
            this.fetchSplitSummary();
            this.fetchBinlessItems();
            this.fetchStocksVsSales();
            this.fetchCashAtHand();
            // this.fetchNumberSeries();
        },

        computed: {

            eodRoutineDetails() {
                return window.eodRoutineDetails;
            },

            // toaster() {
            //     return new Form();
            // },
        },

       methods:{
        
        getFilters(){
            return {
                        day: this.eodRoutineDetails.day,
                        branch_id: this.eodRoutineDetails.branch_id,
                    }

        },
        fetchReturnSummary() {
                    this.loadingReturns = true;
                    axios.get('/eod-routine/run-eod-routine/fetch-return-summary', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingReturns = false;
                        this.returnSummary = response.data.returnSummary;
                        this.pendingReturns = response.data.pendingReturns;
                    }).catch(error => {
                        this.loadingReturns = true;
                        // this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
        fetchSplitSummary() {
                    this.loadingSplits = true;
                    axios.get('/eod-routine/run-eod-routine/fetch-splits', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingSplits = false;
                        this.missingOnSplit = response.data.childrenWithoutQty;
                        this.pendingSplits = response.data.pendingSplitDispatch;
                    }).catch(error => {
                        this.loadingSplits = true;
                        // this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
        fetchBinlessItems() {
                    this.loadingBinlessItems = true;
                    axios.get('/eod-routine/run-eod-routine/fetch-binless-items', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingBinlessItems = false;
                        this.binlessItems = response.data.binlessItems;
                    }).catch(error => {
                        this.loadingBinlessItems = true;
                        // this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
        fetchStocksVsSales() {
                    axios.get('/eod-routine/run-eod-routine/fetch-sales-vs-stocks', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingUnbalancedTransactions = false;
                        this.stocksVsSales = response.data.salesVsStocks;
                        this.unbalancedInvoicesExist = response.data.unbalancedInvoicesExist;
                        this.unbalanced_invoices = response.data.unbalanced_invoices;
                        this.unbalancedInvoiceIds = this.unbalanced_invoices.map(invoice => invoice.id);

                    }).catch(error => {
                        this.loadingUnbalancedTransactions = true;
                    });
                },
        fetchNumberSeries() {
                    axios.get('/eod-routine/run-eod-routine/fetch-number-series', {
                        params: this.getFilters()
                    }).then(response => {
                        console.log(response);
                        this.loadingNumberSeries = false;
                        this.salesSummary = response.data.salesSummary;
                    }).catch(error => {
                        this.loadingNumberSeries = true;
                    });
                },
        fetchCashAtHand() {
                    this.loadingCashAtHand = true;
                    axios.get('/eod-routine/run-eod-routine/fetch-cash-at-hand', {
                        params: this.getFilters()
                    }).then(response => {
                        console.log(response);
                        this.posCashAtHand = response.data.posCashBanking;
                        this.cashBankingBalance = response.data.todaysBalance;
                        this.loadingCashAtHand = false;

                    }).catch(error => {
                        this.loadingCashAtHand = true;
                    });
                },
        handleNextStep(step) {
                 
                    const nextStepBtn = $('div.setup-panel div a[href="#step-' + (step + 1) + '"]');
                    if (nextStepBtn.length) {
                        nextStepBtn.removeAttr('disabled').trigger('click');
                    }
                },
        verifyReturns() {
                    this.loadingReturns = true;
                    let payload = this.getFilters();
                    $(".btn-loader").show();
                    axios.post('/eod-routine/run-eod-routine/verify-returns', payload).then(response => {
                        $(".btn-loader").hide();
                        this.returnsPassed = response.data.returnsPassed;
                        this.loadingReturns = false;
                        setTimeout(() => {
                            this.fetchReturnSummary();
                        }, 500);
                    }).catch(error => {
                        this.loadingReturns = true;
                        $(".btn-loader").hide();
                        // this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },
        
        verifySplits() {
                    let payload = this.getFilters();
                    $(".btn-loader").show();
                    axios.post('/eod-routine/run-eod-routine/verify-splits', payload).then(response => {
                        $(".btn-loader").hide();
                        this.splitsPassed = response.data.splitsPassed;
                        this.loadingSplits = false;
                        setTimeout(() => {
                            this.fetchSplitSummary();
                        }, 500);
                    }).catch(error => {
                        this.loadingSplits = true;
                        $(".btn-loader").hide();
                    })
                },
        verifyBinlessItems() {
                    let payload = this.getFilters();
                    $(".btn-loader").show();
                    axios.post('/eod-routine/run-eod-routine/verify-binless-tems', payload).then(response => {
                        $(".btn-loader").hide();
                        this.binlessItemsPassed = response.data.binlessItemsPassed;
                        this.loadingBinlessItems = false;
                        setTimeout(() => {
                            this.fetchBinlessItems();
                        }, 500);
                    }).catch(error => {
                        this.loadingBinlessItems = true;
                        $(".btn-loader").hide();
                    })
                },
        verifyUnbalancedTransactions() {
                    let payload = this.getFilters();
                    $(".btn-loader").show();
                    axios.post('/eod-routine/run-eod-routine/verify-stocks-vs-sales', payload).then(response => {
                        $(".btn-loader").hide();
                        this.loadingUnbalancedTransactions = false;
                        this.unbalancedTransactionsPassed = response.data.unbalancedTransactionsPassed;
                        setTimeout(() => {
                            this.fetchStocksVsSales();
                        }, 500);
                    }).catch(error => {
                        this.loadingUnbalancedTransactions = true;
                        $(".btn-loader").hide();
                    })
                },
        fireCashAtHandModal(){
            $("#cashBankingConfirmation").modal("show");
            $("#system_cash_at_hand").val(this.cashBankingBalance);

                },
        verifyCashBanking(){
                    let payload = {
                        ...this.getFilters(), 
                        entered_amount: $("#cashiers_amount").val(), 
                        system_amount: this.cashBankingBalance 
                    };
                    $(".btn-loader").show();
                    axios.post('/eod-routine/run-eod-routine/verify-cash-at-hand', payload).then(response => {
                        $(".btn-loader").hide();
                        this.loadingCashAtHand = false;
                        this.cashAtHandPassed = response.data.cashAtHandPassed;
                        $("#cashBankingConfirmation").modal("hide");

                        setTimeout(() => {
                            this.fetchCashAtHand();
                        }, 500);
                    }).catch(error => {
                        this.loadingCashAtHand = true;
                        $(".btn-loader").hide();
                    })

                },
        balanceInvoices(){
                    this.loadingUnbalancedTransactions = true;
                    let payload = {
                            ...this.getFilters(),
                            invoice_ids: this.unbalancedInvoiceIds ,
                        };
                    axios.post('/eod-routine/run-eod-routine/balance-transactions', payload).then(response => {
                        $(".btn-loader").hide();
                        this.loadingUnbalancedTransactions = false;
                        setTimeout(() => {
                            this.fetchStocksVsSales();
                        }, 500);
                    }).catch(error => {
                        this.loadingUnbalancedTransactions = true;
                    })

                },
        
        closeDay() {
                    let payload = this.getFilters();
                    axios.post('/eod-routine/run-eod-routine/close-day', payload).then(response => {
                        window.location.href = response.data.redirectUrl; 
                    }).catch(error => {
                        console.log(error);
                        Swal.fire({
                        title: 'Error!',
                        text:  error.response?.data?.message ,
                        icon: 'error',
                        confirmButtonText: 'Close',
                        timer:10000,
                        timerProgressBar: true,
                        })
                    })
                },
       },
    })
    app.mount('#vue-mount')



</script>
@endsection
