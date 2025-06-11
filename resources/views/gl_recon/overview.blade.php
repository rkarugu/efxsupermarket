@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branches = {!! $branches !!};
        window.channels = {!! $channels !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> GL Reconciliation </h3>

                    <div class=d-flex>
                        <button class="btn btn-primary" @click="promptUpdateOpeningBalances">
                            <i class="fas fa-money-check-dollar btn-icon"></i> Opening Balances
                        </button>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-2">
                            <label for="from_date" class="control-label"> Recon Date </label>
                            <input type="date" name="from_date" id="from_date" class="form-control">
                        </div>

                        <div class="form-group col-md-2">
                            <label for="branch_id" class="control-label"> Branch </label>
                            <select id="branch_id" class="form-control mlselect" required>
                                <option value="" disabled selected>Select a branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="channel" class="control-label"> Channel </label>
                            <select id="channel" class="form-control mlselect" required>
                                <option value="0" selected>All</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->title }}"> {{ $channel->title }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label for="status" class="control-label"> Status </label>
                            <select id="status" class="form-control mlselect" required>
                                <option value="all" selected> All </option>
                                <option value="pending"> Pending </option>
                                <option value="closed"> Closed </option>
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-primary" @click="fetchRecords"><i class="fas fa-search btn-icon"></i>
                                    Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <table class="table table-hover table-bordered" id="records-tablee">
                    <thead>
                        <tr>
                            <th> Date </th>
                            <th> Account </th>
                            <th style="text-align: right;"> Opening Balance </th>
                            <th style="text-align: right;"> Bank Debits</th>
                            <th style="text-align: right;"> System Debits</th>
                            <th style="text-align: right;"> Variance </th>
                            <th style="text-align: right;"> Bank Credits </th>
                            <th style="text-align: right;"> System Credits </th>
                            <th style="text-align: right;"> Variance </th>
                            <th style="text-align: right;"> Closing Balance </th>
                            <th style="width: 3%;"></th>
                        </tr>
                    </thead>

                    <tbody v-cloak>
                        <tr v-for="(record, index) in records" :key="index">
                            <td> @{{ record.date }}</td>
                            <td> @{{ record.channel }}</td>
                            <td style="text-align: right;"> @{{ record.opening_balance }}</td>
                            <td style="text-align: right;"> @{{ record.bank_debits }}</td>
                            <td style="text-align: right;"> @{{ record.system_debits }}</td>
                            <td style="text-align: right;"> @{{ record.debits_variance }}</td>
                            <td style="text-align: right;"> @{{ record.bank_credits }}</td>
                            <td style="text-align: right;"> @{{ record.system_credits }}</td>
                            <td style="text-align: right;"> @{{ record.credits_variance }}</td>
                            <td style="text-align: right;"> @{{ record.closing_balance }}</td>
                            <td style="width: 3%; text-align: center;">
                                <a :href="`/banking/route/details?date=${record.date}&branch=${record.branch}`"
                                    title="View Details"><i class="fas fa-eye"></i></a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="opening-balances-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Bank Opening Balances </h3>
                    </div>

                    <div class="box-body">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th> Bank Account </th>
                                    <th> Opening Balance </th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="account in openingBalances" :key="account.id">
                                    <td v-cloak> @{{ account.account_name }} </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="account.opening_balance">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="updateOpeningBalances">Post</button>
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
                    openingBalances: []
                }
            },

            mounted() {
                $("body").addClass('sidebar-collapse');

                $("#branch_id").val(10);
                $(".mlselect").select2();

                let today = dayjs().format('YYYY-MM-DD');
                let fromDate = dayjs().subtract(1, 'day').format('YYYY-MM-DD');
                $("#from_date").val(fromDate);

                this.fetchRecords();
                this.fetchROpeningBalances();
            },

            computed: {
                branches() {
                    return window.branches
                },

                channels() {
                    return window.channels
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
                        from_date: $("#from_date").val(),
                        branch_id: $("#branch_id").val(),
                        channel: $("#channel").val(),
                        status: $("#status").val()
                    }
                },

                fetchRecords() {
                    $(".btn-loader").show();
                    axios.get('/banking/gl-recon/overview/records', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.records = response.data;
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchROpeningBalances() {
                    axios.get('/banking/gl-recon/opening-balances', {
                        params: this.getFilters()
                    }).then(response => {
                        this.openingBalances = response.data;
                    }).catch(error => {
                        //
                    });
                },

                promptUpdateOpeningBalances() {
                    $("#opening-balances-modal").modal("show");
                },

                updateOpeningBalances() {
                    $(".btn-loader").show();
                    axios.post('/banking/gl-recon/opening-balances/update', {
                        balances: JSON.stringify(this.openingBalances)
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.fetchROpeningBalances();

                        $("#opening-balances-modal").modal("hide");
                        this.toaster.successMessage('Opening balances updated succesfully');
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
