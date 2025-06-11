<template>
    <Card cardTitle="Payroll Summary Report">
        <div class="row" id="filters">
            <div class="col-md-2 form-group">
                <label>Branch</label>
                <Select2Select 
                    :options="branches" 
                    optionValue="id" 
                    optionLabel="name" 
                    :disabled="processing"
                    v-model="branchId" 
                />
            </div>

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
    branches: {
        type: String,
        required: true
    },
    payrollMonths: {
        type: String,
        required: true
    },
})

const branches = JSON.parse(props.branches)
branches.unshift({
    id: 0,
    name: 'ALL'
})
const payrollMonths = JSON.parse(props.payrollMonths)

const branchId = ref(0)
const payrollMonthId = ref('')

const processing = ref(false)

const generateReport = () => {
    if (!payrollMonthId.value) {
        formUtil.errorMessage("Please select a payroll month.")
        return
    }
    
    processing.value = true
    
    apiClient.post('hr/payroll/reports/payroll-summary-report', {
        branch_id: branchId.value == '0' ? '' : branchId.value,
        payroll_month_id: payrollMonthId.value,
    }, {
        responseType: 'blob'
    })
        .then(response => {

            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;

            link.setAttribute('download', 'payroll_summary_report.xlsx');
            
            document.body.appendChild(link);
            link.click();

            document.body.removeChild(link);
            
            formUtil.successMessage("Payroll summary report has been generated successfully.")

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
