<template>
    <Card :cardTitle="`${payPeriod?.branch?.name ?? ''} CASUALS PAY PERIOD (${payPeriod?.start_date ?? ''} - ${payPeriod?.end_date ?? ''})`">
        
        <template #header-action>
            <div>
                <button
                    class="btn btn-info"
                    data-toggle="modal"
                    data-target="#bulk-upload-modal"
                    v-if="(permissions.includes('casuals-pay-pay-periods___upload-register') || userRole == '1') && !payPeriodClosed"
                >
                    <i class="fa fa-upload"></i>
                    Upload Register
                </button>
                <button class="btn btn-primary" style="margin-left: 10px;" @click="refreshCasualsList">
                    <i class="fas fa-arrows-rotate"></i>
                    Refresh Casuals List
                </button>
            </div>
        </template>
        
        <template v-if="!loader"> 
            <div class="row">
                <div class="col-sm-12">
                    <p v-if="!payPeriod.initial_approval || !payPeriod.final_approval">
                        <strong>APPROVAL STAGE: </strong>
                        <span class="badge bg-green" v-if="!payPeriod.initial_approval">Initial</span>
                        <span class="badge bg-green" v-else>Final</span>
                    </p>
                    <template v-if="payPeriod.initial_approval">
                        <p>
                            <strong>INITIAL APPROVER: </strong>
                            <span>{{ payPeriod.initial_approver.name }}</span>
                        </p>
                        <p>
                            <strong>INITIAL APPROVAL DATE: </strong>
                            <span>{{ dayjs(payPeriod.initial_approval_date).format('DD-MM-YYYY HH:mm:ss') }}</span>
                        </p>
                    </template>
                    <template v-if="payPeriod.final_approval">
                        <p>
                            <strong>FINAL APPROVER: </strong>
                            <span>{{ payPeriod.final_approver.name }}</span>
                        </p>
                        <p>
                            <strong>FINAL APPROVAL DATE: </strong>
                            <span>{{ dayjs(payPeriod.final_approval_date).format('DD-MM-YYYY HH:mm:ss') }}</span>
                        </p>
                    </template>

                    <button 
                        class="btn btn-primary pull-right" 
                        @click="approvePayPeriod('initial')"
                        :disabled="processing"
                        v-if="!payPeriod.initial_approval && (permissions.includes('casuals-pay-pay-periods___initial-approval') || userRole == 1)"
                    >
                        <i class="fas fa-circle-check"></i>
                        Initial Approve
                    </button>

                    <button 
                        class="btn btn-primary pull-right" 
                        @click="approvePayPeriod('final')"
                        :disabled="processing"
                        v-if="payPeriod.initial_approval && !payPeriod.final_approval && (permissions.includes('casuals-pay-pay-periods___final-approval') || userRole == 1)"
                    >
                        <i class="fas fa-circle-check"></i>
                        Final Approve
                    </button>

                    <a
                        :href="`/admin/hr/payroll/casuals-pay/pay-periods/${id}/print` "
                        class="btn btn-primary pull-right"
                        target="_blank"
                        style="margin-right: 10px;"
                    >
                        <i class="fas fa-print"></i>
                        Print
                    </a>
                </div>
            </div>

            <hr style="margin-top: 10px;">
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover nowrap" id="create_datatable_10">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>ID No.</th>
                            <th>Phone No.</th>
                            <template v-for="date in dates">
                                <th>{{ dayjs(date).format('ddd D/M') }}</th>
                            </template>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(payPeriodDetail, index) in payPeriodDetails" :key="payPeriodDetail.id">
                            <td>{{ index + 1 }}</td>
                            <td>{{ payPeriodDetail.casual.full_name }}</td>
                            <td>{{ payPeriodDetail.casual.id_no }}</td>
                            <td>{{ payPeriodDetail.casual.phone_no }}</td>
                            <template v-for="date in dates">
                                <td style="text-align: center;">
                                    <label
                                        v-if="
                                            (permissions.includes('casuals-pay-pay-periods___initial-approval') ||
                                            permissions.includes('casuals-pay-pay-periods___final-approval') ||
                                            userRole == 1) &&
                                            !payPeriodClosed
                                        "
                                    >
                                        <input
                                            type="checkbox"
                                            :disabled="dayjs(date).isAfter(dayjs())"
                                            v-model="form[index][date]"
                                            @change="updateAmount(index)"
                                        >
                                    </label>
                                    <template v-else>
                                        <span class="badge bg-green" v-if="form[index][date]">Present</span>
                                        <span class="badge" v-else>Absent</span>
                                    </template>
                                </td>
                            </template>
                            <td class="text-right">{{ numberWithCommas(form[index].amount.toFixed(2)) }}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="11" class="text-right">TOTAL</th>
                            <th class="text-right">{{ numberWithCommas(totalAmount.toFixed(2)) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <hr>

            <div class="row" v-if="!payPeriodClosed && (permissions.includes('casuals-pay-pay-periods___initial-approval') || permissions.includes('casuals-pay-pay-periods___final-approval') || userRole == 1)">
                <div class="col-md-12">
                    <button 
                        class="btn btn-primary pull-right" 
                        style="margin-left: 10px;"
                        :disabled="processing"
                        @click="saveDetails"
                    >
                        <i class="fas fa-floppy-disk"></i>
                        Save
                    </button>
                    <button class="btn btn-secondary pull-right" style="margin-left: 10px;" :disabled="processing" @click="performReset">
                        <i class="fas fa-ban"></i>
                        Reset
                    </button>
                    <button class="btn btn-primary pull-right" :disabled="processing" @click="performRefresh" v-if="!payPeriodClosed">
                        <i class="fas fa-arrows-rotate"></i>
                        Refresh
                    </button>
                </div>
            </div>
        </template>

        <Loader v-else />
    </Card>

    <div class="modal fade" id="bulk-upload-modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Upload Register</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="file" class="form-control" id="upload-file" accept=".xlsx" :onchange="documentChanged">
                        <span style="font-size: 12px; font-style: italic">No file selected</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <a :href="`/admin/hr/payroll/casuals-pay/pay-periods/${id}/register-template`" class="btn btn-warning pull-left" download>Download Template</a>
                    <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled="processing" @click="submitBulkUpload">Upload</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <Modal
        id="approval-modal"
        modalTitle="Approve Pay Period"
        :buttonText="`${lodash.upperFirst(approvalStage)} Approve`"
        :processing
        @submit-clicked="handleApproval"
    >
        <template #modal-body>
            Are you sure you want to approve the pay period?
        </template>
    </Modal>
</template>

<script setup>
import dayjs from 'dayjs'
import lodash from 'lodash'
import { computed, onMounted, ref, watch } from 'vue'
import Card from "@/components/ui/Card.vue"
import Modal from "@/components/ui/Modal.vue"
import Loader from "@/components/ui/Loader.vue"
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
    casualPay: {
        type: String,
        required: true
    }
})

