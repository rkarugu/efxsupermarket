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
                    <h3 class="box-title"> Debtors Report </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-3">
                            <label for="branch_id" class="control-label"> Branch </label>
                            <select id="branch_id" class="form-control mlselect" required>
                                <option value="" disabled selected>Select a branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="from_date" class="control-label"> From Date </label>
                            <input type="date" name="from_date" id="from_date" class="form-control">
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-primary" @click="generateReport"><i class="fas fa-search btn-icon"></i> Search</button>
                                <button class="btn btn-primary ml-12" @click="generateReportPdf"><i class="fas fa-file-pdf btn-icon"></i> PDF </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <table class="table table-hover table-bordered" id="report-table">
                            <thead>
                                <tr>
                                    <th style="width: 3%;"> # </th>
                                    <th> Account </th>
                                    <th style="text-align: right;"> Opening Balance </th>
                                    <th style="text-align: right;"> Y Sales </th>
                                    <th style="text-align: right;"> Total Balance </th>
                                    <th style="text-align: right;"> Collections </th>
                                    <th style="text-align: right;"> Returns </th>
                                    <th style="text-align: right;"> Total Collections </th>
                                    <th style="text-align: right;"> Discount Returns </th>
                                    <th style="text-align: right;"> Fraud </th>
                                    <th style="text-align: right;"> Closing Balance </th>
                                </tr>
                            </thead>
        
                            <tbody v-cloak>
                                <tr v-for="(record, index) in report" :key="index" v-cloak>
                                    <th style="width: 3%;"> @{{ index + 1 }} </th>
                                    <td> @{{ record.account_name }}</td>
                                    <td style="text-align: right;"> @{{ record.bf }}</td>
                                    <td style="text-align: right;"> @{{ record.ysales }}</td>
                                    <td style="text-align: right;"> @{{ record.total_balance }}</td>
                                    <td style="text-align: right;"> @{{ record.payments }}</td>
                                    <td style="text-align: right;"> @{{ record.rtns }}</td>
                                    <td style="text-align: right;"> @{{ record.today_credits }}</td>
                                    <td style="text-align: right;"> @{{ record.discount_returns }}</td>
                                    <td style="text-align: right;"> @{{ record.fraud }}</td>                                
                                    <td style="text-align: right;"> @{{ record.cf }}</td>
                                </tr>
                            </tbody>

                            <tfoot>
                                <tr v-cloak>
                                    <th colspan="2" style="text-align: center;"> TOTALS </th>
                                    <th style="text-align: right;"> @{{ totals.bf }}</th>
                                    <th style="text-align: right;"> @{{ totals.ysales }}</th>
                                    <th style="text-align: right;"> @{{ totals.total_balance }}</th>
                                    <th style="text-align: right;"> @{{ totals.payments }}</th>
                                    <th style="text-align: right;"> @{{ totals.rtns }}</th>
                                    <th style="text-align: right;"> @{{ totals.today_credits }}</th>
                                    <th style="text-align: right;"> @{{ totals.discount_returns }}</th>                              
                                    <th style="text-align: right;"> @{{ totals.fraud }}</th>                                
                                    <th style="text-align: right;"> @{{ totals.cf }}</th>
                                </tr>
                            </tfoot>
                        </table>
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
                    report: [],
                    totals: {}
                }
            },

            mounted() {
                $("body").addClass('sidebar-collapse');

                $("#branch_id").val(10);
                $(".mlselect").select2();

                let toDate = dayjs().format('YYYY-MM-DD');
                $("#from_date").val(toDate);

                this.generateReport();
            },

            computed: {
                branches() {
                    return window.branch
                },

                toaster() {
                    return new Form();
                },
            },

            methods: {
                getFilters() {
                    return {
                        from_date: $("#from_date").val(),
                        branch_id: $("#branch_id").val(),
                    }
                },

                generateReport() {
                    $(".btn-loader").show();
                    axios.get('/sales-and-receivables/reports/debtors-report/generate', {
                        params: this.getFilters()
                    }).then(response => {
                        $(".btn-loader").hide();
                        this.report = response.data;
                        this.totals = this.report.pop();

                        setTimeout(() => {
                            this.table?.destroy();
                            this.table = $('#report-table').DataTable({
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
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                
                generateReportPdf() {
                    let from_date = $("#from_date").val();
                    let branch_id = $("#branch_id").val();
                    let url = `/sales-and-receivables/reports/debtors-report/generate?from_date=${from_date}&branch_id=${branch_id}&intent=pdf`;
                    window.location.assign(url);
                },
            },
        })

        app.mount('#vue-mount')
    </script>
@endsection
