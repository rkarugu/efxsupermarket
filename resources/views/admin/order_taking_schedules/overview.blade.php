@extends('layouts.admin.admin')

@section('content')
    @php
        $user = getLoggeduserProfile();
    @endphp

    <script>
        window.user = {!! $user !!};
        window.logs = {!! $logs !!};
    </script>

    <section class="content" id="order-taking-overview">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="box-title"> Order Taking Schedule</h3>

                    <div>
                        <span class="box-title">
                            <i class="fa fa-calendar" style="display: inline-block; margin-right: 5px;"></i> {{ \Carbon\Carbon::now()->toFormattedDayDateString() }}
                        </span>
                    </div>

                    <div>
                        <select name="branch_id" id="branch_id" class="form-control" v-cloak>
                            <option v-for="branch in allBranches" :key="branch.id" :value="branch.id"> @{{ branch.name }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div id="top-cards" class="d-flex justify-content-between">
                    <div class="major-detail d-flex flex-column justify-content-between border-info">
                        <div class="d-flex">
                            <i class="fas fa-route major-detail-icon"></i>
                            <span class="major-detail-title"> Scheduled Shifts </span>
                        </div>

                        <span class="major-detail-value" v-cloak> @{{ summary.scheduled_shifts }} </span>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-primary">
                        <div class="d-flex">
                            <i class="fas fa-user-clock major-detail-icon"></i>
                            <span class="major-detail-title"> Active Shifts </span>
                        </div>

                        <span class="major-detail-value" v-cloak> @{{ summary.active_shifts }} </span>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-danger">
                        <div class="d-flex">
                            <i class="fas fa-user-times major-detail-icon"></i>
                            <span class="major-detail-title"> Pending Shifts </span>
                        </div>

                        <span class="major-detail-value" v-cloak> @{{ summary.pending_shifts }} </span>
                    </div>

                    <div class="major-detail d-flex flex-column justify-content-between border-success">
                        <div class="d-flex">
                            <i class="fas fa-user-check major-detail-icon"></i>
                            <span class="major-detail-title"> Completed Shifts </span>
                        </div>

                        <span class="major-detail-value" v-cloak> @{{ summary.closed_shifts }} </span>
                    </div>
                </div>

                <div class="mt-20 d-flex justify-content-between w-100">
{{--                    <div id="activity" class="overview-card">--}}
{{--                        <div class="box">--}}
{{--                            <div class="box-header with-border">--}}
{{--                                <h3 class="box-title"> Activity </h3>--}}
{{--                            </div>--}}

{{--                            <div class="box-body">--}}
{{--                                <ul v-if="logs.length > 0">--}}
{{--                                    <li v-for="(log, index) in logs" :key="index" v-cloak>--}}
{{--                                        <div class="d-flex justify-content-between">--}}
{{--                                            <span> @{{ log.user_name }} </span>--}}
{{--                                            <span> @{{ log.created_at }} </span>--}}
{{--                                        </div>--}}
{{--                                        <span> @{{ log.activity }} </span>--}}
{{--                                    </li>--}}
{{--                                </ul>--}}

{{--                                <p v-else> No activity in the routes yet. </p>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}

                    <div id="targets" class="overview-card flex-grow-1">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"> Targets vs Actuals </h3>
                            </div>

                            <div class="box-body">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Target Type</th>
                                        <th>Target Value</th>
                                        <th>Actual Value</th>
                                        <th>Variance</th>
                                        <th>Performance</th>
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(row, index) in targetsData" :key="index" v-cloak>
                                        <th scope="row">@{{ index + 1 }}</th>
                                        <td> @{{ row.type }}</td>
                                        <td> @{{ row.value }}</td>
                                        <td> @{{ row.actual }}</td>
                                        <td> @{{ row.variance }}</td>
                                        <td> @{{ row.performance }}%</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-20 w-100">
                    <div class="box">
                        <div class="box-header with-border">
                            <h3 class="box-title"> Schedule List </h3>
                        </div>

                        <div class="box-body">
                            <div class="table-responsive">
                                <table class="table" id="schedule-list">
                                    <thead>
                                    <tr>
                                        <th style="width: 3%;">#</th>
                                        <th> Route </th>
                                        <th> Customers </th>
                                        <th> Status </th>
                                        <th> Start Time </th>
                                        {{-- <th> Punctuality </th> --}}
                                        <th> Close Time </th>
                                        <th> Shift Total </th>
                                        {{-- <th> Actions </th> --}}
                                    </tr>
                                    </thead>

                                    <tbody>
                                    <tr v-for="(shift, index) in list" :key="shift.id" v-cloak>
                                        <th scope="row" style="width: 3%;"> @{{ index + 1 }} </th>
                                        <td> @{{ shift.route_salesman }} </td>
                                        <td> @{{ shift.shift_customers_count }} </td>
                                        <td> @{{ shift.display_status }} </td>
                                        <td> @{{ shift.starting_time }} </td>
                                        {{-- <td> @{{ shift.punctuality }} </td> --}}
                                        <td> @{{ shift.ctime }} </td>
                                        <td> @{{ shift.formatted_shift_total }} </td>
                                        {{-- <td>  </td> --}}
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        .major-detail {
            border: 2px solid;
            border-radius: 15px;
            padding: 10px 15px;
            height: 80px;
            flex-grow: 1 !important;
            margin-right: 20px;
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

        .major-detail.border-info {
            border-color: #0dcaf0;
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
    <script src="{{asset('js/sweetalert.js')}}"></script>
    <script src="{{asset('js/form.js')}}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script type="importmap">
        {
          "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
          }
        }
    </script>
    <script type="module">
        import {createApp} from 'vue';

        const app = createApp({
            data() {
                return {
                    selectedBranchId: 0,
                    branches: [],
                    summary: {},
                    targetsData: [],
                    list: []
                }
            },

            created() {
                this.fetchBranches()
                this.fetchScheduleSummary()
                this.fetchTargets()
                this.fetchList()
            },


            mounted() {
                $("#branch_id").select2();
                $("#branch_id").change(() => {
                    this.selectedBranchId = $("#branch_id").val();

                    this.fetchScheduleSummary()
                    this.fetchTargets()
                });

                if (this.currentUser.role_id !== 4) {
                    this.selectedBranchId = this.currentUser.restaurant_id;
                }
            },

            computed: {
                currentUser() {
                    return window.user
                },

                logs() {
                    return window.logs
                },


                toaster() {
                    return new Form();
                },

                allBranches() {
                    if (this.currentUser.role_id === 1) {
                        this.branches.push({
                            id: 0,
                            'name': 'All Branches'
                        })
                    }

                    return this.branches
                }
            },

            methods: {
                fetchBranches() {
                    axios.get('/api/branches').then(res => {
                        this.branches = res.data.data
                    }).catch(() => {
                    })
                },

                fetchScheduleSummary() {
                    axios.get('/api/order-taking-schedule/get-summary', {
                        params: {
                            branch_id: this.selectedBranchId
                        }
                    }).then(res => {
                        this.summary = res.data.data
                    }).catch((error) => {
                        let message = error.response ? error.response.message : error
                        this.toaster.errorMessage(message)
                    })
                },

                fetchTargets() {
                    axios.get('/api/order-taking-schedule/get-targets-vs-actuals', {
                        params: {
                            branch_id: this.selectedBranchId
                        }
                    }).then(res => {
                        this.targetsData = res.data.data
                    }).catch((error) => {
                        let message = error.response ? error.response.message : error
                        this.toaster.errorMessage(message)
                    })
                },

                fetchList() {
                    axios.get('/api/order-taking-schedule/get-list', {
                        params: {
                            branch_id: this.selectedBranchId
                        }
                    }).then(res => {
                        this.list = res.data.data

                        setTimeout(() => {
                            if (this.table) {
                                this.table.destroy();
                            }

                            this.table = $('#schedule-list').DataTable({
                                'paging': true,
                                'lengthChange': true,
                                'searching': true,
                                'ordering': true,
                                'info': true,
                                'autoWidth': false,
                                'pageLength': 10,
                                'initComplete': function (settings, json) {
                                    let info = this.api().page.info();
                                    let total_record = info.recordsTotal;
                                    if (total_record < 11) {
                                        $('.dataTables_paginate').hide();
                                    }
                                },
                                'aoColumnDefs': [{
                                    'bSortable': false,
                                    'aTargets': 'noneedtoshort'
                                }],
                                //"aaSorting": [ [0,'desc'] ]

                            });
                        }, 50)
                    }).catch((error) => {
                        let message = error.response ? error.response.message : error
                        this.toaster.errorMessage(message)
                    })
                },
            },
        })

        app.mount('#order-taking-overview')
    </script>
@endsection
