@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.date = {!! json_encode($date) !!};
        window.branch = {!! $branch !!};
        window.user = {!! $user !!};
    </script>


    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Route Banking Overview - {{ $date }} </h3>

                    <div class="d-flex">
                        @if (can('close-banking', 'reconciliation'))
                            <button class="btn btn-success btn-sm" style="margin-right: 5px;" @click="promptApprove" v-if="records[0]?.status === 'Pending'">
                                <i class="fas fa-list-check btn-icon"></i> Approve & Close </button>
                        @endif
                        <button class="btn btn-success btn-sm" style="margin-right: 5px;" @click="downloadPdf" > <i class="fas fa-download"></i>
                            Download </button>

                        <a href="{{ url()->previous() }}" class="btn btn-success btn-sm" > <i
                                class="fas fa-arrow-left btn-icon"></i> Back </a>
                       
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="box-header with-border">
                    <h3 class="box-title"> Summary </h3>
                </div>
                <div class="table-responsive">

                    <table class="table table-hover table-bordered" id="records-tablee">
                        <thead>
                            <tr>
                                <th style="text-align: right;"> Y Sales </th>
                                <th style="text-align: right;"> RTNs </th>
                                <th style="text-align: right;"> EAZZY</th>
                                <th style="text-align: right;"> EQUITY MAIN</th>
                                <th style="text-align: right;"> VOOMA </th>
                                <th style="text-align: right;"> KCB MAIN </th>
                                <th style="text-align: right;"> MPESA </th>
                                <th style="text-align: right;"> TOTAL RCTS </th>
                                <th style="text-align: right;"> VERIFIED </th>
                                <th style="text-align: right;"> FRAUD JOURNALS </th>
                                <th> UNVERIFIED </th>
                                <th> Y SALES VARIANCE </th>
                                <th> RUNNING BALANCE </th>
                            </tr>
                        </thead>

                        <tbody v-cloak>
                            <tr v-for="(record, index) in records" :key="index">
                                <td style="text-align: right;"> @{{ record.sales }}</td>
                                <td style="text-align: right;"> @{{ record.returns }}</td>
                                <td style="text-align: right;"> @{{ record.eazzy }}</td>
                                <td style="text-align: right;"> @{{ record.equity }}</td>
                                <td style="text-align: right;"> @{{ record.vooma }}</td>
                                <td style="text-align: right;"> @{{ record.kcb }}</td>
                                <td style="text-align: right;"> @{{ record.mpesa }}</td>
                                <td style="text-align: right;"> @{{ record.total_receipts }}</td>
                                <td style="text-align: right;"> @{{ record.verified_receipts }}</td>
                                <td style="text-align: right;"> @{{ record.fraud }}</td>
                                <td style="text-align: right;"> @{{ record.variance }}</td>
                                <td style="text-align: right;"> @{{ record.debtors_variance }}</td>
                                <td style="text-align: right;"> @{{ record.running_balance }}</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="box-header">
                    <h3 class="box-title"> Bank Reconciliation </h3>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered mt-10">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Account </th>
                                    <th style="text-align: right;"> Same Day Collections </th>
                                    <th style="text-align: right;"> Late Utilizations </th>
                                    <th style="text-align: right;"> Total Collections </th>
                                    <th style="text-align: right;"> Utilized Unknowns </th>
                                    <th style="text-align: right;"> Actual Unknowns </th>
                                    <th style="text-align: right;"> Total Unknowns </th>
                                    <th style="text-align: right;"> Nominal Total </th>
                                    <th> Sweep Account </th>
                                    <th style="text-align: right;"> Sweep Total</th>
                                    <th style="text-align: right;"> Variance </th>
                                </tr>
                            </thead>

                            <tbody v-cloak>
                                <tr v-for="(record, index) in bankReconciliation" :key="index">
                                    <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                    <td> @{{ record.collection_account }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_same_day_collections }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_late_utilizations }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_total_collection }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_utilized_unknowns }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_actual_unknowns }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_total_unknowns }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_nominal_total }} </td>
                                    <td> @{{ record.sweep_account }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_sweep_total }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_variance }} </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>


                </div>

                <hr>

                <ul class="nav nav-tabs" id="data-tabs">
                    {{-- <li class="active"><a href="#verified" data-toggle="tab">Verified Receipts</a></li> --}}
                    <li class="active"><a href="#sales" data-toggle="tab"> <i class="fas fa-cash-register btn-icon"></i>
                            Y Sales </a></li>
                    <li><a href="#returns" data-toggle="tab"><i class="fas fa-undo btn-icon"></i> Returns </a></li>
                    <li><a href="#eazzy" data-toggle="tab"> <i class="fas fa-comments-dollar btn-icon"></i> Eazzy </a></li>
                    <li><a href="#eb_main" data-toggle="tab"> <i class="fas fa-building-columns btn-icon"></i> Equity
                            Main
                        </a></li>
                    <li><a href="#vooma" data-toggle="tab"> <i class="fas fa-comments-dollar btn-icon"></i> Vooma </a>
                    </li>
                    <li><a href="#kcb" data-toggle="tab"> <i class="fas fa-building-columns btn-icon"></i> KCB Main
                        </a>
                    </li>
                    <li><a href="#mpesa" data-toggle="tab"> <i class="fas fa-mobile btn-icon"></i> MPESA </a></li>
                    <li><a href="#fraud" data-toggle="tab"><i class="fas fa-hat-cowboy btn-icon"></i> Fraud Journals </a>
                    </li>
                    <li><a href="#unverified" data-toggle="tab"><i class="fas fa-times-circle btn-icon"></i> Unverified
                            Receipts </a></li>
                    {{-- <li><a href="#accounts" data-toggle="tab"> Unpaid Accounts </a></li> --}}
                </ul>

                <div class="tab-content">

                    {{-- <div class="tab-pane active" id="verified" v-cloak>
                        <div class="box-body">
                            <p v-if="verifiedLoading"> Loading data... </p>
                            <table class="table" id="verified-table" v-else v-cloak>
                                <thead>
                                    <tr>
                                        <th> Trans Date </th>
                                        <th> Document No </th>
                                        <th> Route </th>
                                        <th> Channel </th>
                                        <th> System Reference </th>
                                        <th> Bank Date </th>
                                        <th style="width: 15%;"> Bank Reference </th>
                                        <th> Status </th>
                                        <th style="text-align: right;"> Amount </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(record, index) in verifiedReceipts" :key="index">
                                        <td> @{{ record.created_at }} </td>
                                        <td> @{{ record.document_no }} </td>
                                        <td> @{{ record.route }} </td>
                                        <td> @{{ record.channel }} </td>
                                        <td> @{{ record.reference }} </td>
                                        <td> @{{ record.bank_date }} </td>
                                        <td style="width: 15%;"> @{{ record.bank_ref }} </td>
                                        <td> @{{ record.verification_status }} </td>
                                        <td style="text-align: right;"> @{{ record.amount }} </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="7" style="text-align: right;"> TOTAL </th>
                                        <th> @{{ verifiedReceipts[0]?.total }} </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div> --}}
                    <div class="tab-pane active" id="sales" v-cloak>
                        @include('banking_approval.partials.route.sales')
                    </div>
                    <div class="tab-pane" id="returns" v-cloak>
                        @include('banking_approval.partials.route.returns')
                    </div>

                    <div class="tab-pane" id="eazzy" v-cloak>
                        @include('banking_approval.partials.route.eazzy')
                    </div>
                    <div class="tab-pane" id="eb_main" v-cloak>
                        @include('banking_approval.partials.route.ebmain')
                    </div>

                    <div class="tab-pane" id="vooma" v-cloak>
                        @include('banking_approval.partials.route.vooma')
                    </div>

                    <div class="tab-pane" id="kcb" v-cloak>
                        @include('banking_approval.partials.route.kcb')
                    </div>

                    <div class="tab-pane" id="mpesa" v-cloak>
                        @include('banking_approval.partials.route.mpesa')
                    </div>


                    <div class="tab-pane" id="fraud" v-cloak>
                        <div class="box-body">
                            <table class="table" id="fraud-table" v-cloak>
                                <thead>
                                    <tr>
                                        <th> Trans Date </th>
                                        <th> Document No </th>
                                        <th> Route </th>
                                        <th> Channel </th>
                                        <th> Narrative </th>
                                        <th> Posting Date </th>
                                        <th> Posted By </th>
                                        <th> Comments </th>
                                        <th style="text-align: right;"> Amount </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(record, index) in fraudTrans" :key="index">
                                        <td> @{{ record.trans_date }} </td>
                                        <td> @{{ record.document_no }} </td>
                                        <td> @{{ record.route }} </td>
                                        <td> @{{ record.channel }} </td>
                                        <td> @{{ record.narrative }} </td>
                                        <td> @{{ record.posting_date }} </td>
                                        <td> @{{ record.blamable }} </td>
                                        <td> @{{ record.comments }} </td>
                                        <td style="text-align: right;"> @{{ record.amount }} </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="8" style="text-align: right;"> TOTAL </th>
                                        <th style="text-align: right;"> @{{ fraudTrans[0]?.total }} </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="unverified" v-cloak>
                        <div class="box-body">
                            <div class="d-flex justify-content-end" style="margin-bottom: 10px;"
                                v-if="unVerifiedReceipts.length > 0">
                                <button class="btn btn-primary" @click="promptSuspendUnverified">
                                    <i class="fas fa-handshake-angle btn-icon"></i> Resolve To Fraud
                                </button>
                            </div>

                            <p v-if="unVerifiedLoading"> Loading data... </p>
                            <table class="table" id="unverified-table" v-else v-cloak>
                                <thead>
                                    <tr>
                                        <th> Trans Date </th>
                                        <th> Document No </th>
                                        <th> Route </th>
                                        <th> Channel </th>
                                        <th> System Reference </th>
                                        <th style="text-align: right;"> Amount </th>
                                        <th style="width: 3%;"> <input type="checkbox" v-model="allUnverifiedSelected"
                                                id="selectAllUnverifiedCheckbox"> </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(record, index) in unVerifiedReceipts" :key="index">
                                        <td> @{{ record.trans_date }} </td>
                                        <td> @{{ record.document_no }} </td>
                                        <td> @{{ record.route }} </td>
                                        <td> @{{ record.channel }} </td>
                                        <td> @{{ record.reference }} </td>
                                        <td style="text-align: right;"> @{{ record.amount }} </td>
                                        <td style="width: 3%;">
                                            <input type="checkbox" v-model="record.selected" :id="`select-${record.id}`">
                                        </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="5" style="text-align: right;"> TOTAL </th>
                                        <th style="text-align: right;"> @{{ unVerifiedReceipts[0]?.total }} </th>
                                        <th style="width: 3%;"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    {{-- <div class="tab-pane" id="others" v-cloak>
                        <div class="box-body">
                            <table class="table" id="others-table" v-cloak>
                                <thead>
                                    <tr>
                                        <th> Trans Date </th>
                                        <th> Document No </th>
                                        <th> Route </th>
                                        <th> System Reference </th>
                                        <th style="text-align: right;"> Amount </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(record, index) in otherReceivables" :key="index">
                                        <td> @{{ record.trans_date }} </td>
                                        <td> @{{ record.document_no }} </td>
                                        <td> @{{ record.route }} </td>
                                        <td> @{{ record.reference }} </td>
                                        <td style="text-align: right;"> @{{ record.amount }} </td>
                                    </tr>
                                </tbody>

                                <tfoot>
                                    <tr>
                                        <th colspan="4" style="text-align: right;"> TOTAL </th>
                                        <th> @{{ otherReceivables[0]?.total }} </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div> --}}

                    {{-- <div class="tab-pane" id="accounts" v-cloak></div> --}}
                </div>
            </div>
        </div>

        <div class="modal fade" id="suspend-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Suspend Unverified Receipts </h3>
                    </div>

                    <div class="box-body">
                        <p> This action will expunge the selected receipts, totalling to
                            <strong>@{{ selectedUnverifiedTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') }}</strong>,
                            from the respective route accounts and post them to fraud.
                        </p>

                        <div class="form-group">
                            <label for="comments" class="control-label"> Comments </label>
                            <textarea v-model="unverifiedReceiptsComment" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="suspendUnverified">Expunge &
                                Resolve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="approve-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Approve & Close </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to approve route banking for this day?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="approveAndClose"> Yes, Approve
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader" />
    </span>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('js/dayjs.min.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script>
        dayjs().format()
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
                    records: [],
                    verifiedLoading: true,
                    verifiedReceipts: [],
                    unVerifiedLoading: true,
                    unVerifiedReceipts: [],
                    otherReceivables: [],
                    allUnverifiedSelected: false,
                    unverifiedReceiptsComment: null,
                    selectedUnverifiedTotal: 0,
                    selectedUnverifiedReceipts: [],
                    fraudTrans: [],

                    bankReconciliation: [],

                    loadingSales: true,
                    sales: [],
                    salesGrossTotal: 0,

                    loadingReturns: true,
                    returns: [],
                    returnsTotal: 0,

                    loadingEazzy: true,
                    eazzyVerified: [],
                    eazzyVerifiedTotal: 0,
                    eazzyUnknown: [],
                    eazzyUnknownTotal: 0,

                    loadingEbMain: true,
                    ebMainVerified: [],
                    ebMainVerifiedTotal: 0,
                    ebMainUnknown: [],
                    ebMainUnknownTotal: 0,

                    loadingVooma: true,
                    voomaVerified: [],
                    voomaVerifiedTotal: 0,
                    voomaUnknown: [],
                    voomaUnknownTotal: 0,

                    loadingKcbMain: true,
                    kcbMainVerified: [],
                    kcbMainVerifiedTotal: 0,
                    kcbMainUnknown: [],
                    kcbMainUnknownTotal: 0,

                    loadingMpesa: true,
                    mpesaVerified: [],
                    mpesaVerifiedTotal: 0,
                    mpesaUnknown: [],
                    mpesaUnknownTotal: 0,
                }
            },

            mounted() {
                $("body").addClass('sidebar-collapse');

                this.fetchRecords();
                this.fetchBankSummary();
                this.fetchVerifiedReceipts();
                this.fetchSales();
                this.fetchReturns();
                this.fetchFraudTrans();
                this.fetchUnVerifiedReceipts();
                this.fetchOtherReceivables();
                this.fetchEazzy();
                this.fetchEbMain();
                this.fetchVooma();
                this.fetchKcbMain();
                this.fetchMpesa();
            },

            computed: {
                date() {
                    return window.date
                },

                branch() {
                    return window.branch
                },

                user() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
            },

            created() {
                // this.fetchPendingRecords();
            },

            methods: {
                getFilters() {
                    return {
                        to_date: this.date,
                        from_date: this.date,
                        branch_id: this.branch
                    }
                },

                fetchRecords() {
                    axios.get('/banking/route/overview/records', {
                        params: this.getFilters()
                    }).then(response => {
                        this.records = response.data;
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
                fetchBankSummary() {
                    axios.get('/banking/route/bank-summary', {
                        params: this.getFilters()
                    }).then(response => {
                        this.bankReconciliation = response.data.records;
                    }).catch(error => {
                    });
                },

                fetchVerifiedReceipts() {
                    this.verifiedLoading = true;
                    axios.get('/banking/route/details/verified', {
                        params: this.getFilters()
                    }).then(response => {
                        this.verifiedLoading = false;
                        this.verifiedReceipts = response.data;

                        setTimeout(() => {
                            this.verifiedTable = $('#verified-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function(settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                    if (total_record < 11) {
                                    }
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);
                    }).catch(error => {
                        this.verifiedLoading = false;
                    });
                },
                fetchSales() {
                    this.loadingSales = true;
                    axios.get('/banking/route/records/sales', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingSales = false;
                        this.sales = response.data.records;
                        this.salesGrossTotal = response.data.gross_total;


                        setTimeout(() => {
                            this.salesTable?.destroy();
                            this.salesTable = $('#sales-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function(settings, json) {

                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);
                    }).catch(error => {
                        this.loadingSales = false;
                    });
                },

                //returns
                fetchReturns() {
                    this.loadingReturns = true;
                    axios.get('/banking/route/records/returns', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingReturns = false;
                        this.returns = response.data.returns;
                        this.returnsTotal = response.data.returnsTotal;


                        setTimeout(() => {
                            this.returnsTable?.destroy();
                            this.returnsTable = $('#returns-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function(settings, json) {

                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);
                    }).catch(error => {
                        this.loadingReturns = false;
                    });
                },
                fetchEazzy() {
                    axios.get('/banking/route/records/eazzy', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingEazzy = false;
                        this.eazzyVerified = response.data.verified;
                        this.eazzyUnknown = response.data.unknown;
                        this.eazzyUnknownTotal = response.data.unknownTotal;
                        this.eazzyVerifiedTotal = response.data.verifiedTotal;

                    }).catch(error => {
                        this.loadingEazzy = false;
                    });
                },
                fetchEbMain() {
                    axios.get('/banking/route/records/eb-main', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingEbMain = false;
                        this.ebMainVerified = response.data.verified;
                        this.ebMainUnknown = response.data.unknown;
                        this.ebMainUnknownTotal = response.data.unknownTotal;
                        this.ebMainVerifiedTotal = response.data.verifiedTotal;

                    }).catch(error => {
                        this.loadingEbMain = false;
                    });
                },
                fetchVooma() {
                    axios.get('/banking/route/records/vooma', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingVooma = false;
                        this.voomaVerified = response.data.verified;
                        this.voomaUnknown = response.data.unknown;
                        this.voomaUnknownTotal = response.data.unknownTotal;
                        this.voomaVerifiedTotal = response.data.verifiedTotal;

                    }).catch(error => {
                        this.loadingVooma = false;
                    });
                },
                fetchKcbMain() {
                    axios.get('/banking/route/records/kcb-main', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingKcbMain = false;
                        this.kcbMainVerified = response.data.verified;
                        this.kcbMainUnknown = response.data.unknown;
                        this.kcbMainUnknownTotal = response.data.unknownTotal;
                        this.kcbMainVerifiedTotal = response.data.verifiedTotal;
                    }).catch(error => {
                        this.loadingKcbMain = false;

                    });
                },
                fetchMpesa() {
                    axios.get('/banking/route/records/mpesa', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingMpesa = false;
                        this.mpesaVerified = response.data.verified;
                        this.mpesaUnknown = response.data.unknown;
                        this.mpesaUnknownTotal = response.data.unknownTotal;
                        this.mpesaVerifiedTotal = response.data.verifiedTotal;
                    }).catch(error => {
                        this.loadingMpesa = false;

                    });
                },

                fetchFraudTrans() {
                    axios.get('/banking/route/details/fraud', {
                        params: this.getFilters()
                    }).then(response => {
                        this.fraudTrans = response.data;

                        setTimeout(() => {
                            this.fraudTable?.destroy();
                            this.fraudTable = $('#fraud-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function(settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                    if (total_record < 11) {
                                        // $('.dataTables_paginate').hide();
                                    }
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);
                    }).catch(error => {
                        //
                    });
                },

                fetchUnVerifiedReceipts() {
                    this.unVerifiedLoading = true;
                    axios.get('/banking/route/details/unverified', {
                        params: this.getFilters()
                    }).then(response => {
                        this.unVerifiedLoading = false;
                        this.unVerifiedReceipts = response.data;

                        setTimeout(() => {
                            this.unVerifiedTable?.destroy();
                            this.unVerifiedTable = $('#unverified-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 200,
                                'initComplete': function(settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                    if (total_record < 201) {
                                        // $('.dataTables_paginate').hide();
                                    }
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);

                        setTimeout(() => {
                            $('#selectAllUnverifiedCheckbox').change(() => {
                                let checked = $('#selectAllUnverifiedCheckbox').prop(
                                    'checked');

                                if (checked) {
                                    this.allUnverifiedSelected = true
                                    this.unVerifiedReceipts.forEach((entry) => {
                                        $(`#select-${entry.id}`).prop('checked',
                                            true)
                                    })
                                } else {
                                    this.allUnverifiedSelected = false
                                    this.unVerifiedReceipts.forEach((entry) => {
                                        $(`#select-${entry.id}`).prop('checked',
                                            false)
                                    })
                                }
                            });
                        }, 2000);
                    }).catch(error => {
                        this.unVerifiedLoading = false;
                    });
                },

                fetchOtherReceivables() {
                    axios.get('/banking/route/details/others', {
                        params: this.getFilters()
                    }).then(response => {
                        this.otherReceivables = response.data;

                        setTimeout(() => {
                            this.othersTable = $('#others-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function(settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                    if (total_record < 11) {
                                        // $('.dataTables_paginate').hide();
                                    }
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);
                    }).catch(error => {
                        //
                    });
                },

                promptSuspendUnverified() {
                    this.unverifiedReceiptsComment = null;
                    this.selectedUnverifiedTotal = 0;
                    this.selectedUnverifiedReceipts = [];
                    let selectedIds = [];

                    this.unVerifiedReceipts.forEach((entry) => {
                        if ($(`#select-${entry.id}`).prop('checked') == true) {
                            this.selectedUnverifiedReceipts.push(entry.id);
                            this.selectedUnverifiedTotal += parseFloat(entry.raw_amount);
                        }
                    })

                    if (this.selectedUnverifiedReceipts.length === 0) {
                        return this.toaster.errorMessage('Please select at least one receipt to resolve');
                    }

                    $("#suspend-modal").modal("show");
                },

                approveAndClose() {
                    $("#approve-modal").modal("hide");
                    $(".btn-loader").show();
                    let verifiedIds = [];
                    this.verifiedReceipts.forEach((entry) => {
                        verifiedIds.push(entry.id);
                    })

                    this.fraudTrans.forEach((entry) => {
                        verifiedIds.push(entry.id);
                    })


                    let payload = {
                        ids: verifiedIds,
                        blamable: this.user.id
                    };

                    axios.post('/banking/route/details/approve', payload).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage('Transactions approved successfully.');
                        window.location.reload();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                promptApprove() {
                    if ((this.unVerifiedReceipts?.length ?? 0) > 0) {
                        return this.toaster.errorMessage(
                            'This day has unresolved unverified receipts and cannot be closed.');
                    }

                    $("#approve-modal").modal("show");
                },

                suspendUnverified() {
                    $(".btn-loader").show();
                    let payload = {
                        ids: this.selectedUnverifiedReceipts,
                        comment: this.unverifiedReceiptsComment,
                        blamable: this.user.id
                    };

                    axios.post('/banking/route/details/unverified/suspend', payload).then(response => {
                        $(".btn-loader").hide();
                        $("#suspend-modal").modal("hide");
                        this.toaster.successMessage('Unverified receipts suspended successfully.');

                        setTimeout(() => {
                            this.unVerifiedTable?.destroy();
                            this.fraudTable?.destroy();

                            this.unVerifiedReceipts = [];
                            this.allUnverifiedSelected = false;
                            this.unverifiedReceiptsComment = null;
                            this.selectedUnverifiedTotal = 0;
                            this.selectedUnverifiedReceipts = [];

                            this.fetchRecords();
                            this.fetchFraudTrans();
                            this.fetchUnVerifiedReceipts();
                        }, 500);
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
                downloadPdf() {
                        axios
                            .get('/banking/route/route-sale-banking-overview/print', {
                                params: this.getFilters(),
                                responseType: 'blob', 
                            })
                            .then(response => {
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;
                                link.setAttribute('download', 'Cash_Sales_Banking_Overview.pdf'); 
                                document.body.appendChild(link);
                                link.click();
                                link.remove();
                            })
                            .catch(error => {
                                console.error('Error downloading the PDF:', error);
                            });
                        },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
