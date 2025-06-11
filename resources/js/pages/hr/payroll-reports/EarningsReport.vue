<template>
    <Card cardTitle="Earnings Report">
        <div class="row" id="filters">
            <div class="col-md-3 form-group">
                <label>Payroll Month</label>
                <Select2Select 
                    :options="payrollMonths" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select payroll month..." 
                    :disabled="processing"
                    v-model="payrollMonthId" 
                />
            </div>

            <div class="col-md-2 form-group">
                <label>Earning</label>
                <Select2Select 
                    :options="earnings" 
                    optionValue="name" 
                    optionLabel="name" 
                    placeholder="Select earning..." 
                    :disabled="processing"
                    v-model="earning" 
                />
            </div>

            <div class="form-group">
                <button type="button" class="btn btn-success" :disabled="processing" @click="generateReport">
                    <i class="fas fa-file-excel"></i>
                    Generate Report
                </button>
            </div>
        </div>
    </Card>
</template>

<script setup>
import { ref } from 'vue'
import Card from "@/components/ui/Card.vue"
import formUtil from '@/composables/useForm.js'
import { useApi } from '@/composables/useApi.js'
import Select2Select from "@/components/ui/form/Select2Select.vue"

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
    payrollMonths: {
        type: String,
        required: true
    },
    earnings: {
        type: String,
        required: true
    },
})

const payrollMonths = JSON.parse(props.payrollMonths)
const earnings = JSON.parse(props.earnings)
earnings.unshift({
    name: 'Basic Pay'
})

const payrollMonthId = ref('')
const earning = ref('')

const processing = ref(false)

const generateReport = () => {
    if (!payrollMonthId.value) {
        formUtil.errorMessage("Please select a payroll month.")
        return
    }

    if (!earning.value) {
        formUtil.errorMessage("Please select an earning.")
        return
    }
    
    processing.value = true
    
    apiClient.post('hr/payroll/reports/earnings-report', {
        payroll_month_id: payrollMonthId.value,
        earning: earning.value,
    }, {
        responseType: 'blob'
    })
        .then(response => {

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;

            link.setAttribute('download', 'earnings_report.xlsx');
            
            document.body.appendChild(link);
            link.click();

            document.body.removeChild(link);
            
            formUtil.successMessage("Earnings report has been generated successfully.")

            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

</script>

<style scoped>
    #filters {
        display: flex;
        align-items: flex-end;
    }
</style>