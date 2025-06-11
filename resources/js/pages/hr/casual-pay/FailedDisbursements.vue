<template>
    <Card cardTitle="Failed Disbursements">
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
            
            <div class="form-group">
                <button type="button" class="btn btn-success" :disabled="processing" @click="handleFilter">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </div>
            
        </div>

        <hr style="margin-top: 0;">

        <template v-if="!loader">
            <div class="table-responsive">
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
                            <th class="noneedtoshort">
                                <label>
                                    <input type="checkbox" v-model="checkAll">
                                </label>
                            </th>
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
                            <td>
                                <label>
                                    <input type="checkbox" :value="disbursement.id" v-model="selectedDisbursements">
                                </label>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th class="text-right" colspan="6">Total</th>
                            <th class="text-right">{{ numberWithCommas(totalDisbursements) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="text-right" v-if="disbursements.length && (permissions.includes('casuals-pay-failed-disbursements___recheck-and-resend') || userRole == 1)">
                <button
                    class="btn btn-primary"
                    style="margin-right: 10px;"
                    data-toggle="modal"
                    data-target="#expunge-modal"
                    :disabled="!selectedDisbursements.length || processing"
                    v-if="permissions.includes('casuals-pay-failed-disbursements___expunge') || userRole == 1"
                >
                    <i class="fas fa-circle-xmark"></i>
                    Expunge

                </button>
                <button class="btn btn-primary" :disabled="!selectedDisbursements.length || processing" @click="recheckAndResend">
                    <i class="fas fa-arrows-rotate"></i>
                    Recheck and Resend
                </button>
            </div>
        </template>

        <Loader v-else />
    </Card>

    <Modal
        id="expunge-modal"
        modalTitle="Expunge Disbursements"
        buttonText="Expunge"
        :processing
        @submit-clicked="expunge"
    >
        <template #modal-body>
            Are you sure you want to expunge these disbursements?
        </template>
    </Modal>
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import Card from "@/components/ui/Card.vue"
import Modal from "@/components/ui/Modal.vue"
import Loader from "@/components/ui/Loader.vue"
import Select2Select from "@/components/ui/form/Select2Select.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'
import { numberWithCommas } from '@/utils.js'

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

const branches = JSON.parse(props.branches)
branches.unshift({
    id: 0,
    name: 'ALL'
})
const permissions = Object.keys(JSON.parse(props.userPermissions))

const selectedDisbursements = ref([])

const checkAll = ref(false)

watch(() => checkAll.value, (value) => {
    if (value) {
        selectedDisbursements.value = disbursements.value.map(disbursement => disbursement.id)
    } else {
        selectedDisbursements.value = []
    }
})

const processing = ref(false)

const recheckAndResend = async () => {
    processing.value = true

    try {
        let response = await apiClient.post('hr/payroll/casual-pay/failed-disbursements-recheck-and-resend', selectedDisbursements.value)

        formUtil.successMessage(response.data.message)

        await fetchDisbursements(branchId.value == '0' ? '' : branchId.value)

        selectedDisbursements.value = []

        processing.value = false
        
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
        processing.value = false
    }
}

const expunge = async () => {
    processing.value = true

    try {
        let response = await apiClient.post('hr/payroll/casual-pay/failed-disbursements-expunge', selectedDisbursements.value)

        formUtil.successMessage(response.data.message)

        await fetchDisbursements(branchId.value == '0' ? '' : branchId.value)

        processing.value = false

        selectedDisbursements.value = []

        $('#expunge-modal').modal('hide')
        
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
        processing.value = false
    }
}

const branchId = ref(0)

const filter = ref(false)

const handleFilter = async () => {
    processing.value = true

    await fetchDisbursements(branchId.value)

    filter.value = true
    processing.value = false
    selectedDisbursements.value = []
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

const fetchDisbursements = async (branchId = '') => {
    loader .value = true

    try {
        let response = await apiClient('hr/payroll/casual-pay/failed-disbursements', {
            params: {
                branch_id: branchId == '0' ? '' : branchId
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

th:has(label), 
td:has(label) {
    padding: 0 !important;
    text-align: center;
}

label {
    width: 100%;
    padding: 8px;
}

</style>