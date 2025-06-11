@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branches = {!! $branches !!};
        window.channels = {!! $channels !!};
        window.user = {!! $user !!};
        window.selectBranch = {!! $selectBranch !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Cash Sales Banking Overview </h3>

                    <div class="d-flex" v-cloak>
                        <div v-if="bankingRecord.stage == 1">
                           
                            @if (can('complete-verification', 'reconciliation'))
                                <button class="btn btn-success btn-sm" @click="promptCompleteVerification"
                                    v-if="showCompleteVerification">
                                    <i class="fas fa-thumbs-up btn-icon"></i>
                                    Complete Verification
                                </button>
                            @endif
                            @if (can('run-verification', 'reconciliation'))
                                <button class="btn btn-success btn-sm" @click="runVerification" v-else>
                                    <i class="fas fa-list-check btn-icon"></i>
                                    Run Verification
                                </button>
                            @endif
                        </div>
                        <div v-else>
                            @if (can('close-banking', 'reconciliation'))
                                <button class="btn btn-success btn-sm ml-12" @click="promptApproval"
                                    v-if="!bankingRecord.closed">
                                    <i class="fas fa-thumbs-up btn-icon"></i>Approve & Close
                                </button>
                            @endif
                            @if (can('download-summary', 'reconciliation'))
                                <button class="btn btn-success btn-sm ml-12" @click="downloadPdf" v-if="bankingRecord.closed">
                                    <i class="fas fa-download"></i>
                                    Download
                                </button>
                            @endif
                         
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-2">
                            <label for="branch_id" class="control-label"> Branch </label>
                            <select id="branch_id" class="form-control mlselect" required>
                                <option value="" disabled selected>Select a branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if ($branch->id == $selectBranch) selected @endif>
                                        {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="date" class="control-label"> From Date </label>
                            <input type="date" name="date" id="date" class="form-control"
                                value="{{ $date }}">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="status" class="control-label"> Status </label>
                            <select id="status" class="form-control mlselect" required>
                                <option value="all" selected> All </option>
                                <option value="pending"> Pending </option>
                                <option value="closed"> Closed </option>
                            </select>
                        </div>

                        <div class="form-group col-md-1">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-success btn-sm" @click="fetchRecords"><i
                                        class="fas fa-search btn-icon"></i>
                                    Search</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6><strong> KEY: </strong></h6>
                            <span> <strong> Total Rcts </strong> = Eazzy + EB Main + Vooma + KCB Main + Mpesa +
                                CDM</span><br>
                            <span> <strong> Sale Var </strong> = Net Sales - Total Rcts</span>

                        </div>
                        {{-- <div class="col-md-3">
                            <span><span style="font-weight: bold; font-size:15px;"> OPENING BALANCE: </span> <span style="font-weight: bold; font-size:25px;">@{{ formatNumber(openingBalance) }} </span></span><br>
                            <span><span style="font-weight: bold; font-size:15px;"> TODAY'S BALANCE: </span> <span style="font-weight: bold; font-size:25px;">@{{ formatNumber(todaysBalance) }} </span></span><br>
                            <span><span style="font-weight: bold; font-size:15px;"> CLOSING BALANCE: </span> <span style="font-weight: bold; font-size:25px;">@{{ formatNumber(openingBalance + todaysBalance) }} </span></span>
                          
                          
                        </div> --}}
                    </div>
                </div>

                <div class="box-header">
                    <h3 class="box-title"> Sales Reconciliation </h3>
                    <div class="table-responsive">

                        <table class="table table-hover table-bordered mt-10" id="records-tablee">
                            <thead>
                                <tr>
                                    <th style="text-align: right;"> Sales </th>
                                    <th style="text-align: right;"> Rtns </th>
                                    <th style="text-align: right;"> Expenses </th>
                                    <th style="text-align: right;"> Net Sales </th>
                                    <th style="text-align: right;"> Eazzy </th>
                                    <th style="text-align: right;"> Equity Main </th>
                                    <th style="text-align: right;"> Vooma </th>
                                    <th style="text-align: right;"> KCB Main </th>
                                    <th style="text-align: right;"> MPESA </th>
                                    <th style="text-align: right;"> CDM </th>
                                    <th style="text-align: right;"> Total Rcts </th>
                                    <th style="text-align: right;"> Verified </th>
                                    <th style="text-align: right;"> Sales Var </th>
                                    <th style="text-align: right;"> Allocated CDM </th>
                                    <th style="text-align: right;"> Allocated CB </th>
                                    <th style="text-align: right;"> Balance </th>
                                </tr>
                            </thead>

                            <tbody v-cloak>
                                {{-- <tr v-for="(record, index) in records" :key="index"> --}}
                                <tr>
                                    <td style="text-align: right;"> @{{ records.sales }} </td>
                                    <td style="text-align: right;"> @{{ records.returns }} </td>
                                    <td style="text-align: right;"> @{{ records.expenses }} </td>
                                    <td style="text-align: right;"> @{{ records.net_sales }} </td>
                                    <td style="text-align: right;"> @{{ records.eazzy }} </td>
                                    <td style="text-align: right;"> @{{ records.eb_main }} </td>
                                    <td style="text-align: right;"> @{{ records.vooma }} </td>
                                    <td style="text-align: right;"> @{{ records.kcb_main }} </td>
                                    <td style="text-align: right;"> @{{ records.mpesa }} </td>
                                    <td style="text-align: right;"> @{{ records.cdm }} </td>
                                    <td style="text-align: right;"> @{{ records.total_bankings }} </td>
                                    <td style="text-align: right;"> @{{ records.verified }} </td>
                                    <td style="text-align: right;"> @{{ records.sales_variance }} </td>
                                    <td style="text-align: right;"> @{{ records.allocated_cdms }} </td>
                                    <td style="text-align: right;"> @{{ records.allocated_cb }} </td>
                                    <td style="text-align: right;"> @{{ records.balance }} </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>

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

                <ul class="nav nav-tabs" id="data-tabs" style="font-weight: 800;">
                    <li class="active"><a href="#sales" data-toggle="tab"> <i
                                class="fas fa-cash-register btn-icon"></i>
                            Sales </a></li>
                    <li><a href="#returns" data-toggle="tab"> <i class="fas fa-rotate-left btn-icon"></i> Returns </a>
                    </li>
                    <li><a href="#eazzy" data-toggle="tab"> <i class="fas fa-comments-dollar btn-icon"></i> Eazzy </a>
                    </li>
                    <li><a href="#eb_main" data-toggle="tab"> <i class="fas fa-building-columns btn-icon"></i> Equity
                            Main
                        </a></li>
                    <li><a href="#vooma" data-toggle="tab"> <i class="fas fa-comments-dollar btn-icon"></i> Vooma </a>
                    </li>
                    <li><a href="#kcb" data-toggle="tab"> <i class="fas fa-building-columns btn-icon"></i> KCB Main
                        </a>
                    </li>
                    <li><a href="#mpesa" data-toggle="tab"> <i class="fas fa-mobile btn-icon"></i> MPESA </a></li>
                    <li><a href="#unverified" data-toggle="tab"> <i class="fas fa-sack-xmark btn-icon"></i> Unverified
                        </a></li>
                    <li><a href="#cdm" data-toggle="tab"> <i class="fas fa-receipt btn-icon"></i> CDM Deposits </a>
                    </li>
                    <li><a href="#cash_banking" data-toggle="tab"> <i class="fas fa-coins btn-icon"></i> Cash Banking
                        </a></li>
                    <li><a href="#manual_allocations" data-toggle="tab"> <i class="fas fa-cog btn-icon"></i> Manual
                            Allocations
                        </a></li>
                    <li><a href="#short_banking_comments" data-toggle="tab"> <i class="fas fa-coins btn-icon"></i> Short
                            Banking Comments
                        </a></li>

                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="sales" v-cloak>
                        @include('banking_approval.partials.sales')
                    </div>

                    <div class="tab-pane" id="returns" v-cloak>
                        @include('banking_approval.partials.returns')
                    </div>

                    <div class="tab-pane" id="eazzy" v-cloak>
                        @include('banking_approval.partials.eazzy')
                    </div>

                    <div class="tab-pane" id="eb_main" v-cloak>
                        @include('banking_approval.partials.ebmain')
                    </div>

                    <div class="tab-pane" id="vooma" v-cloak>
                        @include('banking_approval.partials.vooma')
                    </div>

                    <div class="tab-pane" id="kcb" v-cloak>
                        @include('banking_approval.partials.kcb')
                    </div>

                    <div class="tab-pane" id="mpesa" v-cloak>
                        @include('banking_approval.partials.mpesa')
                    </div>

                    <div class="tab-pane" id="unverified" v-cloak>
                        @include('banking_approval.partials.unverified')
                    </div>

                    <div class="tab-pane" id="cdm" v-cloak>
                        @include('banking_approval.partials.cdm')
                    </div>

                    <div class="tab-pane" id="cash_banking" v-cloak>
                        @include('banking_approval.partials.cb')
                    </div>
                    <div class="tab-pane" id="manual_allocations" v-cloak>
                        @include('banking_approval.partials.manual_allocations')
                    </div>
                    <div class="tab-pane" id="short_banking_comments" v-cloak>
                        @include('banking_approval.partials.sb_comments')
                    </div>
                </div>

            </div>
        </div>

        <div class="modal fade" id="confirm-cdm-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> CDM Deposit Allocation </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to allocate transaction "@{{ searchedCdm.reference }}" of @{{ searchedCdm.amount }}
                        to drop number @{{ selectedDrop.reference }}?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="allocateCdm" id="allocateCdmBtn">
                                Yes, Allocate</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-cb-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Cash Banking Deposit Allocation </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to allocate transaction "@{{ searchedCb.reference }}" of @{{ searchedCb.amount }}
                        to today's cash banking?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="allocateCb" id="allocateCbBtn">
                                Yes, Allocate</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="shortBankingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Short Banking Comments</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="text" class="form-control" name="sb_amount" id="sb_amount" required>
                        </div>


                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="sb_type" id="sb_type" class="form-control sb_type">
                                <option value="Director">Director</option>
                                <option value="Staff Short">Staff Short</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Comment</label>
                            <textarea class="form-control" name="sb_comment" id="sb_comment" rows="3" required></textarea>
                        </div>

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <a id="confirmBtn" href="#" class="btn btn-success btn-sm" @click="saveShortbanking">
                                <i class="fas fa-paper-plane"></i> Save</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="editShortBankingModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> Edit Short Banking Comments</h3>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="form-group hidden">
                            <label for="">Amount</label>
                            <input type="text" class="form-control" name="sb_id" id="sb_id" required>
                        </div>

                        <div class="form-group">
                            <label for="">Amount</label>
                            <input type="text" class="form-control" name="sb_amount_edit" id="sb_amount_edit"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="">Type</label>
                            <select name="sb_type_edit" id="sb_type_edit" class="form-control sb_type">
                                <option value="Director">Director</option>
                                <option value="Staff Short">Staff Short</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="">Comment</label>
                            <textarea class="form-control" name="sb_comment_edit" id="sb_comment_edit" rows="3" required></textarea>
                        </div>

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
                            <a id="confirmBtnEdit" href="#" class="btn btn-success btn-sm"
                                @click="editShortbanking"> <i class="fas fa-paper-plane"></i> Save</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-verify-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Complete Verification </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to complete verification for this day? 
                        <br>
                        <br>
                        This action will serve as an
                        acknowledgement that
                        all the figures are correct and ready for approval.
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" @click="confirmCompleteVerification" id="confirmCompleteVerificationBtn">
                                Yes, Complete</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-approve-modal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog" role="document">
            <div class="modal-content box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"> Approve & Close Banking </h3>
                </div>

                <div class="box-body">
                    Are you sure you want to approve and close banking for this day?
                </div>

                <div class="box-footer">
                    <div class="box-header-flex">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" @click="confirmApproveAndClose" @dbclick="confirmApproveAndClose"  id="confirmCompleteVerificationBtn">
                            Yes, Approve</button>
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
                    bankingRecord: {},

                    loadingSales: true,
                    sales: [],
                    salesGrossTotal: 0,
                    salesDiscountTotal: 0,
                    salesNetTotal: 0,

                    loadingReturns: true,
                    returns: [],
                    returnsTotal: 0,

                    cdms: [],
                    cdmTotal: {},
                    loadingCdms: true,
                    cdmErrorMessage: null,
                    cdmSearchAmount: null,
                    cdmSearchRef: null,
                    searchedCdm: {},
                    selectedDropId: null,
                    selectedDrop: {},
                    drops: [],

                    cb: [],
                    loadingCb: true,
                    cbErrorMessage: null,
                    cbSearchAmount: null,
                    cbSearchRef: null,
                    searchedCb: {},

                    loadingUnverified: true,
                    unverified: [],
                    unverifiedTotal: 0,

                    bankReconciliation: [],

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

                    sb: [],
                    loadingSb: true,
                    sbErrorMessage: null,
                    sbTotal: 0,
                    rawSbTotal: 0,

                    loadingManualAllocations: true,
                    manualAllocations: [],
                    manualAllocationsTotal: 0,

                    showCompleteVerification: false

                }
            },

            mounted() {
                $("body").addClass('sidebar-collapse');

                $("#branch_id").val(this.selectBranch);
                $(".mlselect").select2();
                $(".sb_type").select2();

                let today = dayjs().format('YYYY-MM-DD');
                let yesterday = dayjs().subtract(1, 'day').format('YYYY-MM-DD');

                this.fetchRecords();
            },

            computed: {
                branches() {
                    return window.branches
                },

                channels() {
                    return window.branches
                },

                user() {
                    return window.user
                },
                selectBranch() {
                    return window.selectBranch
                },


                toaster() {
                    return new Form();
                },
            },

            methods: {
                getFilters() {
                    return {
                        date: $("#date").val(),
                        branch_id: $("#branch_id").val(),
                        status: $("#status").val(),
                        channel: $("#channel").val(),
                    }
                },
                formatNumber(value) {
                    return Number(value).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    });
                },


                downloadPdf() {
                    axios
                        .get('/banking/pos/cash-sale-banking-overview/print', {
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



                fetchRecords() {
                    $(".btn-loader").show();
                    axios.get('/banking/pos/records', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.records = response.data.sales;
                        this.bankingRecord = response.data.bankingRecord;

                        this.fetchBankSummary();
                        this.fetchSales();
                        this.fetchReturns();
                        this.fetchCdms();
                        this.fetchDrops();
                        this.fetchUnverified();
                        this.fetchCb();
                        this.fetchEazzy();
                        this.fetchEbMain();
                        this.fetchVooma();
                        this.fetchKcbMain();
                        this.fetchMpesa();
                        this.fetchSb();
                        this.fetchManualAllocations();


                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchBankSummary() {
                    axios.get('/banking/pos/bank-summary', {
                        params: this.getFilters()
                    }).then(response => {
                        this.bankReconciliation = response.data.records;
                    }).catch(error => {
                        // this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                runVerification() {
                    let payload = this.getFilters();
                    $(".btn-loader").show();
                    axios.post('/banking/pos/records/verify', payload).then(response => {
                        $(".btn-loader").hide();

                        setTimeout(() => {
                            this.fetchRecords();
                        }, 500);
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

                fetchCdms() {
                    this.loadingCdms = true;
                    axios.get('/banking/pos/records/cdms', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingCdms = false;
                        this.cdms = response.data.cdms;
                        this.cdmTotal = response.data.total;

                        setTimeout(() => {
                            this.cdmsTable?.destroy();
                            this.cdmsTable = $('#cdms-table').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 100,
                                'initComplete': function(settings, json) {

                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                            });
                        }, 500);
                    }).catch(error => {
                        this.loadingCdms = false;
                    });
                },

                fetchDrops() {
                    axios.get('/banking/pos/records/drops', {
                        params: this.getFilters()
                    }).then(response => {
                        this.drops = response.data;

                        setTimeout(() => {
                            if ($("#drop_id").data('select2')) {
                                $("#drop_id").select2('destroy')
                            }
                            $("#drop_id").select2();

                            $("#drop_id").change(() => {
                                this.selectedDropId = $("#drop_id").val();
                                this.selectedDrop = this.drops.find(d => d.id == this
                                    .selectedDropId);
                            });
                        }, 500);
                    }).catch(error => {
                        this.loadingCdms = false;
                    });
                },

                searchCdm() {
                    $("#searchCdmBtn").prop('disabled', true);
                    this.cdmErrorMessage = null;

                    if (!this.selectedDropId) {
                        this.cdmErrorMessage = 'Please select a drop transaction for allocation';
                        $("#searchCdmBtn").prop('disabled', false);
                        return;
                    }

                    if (!this.cdmSearchAmount || !this.cdmSearchRef) {
                        this.cdmErrorMessage = 'Please enter both cdm deposit amount and reference to search';
                        $("#searchCdmBtn").prop('disabled', false);
                        return;
                    }

                    let payload = {
                        drop_id: $("#drop_id").val(),
                        amount: this.cdmSearchAmount,
                        reference: this.cdmSearchRef,
                    };

                    $(".btn-loader").show();
                    axios.post('/banking/pos/records/cdms/search', payload).then(response => {
                        $(".btn-loader").hide();
                        $("#searchCdmBtn").prop('disabled', false);

                        this.searchedCdm = response.data;

                        $("#confirm-cdm-modal").modal("show");
                    }).catch(error => {
                        $(".btn-loader").hide();
                        $("#searchCdmBtn").prop('disabled', false);
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

                allocateCdm() {
                    let payload = {
                        drop_id: this.selectedDropId,
                        bank_id: this.searchedCdm.id
                    };

                    $("#confirm-cdm-modal").modal("hide");
                    $(".btn-loader").show();
                    axios.post('/banking/pos/records/cdms/allocate', payload).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage('CDM Deposit allocated successfully.');

                        setTimeout(() => {
                            this.cdmSearchAmount = null;
                            this.cdmSearchRef = null;
                            this.searchedCdm = {};
                            this.selectedDropId = null;
                            this.selectedDrop = {};
                            $("#drop_id").val(null);

                            this.fetchRecords();
                        }, 500);
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

                fetchSales() {
                    this.loadingSales = true;
                    axios.get('/banking/pos/records/sales', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingSales = false;
                        this.sales = response.data.records;
                        this.salesNetTotal = response.data.total;
                        this.salesGrossTotal = response.data.gross_total;
                        this.salesDiscountTotal = response.data.discount_total;

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
                    axios.get('/banking/pos/records/returns', {
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

                fetchUnverified() {
                    this.loadingUnverified = true;
                    axios.get('/banking/pos/records/unverified', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingUnverified = false;
                        this.unverified = response.data.records;
                        this.unverifiedTotal = response.data.total;

                        setTimeout(() => {
                            this.unverifiedTable?.destroy();
                            this.unverifiedTable = $('#unverified-table').DataTable({
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
                        this.loadingUnverified = false;
                    });
                },

                fetchCb() {
                    this.loadingCb = true;
                    axios.get('/banking/pos/records/cash-banking', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingCb = false;
                        this.cb = response.data.records;
                    }).catch(error => {
                        this.loadingCb = false;
                    });
                },
                fetchSb() {
                    this.loadingSb = true;
                    axios.get('/banking/pos/records/short-banking', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingSb = false;
                        this.sb = response.data.records;
                        this.sbTotal = response.data.sbTotal;
                        this.rawSbTotal = response.data.rawSbTotal;

                        this.showCompleteVerification = (this.records.raw_rcts == this.records
                            .raw_verified) && (this.rawSbTotal == this.records.raw_balance);
                    }).catch(error => {
                        this.loadingSb = false;
                    });
                },
                fetchOpeningBalance() {
                    axios.get('/banking/pos/opening-balance', {
                        params: this.getFilters()
                    }).then(response => {
                        this.openingBalance = response.data.openingBalance;
                    }).catch(error => {});
                },
                shortBankingModalInitiate() {
                    $("#shortBankingModal").modal("show");

                },
                saveShortbanking() {
                    $("#confirmBtn").prop('disabled', true);
                    const sbAmount = $('#sb_amount').val();
                    const sbComment = $('#sb_comment').val();
                    const sbType = $('#sb_type').val();
                    const date = $("#date").val();
                    const branch_id = $("#branch_id").val();
                    let form = new Form();

                    $.ajax({
                        url: '{{ route('allocate-pos-short-banking') }}',
                        method: 'POST',
                        data: {
                            amount: sbAmount,
                            comment: sbComment,
                            date: date,
                            branch_id: branch_id,
                            type: sbType,
                            _token: '{{ csrf_token() }}'
                        },

                        success: (response) => {
                            form.successMessage('Comment Saved Successfully.');
                            $("#shortBankingModal").modal("hide");
                            $('#sb_amount').val('');
                            $('#sb_comment').val('');
                            $('#sb_type').val('');
                            setTimeout(() => {
                                this.fetchRecords();
                                this.fetchSb();
                            }, 500);
                        },
                        error: function(error) {
                            form.errorMessage(error.response.data.message);
                        }
                    });

                },

                searchCb() {
                    $("#searchCbBtn").prop('disabled', true);
                    this.cbErrorMessage = null;

                    if (!this.cbSearchAmount || !this.cbSearchRef) {
                        this.cbErrorMessage =
                            'Please enter both cash banking deposit amount and reference to search';
                        $("#searchCbBtn").prop('disabled', false);
                        return;
                    }

                    let payload = {
                        amount: this.cbSearchAmount,
                        reference: this.cbSearchRef,
                    };

                    $(".btn-loader").show();
                    axios.post('/banking/pos/records/cash-banking/search', payload).then(response => {
                        $(".btn-loader").hide();
                        $("#searchCbBtn").prop('disabled', false);

                        this.searchedCb = response.data;

                        $("#confirm-cb-modal").modal("show");
                    }).catch(error => {
                        $(".btn-loader").hide();
                        $("#searchCbBtn").prop('disabled', false);
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

                allocateCb() {
                    let payload = {
                        bank_id: this.searchedCb.id,
                        date: $("#date").val(),
                        branch_id: $("#branch_id").val()
                    };

                    $("#confirm-cb-modal").modal("hide");
                    $(".btn-loader").show();
                    axios.post('/banking/pos/records/cash-banking/allocate', payload).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage('Cash banking deposit allocated successfully.');

                        setTimeout(() => {
                            this.cbSearchAmount = null;
                            this.cbSearchRef = null;
                            this.searchedCb = {};

                            this.fetchRecords();
                        }, 500);
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

                fetchEazzy() {
                    axios.get('/banking/pos/records/eazzy', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingEazzy = false;
                        this.eazzyVerified = response.data.verified;
                        this.eazzyUnknown = response.data.unknown;
                        this.eazzyUnknownTotal = response.data.unknownTotal;
                        this.eazzyVerifiedTotal = response.data.verifiedTotal;

                        // setTimeout(() => {
                        //     this.eazzyVerifiedTable?.destroy();
                        //     this.eazzyVerifiedTable = $('#eazzy-verified-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });

                        //     this.eazzyUnknownTable?.destroy();
                        //     this.eazzyUnknownTable = $('#eazzy-unknown-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });
                        // }, 500);
                    }).catch(error => {
                        this.loadingEazzy = false;
                    });
                },
                fetchEbMain() {
                    axios.get('/banking/pos/records/eb-main', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingEbMain = false;
                        this.ebMainVerified = response.data.verified;
                        this.ebMainUnknown = response.data.unknown;
                        this.ebMainUnknownTotal = response.data.unknownTotal;
                        this.ebMainVerifiedTotal = response.data.verifiedTotal;

                        // setTimeout(() => {
                        //     this.ebMainVerifiedTable?.destroy();
                        //     this.ebMainVerifiedTable = $('#eb-main-verified-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });

                        //     this.ebMainUnknownTable?.destroy();
                        //     this.ebMainUnknownTable = $('#eb-main-unknown-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });
                        // }, 500);
                    }).catch(error => {
                        this.loadingEbMain = false;
                    });
                },
                fetchVooma() {
                    axios.get('/banking/pos/records/vooma', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingVooma = false;
                        this.voomaVerified = response.data.verified;
                        this.voomaUnknown = response.data.unknown;
                        this.voomaUnknownTotal = response.data.unknownTotal;
                        this.voomaVerifiedTotal = response.data.verifiedTotal;

                        // setTimeout(() => {
                        //     this.voomaVerifiedTable?.destroy();
                        //     this.voomaVerifiedTable = $('#vooma-verified-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });

                        //     this.voomaUnknownTable?.destroy();
                        //     this.voomaUnknownTable = $('#vooma-unknown-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });
                        // }, 500);
                    }).catch(error => {
                        this.loadingVooma = false;
                    });
                },
                fetchKcbMain() {
                    axios.get('/banking/pos/records/kcb-main', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingKcbMain = false;
                        this.kcbMainVerified = response.data.verified;
                        this.kcbMainUnknown = response.data.unknown;
                        this.kcbMainUnknownTotal = response.data.unknownTotal;
                        this.kcbMainVerifiedTotal = response.data.verifiedTotal;

                        // setTimeout(() => {
                        //     this.kcbMainVerifiedTable?.destroy();
                        //     this.kcbMainVerifiedTable = $('#kcb-main-verified-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });

                        //     this.kcbMainUnknownTable?.destroy();
                        //     this.kcbMainUnknownTable = $('#kcb-main-unknown-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });
                        // }, 500);
                    }).catch(error => {
                        this.loadingKcbMain = false;

                    });
                },
                fetchMpesa() {
                    axios.get('/banking/pos/records/mpesa', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingMpesa = false;
                        this.mpesaVerified = response.data.verified;
                        this.mpesaUnknown = response.data.unknown;
                        this.mpesaUnknownTotal = response.data.unknownTotal;
                        this.mpesaVerifiedTotal = response.data.verifiedTotal;

                        // setTimeout(() => {
                        //     this.mpesaVerifiedTable?.destroy();
                        //     this.mpesaVerifiedTable = $('#mpesa-verified-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });

                        //     this.mpesaUnknownTable?.destroy();
                        //     this.mpesaUnknownTable = $('#mpesa-unknown-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 10,
                        //         'initComplete': function(settings, json) {

                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });
                        // }, 500);
                    }).catch(error => {
                        this.loadingMpesa = false;

                    });
                },
                editSbRecord(id) {
                    axios.get('/banking/pos/fetch-short-banking-comment-record', {
                        params: {
                            id: id
                        },
                    }).then(response => {
                        console.log(response.data);
                        $('#sb_amount_edit').val(response.data.amount);
                        $('#sb_comment_edit').val(response.data.comment);
                        $('#sb_type_id').val(response.data.type);
                        $('#sb_id').val(response.data.id);
                        $("#editShortBankingModal").modal("show");

                    }).catch(error => {});


                },
                editShortbanking() {
                    const sbAmountEdit = $('#sb_amount_edit').val();
                    const sbCommentEdit = $('#sb_comment_edit').val();
                    const sbTypeEdit = $('#sb_type_edit').val();
                    const sbId = $("#sb_id").val();
                    let form = new Form();

                    $.ajax({
                        url: '{{ route('edit-short-banking-comment') }}',
                        method: 'POST',
                        data: {
                            amount: sbAmountEdit,
                            comment: sbCommentEdit,
                            id: sbId,
                            type: sbTypeEdit,
                            _token: '{{ csrf_token() }}'
                        },

                        success: (response) => {
                            form.successMessage('Comment updated Successfully.');
                            $("#editShortBankingModal").modal("hide");
                            setTimeout(() => {
                                this.fetchRecords();
                                this.fetchSb();
                            }, 500);
                        },
                        error: function(error) {
                            form.errorMessage(error.response.data.message);
                        }
                    });

                },
                fetchManualAllocations() {
                    this.loadingSales = true;
                    axios.get('/banking/pos/records/manual-allocations', {
                        params: this.getFilters()
                    }).then(response => {
                        this.loadingManualAllocations = false;
                        this.manualAllocations = response.data.records;
                        this.manualAllocationsTotal = response.data.manualAllocationsTotal;

                    }).catch(error => {
                        this.loadingManualAllocations = false;
                    });
                },

                promptCompleteVerification() {
                    $("#confirm-verify-modal").modal("show");
                },

                confirmCompleteVerification() {
                    let payload = {
                        banking_record_id: this.bankingRecord.id,
                        user_id: this.user.id
                    };

                    $("#confirm-verify-modal").modal("hide");
                    $(".btn-loader").show();
                    axios.post('/banking/pos/complete-verification', payload).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage('Verification completed successfully.');

                        this.fetchRecords();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

                promptApproval() {
                    $("#confirm-approve-modal").modal("show");
                },

                confirmApproveAndClose() {
                    let payload = {
                        banking_record_id: this.bankingRecord.id,
                        user_id: this.user.id
                    };

                    $("#confirm-approve-modal").modal("hide");
                    $(".btn-loader").show();
                    axios.post('/banking/pos/approve-and-close', payload).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage('Banking approval initiated successfully. Please refresh the page after a short whole for an update.');

                        this.fetchRecords();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    })
                },

            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
