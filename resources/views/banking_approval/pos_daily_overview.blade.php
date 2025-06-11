@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branches = {!! $branches !!};
        window.user = {!! $user !!};

    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> POS Banking Overview </h3>
                    <div>
                        <span>
                            <h3 class="box-title" style="font-weight: 900; font-size: 25px;">Current Balance: &nbsp;</h3> 
                            <span> 
                                <a :href="balanceLink" style="font-size: 25px; font-weight: 900;" target="_blank" title="Balance">@{{ formatNumber(balance) }}</a>
                            </span>
                        </span>
                    </div>

                </div>

            </div>


            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-2">
                            <label for="branch_id" class="control-label"> Branch </label>
                            <select id="branch_id" class="form-control mlselect" v-model="selectedBranch"
                                @change="branchChanged" required>
                                <option value="" disabled selected>Select a branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="from_date" class="control-label"> From Date </label>
                            <input type="date" name="from_date" id="from_date" class="form-control">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="to_date" class="control-label"> To Date </label>
                            <input type="date" name="to_date" id="to_date" class="form-control">
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-primary" @click="fetchRecords"><i class="fas fa-search btn-icon"></i>
                                    Search</button>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6><strong> KEY: </strong></h6>
                            <span> <strong> Total Rcts </strong> = Eazzy + EB Main + Vooma + KCB Main + Mpesa +
                                CDM</span><br>
                            <span> <strong> Sale Var </strong> = Net Sales - Total Rcts</span>

                        </div>
                    </div>
                </div>

                <hr>
                <div class="col-md-12 table-responsive">


                    <table class="table table-hover table-bordered mt-10 " id="records-table">
                        <thead>
                            <tr>
                                <th> Date </th>
                                <th style="text-align: right;"> Sales </th>
                                <th style="text-align: right;"> Returns </th>
                                <th style="text-align: right;"> Expenses </th>
                                <th style="text-align: right;"> Net Sales </th>
                                <th style="text-align: right;"> Eazzy </th>
                                <th style="text-align: right;"> EB Main </th>
                                <th style="text-align: right;"> Vooma </th>
                                <th style="text-align: right;"> KCB Main </th>
                                <th style="text-align: right;"> MPESA </th>
                                <th style="text-align: right;"> CDM </th>
                                <th style="text-align: right;"> Total Rcts </th>
                                <th style="text-align: right;"> Verified </th>
                                <th style="text-align: right;"> Sales Var </th>
                                <th style="text-align: right;"> CDM Alloc </th>
                                <th style="text-align: right;"> CB Alloc </th>
                                <th style="text-align: right;"> Bal </th>
                                <th></th>
                            </tr>
                        </thead>

                        <tbody v-cloak>
                            <tr v-for="(record, index) in records" :key="index">
                                <td> @{{ record.date }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_sales }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_returns }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_expenses }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_net_sales }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_eazzy }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_eb_main }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_vooma }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_kcb_main }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_mpesa }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_cdm }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_total_bankings }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_verified }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_sales_variance }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_allocated_cdms }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_allocated_cb }} </td>
                                <td style="text-align: right;"> @{{ record.formatted_balance }} </td>
                                <td style="text-align: center;">
                                    <a :href="`/banking/pos/daily-overview/details?date=${record.date}&branch=${record.branch}`"
                                        title="View Details" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th> TOTALS </th>
                                <th style="text-align: right;"> @{{ totals.sales }} </th>
                                <th style="text-align: right;"> @{{ totals.returns }} </th>
                                <th style="text-align: right;"> @{{ totals.expenses }} </th>
                                <th style="text-align: right;"> @{{ totals.net_sales }} </th>
                                <th style="text-align: right;"> @{{ totals.eazzy }} </th>
                                <th style="text-align: right;"> @{{ totals.eb_main }} </th>
                                <th style="text-align: right;"> @{{ totals.vooma }} </th>
                                <th style="text-align: right;"> @{{ totals.kcb_main }} </th>
                                <th style="text-align: right;"> @{{ totals.mpesa }} </th>
                                <th style="text-align: right;"> @{{ totals.cdm }} </th>
                                <th style="text-align: right;"> @{{ totals.total_bankings }} </th>
                                <th style="text-align: right;"> @{{ totals.verified }} </th>
                                <th style="text-align: right;"> @{{ totals.sales_variance }} </th>
                                <th style="text-align: right;"> @{{ totals.allocated_cdms }} </th>
                                <th style="text-align: right;"> @{{ totals.allocated_cb }} </th>
                                <th style="text-align: right;"> @{{ totals.balance }} </th>
                                <th></th>

                            </tr>
                        </tfoot>
                    </table>
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
                    totals: {},
                    balance: 0,
                    selectedBranch: null,
                    balanceLink: '{{ route('short-bankings-details') }}'

                }
            },

            mounted() {
                this.selectedBranch = this.user.restaurant_id;

                $("body").addClass('sidebar-collapse');

                $("#branch_id").val(this.user.restaurant_id);
                $(".mlselect").select2().on('select2:select', (e) => {
                    this.selectedBranch = e.target.value;
                    this.branchChanged();
                });

                let toDate = dayjs().format('YYYY-MM-DD');
                $("#from_date").val(toDate);
                $("#to_date").val(toDate);
                this.branchChanged();


                this.fetchRecords();
                this.fetchBalance();
            },

            computed: {
                branches() {
                    return window.branches
                },

                toaster() {
                    return new Form();
                },

                user() {
                    return window.user
                },

            },

            methods: {
                getFilters() {
                    return {
                        to_date: $("#to_date").val(),
                        from_date: $("#from_date").val(),
                        branch_id: $("#branch_id").val(),
                    }
                },
                branchChanged() {
                    this.updateBalanceLink();
                },
                generateBalanceLink() {
                    if (this.selectedBranch) {
                        return `{{ route('short-bankings-details') }}?branch_id=${this.selectedBranch}`;
                    }
                    return `{{ route('short-bankings-details') }}`;
                },
                updateBalanceLink() {
                    this.balanceLink = this.generateBalanceLink();
                },
                formatNumber(value) {
                    return Number(value).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    });
                },

                fetchRecords() {
                    this.fetchBalance();
                    $(".btn-loader").show();
                    axios.get('/banking/pos/daily-records', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.records = response.data.records;
                        this.totals = response.data.totals;
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
                fetchBalance() {
                    axios.get('/banking/pos/opening-balance', {
                        params: this.getFilters()
                    }).then(response => {
                        this.balance = response.data.balance;
                    }).catch(error => {});
                },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