const permissions = Object.keys(JSON.parse(props.userPermissions))

const initDataTable = () => {
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
        });
    })
}

const loader = ref(true)

const payPeriod = ref({})
const fetchPayPeriod = async () => {
    loader.value = true
    
    try {
        let response = await apiClient.get(`${baseUrl}/casuals-pay-period/${props.id}`)
        
        payPeriod.value = response.data

        loader.value = false

        initDataTable()
        
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

const payPeriodClosed = computed(() => payPeriod.value.status == 'closed')

const payPeriodDetails = computed(() => payPeriod.value.casuals_pay_period_details)

const dates = computed(() => {
    if (payPeriodDetails.value && payPeriodDetails.value.length) {
        return Object.keys(payPeriodDetails.value[0].dates)
    }
})

watch(() => dates.value, (dates) => {
    if (dates.length) {
        initForm()
    }
})

const form = ref([])

const initForm = () => {
    form.value = []
    
    payPeriodDetails.value.forEach(payPeriodDetail => {
        let formObject = {
            id: payPeriodDetail.id,
        }
        
        dates.value.forEach(date => {
            formObject[date] = payPeriodDetail.dates[date]
        })

        formObject['amount'] = payPeriodDetail.amount
        
        form.value.push(formObject)
    })
}

const totalAmount = computed(() => form.value.reduce((total, formItem) => total + formItem.amount, 0))

const updateAmount = (index) => {
    let daysPresent = 0
    dates.value.forEach(date => {
        for(let key in form.value[index]) {
            if (key === date && form.value[index][key]) {
                daysPresent++
            }
        }
    })

    form.value[index]['amount'] = daysPresent * parseFloat(props.casualPay)
}

const processing = ref(false)
const performReset = () => {
    form.value = []

    initForm()
}

const performRefresh = () => {
    form.value.forEach((formItem, index) => {
        updateAmount(index)
    })
}

const saveDetails = () => {
    processing.value = true

    apiClient.post(`${baseUrl}/casuals-pay-period-details/${props.id}/update`, form.value)
        .then(response => {
            formUtil.successMessage(response.data.message)
            
            processing.value = false

            fetchPayPeriod()
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const approvalStage = ref('')

const approvePayPeriod = (stage) => {
    approvalStage.value = stage

    $('#approval-modal').modal('show')
}

const handleApproval = async () => {
    processing.value = true

    apiClient.post(`${baseUrl}/casuals-pay-period-details/${props.id}/approve`, {
        stage: approvalStage.value,
        casuals_pay_period_details: form.value
    })
        .then(response => {
            formUtil.successMessage(response.data.message)
            
            processing.value = false

            fetchPayPeriod()

            $('#approval-modal').modal('hide')
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

const submitBulkUpload = () => {
    const file = document.getElementById('upload-file')

    if (!file.files.length) {
        formUtil.errorMessage('Select a file to upload')
        return
    }

    processing.value = true

    const formData = new FormData()

    formData.append('uploaded_file', file.files[0])

    apiClient.post(`${baseUrl}/casuals-pay-period-details/${props.id}/upload-register`, formData, {
        responseType: 'blob'
    })
        .then(response => {
            fetchPayPeriod()

            if (response.status == 201) {
                
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', 'casuals_pay_period_register_errors.xlsx');
                
                document.body.appendChild(link);
                link.click();

                document.body.removeChild(link);
                
                formUtil.warningMessage("Some errors were encountered during upload.")
            } else {
                formUtil.successMessage('Casuals pay period register uploaded successfully')
            }
            
            $('#bulk-upload-modal').modal('hide')

            file.value = ''
            $(file).val('').trigger('change')

            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
    
}

const refreshCasualsList = () => {
    processing.value = true

    apiClient.post(`${baseUrl}/casuals-pay-period-details/${props.id}/refresh-casuals-list`)
        .then(response => {
            fetchPayPeriod()
            
            formUtil.successMessage(response.data.message)
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

onMounted(async () => {
    $('body').addClass('sidebar-collapse');

    await fetchPayPeriod()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }

    td:has(label) {
        padding: 0 !important;
    }

    label {
        width: 100%;
        padding: 8px;
    }

</style>
