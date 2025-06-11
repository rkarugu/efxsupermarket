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
        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <h3 class="box-title"> Requests Pending @{{ page }} Approval </h3>
            </div>

            <div class="box-body">
                <div class="session-message-container">
                    @include('message')
                </div>

                <div class="d-flex justify-content-between" style="align-items: flex-end;">
                    <div>
                        <form>
                            @csrf
    
                            <div class="row d-flex" style="align-items: flex-end">
                                <div class="col-md-2">
                                    <label for="start-date">Start Date</label>
                                    <input type="date" name="start_date" id="start-date" class="form-control" value="{{ request()->get('start_date') }}">
                                </div>
    
                                <div class="col-md-2">
                                    <label for="end-date">End Date</label>
                                    <input type="date" name="end_date" id="end-date" class="form-control" value="{{ request()->get('end_date') }}">
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
                                    <a class="btn btn-success ml-12" :href="pageRoute">Clear</a>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                    
                    <div class="d-flex">
                        <button class="btn btn-primary" style="min-width: 75px" :disabled="!requestsToBatchApprove.length || processing" @click="confirmBatchApprove" v-if="batchAction == 'approve'">Approve</button>
                        <button class="btn btn-primary" style="min-width: 75px" :disabled="!requestsToBatchReject.length || processing" @click="confirmBatchReject" v-if="batchAction == 'reject'">Reject</button>
                        
                        <div style="margin-left: 10px; min-width: 160px;" v-if="canPerformBatchActions">
                            <select class="form-control" data-placeholder="Select batch action..." v-model="batchAction" :onchange="changeBatchAction">
                                <option value="approve" v-if="canBatchApprove">Batch Approve</option>
                                <option value="reject" v-if="canBatchReject">Batch Reject</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr>

                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th style="width: 3%;">#</th>
                            <th>Date</th>
                            <th>Branch</th>
                            {{-- <th>Department</th> --}}
                            <th>Created By</th>
                            <th>Account</th>
                            <th>Petty Cash No</th>
                            <th>Petty Cash Type</th>
                            <th>Payees</th>
                            <th>Routes</th>
                            <th style="text-align: right">Total Amount</th>
                            <th class="noneedtoshort" v-if="canViewActions">
                                <div v-if="['approve', 'reject'].includes(batchAction)">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" v-model="checkAll">
                                        </label>
                                    </div>
                                </div>
                                <div v-else>
                                    Actions
                                </div>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="(request, index) in pettyCashRequests" :key="request.id">
                            <th style="width: 3%;" scope="row">@{{ ++index }}</th>
                            <td>@{{ dayjs(request.created_at).format('YYYY-MM-DD HH:mm:ss') }}</td>
                            <td>@{{ request.restaurant.name }}</td>
                            {{-- <td>@{{ request.department.department_name }}</td> --}}
                            <td>@{{ request.created_by.name }}</td>
                            <td>@{{ request.chart_of_account.account_name }}</td>
                            <td>@{{ request.petty_cash_no }}</td>
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
                            <td style="text-align: center" v-if="canViewActions">
                                <div class="action-button-div" v-if="!batchAction">
                                    <a :href="`${approveRoute}/${request.id}`">
                                        <i class="fa fa-arrow-right fa-lg" style="color: #337ab7;" title="Approve"></i>
                                    </a>
                                </div>
                                <div v-else>
                                    <div v-if="batchAction == 'approve'">
                                        <div class="checkbox" style="margin-block: 0">
                                            <label>
                                                <input type="checkbox" :Value="request.id" v-model="requestsToBatchApprove">
                                            </label>
                                        </div>
                                    </div>
                                    <div v-if="batchAction == 'reject'">
                                        <div class="checkbox" style="margin-block: 0">
                                            <label>
                                                <input type="checkbox" :Value="request.id" v-model="requestsToBatchReject">
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="10" style="text-align: right">Total</th>
                            <td style="text-align: right">@{{ numberWithCommas(totalAmount) }}</td>
                            <td v-if="canViewActions"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> @{{ modalTitle }} </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        @{{ modalMessage }}
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" :disabled="processing" @click="modalActionRef">@{{ modalAction }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script>

        axios.defaults.baseURL = '/api'

        axios.interceptors.response.use(
            response => response,
            error => {
                if (error.response && error.response.status === 401) {
                    window.location = '/'
                }
                return Promise.reject(error);
            }
        );
        
    </script>

    <script type="module">
        import { createApp, computed, onMounted, ref, watch } from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const page = '{!! $page !!}'
                const pettyCashRequests = {!! $pettyCashRequests !!}

                const permissions = Object.keys(user.permissions)

                const canBatchApprove = computed(() => {
                    if (page == 'Initial') {
                        return permissions.includes('petty-cash-requests-initial-approval___batch-approve') || 
                            user.role_id == 1
                    } else if (page == 'Final') {
                        return permissions.includes('petty-cash-requests-final-approval___batch-approve') || 
                            user.role_id == 1
                    }
                })

                const canBatchReject = computed(() => {
                    if (page == 'Initial') {
                        return permissions.includes('petty-cash-requests-initial-approval___batch-reject') || 
                            user.role_id == 1
                    } else if (page == 'Final') {
                        return permissions.includes('petty-cash-requests-final-approval___batch-reject') || 
                            user.role_id == 1
                    }
                })
                
                const canPerformBatchActions = computed(() => {
                    return canBatchApprove.value || canBatchReject.value
                })

                const canViewActions = computed(() => {
                    if (page == 'Initial') {
                        return permissions.includes('petty-cash-requests-initial-approval___approve') || canPerformBatchActions.value
                    } else if (page == 'Final') {
                        return permissions.includes('petty-cash-requests-final-approval___approve') || canPerformBatchActions.value
                    }
                })

                const driverGrn = (request) => {
                    return request.petty_cash_type?.slug == 'driver-grn'
                }

                const driverGrnRef = (request) => {
                    let item = request.petty_cash_request_items[0]
                    return item.grn?.grn_number ?? item.transfer?.transfer_no
                }
                
                let approveRoute = ''
                let pageRoute = ''
                if (page == 'Initial') {
                    approveRoute = '/admin/petty-cash-requests/initial-approval-approve'
                    pageRoute = '/admin/petty-cash-requests/initial-approval'
                } else if (page == 'Final') {
                    approveRoute = '/admin/petty-cash-requests/final-approval-approve'
                    pageRoute = '/admin/petty-cash-requests/final-approval'
                }

                const formUtil = new Form()

                const totalAmount = computed(() => {
                    return pettyCashRequests.reduce((acc, pettyCashRequest) => acc + pettyCashRequest.total_amount, 0).toFixed(2)
                })
                
                const batchAction = ref('')

                const changeBatchAction = (event) => {
                    batchAction.value = $(event.target).val()

                    checkAll.value = false
                    requestsToBatchApprove.value = []
                    requestsToBatchReject.value = []
                }

                const checkAll = ref(false)

                watch(() => checkAll.value, (value) => {
                    if (value) {
                        if (batchAction.value == 'approve') {
                            requestsToBatchApprove.value = pettyCashRequests.map(request => request.id)
                        } else if (batchAction.value == 'reject') {
                            requestsToBatchReject.value = pettyCashRequests.map(request => request.id)
                        }
                    } else {
                        if (batchAction.value == 'approve') {
                            requestsToBatchApprove.value = []
                        } else if (batchAction.value == 'reject') {
                            requestsToBatchReject.value = []
                        }
                    }
                })
                
                const requestsToBatchApprove = ref([])

                const modalTitle = ref('')
                const modalMessage = ref('')
                const modalAction = ref('')
                const modalActionRef = ref(null)
                const confirmBatchApprove = () => {
                    modalTitle.value = 'Approve Multiple Requests?'
                    modalMessage.value = 'Are you sure you want to approve these requests?'
                    modalAction.value = 'Approve'
                    modalActionRef.value = processBatch
                    
                    $('#confirm-modal').modal('show')
                }
                
                const requestsToBatchReject = ref([])
                const confirmBatchReject = () => {
                    modalTitle.value = 'Reject Multiple Requests?'
                    modalMessage.value = 'Are you sure you want to reject these requests?'
                    modalAction.value = 'Reject'
                    modalActionRef.value = processBatch
                    
                    $('#confirm-modal').modal('show')
                }

                const processing = ref(false)
                const processBatch = () => {
                    processing.value = true

                    let uri = ''
                    let requestIds = ''

                    if (batchAction.value == 'approve') {
                        uri = 'petty-cash-request-batch-approve'
                        requestIds = requestsToBatchApprove.value

                    } else if (batchAction.value == 'reject') {
                        uri = 'petty-cash-request-batch-reject'
                        requestIds = requestsToBatchReject.value
                    }

                    axios.post(uri, {
                        stage : page.toLowerCase(),
                        requestIds
                    })
                        .then(response => {
                            $('#confirm-modal').modal('hide')
                            
                            formUtil.successMessage(response.data.message)

                            setTimeout(() => {
                                window.location.reload()
                            }, 2000);
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
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
                    user,
                    page,
                    dayjs,
                    permissions,
                    numberWithCommas,
                    pettyCashRequests,
                    approveRoute,
                    confirmBatchApprove,
                    requestsToBatchApprove,
                    modalTitle,
                    modalMessage,
                    modalAction,
                    modalActionRef,
                    processing,
                    batchAction,
                    changeBatchAction,
                    requestsToBatchReject,
                    confirmBatchReject,
                    checkAll,
                    canPerformBatchActions,
                    canViewActions,
                    pageRoute,
                    totalAmount,
                    canBatchApprove,
                    canBatchReject,
                    driverGrn,
                    driverGrnRef,
                }
            }
        }).mount('#app')
    </script>
@endsection
