@extends('layouts.admin.admin')

@section('content')
    <div id="app" v-cloak>
        <section class="content" style="padding-bottom: 0">
            <div class="box box-primary" style="margin-bottom: 10px">
                <div class="box-header with-border">
                    <h3 class="box-title">Petty Cash Request</h3>
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
                                        <select class="form-control" :disabled="!permissions.includes('petty-cash-requests-request___change-branch') && user.role_id != 1" v-model="form.branch_id" :onchange="branchChanged">
                                            <option :value="branch.id" :selected="branch.id == user.restaurant_id" v-for="branch in branches" :key="branch.id">
                                                @{{ branch.name }}
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="form-group">
                                    <label for="inputEmail3" class="col-sm-5 control-label">Department</label>
                                    <div class="col-sm-7">
                                        <select class="form-control" id="department-select" :disabled="!permissions.includes('petty-cash-requests-request___change-branch') && user.role_id != 1" v-model="form.department_id" :onchange="departmentChanged">
                                            <option :value="department.id" v-for="department in departments" :key="department.id">
                                                @{{ department.department_name }}
                                            </option>
                                        </select>
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
                                    <label for="inputEmail3" class="col-sm-5 control-label">Expense Account</label>
                                    <div class="col-sm-6">
                                        <select class="form-control" id="account-select" :disabled="user.role_id != 1" :onchange="accountChanged" v-model="form.account_id">
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
                                    <th v-show="form.tax_type != 'without'">VAT</th>
                                    <th v-show="form.tax_type != 'without'">CU Invoice No</th>
                                    <th>Reason for Payment</th>
                                    <th style="width: 150px">Attach Receipts/Docs</th>
                                    <th v-show="form.type == 'driver-grn'">GRN</th>
                                    <th v-show="form.type == 'driver-grn'">Transfer No.</th>
                                    <th v-show="form.type == 'travel-delivery'">Loading Schedule</th>
                                    <th style="width: 50px"></th>
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
                                    <td>
                                        <input type="file" class="form-control" :id="`files-${index}`" multiple>
                                    </td>
                                    <td v-show="form.type == 'driver-grn'">
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
                                    <td v-show="form.type == 'driver-grn'">
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
                                        <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)">
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
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" :disabled="processing" @click="submitForm">Process</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
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
        import { createApp, onMounted, ref, computed } from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}

                const permissions = Object.keys(user.permissions)

                const formUtil = new Form()

                const initSelect2 = (element) => {
                    $(element).select2({
                        placeholder: 'Select...',

                        templateResult: (data) => data.text,

                        templateSelection: (data) => $(data.element).data('display') ?? data.text
                    });
                }
                
                const form = ref({
                    user_id: user.id,
                    branch_id: user.restaurant_id ?? '',
                    department_id: '',
                    type: '',
                    vehicle_id: '',
                    repair_type: '',
                    account_id: '',
                    tax_type: 'exclusive',

                    lineItems: [
                        {
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
                            route_id: '',
                            transfer_id: '',
                        }
                    ]
                })

                const vehicleRepair = computed(() => {
                    return form.value.type == 'repairs-maintenance-motor-vehicle'
                })

                const payeeTypeLocked = ref(false)

                const branchChanged = (event) => {
                    form.value.branch_id = $(event.target).val()

                    // fetchDepartments()
                    
                    fetchUsers()
                    
                    fetchRoutes()
                    
                    fetchTransfers()

                    if (form.value.type == 'parking-fees') {
                        form.value.lineItems.forEach((lineItem, index) => {
                            lineItem.route_id =''

                            $(`#route-${index}`).val(null).trigger('change')
                        })
                    }

                    form.value.lineItems.forEach((lineItem, index) => {
                        lineItem.payee_type =''

                        $(`#payee-type-select-${index}`).val('').trigger('change')
                    })
                }

                const departmentChanged = (event) => {
                    form.value.department_id = $(event.target).val();
                }
                
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

                    form.value.account_id = pettyCashTypes.value.find(pettyCashType => pettyCashType.slug == type)?.wa_charts_of_account_id
                    $('#account-select').val(form.value.account_id).trigger('change')

                    form.value.lineItems.forEach((lineItem, index) => {
                        $(`#grn-${index}`).val(null).trigger('change')
                        $(`#transfer-${index}`).val(null).trigger('change')
                        $(`#route-${index}`).val(null).trigger('change')
                        $(`#delivery-schedule-${index}`).val(null).trigger('change')
                        
                        lineItem.route_id =''
                        lineItem.transfer_id =''
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
                        if (form.value.lineItems[index].payee_type) {
                            $(`#placeholder-select-${index}`).css('display', 'none')
                        }
                        
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

                        form.value.lineItems[index].delivery_schedule_id = deliverySchedules.value.find(deliverySchedule => deliverySchedule.route_id == routeId)?.id
                        form.value.lineItems[index].phone_number = route?.wa_customer.telephone
                        
                        let employeeId = route?.salesman_user[0]?.id
                        $(`#employee-select-${index}`).val(employeeId).trigger('change')
                        form.value.lineItems[index].employee_id = employeeId
                    }

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
    
                        let taxAmount = (amount * taxValue) / (taxValue + 100);
    
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

                const addLineItem = () => {
                    form.value.lineItems.push({
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
                        route_id: '',
                        transfer_id: '',
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

                const processing = ref(false)
                const submitForm = () => {
                    if (!form.value.branch_id) {
                        formUtil.errorMessage('Select a branch')
                        return
                    }

                    // if (!$('#department-select').val()) {
                    //     formUtil.errorMessage('Select a department')
                    //     return
                    // }

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
                        form.value.lineItems.forEach((lineItem, index) => {
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

                            if (form.value.type == 'driver-grn') {
                                if (!lineItem.grn_number && !lineItem.transfer_id) {
                                    formUtil.errorMessage(`Select GRN or transfer no for ${lineItem.payee_name}`)
                                    error = true
                                    return
                                } else if (lineItem.grn_number && lineItem.transfer_id) {
                                    formUtil.errorMessage(`Select only GRN or transfer no for ${lineItem.payee_name}`)
                                    error = true
                                    return
                                }
                            }

                            if (form.value.type == 'parking-fees' && !lineItem.route_id) {
                                formUtil.errorMessage(`Select route for ${lineItem.payee_name}`)
                                error = true
                                return
                            }

                            let files = document.getElementById(`files-${index}`).files

                            if (!files.length) {
                                formUtil.errorMessage(`Attach atleast one file for ${lineItem.payee_name}`)
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
                                
                                let files = document.getElementById(`files-${i}`).files
                                if (files.length) {
                                    for (let index in files) {
                                        formData.append(`files${i}[${index}]`, files[index])
                                    }
                                }
                            }

                        }
                    }

                    axios.post('petty-cash-request-create', formData)
                        .then(response => {

                            formUtil.successMessage(response.data.message)

                            setTimeout(() => {
                                window.location.reload()
                            }, 1000)
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                        
                }

                const departments = ref([]);
                const fetchDepartments = () => {
                    form.value.department_id = ''
                    $('#department-select').val('')
                    
                    axios.get(`departments-by-branch/${form.value.branch_id}`)
                        .then(response => {
                            departments.value = response.data

                            let department = departments.value.find(department => department.id == user.wa_department_id)

                            form.value.department_id = department ? department.id : ''
                            if (department) {
                                $('#department-select').val(form.value.department_id)
                            }

                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                        })
                }

                const fetchData = (uri, refVariable) => {
                    axios.get(uri)
                        .then(response => refVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }
                
                const routes = ref([])
                const fetchRoutes = () => {
                    fetchData(`routes-by-branch/${form.value.branch_id}`, routes)
                }

                const fetchUsers = () => {
                    fetchData(`users-by-branch/${form.value.branch_id}`, users)
                }

                const transfers = ref([])
                const fetchTransfers = () => {
                    fetchData(`transfers-by-branch/${form.value.branch_id}`, transfers)
                }
                    
                const branches = ref([])
                const accounts = ref([])
                const deliverySchedules = ref([])
                const pendingGrns = ref([])
                const taxManagers = ref([])
                const pettyCashTypes = ref([])
                const vehicles = ref([])
                const users = ref([])
                const suppliers = ref([])
                onMounted(() => {
                    // Fetch branches
                    fetchData('user-branches', branches)
                    
                    // Fetch departments
                    // fetchDepartments()
                    
                    // Fetch petty cash types
                    fetchData('user-petty-cash-request-types', pettyCashTypes)

                    // Fetch delivery schedules
                    fetchData('delivery-schedules-list', deliverySchedules)

                    // Fetch pending grns
                    fetchData('pending-grn-list', pendingGrns)

                    // Fetch tax managers
                    fetchData('tax-managers-list', taxManagers)

                    // Fetch vehicles
                    fetchData('vehicles-list', vehicles)

                    // Fetch suppliers
                    fetchData('suppliers-list', suppliers)
                    
                    // Fetch expense accounts
                    fetchData('expense-accounts', accounts)
                    
                    fetchRoutes()

                    fetchUsers()
                    
                    fetchTransfers()

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
                    branches,
                    addLineItem,
                    removeItem,
                    submitForm,
                    numberWithCommas,
                    processing,
                    branchChanged,
                    departments,
                    accounts,
                    deliverySchedules,
                    pettyCashTypeChanged,
                    accountChanged,
                    formatAmount,
                    departmentChanged,
                    deliveryScheduleChanged,
                    totalWithTax,
                    totalSubTotal,
                    totalTax,
                    taxTypeChanged,
                    taxManagers,
                    vatChanged,
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
                    routes,
                    routeChanged,
                    transferChanged,
                    transfers,
                    permissions,
                    payeeTypeLocked
                }
            }
        }).mount('#app')
    </script>
@endsection

