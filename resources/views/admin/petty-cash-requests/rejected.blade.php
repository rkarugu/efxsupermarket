@extends('layouts.admin.admin')

@push('styles')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>

    <style>
        .table-icon {
            height: 30px;
            width: 30px;
            margin-right: 5px;
            border-radius: 50%;
            background-color: #ccc;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .table-icon i {
            font-size: 18px;
            color: #333;
        }
    </style>
@endpush

@section('content')
    <section class="content" id="app">
        <div class="session-message-container">
            @include('message')
        </div>

        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <h3 class="box-title"> Rejected Requests </h3>
            </div>

            <div class="box-body">
                <div>
                    <form>
                        @csrf

                        <div class="row d-flex" style="align-items: flex-end">
                            <div class="col-md-2">
                                <label for="start-date">Start Date</label>
                                <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request()->get('start_date') ?? date('Y-m-d') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="end-date">End Date</label>
                                <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request()->get('end_date') ?? date('Y-m-d') }}">
                            </div>

                            <div class="col-md-2">
                                <label for="branch">Branch</label>
                                <select class="form-control" name="branch" id="branch">
                                    <option value="" selected disabled></option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}" {{ request()->get('branch') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-2">
                                <label for="petty-cash-type">Petty Cash Type</label>
                                <select class="form-control" name="type" id="petty-cash-type">
                                    <option value="" selected disabled></option>
                                    @foreach ($pettyCashTypes as $pettyCashType)
                                        <option value="{{ $pettyCashType->slug }}" {{ request()->get('type') == $pettyCashType->slug ? 'selected' : '' }}>{{ $pettyCashType->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <button type="submit" class="btn btn-success">Filter</button>
                                <a class="btn btn-success ml-12" href="{{ route('petty-cash-request.rejected') }}">Clear</a>
                            </div>
                            
                        </div>
                    </form>
                </div>

                <hr>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Department</th>
                            <th>Rejected By</th>
                            <th>Account</th>
                            <th>Petty Cash No</th>
                            <th>Petty Cash Type</th>
                            <th>Payees</th>
                            <th>Routes</th>
                            <th style="text-align: right">Total Amount</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(request, index) in pettyCashRequests" :key="request.id">
                            <th style="width: 3%;" scope="row">@{{ index + 1 }}</th>
                            <td>@{{ dayjs(request.rejected_date).format('YYYY-MM-DD HH:mm:ss') }}</td>
                            <td>@{{ request.restaurant.name }}</td>
                            <td>@{{ request.department.department_name }}</td>
                            <td>@{{ request.rejected_by.name }}</td>
                            <td>@{{ request.chart_of_account.account_name }}</td>
                            <td>
                                <a :href="`/admin/petty-cash-requests/rejected-details/${request.petty_cash_no}`">
                                    @{{ request.petty_cash_no }}
                                </a>
                            </td>
                            <td>
                                <span>@{{ request.petty_cash_type?.name }}</span>
                                <span v-if="driverGrn(request)">(@{{ driverGrnRef(request) }})</span>
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center;">
                                    <div 
                                        class="table-icon" 
                                        data-toggle="tooltip" 
                                        :title="`${requestItem.payee_name}: ${numberWithCommas(requestItem.amount)}`"
                                        v-for="requestItem in request.petty_cash_request_items" :key="requestItem.id"
                                    >
                                        <i class="fa fa-user"></i>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; justify-content: center;">
                                    <div v-for="requestItem in request.petty_cash_request_items" :key="requestItem.id">
                                        <div 
                                            class="table-icon" 
                                            data-toggle="tooltip" 
                                            :title="requestItem.route.route_name"
                                            v-if="requestItem.route"
                                        >
                                            <i class="fa fa-map-marker"></i>
                                        </div>

                                    </div>
                                </div>
                            </td>
                            <td style="text-align: right">@{{ numberWithCommas(request.total_amount.toFixed(2)) }}</td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="10" style="text-align: right">Total</th>
                            <td style="text-align: right">@{{ numberWithCommas(totalAmount) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
<script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ? 'https://unpkg.com/vue@3/dist/vue.esm-browser.js' : 'https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js' }}"
            }
        }
    </script>

    <script type="module">
        import { createApp, computed, onMounted, ref, watch } from 'vue';

        createApp({
            setup() {
                const pettyCashRequests = {!! $pettyCashRequests !!}

                const totalAmount = computed(() => {
                    return pettyCashRequests.reduce((acc, pettyCashRequest) => acc + pettyCashRequest.total_amount, 0).toFixed(2)
                })

                const driverGrn = (request) => {
                    return request.petty_cash_type?.slug == 'driver-grn'
                }

                const driverGrnRef = (request) => {
                    let item = request.petty_cash_request_items[0]
                    return item.grn?.grn_number ?? item.transfer?.transfer_no
                }
                
                onMounted(() => {
                    $('select').select2({
                        placeholder: 'Select...',
                        allowClear: true
                    })

                    $('table').DataTable({
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'ordering': true,
                        'info': true,
                        'autoWidth': false,
                        'pageLength': 25,
                        'initComplete': function (settings, json) {
                            let info = this.api().page.info();
                            let total_record = info.recordsTotal;
                            if (total_record < 26) {
                                $('.dataTables_paginate').hide();
                            }
                        },
                        'aoColumnDefs': [{
                            'bSortable': false,
                            'aTargets': 'noneedtoshort'
                        }],
                    });
                    
                    $('body').addClass('sidebar-collapse');

                })

                return {
                    dayjs,
                    numberWithCommas,
                    pettyCashRequests,
                    totalAmount,
                    driverGrn,
                    driverGrnRef,
                }
            }
        }).mount('#app')
    </script>
@endsection
