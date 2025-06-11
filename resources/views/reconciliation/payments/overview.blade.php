@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branches = {!! $branches !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    <div class="col-md-9">
                        <div class="form-group col-md-3">
                            <label for="branch_id" class="control-label"> Branch </label>
                            <select id="branch_id" class="form-control mlselect">
                                <option value="" selected disabled></option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="start_date" class="control-label"> Sales Date </label>
                            <input type="date" id="start_date" class="form-control" required value="{{ \Carbon\Carbon::today()->toDateString() }}">
                        </div>

{{--                        <div class="form-group col-md-3">--}}
{{--                            <label for="start_date" class="control-label">End Date </label>--}}
{{--                            <input type="date" id="end_date" class="form-control">--}}
{{--                        </div>--}}

                        <div class="form-group col-md-2">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-primary" @click="refresh"><i class="fas fa-magnifying-glass"></i> Filter</button>
                                <button class="btn btn-primary ml-12" @click="clearFilters"><i class="fas fa-xmark"></i> Clear</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3" style="border-left: 1px solid rgba(0, 0, 0, .125);">
                        <h3 class="box-title" style="margin: 0;"> Debtors Balance </h3>
                        <span style="font-weight: 800; font-size: 35px;" v-cloak> @{{ debtorsBalance }} </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-cloak>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3> @{{ summary.all }} </h3>
                        <p>TOTAL RECEIPTS</p>
                    </div>

                    <div class="icon">
                        <i class="fa fa-fw fa-hand-holding-dollar"></i>
                    </div>

                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" class="small-box-footer">
                        View All <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3> @{{ summary.approved }} </h3>
                        <p>APPROVED RECEIPTS</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-thumbs-up"></i>
                    </div>

                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" class="small-box-footer">
                        View All <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-yellow">
                    <div class="inner">
                        <h3> @{{ summary.verified }} </h3>
                        <p>PENDING APPROVAL</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-money-bill-transfer"></i>
                    </div>

                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" class="small-box-footer">
                        View All <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3> @{{ summary.pending }} </h3>
                        <p>PENDING VERIFICATION</p>
                    </div>

                    <div class="icon">
                        <i class="fa fa-fw fa-filter-circle-dollar"></i>
                    </div>

                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" class="small-box-footer">
                        View All <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Sales Vs Receipts </h3>
                </div>
            </div>
            <div class="box-body">
                    <table class="table">
                        <thead>
                          <tr>
                            <th>Sales</th>
                            <th>Receipts</th>
                            <th>Variance</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <th style="text-align: right;" v-cloak>@{{ salesVsReceiptsData.sales }}</th>
                            <th style="text-align: right;" v-cloak>@{{ salesVsReceiptsData.receipts }}</th>
                            <th style="text-align: right;" v-cloak>@{{ salesVsReceiptsData.variance }}</th>
                          </tr>
                        </tbody>
                      </table>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-center">
            <div class="col-lg-5 col-md-5 box box-primary" >
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Reconciliation Issues </h3>
                    </div>
                </div>
                <div class="box-body">
                        <table class="table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Issue</th>
                                <th>Count</th>
                                <th>Amount</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th>1.</th>
                                <td>Duplicate Entries</td>
                                <td style="text-align: center;" v-cloak>@{{reconIssuesData.duplicate_count}}</td>
                                <td style="text-align: right;" v-cloak>@{{reconIssuesData.duplicate}}</td>
                                <td><a href=""><i class="fas fa-eye" title="view"></i></a></td>
                              </tr>
                              <tr>
                                <th>2</th>
                                <td>Missing Entries</td>
                                <td style="text-align: center;" v-cloak>@{{reconIssuesData.missing_count}}</td>
                                <td style="text-align: right;" v-cloak>@{{reconIssuesData.missing}}</td>
                                <td><a href=""><i class="fas fa-eye" title="view"></i></a></td>
                              </tr>
                              <tr>
                                <th>3</th>
                                <td>Unknown Bankings</td>
                                <td style="text-align: center;" v-cloak>@{{reconIssuesData.unknown_count}}</td>
                                <td style="text-align: right;" v-cloak>@{{reconIssuesData.unknown}}</td>
                                <td><a href=""><i class="fas fa-eye" title="view"></i></a></td>
                              </tr>
                            </tbody>
                          </table>
                </div>

            </div>
            <div class="col-md-1 col-lg-1" ></div>
            <div class="col-lg-5 col-md-5 box box-primary" >
                <div class="box-header with-border">
                    <div class="box-header-flex">
                        <h3 class="box-title"> Reconciliation Resolution </h3>
                    </div>
                </div>
                <div class="box-body">
                        <table class="table">
                            <thead>
                              <tr>
                                <th>#</th>
                                <th>Resolution</th>
                                <th>Count</th>
                                <th>Amount</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <th>1.</th>
                                <td>Suspended Transactions</td>
                                <td style="text-align: center;" v-cloak>@{{reconResolutionData.suspended_count}}</td>
                                <td style="text-align: right;" v-cloak>@{{reconResolutionData.suspended}}</td>
                                <td><a href=""><i class="fas fa-eye" title="view"></i></a></td>
                              </tr>
                              <tr>
                                <th>2.</th>
                                <td>Expunged  Transactions</td>
                                <td style="text-align: center;" v-cloak>@{{reconResolutionData.expunged_count}}</td>
                                <td style="text-align: right;" v-cloak>@{{reconResolutionData.expunged}}</td>
                                <td><a href=""><i class="fas fa-eye" title="view"></i></a></td>
                              </tr>
                              <tr>
                                <th>3.</th>
                                <td>Manual Uploads</td>
                                <td style="text-align: center;" v-cloak>@{{reconResolutionData.manual_count}}</td>
                                <td style="text-align: right;" v-cloak>@{{reconResolutionData.manual}}</td>
                                <td><a href=""><i class="fas fa-eye" title="view"></i></a></td>
                              </tr>
                            </tbody>
                          </table>
                </div>

            </div>
            

        </div>

       
           

    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/axios.min.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>
    <script type="importmap">
        {
          "imports": {
            "vue": "/js/vue.esm-browser.js"
          }
        }
    </script>

    <script type="module">
        import {createApp} from 'vue';

        const app = createApp({
            data() {
                return {
                    debtorsBalance: 0,
                    summary: {
                        all: 0,
                        verified: 0,
                        approved: 0,
                        pending: 0
                    },
                    salesVsReceiptsData: {
                        sales: 0,
                        receipts: 0,
                        variance: 0
                    },
                    reconIssuesData:  {
                        missing: 0,
                        missing_count: 0,
                        duplicate: 0,
                        duplicate_count: 0,
                        unknown: 0,
                        unknown_count: 0,
                    },
                    reconResolutionData:  {
                        suspended: 0,
                        suspended_count: 0,
                        expunged: 0,
                        expunged_count: 0,
                        manual: 0,
                        manual_count: 0,
                    }
                }
            },

            created() {
                this.refresh();
            },

            mounted() {
                $(".mlselect").select2();

                $('body').addClass('sidebar-collapse');
            },

            computed: {
                branches() {
                    return window.branches
                },
            },

            methods: {
                getFilters() {
                    return {
                        branch_id: $('#branch_id').val(),
                        start_date: $('#start_date').val(),
                        end_date: $('#end_date').val(),
                    }
                },

                clearFilters() {
                    $('#branch_id').val(null);
                    $('#start_date').val(null);
                    $('#end_date').val(null);

                    this.refresh();
                },

                refresh() {
                    this.fetchDebtorsBalance();
                    this.fetchSummary();
                    this.fetchSalesVsReceipts();
                    this.fetchReconIssues();
                    this.fetchReconResolutions();

                },

                fetchDebtorsBalance() {
                    axios.get('{{ route("$base_route.overview.debtors-balance") }}', {params: this.getFilters()})
                        .then(response => {
                            if (response.data.success) {
                                this.debtorsBalance = response.data.data;
                            } else {
                                this.toaster.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.toaster.error(error);
                        });
                },

                fetchSummary() {
                    axios.get('{{ route("$base_route.overview.summary") }}', {params: this.getFilters()})
                        .then(response => {
                            if (response.data.success) {
                                this.summary = response.data.data;
                            } else {
                                this.toaster.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.toaster.error(error);
                        });
                },
                fetchSalesVsReceipts() {
                    axios.get('{{ route("$base_route.overview.salesVsReceipts") }}', {params: this.getFilters()})
                        .then(response => {
                            if (response.data.success) {
                                const sales = response.data.data.sales;
                                const receipts = response.data.data.receipts;
                                const variance = response.data.data.variance;

                                this.salesVsReceiptsData = { sales, receipts, variance };
                            } else {
                                this.toaster.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.toaster.error(error);
                        });

                },
                fetchReconIssues() {
                    axios.get('{{ route("$base_route.overview.getReconIssues") }}', {params: this.getFilters()})
                        .then(response => {
                            if (response.data.success) {
                                const missing = response.data.data.missing;
                                const missing_count = response.data.data.missing_count;
                                const duplicate = response.data.data.duplicate;
                                const duplicate_count = response.data.data.duplicate_count;
                                const unknown = response.data.data.unknown;
                                const unknown_count = response.data.data.unknown_count;
                                this.reconIssuesData = { missing, missing_count, duplicate, duplicate_count, unknown, unknown_count};
                            } else {
                                this.toaster.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.toaster.error(error);
                        });

                },
                fetchReconResolutions() {
                    axios.get('{{ route("$base_route.overview.getReconResolutions") }}', {params: this.getFilters()})
                        .then(response => {
                            if (response.data.success) {
                                const suspended = response.data.data.suspended;
                                const suspended_count = response.data.data.suspended_count;
                                const expunged = response.data.data.expunged;
                                const expunged_count = response.data.data.expunged_count;
                                const manual= response.data.data.manual;
                                const manual_count = response.data.data.manual_count;
                                this.reconResolutionData = { suspended, suspended_count, expunged, expunged_count, manual, manual_count};
                            } else {
                                this.toaster.error(response.data.message);
                            }
                        })
                        .catch(error => {
                            console.log(error);
                            this.toaster.error(error);
                        });

                },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection