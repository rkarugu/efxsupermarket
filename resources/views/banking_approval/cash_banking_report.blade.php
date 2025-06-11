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
                <div class="box-header-flex">
                    <h3 class="box-title"> Cash Banking Report </h3>
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
                            <label for="date" class="control-label"> Date </label>
                            <input type="date" name="date" id="date" class="form-control">
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

                <table class="table table-hover table-bordered" id="records-tablee">
                    <thead>
                        <tr>
                            <th> Cashier</th>
                            <th> Cashier Type </th>
                            <th style="text-align: right;"> CS</th>
                            <th style="text-align: right;"> CSR</th>
                            <th style="text-align: right;"> TSR</th>
                            <th style="text-align: right;"> INV</th>
                            <th style="text-align: right;"> CRN</th>
                            <th style="text-align: right;"> NS</th>
                            {{-- <th style="text-align: right;"> CASH </th> --}}
                            <th style="text-align: right;"> PDQ </th>
                            <th style="text-align: right;"> CHQ </th>
                            <th style="text-align: right;"> DRP </th>
                            <th style="text-align: right;"> EC </th>
                        </tr>
                    </thead>

                    <tbody v-cloak>
                        <tr v-for="(record, index) in records" :key="index">
                            <td> @{{ record.cashier }}</td>
                            <td> @{{ record.cashier_type }}</td>
                            <td style="text-align: right;"> @{{ record.fcs }}</td>
                            <td style="text-align: right;"> @{{ record.fcsr }}</td>
                            <td style="text-align: right;"> @{{ record.ftsr }}</td>
                            <td style="text-align: right;"> @{{ record.finv }}</td>
                            <td style="text-align: right;"> @{{ record.fcrn }}</td>
                            <td style="text-align: right;"> @{{ record.fns }}</td>
                            {{-- <td style="text-align: right;"> @{{ record.fcp }}</td> --}}
                            <td style="text-align: right;"> @{{ record.fdb }}</td>
                            <td style="text-align: right;"> @{{ record.fchq }}</td>
                            <td style="text-align: right;"> @{{ record.fdrp }}</td>
                            <td style="text-align: right;"> @{{ record.fec }}</td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr v-cloak>
                            <th colspan="2"> @{{ totalsColumn.cashier_type }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.cs }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.csr }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.tsr }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.inv }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.crn }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.ns }}</th>
                            {{-- <th style="text-align: right;"> @{{ totalsColumn.cp }}</th> --}}
                            <th style="text-align: right;"> @{{ totalsColumn.db }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.chq }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.drp }}</th>
                            <th style="text-align: right;"> @{{ totalsColumn.ec }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title"> Chief Cashier Report </h3>
            </div>

            <div class="box-body">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="text-align: right;"> Expected Cash B/f </th>
                            <th style="text-align: right;"> Cash Banked </th>
                            <th style="text-align: right;"> Variance </th>
                            <th style="text-align: right;"> Initiated Drops </th>
                            <th style="text-align: right;"> CDM Deposits </th>
                            <th style="text-align: right;"> Variance (Drops - CDM) </th>
                            <th style="text-align: right;"> CRD </th>
                            <th style="text-align: right;"> Banked CRD </th>
                            <th style="text-align: right;"> Variance </th>
                            <th style="text-align: right;"> Tablet Returns </th>
                            <th style="text-align: right;"> Expected Cash Banking </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-cloak>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.bf }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.banked_cash }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.cash_variance }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.drops }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.cdms }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.variance }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.crd }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.bcrd }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.crd_variance }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.tsr }} </th>
                            <th style="text-align: right;"> @{{ chiefCashierSummary.ecb }} </th>
                        </tr>
                    </tbody>
                </table>
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
                    totalsColumn: [],
                    chiefCashierSummary: {},
                }
            },

            mounted() {
                $("body").addClass('sidebar-collapse');

                $("#branch_id").val(10);
                if (!this.user.is_hq_user) {
                    $("#branch_id").val(this.user.restaurant_id);
                    $("#branch_id").attr('disabled', true);
                }

                $(".mlselect").select2();

                let date = dayjs().format('YYYY-MM-DD');
                $("#date").val(date);

                this.fetchRecords();
            },

            computed: {
                branches() {
                    return window.branches
                },

                user() {
                    return window.user
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                getFilters() {
                    return {
                        date: $("#date").val(),
                        branch_id: $("#branch_id").val()
                    }
                },

                fetchRecords() {
                    $(".btn-loader").show();
                    axios.get('/cashier-management/cash-banking-report/generate', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.records = response.data;
                        this.totalsColumn = this.records.pop();

                        this.fetchChiefCashierRecords();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchChiefCashierRecords() {
                    $(".btn-loader").show();
                    axios.get('/cashier-management/cash-banking-report/generate-chief-cashier', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.chiefCashierSummary = response.data;
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                getColumnTotal(column) {
                    let total = this.records.reduce((partialSum, record) => partialSum + record[column], 0);
                    return parseFloat(total).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                    // let total = 0;
                    // this.records.forEach((record) => {
                    //     total += record[column];
                    // })

                    // return parseFloat(total).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
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
