<template>
    <Card :cardTitle="`Payroll Month Details (${payrollMonth.name ?? ''})`">
        <template #header-action v-if="permissions.includes('payroll-payroll-month-details___upload-earnings-and-deductions') || userRole == 1">
            <button 
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#upload-modal"
                v-if="!payrollMonthClosed"
            >
                <i class="fa fa-upload"></i>
                Upload Earnings & Deductions
            </button>
        </template>
        
        <div class="row">
            <div class="col-md-2 form-group">
                <Select2Select 
                    :options="branches" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select Branch..." 
                    :disabled="processing"
                    v-model="branchId" 
                />
            </div>

            <div class="col-md-2 form-group">
                <Select2Select 
                    :options="jobTitles" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select job title..." 
                    :disabled="processing"
                    :select2Options="{allowClear: true}"
                    v-model="jobTitleId"  
                />
            </div>
            
            <button type="button" class="btn btn-success" :disabled="processing" @click="handleFilter">
                <i class="fas fa-filter"></i>
                Filter
            </button>

            <a 
                :href="`/admin/${baseUrl}/payroll-months/${payrollMonth.id}/paymaster-report?branch_id=${branchId == '0' ? '' : branchId}`" 
                target="_blank" class="btn btn-success pull-right" 
                :class="{'disabled' : processing}" 
                style="margin-right: 20px;"
            >
                <i class="fas fa-file-excel"></i>
                Export to Excel
            </a>

            <button type="button" class="btn btn-success pull-right" :disabled="processing" style="margin-right: 20px;" @click="processPayroll" v-if="!payrollMonthClosed">
                <i class="fas fa-file-signature"></i>
                Process Payroll
            </button>
        </div>

        <hr style="margin-top: 0;">

        <div class="table-responsive" v-if="!loader">
            <table class="table table-bordered table-hover nowrap" id="create_datatable_10">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Employee Name</th>
                        <th>Employee No.</th>
                        <th>ID No.</th>
                        <th>Branch</th>
                        <th>Basic Pay</th>
                        <template v-for="earning in earnings" :key="earning.id">
                            <th>{{ earning.name }}</th>
                        </template>
                        <th>Gross Pay</th>
                        <th>NSSF</th>
                        <th>Taxable Pay</th>
                        <th>PAYE</th>
                        <th>SHIF</th>
                        <th>Housing Levy</th>
                        <template v-for="deduction in deductions" :key="deduction.id">
                            <th>{{ deduction.name }}</th>
                        </template>
                        <th>Net Pay</th>
                        <th class="noneedtoshort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(payrollMonthDetail, index) in payrollMonthDetails" :key="payrollMonthDetail.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ payrollMonthDetail.employee.full_name }}</td>
                        <td>{{ payrollMonthDetail.employee.employee_no }}</td>
                        <td>{{ payrollMonthDetail.employee.id_no }}</td>
                        <td>{{ payrollMonthDetail.employee.branch.name }}</td>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.basic_pay.toFixed(2)) }}</td>
                        <template v-for="earning in earnings" :key="earning.id">
                            <td class="text-right">{{ numberWithCommas(getEarningAmount(payrollMonthDetail.id, earning.id).toFixed(2)) }}</td>
                        </template>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.gross_pay.toFixed(2)) }}</td>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.nssf.toFixed(2)) }}</td>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.taxable_pay.toFixed(2)) }}</td>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.paye.toFixed(2)) }}</td>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.shif.toFixed(2)) }}</td>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.housing_levy.toFixed(2)) }}</td>
                        <template v-for="deduction in deductions" :key="deduction.id">
                            <td class="text-right">{{ numberWithCommas(getDeductionAmount(payrollMonthDetail.id, deduction.id).toFixed(2)) }}</td>
                        </template>
                        <td class="text-right">{{ numberWithCommas(payrollMonthDetail.net_pay.toFixed(2)) }}</td>
                        <td style="text-align: center; color: #337ab7;">
                            <a href="javascript:void(0)" @click="showEditModal(payrollMonthDetail)" v-if="canEditPayrollMonthDetail && !payrollMonthClosed">
                                <i class="fas fa-edit" title="Edit"></i>
                            </a>
                            <a :href="`/admin/hr/payroll/payroll-month-detail/${payrollMonthDetail.id}/payslip`" target="_blank" style="margin-left: 10px;"v-if="canViewPayslip">
                                <i class="fas fa-file-invoice" title="Payslip"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="5" class="text-right">TOTALS</th>
                        <th class="text-right">{{ getTotal('basic_pay') }}</th>
                        <template v-for="earning in earnings" :key="earning.id">
                            <th class="text-right">{{ getEarningTotal(earning.id) }}</th>
                        </template>
                        <th class="text-right">{{ getTotal('gross_pay') }}</th>
                        <th class="text-right">{{ getTotal('nssf') }}</th>
                        <th class="text-right">{{ getTotal('taxable_pay') }}</th>
                        <th class="text-right">{{ getTotal('paye') }}</th>
                        <th class="text-right">{{ getTotal('shif') }}</th>
                        <th class="text-right">{{ getTotal('housing_levy') }}</th>
                        <template v-for="deduction in deductions" :key="deduction.id">
                            <th class="text-right">{{ getDeductionTotal(deduction.id) }}</th>
                        </template>
                        <th class="text-right">{{ getTotal('net_pay') }}</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <Loader v-else />
    </Card>

    <Modal
        id="payroll-details-modal"
        modalClass="modal-lg"
        modalTitle="Edit Earnings and Deductions"
        buttonText="Save Changes"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                <div class="col-sm-6">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="2">
                                    Earnings
                                    <i class="fas fa-plus"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Basic Pay</td>
                                <td>
                                    <AmountInput v-model="form.basic_pay" />
                                </td>
                            </tr>
                            <tr v-for="(earning, index) in earnings" :key="earning.id">
                                <td>{{ earning.name }}</td>
                                <td>
                                    <AmountInput v-model="form.earnings[index].amount" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right">Total</th>
                                <th>{{ numberWithCommas(modalTotalEarnings) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="col-sm-6">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th colspan="2">
                                    Deductions
                                    <i class="fas fa-minus"></i>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="(deduction, index) in deductions" :key="deduction.id">
                                <td>{{ deduction.name }}</td>
                                <td>
                                    <AmountInput v-model="form.deductions[index].amount" />
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="text-right">Total</th>
                                <th>{{ numberWithCommas(modalTotalDeductions) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </template>
    </Modal>

    <Modal
        id="upload-modal"
        modalTitle="Upload Earnings & Deductions"
        buttonText="Upload"
        :processing
        @submit-clicked="handleUpload"
    >
        <template #modal-body>
            <div class="form-group">
                <label for="">Earning/Deduction</label>
                <Select2Select 
                    :options="earningsAndDeductions" 
                    optionValue="code" 
                    optionLabel="name" 
                    placeholder="Select earning or deduction..." 
                    :disabled="processing"
                    v-model="earningAndDeductionCode" 
                />
            </div>

            <div class="form-group">
                <a 
                    :href="`/admin/hr/payroll/payroll-months/${id}/earnings-and-deductions-template?ed_code=${earningAndDeductionCode}`" 
                    class="btn btn-warning btn-sm" 
                    :class="{'disabled': !earningAndDeductionCode}"
                >
                    Download Template
                </a>
            </div>

            <div class="form-group">
                <label for="">Import File</label>
                <input type="file" class="form-control" id="upload-file" accept=".xlsx" :onchange="documentChanged">
                <span style="font-size: 12px; font-style: italic">No file selected</span>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Card from "@/components/ui/Card.vue"
import Loader from "@/components/ui/Loader.vue"
import Modal from "@/components/ui/Modal.vue"
import Select2Select from "@/components/ui/form/Select2Select.vue"
import AmountInput from '@/components/ui/form/AmountInput.vue'
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'
import { numberWithCommas } from '@/utils.js'

const { apiClient } = useApi()

const baseUrl = 'hr/payroll'

const props = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
    id: {
        type: String,
        required: true
    },
    branches: {
        type: String,
        required: true
    },
    jobTitles: {
        type: String,
        required: true
    },
    earnings: {
        type: String,
        required: true
    },
    deductions: {
        type: String,
        required: true
    },
})

const branches = JSON.parse(props.branches)
branches.unshift({
    id: 0,
    name: 'ALL'
})

const jobTitles = JSON.parse(props.jobTitles)
const earnings = JSON.parse(props.earnings)
const deductions = JSON.parse(props.deductions)
const permissions = Object.keys(JSON.parse(props.userPermissions))

const earningsAndDeductions = computed(() => {
    return [
        ...earnings.filter(earning => earning.name == 'Leave Pay'),
        ...deductions
    ].map(earningAndDeduction => {
        return {
            name: earningAndDeduction.name,
            code: earningAndDeduction.code,
        };
    });
});


const canEditPayrollMonthDetail = computed(() => permissions.includes('payroll-payroll-month-details___edit') || props.userRole == 1)

const canViewPayslip = computed(() => permissions.includes('payroll-payroll-month-details___view-payslip') || props.userRole == 1)

const payrollMonthDetails = computed(() => payrollMonth.value.payroll_month_details)

const getTotal = (key) => {
    return numberWithCommas(payrollMonthDetails.value.reduce((sum, detail) => sum + detail[key], 0).toFixed(2))
}

const getEarningTotal = (id) => {
    return numberWithCommas(
        payrollMonthDetails.value.map(payrollMonthDetail => {
            let earning = payrollMonthDetail.earnings.find(earning => earning.earning_id == id)

            if (earning) {
                return earning.amount
            } else {
                return 0
            }
        })
            .reduce((sum, amount) => sum + amount, 0).toFixed(2)
    )
}

const getDeductionTotal = (id) => {
    return numberWithCommas(
        payrollMonthDetails.value.map(payrollMonthDetail => {
            let deduction = payrollMonthDetail.deductions.find(deduction => deduction.deduction_id == id)

            if (deduction) {
                return deduction.amount
            } else {
                return 0
            }
        })
            .reduce((sum, amount) => sum + amount, 0).toFixed(2)
    )
}

const getEarningAmount = (detailId, earningId) => {
    return payrollMonthDetails.value.find(payrollMonthDetail => payrollMonthDetail.id == detailId)
        .earnings.find(earning => earning.earning_id == earningId)?.amount ?? 0
}

const getDeductionAmount = (detailId, deductionId) => {
    return payrollMonthDetails.value.find(payrollMonthDetail => payrollMonthDetail.id == detailId)
        .deductions.find(deduction => deduction.deduction_id == deductionId)?.amount ?? 0
}

const form = ref({
    id: '',
    basic_pay: '',
    earnings: [],
    deductions: [],
})

const clearForm = () => {
    form.value = {
        id: '',
        basic_pay: '',
        earnings: [],
        deductions: [],
    }
}

const initForm = () => {
    earnings.forEach((earning => {
        form.value.earnings.push({
            id: earning.id,
            amount: ''
        })
    }))


    deductions.forEach((deduction => {
        form.value.deductions.push({
            id: deduction.id,
            amount: ''
        })
    }))
}

initForm()

const modalTotalEarnings = computed(() => {
    return parseFloat(form.value.basic_pay) + 
        form.value.earnings.reduce((sum, earning) => {
            let amount = !isNaN(parseFloat(earning.amount)) ? parseFloat(earning.amount) : 0;
            return sum + amount
        }, 0)
})
const modalTotalDeductions = computed(() => {
    return form.value.deductions.reduce((sum, deduction) => {
        let amount = !isNaN(parseFloat(deduction.amount)) ? parseFloat(deduction.amount) : 0;
        return sum + amount
    }, 0)
})

const showEditModal = (payrollMonthDetail) => {
    clearForm()
    
    initForm()
    
    form.value.id = payrollMonthDetail.id
    form.value.basic_pay = payrollMonthDetail.basic_pay
    
    payrollMonthDetail.earnings.forEach(earning => {
        let formEarning = form.value.earnings.find(formEarning => formEarning.id == earning.earning_id)

        if (formEarning) {
            formEarning.amount = earning.amount
        }
    })

    payrollMonthDetail.deductions.forEach(deduction => {
        let formDeduction = form.value.deductions.find(formDeduction => formDeduction.id == deduction.deduction_id)

        if (formDeduction) {
            formDeduction.amount = deduction.amount
        }
    })

    $('#payroll-details-modal').modal('show')
}

const handleSubmit = () => {
    processing.value = true

    apiClient.post(`${baseUrl}/payroll-month-detail-edit/${form.value.id}`, form.value)
        .then(response => {
            fetchPayrollMonth()
            
            formUtil.successMessage(response.data.message)

            $('#payroll-details-modal').modal('hide')
            
            processing.value = false
            
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const branchId = ref(0)
const jobTitleId = ref('')

const processing = ref(false)

const handleFilter = async () => {
    loader.value = true
    processing.value = true
    
    await fetchPayrollMonth(branchId.value, jobTitleId.value)
    
    loader.value = false
    processing.value = false
}

const loader = ref(true)

const payrollMonth = ref({})
const fetchPayrollMonth = async (branchId = '', jobTitleId = '') => {
    loader.value = true
    
    try {
        let response = await apiClient.get(`${baseUrl}/payroll-month/${props.id}`, {
            params: {
                'branch_id': branchId == '0' ? '' : branchId,
                'job_title_id': jobTitleId,
            }
        })
        
        payrollMonth.value = response.data

        loader.value = false

        $(document).ready(function() {
            $('#create_datatable_10').DataTable({
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
                    if (total_record < 11) {
                        $('.dataTables_paginate').hide();
                    }
                },
                'aoColumnDefs': [{
                    'bSortable': false,
                    'aTargets': 'noneedtoshort'
                }],
                'scrollX': true,
                // 'scrollY': '100vh',
            });
        })
        
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

const payrollMonthClosed = computed(() => payrollMonth.value.status == 'closed')

const processPayroll = async () => {
    loader.value = true
    processing.value = true
    
    try {
        let response = await apiClient.get(`${baseUrl}/process-payroll/${props.id}`, {
            responseType: 'blob',
            params: {
                'branch_id': branchId.value == '0' ? '' : branchId.value
            }
        })

        const url = window.URL.createObjectURL(new Blob([response.data]));
        const link = document.createElement('a');
        link.href = url;

        let filename = dayjs(payrollMonth.value.start_date).format('MMMM_YYYY') + '_Payroll_Summary.xlsx';

        link.setAttribute('download', filename);
        
        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        
        formUtil.successMessage("Payroll processed successfully.")

        processing.value = false

        fetchPayrollMonth()
        
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
        loader.value = false
        processing.value = false
    }
}

const earningAndDeductionCode = ref('')

const handleUpload = () => {
    const file = document.getElementById('upload-file')

    if (!earningAndDeductionCode.value) {
        formUtil.errorMessage('Select an earning or deduction')
        return
    }
    
    if (!file.files.length) {
        formUtil.errorMessage('Select a file to upload')
        return
    }

    processing.value = true

    const formData = new FormData()

    formData.append('uploaded_file', file.files[0])

    apiClient.post(`hr/payroll/payroll-months/${props.id}/earnings-and-deductions-upload?ed_code=${earningAndDeductionCode.value}`, formData, {
        responseType: 'blob'
    })
        .then(response => {
            fetchPayrollMonth(branchId.value, jobTitleId.value)

            if (response.status == 201) {
                
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', 'payroll_month_earnings_deductions_upload_errors.xlsx');
                
                document.body.appendChild(link);
                link.click();

                document.body.removeChild(link);
                
                formUtil.warningMessage("Some errors were encountered during upload.")
            } else {
                formUtil.successMessage('Earning/Deduction uploaded successfully')
            }
            
            $('#upload-modal').modal('hide')

            file.value = ''
            $(file).val('').trigger('change')

            earningAndDeductionCode.value = ''

            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const documentChanged = (event) => {
    let fileInput = event.target

    if (fileInput.files.length) {
        fileInput.nextElementSibling.innerText = fileInput.files[0].name
    } else {
        fileInput.nextElementSibling.innerText = 'No file selected'
    }
}

onMounted(async () => {
    $('body').addClass('sidebar-collapse');

    await fetchPayrollMonth()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }

    .modal table thead th {
        text-transform: uppercase;
    }

    .modal table tbody td {
        padding: 5px
    }

    .disabled {
        pointer-events: none;
        cursor: not-allowed;
    }

</style>
