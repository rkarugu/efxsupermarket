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
                    <h3 class="box-title"> Fuel Approval </h3>
                </div>
            </div>

            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group col-md-3">
                            <label for="branch_id" class="control-label"> Branch </label>
                            <select id="branch_id" class="form-control mlselect" required>
                                <option value="" disabled selected>Select a branch</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-primary"><i class="fas fa-search btn-icon"></i> Search</button>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <table class="table table-hover table-bordered" id="records-table">
                    <thead>
                    <tr>
                        <th style="width: 3%;">#</th>
                        <th> Date</th>
                        <th> Branch</th>
                        <th style="text-align: right;"> Expected</th>
                        <th style="text-align: right;"> Fueled</th>
                        <th style="text-align: right;"> Direct Matches</th>
                        <th style="text-align: right;"> Unknown Resolved</th>
                        <th style="text-align: right;"> Unknown Pending</th>
                        <th style="text-align: right;"> Fuel Total</th>
                        <th style="width: 3%;">Action</th>
                    </tr>
                    </thead>

                    <tbody v-cloak>
                    <tr v-for="(record, index) in records" :key="index" :style="{backgroundColor: (record.unknown_entries_pending > 0) ? '#d0abab': 'white' }">
                        <th style="width: 3%;" scope="row"> @{{ index + 1 }}</th>
                        <td> @{{ record.date }}</td>
                        <td> @{{ record.branch }}</td>
                        <td style="text-align: right;"> @{{ record.expected }}</td>
                        <td style="text-align: right;"> @{{ record.statements }}</td>
                        <td style="text-align: right;"> @{{ record.direct_matches }}</td>
                        <td style="text-align: right;"> @{{ record.unknown_entries }}</td>
                        <td style="text-align: right;"> @{{ record.unknown_entries_pending }}</td>
                        <td style="text-align: right;"> @{{ record.net_total }}</td>
                        <td style="width: 3%; text-align: center;">
                            <a :href="`/admin/fuel-approval/show?date=${record.date}`" title="View Details"><i class="fas fa-eye"></i></a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <span class="btn-loader" style="display:none;">
        <img src="<?= asset('/assets/admin/images/loader.gif') ?>" alt="Loader"/>
    </span>
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
                    records: []
                }
            },

            mounted() {
                $(".mlselect").select2();

                $("body").addClass('sidebar-collapse');
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
                this.fetchRecords();
            },

            methods: {
                fetchRecords() {
                    $(".btn-loader").show();
                    axios.get('{{ route("fuel-approval.records") }}').then(response => {
                        $(".btn-loader").hide();
                        this.records = response.data;

                        setTimeout(() => {
                            this.initDataTable();
                        }, 500)
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                getColumnTotal(column) {
                    return this.records.reduce((partialSum, record) => partialSum + record[column], 0);
                },

                initDataTable() {
                    $('#records-table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 100,
                        'initComplete': function (settings, json) {
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