<template>
    <Card cardTitle="Expunged Disbursements">
        <div class="row filters">
            <div class="col-md-2 form-group">
                <label for="">Branch</label>
                <Select2Select 
                    :options="branches" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select Branch..." 
                    v-model="branchId" 
                />
            </div>

            <div class="col-md-2 form-group">
                <label for="">Month</label>
                <Select2Select 
                    :options="months" 
                    placeholder="Select month..." 
                    v-model="month" 
                />
            </div>

            <div class="col-md-2 form-group">
                <label for="">Year</label>
                <Select2Select 
                    :options="years" 
                    placeholder="Select year..." 
                    v-model="year" 
                />
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-success" :disabled="processing" @click="handleFilter">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-secondary" style="margin-left: 10px;" :disabled="processing" @click="clearFilter" v-if="filter">
                    <i class="fas fa-filter-circle-xmark"></i>
                    Clear Filter
                </button>
            </div>
        </div>

        <hr style="margin-top: 0;">

        <div class="table-responsive" v-if="!loader">
            <table class="table table-bordered table-hover nowrap" id="create_datatable_10">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Period</th>
                        <th>Branch</th>
                        <th>Name</th>
                        <th>Phone No.</th>
                        <th>ID No.</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(disbursement, index) in disbursements" :key="disbursement.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ `${disbursement.casuals_pay_period_detail.casuals_pay_period?.name ?? ''} (${disbursement.casuals_pay_period_detail.casuals_pay_period?.start_date ?? ''} - ${disbursement.casuals_pay_period_detail.casuals_pay_period?.end_date ?? ''})` }}</td>
                        <td>{{ disbursement.casuals_pay_period_detail.casual.branch.name }}</td>
                        <td>{{ disbursement.casuals_pay_period_detail.casual.full_name }}</td>
                        <td>{{ disbursement.casuals_pay_period_detail.casual.phone_no }}</td>
                        <td>{{ disbursement.casuals_pay_period_detail.casual.id_no }}</td>
                        <td class="text-right">{{ numberWithCommas(disbursement.amount) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th class="text-right" colspan="6">Total</th>
                        <th class="text-right">{{ numberWithCommas(totalDisbursements) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <Loader v-else />
    </Card>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Card from "@/components/ui/Card.vue"
import Loader from "@/components/ui/Loader.vue"
import Select2Select from "@/components/ui/form/Select2Select.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'
import { numberWithCommas } from '@/utils.js'

const { apiClient } = useApi()

const props = defineProps({
    branches: {
        type: String,
        required: true
    },
})

const branches = JSON.parse(props.branches)
branches.unshift({
    id: 0,
    name: 'ALL'
})

const years = []

for (let i = 0; i <= 3; i++) {
  years.push( dayjs().year() - i)
}

const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

const branchId = ref(0)
const month = ref('')
const year = ref('')

const processing = ref(false)

const filter = ref(false)

const handleFilter = async () => {
    processing.value = true

    await fetchDisbursements(branchId.value, month.value, year.value)

    filter.value = true
    processing.value = false
}

const clearFilter = async () => {
    processing.value = true

    branchId.value = 0
    month.value = ''
    year.value = ''

    await fetchDisbursements()

    filter.value = false
    processing.value = false
}

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

const disbursements = ref([])

const totalDisbursements = computed(() => disbursements.value.reduce((total, disbursement) => total + disbursement.amount, 0))

const fetchDisbursements = async (branchId = '', month = '', year = '') => {
    loader .value = true

    try {
        let response = await apiClient('hr/payroll/casual-pay/expunged-disbursements', {
            params: {
                branch_id: branchId == '0' ? '' : branchId,
                month: month,
                year: year
            }
        })

        disbursements.value = response.data

        loader.value = false

        initDataTable()

    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
        loader.value = false
    }
}

onMounted(() => {
    fetchDisbursements()
})

</script>

<style scoped>

.text-right {
    text-align: right;
}

.filters {
    display: flex;
    align-items: flex-end;
}

</style>