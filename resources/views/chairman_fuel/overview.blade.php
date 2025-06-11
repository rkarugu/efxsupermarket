@php use Carbon\Carbon; @endphp
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
                                <option value="0" selected>All</option>
                                @foreach($branches as $branch)
                                    <option value="{{ $branch->id }}"> {{ $branch->name }} </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-3">
                            <label for="fueling_date" class="control-label">Fueling Date </label>
                            <input type="date" id="fueling_date" class="form-control" value="{{ Carbon::yesterday()->toDateString() }}">
                        </div>

                        <div class="form-group col-md-2">
                            <label class="control-label">&nbsp; </label>
                            <div class="d-flex">
                                <button class="btn btn-primary" @click="refresh"><i class="fas fa-magnifying-glass"></i> Filter</button>
                                <button class="btn btn-primary ml-12" @click="clearFilters"><i class="fas fa-xmark"></i> Clear</button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3" style="border-left: 1px solid rgba(0, 0, 0, .125);">
                        <h3 class="box-title" style="margin: 0;"> Total Fuel Savings </h3>
                        <span style="font-weight: 800; font-size: 35px;" v-cloak> @{{ fuelSavings }} </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" v-cloak>
            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-blue">
                    <div class="inner">
                        <h3> @{{ summary.sales }} </h3>
                        <p> TOTAL SALES </p>
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
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3> @{{ summary.fuel }} </h3>
                        <p>FUEL TOTAL</p>
                    </div>

                    <div class="icon">
                        <i class="fas fa-gas-pump"></i>
                    </div>

                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" class="small-box-footer">
                        View All <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-lg-3 col-xs-6">
                <div class="small-box bg-red">
                    <div class="inner">
                        <h3> @{{ summary.petty_cash }} </h3>
                        <p>PETTY CASH</p>
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
                <div class="small-box bg-green">
                    <div class="inner">
                        <h3> @{{ summary.profit }} </h3>
                        <p>PROFITABILITY</p>
                    </div>

                    <div class="icon">
                        <i class="fa fa-fw fa-arrow-trend-up"></i>
                    </div>

                    <a href="{{ route('suppliers-overview.suppliers-list') }}" target="_blank" class="small-box-footer">
                        View All <i class="fa fa-arrow-circle-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                <ul class="nav nav-tabs" id="data-tabs">
                    <li class="active"><a href="#verified" data-toggle="tab"> Verified Entries </a></li>
                    {{--                    <li><a href="#above-standard" data-toggle="tab">Above Standard</a></li>--}}
                    <li><a href="#savings" data-toggle="tab">Fuel Savings</a></li>
                    <li><a href="#profitability" data-toggle="tab">Profitability Report</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="verified">
                        <div class="box-body">
                            <table class="table table-bordered table-hover data-tables" id="verified-entries-table" v-if="!verifiedLoading">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Fueling Date</th>
                                    <th>Shift Date</th>
                                    <th>LPO #</th>
                                    <th style="width: 150px;">Route</th>
                                    <th>Vehicle</th>
                                    <th>Receipt #</th>
                                    <th>Tonnage</th>
                                    <th>Standard Fuel</th>
                                    <th>Actual Fuel</th>
                                    <th>Variance</th>
                                    <th style="text-align: right;">Fuel Total</th>
                                    <th style="width: 3%;"><input type="checkbox" checked></th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(entry, index) in verifiedEntries" :key="index" :style="{backgroundColor: (entry.actual_fuel_quantity > entry.manual_fuel_estimate) ? 'red': 'white' }">
                                    <th style="width: 3%;"><i class="fas fa-circle-chevron-down" style="cursor:pointer;"></i></th>
                                    <td> @{{ entry.fueling_date }}</td>
                                    <td> @{{ entry.shift_date }}</td>
                                    <td> @{{ entry.lpo_number }}</td>
                                    <td style="width: 150px;"> @{{ entry.route ?? entry.comments }}</td>
                                    <td> @{{ entry.vehicle }} (@{{ entry.driver }})</td>
                                    <td> @{{ entry.receipt_number }}</td>
                                    <td> @{{ entry.tonnage }}</td>
                                    <td> @{{ entry.manual_fuel_estimate }}L</td>
                                    <td> @{{ entry.actual_fuel_quantity }}L</td>
                                    <td> @{{ entry.actual_fuel_quantity - entry.manual_fuel_estimate }}L</td>
                                    <td style="text-align: right;">@{{ entry.total }}</td>
                                    <td>
                                        <input type="checkbox" v-model="entry.selected">
                                    </td>
                                </tr>
                                </tbody>
                            </table>

                            <p v-else> Loading data, please wait... </p>

                            <div style="margin-top: 20px;" class="d-flex justify-content-end" v-if="!verifiedLoading">
                                <button class="btn btn-primary"><i class="fas fa-list-check btn-icon"></i> Approve Entries</button>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="savings">
                        <div class="box-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th>Shift Type</th>
                                    <th>Tonnage</th>
                                    <th>Dashboard Fuel</th>
                                    <th>Actual Fuel</th>
                                    <th>Fuel Saved</th>
                                    <th style="text-align: right;"> Saved Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $fuelEntries = collect(); @endphp
                                @foreach ($fuelEntries as $entry)
                                    <tr>
                                        <th style="width: 3%;"></th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <th style="text-align: right;"></th>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="profitability">
                        <div class="box-body">
                            <table class="table table-bordered table-hover" id="create_datatable_50">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Date</th>
                                    <th>Vehicle</th>
                                    <th style="text-align: right;">Sales</th>
                                    <th>Tonnage</th>
                                    <th style="text-align: right;">Fuel Total</th>
                                    <th style="text-align: right;">Travel Expense</th>
                                    <th style="text-align: right;">Profitability</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $fuelEntries = collect(); @endphp
                                @foreach ($fuelEntries as $entry)
                                    <tr>
                                        <th style="width: 3%;"></th>
                                        <td></td>
                                        <td></td>
                                        <td style="text-align: right;"></td>
                                        <td></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                        <td style="text-align: right;"></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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
                    fuelSavings: 0.00,
                    profitability: 0.00,
                    summary: {
                        sales: 0.00,
                        petty_cash: 0.00,
                        fuel: 0.00,
                        profit: 0.00
                    },
                    verifiedEntries: [],
                    verifiedLoading: true
                }
            },

            mounted() {
                $(".mlselect").select2();

                $('body').addClass('sidebar-collapse');
                this.refresh();
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
                        fueling_date: $('#fueling_date').val()
                    }
                },

                clearFilters() {
                    $('#branch_id').val(null);
                    $('#start_date').val(null);
                    $('#end_date').val(null);

                    this.refresh();
                },

                refresh() {
                    this.fetchSavings();
                    this.fetchSummary();
                    this.fetchVerified();
                },

                fetchSavings() {
                    axios.get('{{ route("fuel-entry-confirmation.savings") }}', {params: this.getFilters()})
                        .then(response => {
                            this.fuelSavings = response.data.data;
                        })
                        .catch(error => {
                            // this.toaster.error(error);
                        });
                },

                fetchSummary() {
                    axios.get('{{ route("fuel-entry-confirmation.summary") }}', {params: this.getFilters()})
                        .then(response => {
                            this.summary = response.data.data;
                        })
                        .catch(error => {
                            // this.toaster.errorMessage(error.response.data?.message);
                        });
                },

                fetchVerified() {
                    this.verifiedLoading = true;
                    axios.get('{{ route("fuel-entry-confirmation.verified") }}', {
                        params: {
                            fueling_date: $("#fueling_date").val()
                        }
                    }).then(response => {
                        this.verifiedEntries = response.data;
                        this.verifiedLoading = false;
                        // setTimeout(() => {
                        //     this.initDataTables();
                        // }, 2000);
                    }).catch(error => {
                        this.verifiedLoading = false;
                        console.log(error);
                        this.toaster.errorMessage(error.response.data?.message);
                    });
                },

                initDataTables() {
                    $('.data-tables').DataTable()?.destroy();

                    $('.data-tables').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 100,
                        'initComplete': function (settings, json) {
                            //
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
