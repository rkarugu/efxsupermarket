@extends('layouts.admin.admin')

@section('content')
    <div id="app" v-cloak>
        <section class="content" style="padding-bottom: 0">
            <div class="session-message-container">
                @include('message')
            </div>
            
            <div class="box box-primary" style="margin-bottom: 10px">
                <div class="box-header with-border">
                    <h3 class="box-title">Petty Cash Request Details</h3>
                </div>
                
                <div class="box-body">
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-sm-6">
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Created By</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="pettyCashRequest.created_by.name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Date Created</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="dayjs(pettyCashRequest.created_at).format('YYYY-MM-DD')" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">@{{ page }} By</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="contextUser.name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Date @{{ page }}</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="dayjs(contextDate).format('YYYY-MM-DD')" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="pettyCashRequest.restaurant.name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="pettyCashRequest.department.department_name" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Petty Cash Type</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" :value="pettyCashRequest.petty_cash_type.name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px" v-show="vehicleRepair">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Vehicle</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" :value="pettyCashRequest.vehicle?.license_plate_number" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px" v-show="vehicleRepair">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Repair Type</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" :value="_.upperFirst(pettyCashRequest.repair_type)" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Expense Account</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" :value="pettyCashRequest.chart_of_account.account_name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Amounts Are</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control" :value="_.upperFirst(pettyCashRequest.tax_type)" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Total Amount</label>
                                    <div class="col-sm-6">
                                        <p style="font-size: 20px; font-weight:bold; text-align: right">KES @{{ numberWithCommas(totalWithTax) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
        
        <section class="content" style="padding-top: 0">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <div class="col-md-12 no-padding-h ">

                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th v-show="pettyCashRequest.type == 'parking-fees'">Route</th>
                                    <th>Payee Name</th>
                                    <th style="width: 120px">Payee Phone No.</th>
                                    <th style="width: 80px">Amount</th>
                                    <th style="width: 150px" v-show="pettyCashRequest.tax_type != 'without'">VAT</th>
                                    <th v-show="pettyCashRequest.tax_type != 'without'">CU Invoice No</th>
                                    <th>Reason for Payment</th>
                                    <th v-show="pettyCashRequest.type == 'driver-grn'">GRN</th>
                                    <th v-show="pettyCashRequest.type == 'driver-grn'" style="width: 120px">Transfer No.</th>
                                    <th v-show="pettyCashRequest.type == 'travel-delivery'">Loading Schedule</th>
                                    <th style="width: 60px">Files</th>
                                    <th style="width: 60px" v-if="page != 'Rejected'">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(requestItem, index) in requestItems" :key="index">
                                    <td v-show="pettyCashRequest.type == 'parking-fees'">
                                        @{{ requestItem.route?.route_name }}
                                    </td>
                                    <td>
                                        @{{ requestItem.payee_name }}

                                    </td>
                                    <td>
                                        @{{ requestItem.payee_phone_no }}
                                    </td>
                                    <td>
                                        @{{ numberWithCommas(requestItem.amount.toFixed(2)) }}
                                    </td>
                                    <td v-show="pettyCashRequest.tax_type != 'without'">
                                        @{{ `${requestItem.tax_manager?.title} (${requestItem.tax_manager?.tax_value})` }}
                                    </td>
                                    <td v-show="pettyCashRequest.tax_type != 'without'">
                                        @{{ requestItem.cu_invoice_no }}
                                    </td>
                                    <td>
                                        @{{ requestItem.payment_reason }}
                                    </td>
                                    <td v-show="pettyCashRequest.type == 'driver-grn'">
                                        @{{ requestItem.grn?.grn_number }}
                                    </td>
                                    <td v-show="pettyCashRequest.type == 'driver-grn'">
                                        @{{ requestItem.transfer?.transfer_no }}
                                    </td>
                                    <td v-show="pettyCashRequest.type == 'travel-delivery'">
                                        @{{ requestItem.delivery_schedule?.delivery_number }}
                                    </td>
                                    <td style="text-align: center">
                                        <button type="button" class="btn btn-sm" style="background-color: #337ab7; color: white" @click="showFilesModal(requestItem)">
                                            <i class="fa fa-file" aria-hidden="true"></i>
                                        </button>
                                    </td>
                                    <td v-if="page != 'Rejected'">
                                        @{{ _.upperFirst(requestItem.status) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <table class="table table-bordered">
                                    <tr>
                                        <th style="text-align: right">Sub Total</th>
                                        <td style="text-align: right">@{{ numberWithCommas(totalSubTotal.toFixed(2)) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right">Vat</th>
                                        <td style="text-align: right">@{{ numberWithCommas(totalTax.toFixed(2)) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right">Total</th>
                                        <td style="text-align: right">@{{ numberWithCommas(totalWithTax.toFixed(2)) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="files-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ modal.title }} Files</h4>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th>File</th>
                                </tr>
                            </thead>
        
                            <tbody>
                                <tr v-for="(file, index) in modal.files">
                                    <th style="width: 3%;" scope="row">@{{ ++index }}</th>
                                    <td>
                                        <a :href="`/storage/${file.path}`" target="_blank">
                                            @{{ `File ${index}` }}
                                            <i class="fa fa-external-link" style="font-size: 12px"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('uniquepagescript')
    <script src="{{ asset('js/utils.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ? 'https://unpkg.com/vue@3/dist/vue.esm-browser.js' : 'https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js' }}"
            }
        }
    </script>

    <script type="module">
        import { createApp, onMounted, ref, computed } from 'vue';

        createApp({
            setup() {
                const page = "{!! $page !!}"
                const pettyCashRequest = {!! $pettyCashRequest !!}

                let contextUser = null
                let contextDate = null
                if (page == 'Approved') {
                    contextUser = pettyCashRequest.final_approver
                    contextDate = pettyCashRequest.final_approval_date
                } else if (page == 'Rejected') {
                    contextUser = pettyCashRequest.rejected_by
                    contextDate = pettyCashRequest.rejected_date
                }

                const requestItems = pettyCashRequest.petty_cash_request_items

                const vehicleRepair = computed(() => {
                    return pettyCashRequest.type == 'repairs-maintenance-motor-vehicle'
                })

                const totalTax = computed(() => {
                    return requestItems.reduce((acc, requestItem) => acc + parseFloat(requestItem.vat_amount), 0)
                })

                const totalSubTotal = computed(() => {
                    return requestItems.reduce((acc, requestItem) => acc + parseFloat(requestItem.sub_total), 0)
                })
                
                const totalWithTax = computed(() => {
                    return totalSubTotal.value + totalTax.value
                })

                const modal = ref({
                    id: '',
                    title: '',
                    files: []
                })

                const showFilesModal = (requestItem) => {
                    modal.value.id = requestItem.id
                    modal.value.title = requestItem.payee_name
                    modal.value.files = requestItem.petty_cash_request_item_files
                    
                    $('#files-modal').modal('show')
                }

                onMounted(() => {
                    
                    $('body').addClass('sidebar-collapse');

                })
                
                return {
                    pettyCashRequest,
                    requestItems,
                    modal,
                    showFilesModal,
                    totalTax,
                    totalSubTotal,
                    totalWithTax,
                    vehicleRepair,
                    numberWithCommas,
                    dayjs,
                    page,
                    contextUser,
                    contextDate,
                }
            }
        }).mount('#app')
    </script>
@endsection

