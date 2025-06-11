@php use Carbon\Carbon; @endphp
@extends('layouts.admin.admin')

@section('content')
    <script>
        window.record = {!! $record !!};
    </script>

    <section class="content" id="vue-mount">
        <div class="box box-primary">
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title" v-cloak> Verification Details - @{{ verificationRecord.fueling_date }} </h3>

                    <div class="d-flex">
                        <button class="btn btn-primary" @click="runVerification"><i class="fas fa-circle-notch btn-icon"></i> Run verification</button>
                        <a href="{{ url()->previous() }}" class="btn btn-primary ml-12"><i class="fas fa-arrow-left btn-icon"></i> Back</a>
                    </div>
                </div>
            </div>

            <div class="box-body">
                <div class="row" v-cloak>
                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h3> @{{ summary.fueled_entries }} </h3>
                                <p> FUELED ENTRIES </p>
                            </div>

                            <div class="icon">
                                <i class="fa fa-fw fa-gas-pump"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h3> @{{ summary.verified_entries }} </h3>
                                <p>VERIFIED ENTRIES</p>
                            </div>

                            <div class="icon">
                                <i class="fas fa-thumbs-up"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-yellow">
                            <div class="inner">
                                <h3> @{{ summary.missing }} </h3>
                                <p>MISSING IN STATEMENT</p>
                            </div>

                            <div class="icon">
                                <i class="fas fa-file-circle-xmark"></i>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-xs-6">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h3> @{{ summary.unknown }} </h3>
                                <p>UNKNOWN PAYMENTS</p>
                            </div>

                            <div class="icon">
                                <i class="fa fa-fw fa-money-bill-transfer"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <ul class="nav nav-tabs" id="data-tabs">
                    <li class="active"><a href="#verified-entries" data-toggle="tab"> Verified Entries </a></li>
                    <li><a href="#missing-in-statement" data-toggle="tab">Missing in Statement</a></li>
                    <li><a href="#uknown-payments" data-toggle="tab">Unknown Payments</a></li>
                    <li><a href="#unfueled" data-toggle="tab">Un-fueled Routes</a></li>
                    <li><a href="#unutilized" data-toggle="tab">Un-utilized LPOs</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="verified-entries" v-cloak>
                        <div class="box-body">
                            <table class="table table-bordered table-hover data-tables" id="verified-entries-table">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Fuel Date</th>
                                    <th>Shift Date</th>
                                    <th>LPO #</th>
                                    <th style="width: 15%;">Route</th>
                                    <th>Vehicle</th>
                                    <th>Receipt #</th>
                                    <th style="width: 4%;">Ton</th>
                                    <th style="width: 4%;">Std</th>
                                    <th style="width: 4%;">Act</th>
                                    <th style="width: 4%;">Var</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                                </thead>

                                <tbody v-cloak>
                                <template v-for="(entry, index) in verifiedEntries" :key="index">
                                    <tr :style="{backgroundColor: (entry.fuel_variance > 0) ? '#d0abab': 'white' }">
                                        <th style="width: 3%;">
                                            <i class="fas fa-circle-plus" :id="`toggle-icon-${entry.entry_id}`" style="cursor:pointer;" @click="toggleRow(entry)"></i>
                                        </th>
                                        <td> @{{ entry.fueling_date }}</td>
                                        <td> @{{ entry.shift_date }}</td>
                                        <td> @{{ entry.lpo_number }}</td>
                                        <td style="width: 15%;">
                                            <a :href="`/salesman-shift/${entry.salesman_shift_id}`" target="_blank">@{{ entry.route }}</a>
                                        </td>
                                        <td> @{{ entry.vehicle }} (@{{ entry.driver }})</td>
                                        <td> @{{ entry.receipt_number }}</td>
                                        <td style="width: 4%;"> @{{ entry.tonnage }}T</td>
                                        <td style="width: 4%;"> @{{ entry.standard_fuel }}L</td>
                                        <td style="width: 4%;"> @{{ entry.actual_fuel_quantity }}L</td>
                                        <td style="width: 4%;"> @{{ entry.fuel_variance }}L</td>
                                        <td style="text-align: right;">@{{ entry.total }}</td>
                                    </tr>

                                    <tr class="child-row collapse" :id="`child-row-${entry.entry_id}`">
                                        <td colspan="13">
                                            <div class="box">
                                                <div class="box-body">
                                                    <table class="table table-striped table-hover table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th> Standard Distance</th>
                                                            <th> Standard Fuel</th>
                                                            <th> Standard Tonnage</th>
                                                            <th> Actual Distance</th>
                                                            <th> Variance</th>
                                                            <th> Actual Fuel</th>
                                                            <th> Variance</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        <tr>
                                                            <td> @{{ entry.standard_distance }} KM</td>
                                                            <td> @{{ entry.standard_fuel }}L</td>
                                                            <td> @{{ entry.estimate_tonnage }}T</td>
                                                            <td> @{{ entry.manual_distance_covered }} KM</td>
                                                            <td> @{{ (entry.manual_distance_covered - entry.standard_distance).toFixed(2) }} KM</td>
                                                            <td> @{{ entry.actual_fuel_quantity }}L</td>
                                                            <td> @{{ entry.actual_fuel_quantity - entry.standard_fuel }}L</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="d-flex" style="width: 100%; border: 1px solid rgba(0, 0, 0, .125); margin-top: 20px;">
                                                        <div style="border-right: 1px solid rgba(0, 0, 0, .125); padding: 10px;" class="d-flex justify-content-center align-items-center flex-grow-1">
                                                            <img :src="entry.dashboard_photo" alt="Dashboard Photo" width="500" height="400" style="border-radius: 5px;">
                                                        </div>

                                                        <div class="d-flex justify-content-center align-items-center flex-grow-1" style="padding: 10px;">
                                                            <img :src="entry.receipt_photo" alt="Receipt Photo" width="500" height="400" style="border-radius: 5px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                <tfoot v-cloak>
                                <tr>
                                    <th colspan="11" style="text-align: right;"> FUEL TOTAL</th>
                                    <th style="text-align: right;"> @{{ getColumnTotal(verifiedEntries, 'raw_total') }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="missing-in-statement" v-cloak>
                        <div class="box-body">
                            <table class="table table-bordered table-hover data-tables" id="missing-entries-table">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Fuel Date</th>
                                    <th>Shift Date</th>
                                    <th>LPO #</th>
                                    <th style="width: 15%;">Route</th>
                                    <th>Vehicle</th>
                                    <th>Receipt #</th>
                                    <th style="width: 4%;">Ton</th>
                                    <th style="width: 4%;">Std</th>
                                    <th style="width: 4%;">Act</th>
                                    <th style="width: 4%;">Var</th>
                                    <th style="text-align: right;">Total</th>
                                </tr>
                                </thead>

                                <tbody v-cloak>
                                <template v-for="(entry, index) in missingEntries" :key="index">
                                    <tr :style="{backgroundColor: (entry.fuel_variance > 0) ? '#d0abab': 'white' }">
                                        <th style="width: 3%;">
                                            <i class="fas fa-circle-plus" :id="`toggle-icon-${entry.entry_id}`" style="cursor:pointer;" @click="toggleRow(entry)"></i>
                                        </th>
                                        <td> @{{ entry.fueling_date }}</td>
                                        <td> @{{ entry.shift_date }}</td>
                                        <td> @{{ entry.lpo_number }}</td>
                                        <td style="width: 15%;">
                                            <a :href="`/admin/delivery-schedules/${entry.delivery_id}`" target="_blank">@{{ entry.route }}</a>
                                        </td>
                                        <td> @{{ entry.vehicle }} (@{{ entry.driver }})</td>
                                        <td> @{{ entry.receipt_number }}</td>
                                        <td style="width: 4%;"> @{{ entry.tonnage }}T</td>
                                        <td style="width: 4%;"> @{{ entry.standard_fuel }}L</td>
                                        <td style="width: 4%;"> @{{ entry.actual_fuel_quantity }}L</td>
                                        <td style="width: 4%;"> @{{ entry.fuel_variance }}L</td>
                                        <td style="text-align: right;">@{{ entry.total }}</td>
                                    </tr>

                                    <tr class="child-row collapse" :id="`child-row-${entry.entry_id}`">
                                        <td colspan="13">
                                            <div class="box">
                                                <div class="box-body">
                                                    <table class="table table-striped table-hover table-bordered">
                                                        <thead>
                                                        <tr>
                                                            <th> Standard Distance</th>
                                                            <th> Standard Fuel</th>
                                                            <th> Standard Tonnage</th>
                                                            <th> Actual Distance</th>
                                                            <th> Variance</th>
                                                            <th> Actual Fuel</th>
                                                            <th> Variance</th>
                                                        </tr>
                                                        </thead>

                                                        <tbody>
                                                        <tr>
                                                            <td> @{{ entry.standard_distance }} KM</td>
                                                            <td> @{{ entry.standard_fuel }}L</td>
                                                            <td> @{{ entry.estimate_tonnage }}T</td>
                                                            <td> @{{ entry.manual_distance_covered }} KM</td>
                                                            <td> @{{ (entry.manual_distance_covered - entry.standard_distance).toFixed(2) }} KM</td>
                                                            <td> @{{ entry.actual_fuel_quantity }}L</td>
                                                            <td> @{{ entry.actual_fuel_quantity - entry.standard_fuel }}L</td>
                                                        </tr>
                                                        </tbody>
                                                    </table>

                                                    <div class="d-flex" style="width: 100%; border: 1px solid rgba(0, 0, 0, .125); margin-top: 20px;">
                                                        <div style="border-right: 1px solid rgba(0, 0, 0, .125); padding: 10px;" class="d-flex justify-content-center align-items-center flex-grow-1">
                                                            <img :src="entry.dashboard_photo" alt="Dashboard Photo" width="500" height="400" style="border-radius: 5px;">
                                                        </div>

                                                        <div class="d-flex justify-content-center align-items-center flex-grow-1" style="padding: 10px;">
                                                            <img :src="entry.receipt_photo" alt="Receipt Photo" width="500" height="400" style="border-radius: 5px;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                </tbody>

                                <tfoot v-cloak>
                                <tr>
                                    <th colspan="11" style="text-align: right;"> MISSING TOTAL</th>
                                    <th style="text-align: right;"> @{{ getColumnTotal(missingEntries, 'raw_total') }}</th>
                                </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="uknown-payments">
                        <div class="box-body">
                            <table class="table table-hover table-bordered table-striped" id="create_datatable">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Date</th>
                                    <th>Receipt #</th>
                                    <th>Fueled Quantity</th>
                                    <th>Description</th>
                                    <th>Resolved</th>
                                    <th>Comments</th>
                                    <th style="text-align: right;">Fuel Total</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody>
                                <tr v-for="(statement, index) in unknownPayments" :key="index">
                                    <th style="width: 3%;">@{{ index + 1 }}</th>
                                    <td>@{{statement.timestamp }}</td>
                                    <td>@{{statement.receipt_number }}</td>
                                    <td>@{{statement.quantity }}</td>
                                    <td>@{{statement.narrative }}</td>
                                    <td>@{{statement.unknown_resolved ? 'Yes' : 'No' }}</td>
                                    <td>@{{statement.comments ?? '-' }}</td>
                                    <td style="text-align: right;">@{{ statement.fuel_total }}</td>
                                    <td>
                                        <div class="action-button-div">
                                            <i class="fas fa-handshake fa-lg text-success" @click="promptResolveUnknown(statement)" style="cursor:pointer;" title="Resolve"
                                               v-if="!statement.unknown_approved"></i>

                                            <i class="fas fa-handshake-slash fa-lg text-danger ml-12" @click="promptResetUnknown(statement)" style="cursor:pointer;" title="Reset"
                                               v-if="!statement.unknown_approved && statement.unknown_resolved"></i>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="unfueled">
                        <div class="box-body">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Date</th>
                                    <th>LPO #</th>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Fuel Date</th>
                                    <th>Fuel Qty</th>
                                    <th>Resolved</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody v-cloak>
                                <template v-for="(entry, index) in unfueledRoutes" :key="index">
                                    <tr>
                                        <th style="width: 3%;"> @{{ index + 1 }}</th>
                                        <td> @{{ entry.lpo_date }}</td>
                                        <td> @{{ entry.lpo_number }}</td>
                                        <td>
                                            <a :href="`/salesman-shift/${entry.salesman_shift_id}`" target="_blank">@{{ entry.route }}</a>
                                        </td>
                                        <td> @{{ entry.vehicle }} (@{{ entry.driver }})</td>
                                        <td>@{{ entry.fueling_time }}</td>
                                        <td>@{{ entry.actual_fuel_quantity }}L</td>
                                        <td>@{{ entry.unfueled_resolved ? 'Yes' : 'No' }}</td>
                                        <td>@{{ entry.comments ?? '-' }}</td>
                                        <td>
                                            <div class="action-button-div">
                                                <i class="fas fa-handshake fa-lg text-success" @click="promptResolveUnknown(entry)" style="cursor:pointer;" title="Resolve"
                                                   v-if="!entry.unfueled_approved"></i>

                                                <i class="fas fa-handshake-slash fa-lg text-danger ml-12" @click="promptResetUnknown(entry)" style="cursor:pointer;" title="Reset"
                                                   v-if="!entry.unfueled_approved && entry.unfueled_resolved"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="tab-pane" id="unutilized">
                        <div class="box-body">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 3%;">#</th>
                                    <th>Date</th>
                                    <th>LPO #</th>
                                    <th>Route</th>
                                    <th>Vehicle</th>
                                    <th>Resolved</th>
                                    <th>Comments</th>
                                    <th>Action</th>
                                </tr>
                                </thead>

                                <tbody v-cloak>
                                <template v-for="(entry, index) in unutilizedLpos" :key="index">
                                    <tr>
                                        <th style="width: 3%;"> @{{ index + 1 }}</th>
                                        <td> @{{ entry.lpo_date }}</td>
                                        <td> @{{ entry.lpo_number }}</td>
                                        <td>
                                            <a :href="`/salesman-shift/${entry.salesman_shift_id}`" target="_blank">@{{ entry.route }}</a>
                                        </td>
                                        <td> @{{ entry.vehicle }} (@{{ entry.driver }})</td>
                                        <td>@{{ entry.unfueled_resolved ? 'Yes' : 'No' }}</td>
                                        <td>@{{ entry.comments ?? '-' }}</td>
                                        <td>
                                            <div class="action-button-div">
                                                <i class="fas fa-handshake fa-lg text-success" @click="promptResolveUnknown(entry)" style="cursor:pointer;" title="Resolve"
                                                   v-if="!entry.unfueled_approved"></i>

                                                <i class="fas fa-handshake-slash fa-lg text-danger ml-12" @click="promptResetUnknown(entry)" style="cursor:pointer;" title="Reset"
                                                   v-if="!entry.unfueled_approved && entry.unfueled_resolved"></i>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resolve-unknown-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Resolve Unknown Payment #@{{ activeUnknown.receipt_number }} </h3>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="comments" class="control-label"> Resolution/Comments </label>
                            <textarea v-model="newUnknownComment" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="resolveUnknown">Resolve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reset-unknown-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Reset Unknown Payment #@{{ activeUnknown.receipt_number }} </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want to reset this resolved payment to completely unknown?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="resetUnknown">Yes, I Am</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="resolve-unfueled-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Resolve Unfueled route #@{{ activeUnfueled.route }} </h3>
                    </div>

                    <div class="box-body">
                        <div class="form-group">
                            <label for="comments" class="control-label"> Resolution/Comments </label>
                            <textarea v-model="newUnfueledComment" class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="resolveUnfueled">Resolve</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reset-unfueled-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog" role="document">
                <div class="modal-content box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"> Reset unfueled route #@{{ activeUnfueled.route }} </h3>
                    </div>

                    <div class="box-body">
                        Are you sure you want mark this record as unresolved?
                    </div>

                    <div class="box-footer">
                        <div class="box-header-flex">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" @click="resetUnfueled">Yes, I Am</button>
                        </div>
                    </div>
                </div>
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
                    summary: {
                        fueled_entries: 0,
                        verified_entries: 0,
                        missing: 0.00,
                        unknown: 0.00
                    },
                    verifiedEntries: [],
                    missingEntries: [],
                    unknownPayments: [],
                    activeEntry: {},
                    activeUnknown: {},
                    newUnknownComment: null,
                    unfueledRoutes: [],
                    activeUnfueled: {},
                    newUnfueledComment: null,
                    unutilizedLpos: [],
                }
            },

            mounted() {
                $(".mlselect").select2();
                $("body").addClass('sidebar-collapse');
            },

            computed: {
                toaster() {
                    return new Form();
                },

                verificationRecord() {
                    return window.record
                },
            },

            created() {
                this.fetchSummary();
                this.fetchVerifiedEntries();
                this.fetchMissingEntries();
                this.fetchUnknownPayments();
                this.fetchUnfueledRoutes();
                this.fetchUnutilizedLpos();
            },

            methods: {
                getColumnTotal(records, column) {
                    let total = records.reduce((partialSum, record) => partialSum + record[column], 0);
                    return total.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
                },

                fetchSummary() {
                    axios.get('{{ route("fuel-verification.summary") }}', {params: {record_id: this.verificationRecord.id}}).then(response => {
                        this.summary = response.data;
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchVerifiedEntries() {
                    axios.get('{{ route("fuel-verification.verified") }}', {params: {record_id: this.verificationRecord.id}}).then(response => {
                        this.verifiedEntries = response.data;

                        // $('#verified-entries-table').DataTable().destroy();
                        // setTimeout(() => {
                        //     $('#verified-entries-table').DataTable({
                        //         'paging': true,
                        //         'lengthChange': true,
                        //         'searching': true,
                        //         'ordering': true,
                        //         'info': true,
                        //         'autoWidth': false,
                        //         'pageLength': 100,
                        //         'initComplete': function (settings, json) {
                        //             //
                        //         },
                        //         'aoColumnDefs': [{
                        //             'bSortable': false,
                        //             'aTargets': 'noneedtoshort'
                        //         }],
                        //     });
                        // }, 500);
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchUnknownPayments() {
                    axios.get('{{ route("fuel-verification.unknown") }}', {params: {record_id: this.verificationRecord.id}}).then(response => {
                        this.unknownPayments = response.data;

                        // setTimeout(() => {
                        //     this.initDataTables();
                        // }, 2000);
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchMissingEntries() {
                    axios.get('{{ route("fuel-verification.missing") }}', {params: {record_id: this.verificationRecord.id}}).then(response => {
                        this.missingEntries = response.data;

                        // setTimeout(() => {
                        //     this.initDataTables();
                        // }, 2000);
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                runVerification() {
                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-verification.verify") }}', {record_id: this.verificationRecord.id}).then(response => {
                        $(".btn-loader").hide();
                        this.toaster.successMessage(response.data.message);

                        this.fetchSummary();
                        this.fetchVerifiedEntries();
                        this.fetchMissingEntries();
                        this.fetchUnknownPayments();
                        this.fetchUnfueledRoutes();
                        this.fetchUnutilizedLpos();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                initDataTables() {
                    $('.data-tables').DataTable().destroy();

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

                toggleRow(entry) {
                    this.activeEntry = entry;
                    let currentRow = $(`#child-row-${entry.entry_id}`);
                    let currentRowIsClosed = currentRow.hasClass('collapse');

                    this.verifiedEntries.forEach((_entry) => {
                        if (_entry.entry_id !== entry.entry_id) {
                            if (!($(`#child-row-${_entry.entry_id}`).hasClass('collapse'))) {
                                $(`#child-row-${_entry.entry_id}`).addClass('collapse');
                                $(`#toggle-icon-${_entry.entry_id}`).removeClass('fa-circle-minus').addClass('fa-circle-plus');
                            }
                        }
                    })

                    if (currentRow.hasClass('collapse')) {
                        currentRow.removeClass('collapse');
                        $(`#toggle-icon-${entry.entry_id}`).removeClass('fa-circle-plus').addClass('fa-circle-minus');
                    } else {
                        currentRow.addClass('collapse');
                        $(`#toggle-icon-${entry.entry_id}`).removeClass('fa-circle-minus').addClass('fa-circle-plus');
                    }
                },

                promptResolveUnknown(statement) {
                    this.activeUnknown = statement
                    this.newUnknownComment = statement.comments

                    $("#resolve-unknown-modal").modal("show");
                },

                resolveUnknown() {
                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-verification.unknown.resolve") }}', {id: this.activeUnknown.id, comments: this.newUnknownComment}).then(response => {
                        $(".btn-loader").hide();
                        $("#resolve-unknown-modal").modal("hide");
                        this.toaster.successMessage('Unknown payment resolved successfully.');

                        this.fetchUnknownPayments();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                promptResetUnknown(statement) {
                    this.activeUnknown = statement
                    $("#reset-unknown-modal").modal("show");
                },

                resetUnknown() {
                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-verification.unknown.reset") }}', {id: this.activeUnknown.id}).then(response => {
                        $(".btn-loader").hide();
                        $("#reset-unknown-modal").modal("hide");
                        this.toaster.successMessage('Unknown payment reset successfully.');

                        this.fetchUnknownPayments();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchUnfueledRoutes() {
                    axios.get('{{ route("fuel-verification.unfueled") }}', {params: {record_id: this.verificationRecord.id}}).then(response => {
                        this.unfueledRoutes = response.data;
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                fetchUnutilizedLpos() {
                    axios.get('{{ route("fuel-verification.unutilized") }}', {params: {record_id: this.verificationRecord.id}}).then(response => {
                        this.unutilizedLpos = response.data;
                    }).catch(error => {
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                promptResolveUnfueled(entry) {
                    this.activeUnfueled = entry
                    this.newUnfueledComment = entry.comments

                    $("#resolve-unfueled-modal").modal("show");
                },

                resolveUnfueled() {
                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-verification.unfueled.resolve") }}', {id: this.activeUnfueled.id, comments: this.newUnfueledComment}).then(response => {
                        $(".btn-loader").hide();
                        $("#resolve-unfueled-modal").modal("hide");
                        this.toaster.successMessage('Unfueled record resolved successfully.');

                        this.fetchUnfueledEntries();
                    }).catch(error => {
                        $(".btn-loader").hide();
                        this.toaster.errorMessage(error.response?.data?.message ?? error.response?.data);
                    });
                },

                promptResetUnfueled(entry) {
                    this.activeUnfueled = entry
                    $("#reset-unfueled-modal").modal("show");
                },

                resetUnfueled() {
                    $(".btn-loader").show();
                    axios.post('{{ route("fuel-verification.unfueled.reset") }}', {id: this.activeUnfueled.id}).then(response => {
                        $(".btn-loader").hide();
                        $("#reset-unfueled-modal").modal("hide");
                        this.toaster.successMessage('Unfueled record reset successfully.');

                        this.fetchUnfueledPayments();
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