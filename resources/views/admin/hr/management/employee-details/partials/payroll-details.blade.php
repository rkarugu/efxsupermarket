<div style="padding:10px" id="payroll-details-app">
    <div v-cloak>
        <div class="row">
            <div class="form-group col-md-4">
                <label for="pin-no">Pin No. <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="pin-no" placeholder="Enter pin no" v-model="form.pin_no">
            </div>
            <div class="form-group col-md-4">
                <label for="nssf-no">NSSF No. <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="nssf-no" placeholder="Enter NSSF no." v-model="form.nssf_no">
            </div>
            <div class="form-group col-md-4">
                <label for="nhif-no">NHIF No. <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="nhif-no" placeholder="Enter NHIF no." v-model="form.nhif_no">
            </div>
            <div class="form-group col-md-4">
                <label for="helb-no">HELB No.</label>
                <input type="text" class="form-control" id="helb-no" placeholder="Enter HELB no." v-model="form.helb_no">
            </div>
            <div class="form-group col-md-4">
                <label for="basic-pay">Basic Pay <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="basic-pay" placeholder="Enter basic pay" v-model="form.basic_pay" @keyUp="formatAmount($event)">
            </div>
            <div class="form-group col-md-4">
                <label for="payment-mode">Payment Mode <span style="color: red">*</span></label>
                <select class="form-control" id="payment-mode" v-model="form.payment_mode_id" data-key="payment_mode_id" :onchange="setFormValue">
                    <option :value="paymentMode.id" v-for="paymentMode in paymentModes">@{{ paymentMode.name }}</option>
                </select>
            </div>
            <div class="form-group col-md-4" v-show="selectedPaymentMode == 'bank'">
                <label for="bank">Bank <span style="color: red">*</span></label>
                <select class="form-control" id="bank" v-model="form.bank_id" data-key="bank_id" :onchange="setFormValue">
                    <option :value="bank.id" v-for="bank in banks">@{{ bank.name }}</option>
                </select>
            </div>
            <div class="form-group col-md-4" v-show="selectedPaymentMode == 'bank'">
                <label for="bank-branch">Bank Branch <span style="color: red">*</span></label>
                <select class="form-control" id="bank-branch" v-model="form.bank_branch_id" data-key="bank_branch_id" :onchange="setFormValue">
                    <option :value="bankBranch.id" v-for="bankBranch in bankBankBranches" :key="bankBranch.id">@{{ bankBranch.name }}</option>
                </select>
            </div>
            <div class="form-group col-md-4" v-show="selectedPaymentMode == 'bank'">
                <label for="account-name">Account Name <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="account-name" placeholder="Enter account name" v-model="form.account_name">
            </div>
            <div class="form-group col-md-4" v-show="selectedPaymentMode == 'bank'">
                <label for="account-number">Account No. <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="account-number" placeholder="Enter account number" v-model="form.account_no">
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" v-model="form.inclusive_of_house_allowance">
                        Basic pay inclusive of house allowance
                    </label>
                </div>
            </div>
        </div>
        
        <hr>
    
        <div style="text-align: right">
            <button class="btn btn-primary" :disabled="processing" @click="submitForm">
                <i class="fa fa-floppy-disk"></i>
                Save
            </button>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import { createApp, ref, computed, onMounted, watch } from 'vue';

        createApp({
            setup() {
                const employee = {!! $employee !!}

                const formUtil = new Form()

                const form = ref({
                    id: employee.id,
                    pin_no: employee.pin_no ?? '',
                    nssf_no: employee.nssf_no ?? '',
                    nhif_no: employee.nhif_no ?? '',
                    helb_no: employee.helb_no ?? '',
                    basic_pay: numberWithCommas(employee.basic_pay) ?? '',
                    payment_mode_id: employee.payment_mode_id ?? '',
                    bank_id: employee.primary_bank_account?.bank_id ?? '',
                    bank_branch_id: employee.primary_bank_account?.bank_branch_id ?? '',
                    account_name: employee.primary_bank_account?.account_name ?? '',
                    account_no: employee.primary_bank_account?.account_no ?? '',
                    inclusive_of_house_allowance: employee.inclusive_of_house_allowance,
                    
                })

                const bankBankBranches = computed(() => {
                    return bankBranches.value.filter(bankBranch => bankBranch.bank_id == form.value.bank_id)
                })

                const setFormValue = (event) => {
                    let key = event.target.dataset.key

                    form.value[key] = $(event.target).val()
                }

                const formatAmount = (event) => {
                    let value = event.target.value.replace(/,/g, '')
                    
                    form.value.basic_pay = numberWithCommas(value)
                }

                watch(() => form.value.payment_mode_id, () => {
                    form.value.bank_id = ''
                    form.value.bank_branch_id = ''
                    form.value.account_name = ''
                    form.value.account_no = ''

                    $('#bank').val('').trigger('change')
                    $('#bank-branch').val('').trigger('change')
                })

                const paymentModes = ref([])
                const selectedPaymentMode = computed(() => {
                    return paymentModes.value.find(paymentMode => paymentMode.id == form.value.payment_mode_id)?.slug ?? ''
                })

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.pin_no) {
                        formUtil.errorMessage('Enter PIN no.')
                        return
                    }
                    
                    if (!form.value.nssf_no) {
                        formUtil.errorMessage('Enter NSSF no.')
                        return
                    }

                    if (!form.value.nhif_no) {
                        formUtil.errorMessage('Enter NHIF no.')
                        return
                    }

                    if (!form.value.basic_pay) {
                        formUtil.errorMessage('Enter basic pay')
                        return
                    }

                    if (!form.value.bank_id) {
                        formUtil.errorMessage('Select bank')
                        return
                    }

                    if (!form.value.bank_branch_id) {
                        formUtil.errorMessage('Select bank branch')
                        return
                    }
                    
                    if (!form.value.account_name) {
                        formUtil.errorMessage('Enter account name')
                        return
                    }

                    if (!form.value.account_no) {
                        formUtil.errorMessage('Enter account no.')
                        return
                    }
                    
                    processing.value = true

                    const formData = new FormData()
                    
                    for (const key in form.value) {
                        formData.append(key, form.value[key]);

                        if (key == 'basic_pay') {
                            formData.append(key, form.value[key].replace(/,/g, ''))
                        }
                    }

                    axios.post(`hr/management/employees-edit`, formData)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            processing.value = false

                            window.location.reload()
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const fetchData = (uri, referenceVariable) => {
                    axios.get(uri)
                        .then(response => referenceVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.message))
                }

                const banks = ref([])
                const bankBranches = ref([])
                onMounted(() => {
                    // Fetch banks list
                    fetchData(`hr/configurations/bank-list`, banks)

                    // Fetch bank branches list
                    fetchData(`hr/configurations/bank-branch-list`, bankBranches)

                    // Fetch payment modes list
                    fetchData(`hr/configurations/payment-mode-list`, paymentModes)


                    $('#payroll-details-app select').select2({
                        placeholder: 'Select...'
                    })
                })

                return {
                    processing,
                    form, 
                    submitForm,
                    setFormValue,
                    formatAmount,
                    banks,
                    bankBankBranches,
                    paymentModes,
                    selectedPaymentMode
                }
            }
        }).mount('#payroll-details-app')
    </script>
@endpush