<template>
    <Card cardTitle="Casuals Pay Periods">
        <template #header-action>
            <button
                class="btn btn-primary"
                data-toggle="modal" 
                data-target="#pay-period-modal" 
                @click="clearForm"
                v-if="permissions.includes('casuals-pay-pay-periods___create') || userRole == 1"
            >
                <i class="fa fa-book-open"></i>
                Open Pay Period
            </button>
        </template>

        <div class="table-responsive" v-show="!loader">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Status</th>
                        <th class="noneedtoshort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(payPeriod, index) in payPeriods" :key="payPeriod.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ payPeriod.name }}</td>
                        <td>{{ payPeriod.branch.name }}</td>
                        <td>{{ payPeriod.start_date }}</td>
                        <td>{{ payPeriod.end_date }}</td>
                        <td style="text-align: center" >
                            <span class="badge bg-green" v-if="payPeriod.status == 'open'">Open</span>
                            <span class="badge bg-red" v-if="payPeriod.status == 'closed'">Closed</span>
                        </td>
                        <td style="text-align: center; color: #337ab7;">
                            <a :href="`/admin/hr/payroll/casuals-pay/pay-periods/${payPeriod.id}/details`" v-if="canViewDetails">
                                <i class="fas fa-eye" title="View"></i>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Loader v-show="loader" />

        <Modal 
            id="pay-period-modal" 
            modalTitle="Open Pay Period" 
            buttonText="Open Pay Period"
            :processing
            @submit-clicked="handleSubmit"
        >
            <template #modal-body>
                <div class="row">
                    <div class="form-group col-md-12">
                        <label for="month">Month <span style="color: red">*</span></label>
                        <Select2Select 
                            :options="branches" 
                            optionValue="id" 
                            optionLabel="name" 
                            placeholder="Select Branch..." 
                            v-model="form.branch_id" 
                        />
                    </div>
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
                    <div class="form-group col-md-12">
                        <label for="start-date">Start Date <span style="color: red">*</span></label>
                        <input type="date" class="form-control" id="start-date" :disabled="!monthYearSelected" :min="minDate" :max="maxDate" v-model="form.start_date">
                    </div>
                    <div class="form-group col-md-12">
                        <label for="end-date">End Date <span style="color: red">*</span></label>
                        <input type="date" class="form-control" id="end-date" :value="endDate" disabled>
                    </div>
                </div>
            </template>
        </Modal>

    </Card>
</template>

<script setup>
import dayjs from 'dayjs'
import { computed, onMounted, ref, watch } from 'vue'
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
    branches: {
        type: String,
        required: true
    },
})

const permissions = Object.keys(JSON.parse(props.userPermissions))
const branches = JSON.parse(props.branches)

const canViewDetails = computed(() => permissions.includes('casuals-pay-pay-periods___details') || props.userRole == 1)

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

const monthYearSelected = computed(() => form.value.month && form.value.year)

const minDate = computed(() => dayjs(`${form.value.year}-${form.value.month}-01`).format('YYYY-MM-DD'))
const maxDate = computed(() => dayjs(`${form.value.year}-${form.value.month}-01`).endOf('month').format('YYYY-MM-DD'))

const endDate = computed(() => {
    if (form.value.start_date) {
        return dayjs(form.value.start_date).add(6,'day').format('YYYY-MM-DD')
    } else {
        return ''
    }
})

const form = ref({
    branch_id: '',
    month: dayjs().format('MMMM'),
    year: currentYear,
    start_date: '',
    end_date: '',
});

const clearForm = () => {
    form.value = {
        branch_id: '',
        month: dayjs().format('MMMM'),
        year: currentYear,
        start_date: '',
        end_date: '',
    }
}

watch([() => form.value.month, () => form.value.year], () => {
    form.value.start_date = ''
})

watch(() => endDate.value, (value) => {
    form.value.end_date = value
})

const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.branch_id) {
        formUtil.errorMessage('Select branch')
        return
    }

    if (!form.value.month) {
        formUtil.errorMessage('Select month')
        return
    }

    if (!form.value.year) {
        formUtil.errorMessage('Select year')
        return
    }

    if (!form.value.start_date) {
        formUtil.errorMessage('Select start date')
        return
    }

    if (!form.value.end_date) {
        formUtil.errorMessage('Select end date')
        return
    }
    
    processing.value = true

    apiClient.post('hr/payroll/casuals-pay-periods-open', form.value)
       .then(response => {
            loader.value = true

            fetchPayPeriods()
        
            formUtil.successMessage(response.data.message)

            $('#pay-period-modal').modal('hide')
            
            processing.value = false
       })
       .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const loader = ref(true)

const payPeriods = ref([])
const fetchPayPeriods = () => {
    apiClient.get('hr/payroll/casuals-pay-periods-list')
        .then(response => {
            payPeriods.value = response.data

            loader.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
        })
}
onMounted(() => {
    fetchPayPeriods()
})

</script>
