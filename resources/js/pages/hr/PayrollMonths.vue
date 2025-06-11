<template>
    <Card cardTitle="Payroll Months">
        <template #header-action>
            <button
                class="btn btn-primary"
                data-toggle="modal" 
                data-target="#payroll-month-modal" 
                @click="clearForm"
                v-if="permissions.includes('payroll-payroll-months___create') || userRole == 1"
            >
                <i class="fa fa-book-open"></i>
                Open Payroll Month
            </button>
        </template>

        <div class="table-responsive" v-if="!loader">
            <table class="table table-bordered table-hover" id="create_datatable_10">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th class="noneedtoshort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(payrollMonth, index) in payrollMonths" :key="payrollMonth.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ payrollMonth.name }}</td>
                        <td>{{ payrollMonth.start_date }}</td>
                        <td>{{ payrollMonth.end_date }}</td>
                        <td style="text-align: center" >
                            <span class="badge bg-green" v-if="payrollMonth.status == 'open'">Open</span>
                            <span class="badge bg-red" v-if="payrollMonth.status == 'closed'">Closed</span>
                        </td>
                        <td style="text-align: center; color: #337ab7;">
                            <a :href="`/admin/hr/payroll/payroll-months/${payrollMonth.id}/details`" v-if="canViewPayrollMonth">
                                <i class="fas fa-eye" title="View"></i>
                            </a>
                            <a 
                                href="javascript:void(0)" 
                                style="margin-left: 10px;"
                                @click="closePayrollMonth(payrollMonth.id)" 
                                v-if="canClosePayrollMonth"
                            >
                                <i class="fas fa-lock" title="Close"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Loader v-else />

        <Modal 
            id="payroll-month-modal" 
            modalTitle="Open Payroll Month" 
            buttonText="Open Payroll Month"
            :processing
            @submit-clicked="handleSubmit"
        >
            <template #modal-body>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="month">Month <span style="color: red">*</span></label>
                        <Select2Select 
                            :options="months" 
                            placeholder="Select month..." 
                            :select2Options="{minimumResultsForSearch: Infinity}" 
                            v-model="form.month" 
                        />
                    </div>
                    <div class="form-group col-md-12">
                        <label for="year">Year <span style="color: red">*</span></label>
                        <Select2Select 
                            :options="years" 
                            placeholder="Select year..." 
                            :select2Options="{minimumResultsForSearch: Infinity}" 
                            v-model="form.year" 
                        />
                    </div>
                </div>
            </template>
        </Modal>

        <Modal 
            id="close-modal" 
            modalTitle="Close Payroll Month" 
            buttonText="Close Payroll Month"
            :processing
            @submit-clicked="handleClose"
        >
            <template #modal-body>
                <div>
                    Are you sure you want to close this payroll month
                </div>
            </template>
        </Modal>

    </Card>
</template>

<script setup>
import dayjs from 'dayjs'
import { computed, onMounted, ref } from 'vue'
import Card from "@/components/ui/Card.vue"
import Modal from "@/components/ui/Modal.vue"
import Loader from "@/components/ui/Loader.vue"
import Select2Select from "@/components/ui/form/Select2Select.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'
const { apiClient } = useApi()

const props = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
})

const permissions = Object.keys(JSON.parse(props.userPermissions))

const canViewPayrollMonth = computed(() => permissions.includes('payroll-payroll-months___details') || props.userRole == 1)
const canClosePayrollMonth = computed(() => permissions.includes('payroll-payroll-months___close') || props.userRole == 1)

const months = []

const currentYear = dayjs().year()
const currentMonth = dayjs().month() + 1

for (let i = 1; i <= currentMonth; i++) {
  const monthName = dayjs(`${currentYear}-${i}-01`).format('MMMM')
  months.push(monthName)
}

let years = []

for (let i = 0; i <= 3; i++) {
  years.push( dayjs().year() - i)
}

const form = ref({
    month: dayjs().format('MMMM'),
    year: currentYear,
});

const clearForm = () => {
    form.value = {
        month: dayjs().format('MMMM'),
        year: currentYear,
    }
}


const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.month) {
        formUtil.errorMessage('Select month')
        return
    }

    if (!form.value.year) {
        formUtil.errorMessage('Select year')
        return
    }
    
    processing.value = true

    apiClient.post('hr/payroll/payroll-month-open', form.value)
       .then(() => {
            fetchPayrollMonths()
        
            formUtil.successMessage('Payroll month opened successfully')

            $('#payroll-month-modal').modal('hide')
            
            processing.value = false
       })
       .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const payrollMonthId = ref(null)
const closePayrollMonth = (id) => {
    payrollMonthId.value = id

    $('#close-modal').modal('show')
}

const handleClose = () => {
    processing.value = true

    apiClient.post(`hr/payroll/payroll-month/${payrollMonthId.value}/close`)
       .then(() => {
            formUtil.successMessage('Payroll month closed successfully')

            payrollMonthId.value = null

            fetchPayrollMonths()
        
            $('#close-modal').modal('hide')
            
            processing.value = false
       })
       .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const loader = ref(true)

const payrollMonths = ref([])
const fetchPayrollMonths = () => {
    loader.value = true
    
    apiClient.get('hr/payroll/payroll-months-list')
        .then(response => {
            payrollMonths.value = response.data

            loader.value = false

            $(document).ready(function() {
                $('#create_datatable_10').DataTable({
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': true,
                    'info': true,
                    'autoWidth': false,
                    'pageLength': 10,
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
                });
            })

        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
        })
}

onMounted(() => {
    fetchPayrollMonths()
})

</script>
