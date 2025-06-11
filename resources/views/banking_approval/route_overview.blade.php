@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.branches = {!! $branches !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Route Banking Overview </h3>
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
                                <button class="btn btn-primary" @click="fetchRecords"><i
                                        class="fas fa-search btn-icon"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="records-tablee">
                        <thead>
                            <tr>
                                <th> Date </th>
                                <th style="text-align: right;"> Y Sales </th>
                                <th style="text-align: right;"> RTNS </th>
                                <th style="text-align: right;"> EAZZY</th>
                                <th style="text-align: right;"> EQUITY MAIN</th>
                                <th style="text-align: right;"> VOOMA </th>
                                <th style="text-align: right;"> KCB MAIN </th>
                                <th style="text-align: right;"> MPESA </th>
                                <th style="text-align: right;"> TOTAL RCTS </th>
                                <th style="text-align: right;"> VERIFIED </th>
                                <th style="text-align: right;"> FRAUD </th>
                                <th style="text-align: right;"> UNVERIFIED </th>
                                <th style="text-align: right;"> Y SALES VAR </th>
                                <th style="text-align: right;"> R BAL </th>
                                <th style="text-align: right;"> STATUS </th>
                                <th style="width: 3%;"></th>
                            </tr>
                        </thead>
    
                        <tbody v-cloak>
                            <tr v-for="(record, index) in records" :key="index">
                                <td> @{{ record.date }}</td>
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
                                <td> @{{ record.status }}</td>
                                <td style="width: 3%; text-align: center;">
                                    <a :href="`/banking/route/details?date=${record.date}&branch=${record.branch}`" title="View Details"><i class="fas fa-eye"></i></a>
                                </td>
                            </tr>
                        </tbody>
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
                    records: []
                }
            },

            mounted() {
                $("body").addClass('sidebar-collapse');

                $("#branch_id").val(10);
                $(".mlselect").select2();

                let toDate = dayjs().format('YYYY-MM-DD');
                // let fromDate = dayjs().subtract(7, 'day').format('YYYY-MM-DD');
                $("#from_date").val(toDate);
                $("#to_date").val(toDate);

                this.fetchRecords();
            },

            computed: {
                branches() {
                    return window.branches
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
                        to_date: $("#to_date").val(),
                        from_date: $("#from_date").val(),
                        branch_id: $("#branch_id").val(),
                        status: $("#status").val()
                    }
                },

                fetchRecords() {
                    $(".btn-loader").show();
                    axios.get('/banking/route/overview/records', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.records = response.data;

                        // setTimeout(() => {
                        //     this.initDataTable();
                        // }, 500)
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                getColumnTotal(column) {
                    return this.records.reduce((partialSum, record) => partialSum + record[column], 0);
                },

                initDataTable() {
                    this.table?.destroy();
                    this.table = $('#records-table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 100,
                        'initComplete': function(settings, json) {
                            let info = this.api().page.info();
                            let total_record = info.recordsTotal;
                            if (total_record < 101) {
                                $('.dataTables_paginate').hide();
                            }
                        },
                        'aoColumnDefs': [{
                            'bSortable': false,
                            'aTargets': 'noneedtoshort'
                        }],
                    });
                },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
