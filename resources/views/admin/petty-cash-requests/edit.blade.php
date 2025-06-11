@extends('layouts.admin.admin')

@section('content')
    <div id="app" v-cloak>
        <section class="content" style="padding-bottom: 0">
            <div class="box box-primary" style="margin-bottom: 10px">
                <div class="box-header with-border">
                    <h3 class="box-title">Approve Petty Cash Request ({{ $page }})</h3>
                </div>
                <div class="box-body">
                    <div class="row" style="margin-bottom: 20px">
                        <div class="col-sm-6">
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" :value="user.name" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Date</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" readonly value="{{ date('Y-m-d') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" readonly :value="form.branch">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control" readonly :value="form.department">
                                    </div>
                                </div>
                            </div> --}}
                        </div>
                        <div class="col-md-6">
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Petty Cash Type</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="petty-cash-select" v-model="form.type" :onchange="pettyCashTypeChanged">
                                            <option :value="pettyCashType.slug" v-for="pettyCashType in pettyCashTypes" :key="pettyCashType.id">
                                                @{{ pettyCashType.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px" v-show="vehicleRepair">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Vehicle</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="vehicle-select" v-model="form.vehicle_id" :onchange="vehicleChanged">
                                            <option :value="vehicle.id" v-for="vehicle in vehicles" :key="vehicle.id">
                                                @{{ vehicle.license_plate_number }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px"  v-show="vehicleRepair">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Repair Type</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="repair-type-select" v-model="form.repair_type" :onchange="repairTypeChanged">
                                            <option value="service">Service</option>
                                            <option value="garage">Garage</option>
                                            <option value="tyres">Tyres</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Account</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="account-select" :onchange="accountChanged" v-model="form.account_id">
                                            <option :value="account.id" v-for="account in accounts" :key="account.id">
                                                @{{ `${account.account_name} (${account.account_code})` }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Amounts Are</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="tax-type-select" v-model="form.tax_type" :onchange="taxTypeChanged">
                                            <option value="exclusive" selected>Exclusive of Tax</option>
                                            <option value="inclusive">Inclusive of Tax</option>
                                            <option value="without">Without Tax</option>
                                        </select>
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
                        <button type="button" class="btn btn-danger btn-sm" style="position: fixed;bottom: 30%;left:4%;" @click="addLineItem">
                            <i class="fa fa-plus"></i>
                        </button>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th v-show="form.type == 'parking-fees'">Route</th>
                                    <th v-show="form.type != 'parking-fees'">Payee Type</th>
                                    <th>Payee Name</th>
                                    <th style="width: 150px">Payee Phone No.</th>
                                    <th style="width: 150px">Amount</th>
                                    <th style="width: 150px" v-show="form.tax_type != 'without'">VAT</th>
                                    <th v-show="form.tax_type != 'without'">CU Invoice No</th>
                                    <th>Reason for Payment</th>
                                    <th v-show="form.type == 'driver-grn'">GRN</th>
                                    <th v-show="form.type == 'driver-grn'">Transfer No.</th>
                                    <th v-show="form.type == 'travel-delivery'">Loading Schedule</th>
                                    <th style="width: 110px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(lineItem, index) in form.lineItems" :key="index">
                                    <td v-show="form.type == 'parking-fees'">
                                        <select 
                                            class="form-control" 
                                            :id="`route-${index}`"
                                            v-model="form.lineItems[index].route_id"
                                            :data-index="index"
                                            :onchange="routeChanged"
                                        >
                                            <option :value="route.id" :data-display="route.route_name" v-for="route in routes" :key="route.id">
                                                @{{ route.route_name }}
                                            </option>
                                        </select>
                                    </td>
                                    <td v-show="form.type != 'parking-fees'">
                                        <select 
                                            class="form-control" 
                                            :id="`payee-type-select-${index}`"
                                            v-model="form.lineItems[index].payee_type" 
                                            :onchange="payeeTypeChanged"
                                            :data-index="index"
                                            :disabled="payeeTypeLocked"
                                        >
                                            <option value="" selected disabled></option>
                                            <option value="employee">Employee</option>
                                            <option value="supplier">Supplier</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select 
                                            class="form-control" 
                                            :id="`employee-select-${index}`"
                                            v-model="form.lineItems[index].employee_id" 
                                            :onchange="payeeChanged"
                                            :data-index="index"
                                            v-show="form.lineItems[index].payee_type == 'employee'"
                                            :disabled="form.type == 'parking-fees'"
                                        >
                                            <option value="" selected disabled></option>
                                            <option :value="user.id" :data-display="user.name" v-for="user in users" :key="user.id">@{{ `${user.name} (${user.id_number})` }}</option>
                                        </select>
                                        <select 
                                            class="form-control" 
                                            :id="`supplier-select-${index}`"
                                            v-model="form.lineItems[index].supplier_id" 
                                            :onchange="payeeChanged"
                                            :data-index="index"
                                            v-show="form.lineItems[index].payee_type == 'supplier'"
                                        >
                                            <option value="" selected disabled></option>
                                            <option :value="supplier.id" v-for="supplier in suppliers" :key="supplier.id">@{{ supplier.name }}</option>
                                        </select>
                                        <select :id="`placeholder-select-${index}`" v-show="!form.lineItems[index].payee_type">
                                            <option value=""></option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="form.lineItems[index].phone_number">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="form.lineItems[index].amount" @keyUp="formatAmount($event, index)">
                                    </td>
                                    <td v-show="form.tax_type != 'without'">
                                        <select 
                                            class="form-control" 
                                            :id="`tax-type-${index}`"
                                            v-model="form.lineItems[index].vat_id"
                                            :data-index="index"
                                            :onchange="vatChanged"
                                        >
                                            <option :value="taxManager.id" v-for="taxManager in taxManagers" :key="taxManager.id">
                                                @{{ `${taxManager.title} (${taxManager.tax_value})` }}
                                            </option>
                                        </select>
                                    </td>
                                    <td v-show="form.tax_type != 'without'">
                                        <input type="text" class="form-control" v-model="form.lineItems[index].cu_invoice_no">
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" v-model="form.lineItems[index].payment_reason">
                                    </td>
                                    <td v-show="form.type == 'driver-grn' && !form.lineItems[index].id">
                                        <select 
                                            class="form-control" 
                                            :id="`grn-${index}`"
                                            v-model="form.lineItems[index].grn_number"
                                            :data-index="index"
                                            :onchange="grnChanged"
                                        >
                                            <option :value="pendingGrn.grn_number" :data-display="pendingGrn.grn_number" v-for="pendingGrn in pendingGrns" :key="pendingGrn.id">
                                                @{{ `${pendingGrn.grn_number} (${pendingGrn.supplier_name}) ${pendingGrn.date}` }}
                                            </option>
                                        </select>
                                    </td>
                                    <td v-show="form.type == 'driver-grn' && form.lineItems[index].id">
                                        @{{ form.lineItems[index].grn }}
                                    </td>
                                    <td v-show="form.type == 'driver-grn' && !form.lineItems[index].id">
                                        <select 
                                            class="form-control" 
                                            :id="`transfer-${index}`"
                                            v-model="form.lineItems[index].transfer_id"
                                            :data-index="index"
                                            :onchange="transferChanged"
                                        >
                                            <option :value="transfer.id" v-for="transfer in transfers" :key="transfer.id">
                                                @{{ transfer.transfer_no }}
                                            </option>
                                        </select>
                                    </td>
                                    <td v-show="form.type == 'driver-grn' && form.lineItems[index].id">
                                        @{{ form.lineItems[index].transfer_no }}
                                    </td>
                                    <td v-show="form.type == 'travel-delivery'">
                                        <select 
                                            class="form-control" 
                                            :id="`delivery-schedule-${index}`"
                                            v-model="form.lineItems[index].delivery_schedule_id"
                                            :data-index="index"
                                            :onchange="deliveryScheduleChanged"
                                        >
                                            <option :value="deliverySchedule.id" :data-display="deliverySchedule.delivery_number" v-for="deliverySchedule in deliverySchedules" :key="deliverySchedule.id">
                                                @{{ `${deliverySchedule.delivery_number} (${deliverySchedule.route_name}) ${deliverySchedule.created_at}` }}
                                            </option>
                                        </select>
                                    </td>
                                    <td class="text-right">
                                        <button type="button" class="btn btn-sm" style="background-color: #337ab7; color: white" @click="showFilesModal(lineItem)" v-if="lineItem.id">
                                            <i class="fa fa-file" aria-hidden="true"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" style="margin-left: 10px" @click="removeItem(index)">
                                            <i class="fa fa-trash" aria-hidden="true"></i>
                                        </button>
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
                                        <td style="text-align: right">@{{ numberWithCommas(totalSubTotal) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right">Vat</th>
                                        <td style="text-align: right">@{{ numberWithCommas(totalTax) }}</td>
                                    </tr>
                                    <tr>
                                        <th style="text-align: right">Total</th>
                                        <td style="text-align: right">@{{ numberWithCommas(totalWithTax) }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div style="display: flex; justify-content: space-between;">
                            <button class="btn btn-primary" :disabled="processing" @click="saveRequest">Save</button>
                            
                            <div>
                                <button type="button" class="btn btn-primary btn-sm" :disabled="processing" @click="confirmSubmit">Approve</button>
                                <button 
                                    type="button" 
                                    class="btn btn-secondary btn-sm" 
                                    style="margin-left: 10px" 
                                    :disabled="processing" 
                                    @click="confirmReject"
                                    v-if="canReject"
                                >
                                    Reject
                                </button>
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
                                    <th v-if="!pettyCashRequest.final_approval">Action</th>
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
                                    <td style="width: 5%; text-align: center;" v-if="!pettyCashRequest.final_approval">
                                        <a href="#" @click.prevent="confirmFileDelete(file.id)" style="color: red">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="form-group">
                            <label for="" class="form-label">Add Files</label>
                            <input type="file" id="modal-files" class="form-control" multiple>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="addFiles">Save Changes</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
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
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "{{ config('app.env') == 'local' ? 'https://unpkg.com/vue@3/dist/vue.esm-browser.js' : 'https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js' }}"
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
        import {createApp, onMounted, ref, computed } from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const page = "{{ $page }}"
                const pettyCashRequest = {!! $pettyCashRequest !!}
                const redirectRoute = {!! $redirectRoute !!}

                const permissions = Object.keys(user.permissions)

                const formUtil = new Form()

                const canReject = computed(() => {
                    if (page == 'Initial') {
                        return permissions.includes('petty-cash-requests-initial-approval___reject') || user.role_id == 1
                    } else if (page == 'Final') {
                        return permissions.includes('petty-cash-requests-final-approval___reject') || user.role_id == 1
                    }
                })
                
                const initSelect2 = (element) => {
                    $(element).select2({
                        placeholder: 'Select...',

                        templateResult: (data) => data.text,

                        templateSelection: (data) => $(data.element).data('display') ?? data.text
                    });
                }
                
                const form = ref({
                    id: pettyCashRequest.id,
                    user_id: user.id,
                    branch_id: pettyCashRequest.restaurant.id,
                    branch: pettyCashRequest.restaurant.name,
                    // department: pettyCashRequest.department.department_name,
                    type: pettyCashRequest.type,
                    vehicle_id: pettyCashRequest.vehicle_id ?? '',
                    repair_type: pettyCashRequest.repair_type ?? '',
                    account: pettyCashRequest.chart_of_account.account_name,
                    account_id: pettyCashRequest.wa_charts_of_account_id,
                    tax_type: pettyCashRequest.tax_type,

                    lineItems: [],
                    deletedItems: []
                })

                pettyCashRequest.petty_cash_request_items.forEach(requestItem => {
                    form.value.lineItems.push(
                        {
                            id: requestItem.id,
                            payee_type: requestItem.employee_id ? 'employee' : 'supplier',
                            employee_id: requestItem.employee_id ?? '',
                            supplier_id: requestItem.supplier_id ?? '',
                            payee_name: requestItem.employee ? requestItem.employee.name : requestItem.supplier ? requestItem.supplier.name : '',
                            phone_number: requestItem.payee_phone_no,
                            amount: numberWithCommas(requestItem.amount),
                            vat_id: requestItem.tax_manager_id,
                            vat_amount: requestItem.vat_amount,
                            sub_total: requestItem.sub_total,
                            cu_invoice_no: requestItem.cu_invoice_no,
                            payment_reason: requestItem.payment_reason,
                            delivery_schedule_id: requestItem.delivery_schedule_id,
                            grn_number: requestItem.grn_number,
                            grn: requestItem.grn?.grn_number,
                            transfer_id: requestItem.transfer_id,
                            transfer_no: requestItem.transfer?.transfer_no,
                            route_id: requestItem.route_id,
                            files: requestItem.petty_cash_request_item_files
                        }
                    )
                })

                const vehicleRepair = computed(() => {
                    return form.value.type == 'repairs-maintenance-motor-vehicle'
                })

                const payeeTypeLocked = ref(form.value.type == 'supplier-cash-payments' ? true : false)

                const pettyCashTypeChanged = (event) => {
                    let type = $(event.target).val()
                    form.value.type = type

                    if (['driver-grn', 'parking-fees', 'travel-delivery', 'travel-order-taking'].includes(type)) {
                        form.value.tax_type = 'without'
                        $('#tax-type-select').val('without').trigger('change')
                    }

                    if (type == 'supplier-cash-payments') {
                        form.value.lineItems.forEach((lineItem, index) => {
                            lineItem.payee_type = 'supplier'
                            $(`#payee-type-select-${index}`).val('supplier').trigger('change')
                        })
                        payeeTypeLocked.value = true
                    } else {
                        payeeTypeLocked.value = false
                    }

                    form.value.account_id = pettyCashTypes.value.find(pettyCashType => pettyCashType.slug == type).wa_charts_of_account_id
                    $('#account-select').val(form.value.account_id).trigger('change')

                    form.value.lineItems.forEach((lineItem, index) => {
                        $(`#grn-${index}`).val(null).trigger('change')
                        $(`#route-${index}`).val(null).trigger('change')
                        $(`#delivery-schedule-${index}`).val(null).trigger('change')
                        
                        lineItem.route_id =''
                        lineItem.grn_number =''
                        lineItem.delivery_schedule_id =''
                    })

                    $('#vehicle-select').val(null).trigger('change')
                    $('#repair-type-select').val(null).trigger('change')
                }

                const vehicleChanged = (event) => {
                    form.value.vehicle_id = $(event.target).val() ?? '';
                }

                const repairTypeChanged = (event) => {
                    form.value.repair_type = $(event.target).val() ?? '';
                }

                const accountChanged = (event) => {
                    form.value.account_id = $(event.target).val();
                }

                const payeeTypeChanged = (event) => {
                    let index = event.target.dataset.index

                    form.value.lineItems[index].payee_type = $(event.target).val()
                    
                    form.value.lineItems[index].employee_id = ''
                    $(`#employee-select-${index}`).val('').trigger('change')
                    form.value.lineItems[index].supplier_id = ''
                    $(`#supplier-select-${index}`).val('').trigger('change')
                    
                    form.value.lineItems[index].phone_number = ''

                    setTimeout(() => {
                        $(`#placeholder-select-${index}`).css('display', 'none')
                        
                        $('table select').each(function() {
                            if ($(this).css('display') !== 'none') {
                                initSelect2(this)
                            } else {
                                if ($(this).data('select2')) {
                                    $(this).select2('destroy')
                                }
                            }
                        })
                    }, 100)
                }

                const payeeChanged = (event) => {
                    let index = event.target.dataset.index
                    let value = $(event.target).val()

                    let payeeType = form.value.lineItems[index].payee_type

                    let payee = {}
                    let payeePhoneNo = ''
                    if (value) {
                        if (payeeType == 'employee') {
                            payee = users.value.find(user => user.id == value)
                            if (form.value.type != 'parking-fees') {
                                payeePhoneNo = payee.phone_number
                            }
                            form.value.lineItems[index].employee_id = value
                        } else if (payeeType == 'supplier') {
                            payee = suppliers.value.find(supplier => supplier.id == value)
                            payeePhoneNo = payee.telephone
                            form.value.lineItems[index].supplier_id = value
                        }
                    }

                    form.value.lineItems[index].payee_name = payee.name
                    if (payeePhoneNo) {
                        form.value.lineItems[index].phone_number = payeePhoneNo
                    }
                }

                const taxTypeChanged = (event) => {
                    form.value.tax_type = $(event.target).val();

                    form.value.lineItems.forEach((lineItem, index) => {
                        if (form.value.tax_type == 'without') {
                            lineItem.vat_id = ''
                            lineItem.cu_invoice_no = ''
                            
                            $(`#tax-type-${index}`).val('without').trigger('change')
                        }

                        adjustLineItemCalculations(index)
                    })
                }

                const vatChanged = (event) => {
                    let index = event.target.dataset.index

                    form.value.lineItems[index].vat_id = $(event.target).val();

                    adjustLineItemCalculations(index)
                }

                const adjustLineItemCalculations = (index) => {
                    let item = form.value.lineItems[index]
                    let amount = parseFloat(item.amount ? item.amount.replace(/,/g, '')  : 0)

                    let taxManager = taxManagers.value.find(taxManager => taxManager.id == item.vat_id)

                    if (taxManager) {
                        let taxValue = parseFloat(taxManager.tax_value)
    
                        let taxAmount = (taxValue / 100) * amount
    
                        let subTotal = 0
                        if (form.value.tax_type == 'inclusive') {
                            subTotal = amount - taxAmount
                        } else {
                            subTotal = amount
                        }
    
                        form.value.lineItems[index].vat_amount = taxAmount
                        form.value.lineItems[index].sub_total = subTotal

                    } else {
                        form.value.lineItems[index].vat_amount = 0
                        form.value.lineItems[index].sub_total = amount
                    }
                }

                const grnChanged = (event) => {
                    let index = event.target.dataset.index

                    form.value.lineItems[index].grn_number = $(event.target).val();
                }

                const transferChanged = (event) => {
                    let index = event.target.dataset.index

                    form.value.lineItems[index].transfer_id = $(event.target).val();
                }

                const deliveryScheduleChanged = (event) => {
                    let index = event.target.dataset.index

                    form.value.lineItems[index].delivery_schedule_id = $(event.target).val();
                }

                const routeChanged = (event) => {
                    let index = event.target.dataset.index
                    let routeId =  $(event.target).val()

                    if (routeId) {
                        form.value.lineItems[index].route_id = routeId
    
                        form.value.lineItems[index].payee_type = 'employee'
                        $(`#payee-type-select-${index}`).val('employee').trigger('change')

                        let route = routes.value.find(route => route.id == routeId)

                        form.value.lineItems[index].phone_number = route?.wa_customer.telephone
                        
                        let employeeId = route?.salesman_user[0]?.id
                        $(`#employee-select-${index}`).val(employeeId).trigger('change')
                        form.value.lineItems[index].employee_id = id
                    }

                }
                const addLineItem = () => {
                    form.value.lineItems.push({
                        id: '',
                        payee_type: '',
                        employee_id: '',
                        supplier_id: '',
                        payee_name: '',
                        phone_number: '',
                        amount: 0,
                        vat_id: '',
                        vat_amount: 0,
                        sub_total: 0,
                        cu_invoice_no: '',
                        payment_reason: '',
                        delivery_schedule_id: '',
                        grn_number: '',
                        transfer_id: '',
                        route_id: '',
                    })

                    setTimeout(() => {
                        $('table select').each(function() {
                            if ($(this).css('display') !== 'none') {
                                initSelect2(this)
                            } else {
                                if ($(this).data('select2')) {
                                    $(this).select2('destroy')
                                }
                            }
                        })
                    }, 100)

                }

                const removeItem = (index) => {
                    let item = form.value.lineItems[index]

                    if (item.id) {
                        form.value.deletedItems.push(item.id)
                    }

                    form.value.lineItems.splice(index, 1)
                }

                const formatAmount = (event, index) => {
                    let value = event.target.value.replace(/,/g, '')
                    
                    form.value.lineItems[index].amount = numberWithCommas(value)

                    adjustLineItemCalculations(index)
                }

                const totalTax = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.vat_amount), 0).toFixed(2)
                })

                const totalSubTotal = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.sub_total), 0).toFixed(2)
                })
                
                const totalWithTax = computed(() => {
                    return (parseFloat(totalSubTotal.value) + parseFloat(totalTax.value)).toFixed(2)
                })

                const modal = ref({
                    id: '',
                    title: '',
                    files: []
                })

                const showFilesModal = (requestItem) => {
                    modal.value.id = requestItem.id
                    modal.value.title = requestItem.payee_name
                    modal.value.files = requestItem.files
                    
                    $('#files-modal').modal('show')
                }

                const addFiles = () => {
                    let files = document.getElementById('modal-files').files
                    
                    if (!files.length) {
                        formUtil.errorMessage("Please add atleast one file")
                        return
                    }

                    let formData = new FormData()

                    for (let index in files) {
                        formData.append(`files[${index}]`, files[index])
                    }

                    processing.value = true

                    axios.post(`petty-cash-request-item-file/${modal.value.id}`, formData)
                        .then(response => {
                            formUtil.successMessage(response.data.message)
            
                            window.location.reload()
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            processing.value = false
                        })
                }

                const deleteId = ref(null)
                
                const confirmFileDelete = (id) => {
                    modalTitle.value = 'Delete File?'
                    modalMessage.value = 'Are you sure you want to delete this file?'
                    modalAction.value = 'Delete'
                    modalActionRef.value = deleteFile

                    deleteId.value = id
                    
                    $('#confirm-modal').modal('show')
                }
                
                const deleteFile = () => {
                    if (processing.value) {
                        return
                    }
                    
                    processing.value = true

                    axios.delete(`petty-cash-request-item-file/${deleteId.value}`)
                        .then(response => {

                            formUtil.successMessage(response.data.message)

                            window.location.reload()
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            processing.value = false
                        })
                }

                const submitAxios = (formData) => {
                    let uri = ''

                    if (!savingRequest.value) {
                        if (page == 'Final') {
                            uri = 'petty-cash-request-final-approve'
                        } else if (page == 'Initial') {
                            uri = 'petty-cash-request-approve'
                        }
                    } else {
                        uri = 'petty-cash-request-save'
                    }

                    
                    axios.post(uri, formData)
                        .then(response => {

                            formUtil.successMessage(response.data.message)

                            $('#confirm-modal').modal('hide')

                            if (!savingRequest.value) {
                                setTimeout(() => {
                                    window.location = redirectRoute
                                }, 3000)
                            } else {
                                savingRequest.value = false
                                processing.value = false
                            }
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            processing.value = false
                        })
                }

                const processing = ref(false)

                const modalTitle = ref('')
                const modalMessage = ref('')
                const modalAction = ref('')
                const modalActionRef = ref(null)
                const confirmSubmit = () => {
                    modalTitle.value = 'Approve Request?'
                    modalMessage.value = 'Are you sure you want to approve this request?'
                    modalAction.value = 'Approve'
                    modalActionRef.value = submitForm
                    
                    $('#confirm-modal').modal('show')
                }

                const savingRequest = ref(false)
                const saveRequest = () => {
                    savingRequest.value = true

                    submitForm()
                }
                
                const submitForm = () => {

                    if (!form.value.type) {
                        formUtil.errorMessage('Select a petty cash type')
                        return
                    }
                    
                    if (vehicleRepair.value) {
                        if (!form.value.vehicle_id) {
                            formUtil.errorMessage('Select a vehicle')
                            return
                        }

                        if (!form.value.repair_type) {
                            formUtil.errorMessage('Select a repair type')
                            return
                        }
                    }

                    if (!form.value.account_id) {
                        formUtil.errorMessage('Select an account')
                        return
                    }
                    
                    if (!form.value.lineItems.length) {
                        formUtil.errorMessage('Add atleast one payee')
                        return
                    } else {
                        let error = false
                        form.value.lineItems.forEach(lineItem => {
                            if (!lineItem.payee_name) {
                                formUtil.errorMessage('Enter payee name')
                                error = true
                                return
                            }

                            if (!lineItem.phone_number) {
                                formUtil.errorMessage(`Enter payee phone no. for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            if (!/^0\d{9}$/.test(lineItem.phone_number)) {
                                formUtil.errorMessage(`Enter a valid payee phone no. for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            if (!lineItem.amount) {
                                formUtil.errorMessage(`Enter amount for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            if (!/^[0-9]+$/.test(lineItem.amount) && !parseInt(lineItem.amount) > 0) {
                                formUtil.errorMessage(`Enter valid amount for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            if (form.value.tax_type != 'without') {
                                if (!lineItem.vat_id) {
                                    formUtil.errorMessage(`Select VAT for ${lineItem.payee_name}`)
                                    error = true
                                    return
                                }
    
                                if (!lineItem.cu_invoice_no) {
                                    formUtil.errorMessage(`Enter CU Invoice No for ${lineItem.payee_name}`)
                                    error = true
                                    return
                                }
                            }

                            if (!lineItem.payment_reason) {
                                formUtil.errorMessage(`Enter reason for payment for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            if (form.value.type == 'travel-delivery' && !lineItem.delivery_schedule_id) {
                                formUtil.errorMessage(`Select loading schedule for ${lineItem.payee_name}`)
                                error = true
                                return
                            }
                            
                            if (form.value.type == 'driver-grn' && (!lineItem.grn_number && !lineItem.transfer_id)) {
                                formUtil.errorMessage(`Select GRN or transfer no for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            if (form.value.type == 'parking-fees' && !lineItem.route_id) {
                                formUtil.errorMessage(`Select route for ${lineItem.payee_name}`)
                                error = true
                                return
                            }
                        })
                        if (error) {
                            return
                        }
                    }

                    processing.value = true

                    let formData = new FormData()

                    for (const key in form.value) {
                        if (key != 'lineItems') {
                            formData.append(key, form.value[key]);

                        } else {
                            for (let i = 0; i < form.value[key].length; i++) {
                                formData.append(`lineItems[${i}]`, JSON.stringify(form.value.lineItems[i]));
                            }

                        }
                    }

                    submitAxios(formData)
                }

                const confirmReject = () => {
                    modalTitle.value = 'Reject Request?'
                    modalMessage.value = 'Are you sure you want to reject this request?'
                    modalAction.value = 'Reject'
                    modalActionRef.value = rejectRequest
                    
                    $('#confirm-modal').modal('show')
                }

                const rejectRequest = () => {
                    if (processing.value) {
                        return
                    }
                    
                    processing.value = true
                    
                    axios.post('petty-cash-request-reject', {
                        id: form.value.id,
                        stage: page.toLowerCase()
                    })
                        .then(response => {

                            formUtil.successMessage(response.data.message)

                            $('#confirm-modal').modal('hide')

                            setTimeout(() => {
                                window.location = redirectRoute
                            }, 3000)
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const fetchData = (uri, refVariable) => {
                    axios.get(uri)
                        .then(response => refVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const accounts = ref([])
                const pendingGrns = ref([])
                const deliverySchedules = ref([])
                const taxManagers = ref([])
                const pettyCashTypes = ref([])
                const vehicles = ref([])
                const users = ref([])
                const suppliers = ref([])
                const routes = ref([])
                const transfers = ref([])
                onMounted(() => {
                    // Fetch petty cash type
                    fetchData('user-petty-cash-request-types', pettyCashTypes)

                    // Fetch expense accounts
                    fetchData('expense-accounts', accounts)
                    
                    // Fetch pending grns
                    fetchData('pending-grn-list', pendingGrns)
                        
                    // Fetch delivery schedules
                    fetchData('delivery-schedules-list', deliverySchedules)

                    // Fetch tax managers
                    fetchData('tax-managers-list', taxManagers)

                    // Fetch vehicles
                    fetchData('vehicles-list', vehicles)

                    // Fetch users
                    fetchData(`users-by-branch/${form.value.branch_id}`, users)

                    // Fetch suppliers
                    fetchData('suppliers-list', suppliers)

                    fetchData(`routes-by-branch/${form.value.branch_id}`, routes)

                    fetchData(`transfers-by-branch/${form.value.branch_id}`, transfers)

                    $('body').addClass('sidebar-collapse');
                    
                    $('select').each(function() {
                        if ($(this).css('display') !== 'none') {
                            initSelect2(this)
                        }
                    })

                })
                
                return {
                    form,
                    user,
                    addLineItem,
                    removeItem,
                    numberWithCommas,
                    processing,
                    deliverySchedules,
                    formatAmount,
                    deliveryScheduleChanged,
                    pettyCashRequest,
                    showFilesModal,
                    modal,
                    addFiles,
                    taxTypeChanged,
                    vatChanged,
                    totalWithTax,
                    totalTax,
                    totalSubTotal,
                    taxManagers,
                    pettyCashTypeChanged,
                    accountChanged,
                    accounts,
                    pettyCashTypes,
                    vehicles,
                    vehicleRepair,
                    vehicleChanged,
                    repairTypeChanged,
                    payeeTypeChanged,
                    payeeChanged,
                    users,
                    suppliers,
                    pendingGrns,
                    grnChanged,
                    confirmSubmit,
                    modalTitle,
                    modalMessage,
                    modalAction,
                    modalActionRef,
                    confirmFileDelete,
                    confirmReject,
                    routes,
                    routeChanged,
                    saveRequest,
                    permissions,
                    canReject,
                    transferChanged,
                    transfers,
                    payeeTypeLocked
                }
            }
        }).mount('#app')
    </script>
@endsection

