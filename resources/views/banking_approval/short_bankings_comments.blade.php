@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branch_id = {!! $branch->id !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> {{ $branch->name . ' Short Banking Overview' }} </h3>
                    <div>
                        <a href="{{ url()->previous() }}" class="btn btn-success btn-sm"> <i class="fas fa-arrow-left"></i> Back </a>

                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="col-md-12 table-responsive">


                    <table class="table table-hover table-bordered mt-10 " id="records-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th> Date </th>
                                <th style="text-align: right;"> Balance </th>
                                <th style="text-align: right;"> Accounted Short Bankings </th>
                                <th style="text-align: right;"> Variance </th>

                            </tr>
                        </thead>

                        <tbody v-cloak>
                            <template v-for="(record, index) in records" :key="index">
                                <tr>
                                    <td>

                                        <i @click="toggleDetails(index, record.date)"
                                            :class="detailsIndex === index ? 'fas fa-minus-circle ' : 'fas fa-plus-circle '"
                                            style="cursor: pointer; font-size: 1.2rem;">
                                        </i>

                                    </td>
                                    <td> @{{ record.date }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_balance }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_short_bankings }} </td>
                                    <td style="text-align: right;"> @{{ record.formatted_balance_variance }} </td>

                                </tr>
                                <tr v-if="detailsIndex === index">
                                    <td colspan="5">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Comment By</th>
                                                        <th>Type</th>
                                                        <th>Comment</th>
                                                        <th style="text-align: right;">Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(detail, detailIndex) in breakdown" :key="detail.id">
                                                        <th>@{{ detailIndex + 1 }}</th> 
                                                        <td>@{{ detail.name }}</td>
                                                        <td>@{{ detail.type }}</td>
                                                        <td>@{{ detail.comment }}</td>
                                                        <td style="text-align: right;">@{{ formatNumber(detail.amount) }}</td>

                                                    </tr>
                                                    <tr>
                                                        <th colspan="4"> TOTALS </th>
                                                        <th style="text-align: right;"> @{{ formatNumber(breakdownTotal) }} </th>
                        
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="2"> TOTALS </th>
                                <th style="text-align: right;"> @{{ totals.balance }} </th>
                                <th style="text-align: right;"> @{{ totals.total_short_bankings }} </th>
                                <th style="text-align: right;"> @{{ totals.balance_variance }} </th>

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
                    detailsIndex: null,
                    breakdown: [],
                    breakdownTotal:0,


                }
            },

            mounted() {


                this.fetchRecords();
            },

            computed: {

                toaster() {
                    return new Form();
                },

            },

            methods: {
                getFilters() {
                    return {
                        branch_id: branch_id,
                    }
                },


                formatNumber(value) {
                    return Number(value).toLocaleString('en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    });
                },

                fetchRecords() {
                    $(".btn-loader").show();
                    axios.get('/banking/pos/daily-records/balances', {
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
                fetchBreakdown(date) {
                    return axios.get('/banking/pos/daily-short-bankings-breakdown', {
                        params: {
                            branch_id: branch_id,
                            date: date,
                        },
                    });
                },
                toggleDetails(index, date) {
                    if (this.detailsIndex === index) {
                        this.detailsIndex = null;
                        this.breakdown = [];
                    } else {
                        this.detailsIndex = null;
                        this.breakdown = [];

                        this.fetchBreakdown(date)
                            .then((response) => {
                                this.detailsIndex = index;
                                this.breakdown = response.data.breakdown;
                                this.breakdownTotal = response.data.breakdownTotal;
                            })
                            .catch((error) => {
                                this.toaster.errorMessage(error.response?.data?.message ?? error.response
                                ?.data);
                            });
                    }
                },

            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
