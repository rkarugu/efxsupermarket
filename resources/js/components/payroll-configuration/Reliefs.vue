<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-relief___create') || userRole == '1')"
        >
            <i class="fa fa-plus"></i>
            Add Relief
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-show="!loader">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <!-- <th>Earning</th>
                    <th>Deduction</th> -->
                    <th>Amount Type</th>
                    <th>Amount</th>
                    <th>Rate (%)</th>
                    <th>System Reserved?</th>
                    <th class="noneedtoshort">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(relief, index) in reliefs" :key="relief.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ relief.name }}</td>
                    <td>{{ amountType(relief.amount_type) }}</td>
                    <td class="text-right">{{ numberWithCommas(relief.amount) ?? 'N/A'}}</td>
                    <td class="text-right">{{ relief.rate ?? 'N/A'}}</td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="relief.system_reserved">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(relief)" v-if="!relief.system_reserved || userRole == 1">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="reliefs-modal" 
        :modalTitle="`${action} Relief`" 
        :buttonText="`${action} Relief`"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name">Name <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="name" placeholder="Name" v-model="form.name">
                </div>
                <!-- <div class="form-group col-md-12">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" rows="5" placeholder="Description" v-model="form.description"></textarea>
                </div> -->
                <div class="form-group col-md-6">
                    <label for="amount-type">Amount Type <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="amountTypes" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select amount type..."
                        :select2Options="{minimumResultsForSearch: Infinity}" 
                        v-model="form.amount_type" 
                    />
                </div>
                <div class="form-group col-md-6" v-if="form.amount_type == 'fixed_amount'">
                    <label for="amount">Amount <span style="color: red">*</span></label>
                    <AmountInput v-model="form.amount" />
                </div>
                <div class="form-group col-md-6" v-if="form.amount_type == 'percentage'">
                    <label for="percentage">Percentage <span style="color: red">*</span></label>
                    <input type="number" step=".01" min="0" class="form-control" id="percentage" placeholder="Percentage" v-model="form.rate">
                </div>
                <!-- <div class="form-group col-md-6">
                    <label for="amount-type">Relief Type <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="reliefTypes" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select relief type..."
                        :select2Options="{minimumResultsForSearch: Infinity}" 
                        v-model="form.relief_type" 
                    />
                </div>
                <div class="form-group col-md-6" v-if="form.relief_type == 'deduction' || form.relief_type == ''">
                    <label for="deduction">Deduction <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="deductions" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select deduction..."
                        :select2Options="{minimumResultsForSearch: Infinity}" 
                        v-model="form.deduction_id" 
                    />
                </div>
                <div class="form-group col-md-6" v-if="form.relief_type == 'earning'">
                    <label for="earning">Earning <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="earnings" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select earning..."
                        :select2Options="{minimumResultsForSearch: Infinity}" 
                        v-model="form.earning_id" 
                    />
                </div> -->
                <div class="col-md-12" v-if="userRole == 1">
                    <div class="checkbox">
                        <label style="font-weight: bold;">
                            <input type="checkbox" v-model="form.system_reserved"> System Reserved?
                        </label>
                    </div>
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Loader from "@/components/ui/Loader.vue"
import Modal from "@/components/ui/Modal.vue"
import Select2Select from '@/components/ui/form/Select2Select.vue'
import AmountInput from '@/components/ui/form/AmountInput.vue'
import { numberWithCommas } from '@/utils.js'
import formUtil from '@/composables/useForm.js'
import { useApi } from '@/composables/useApi.js'

const props = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
    deductions: {
        type: Array,
        required: true
    },
    earnings: {
        type: Array,
        required: true
    },
})

const permissions = Object.keys(JSON.parse(props.userPermissions))

// const earnings = computed(() => props.earnings.filter(earning => earning.is_reliefable))
// const deductions = computed(() => props.deductions.filter(deduction => deduction.is_reliefable))

const { apiClient } = useApi()

const baseUrl = 'hr/configurations'

const form = ref({
    id: '',
    // earning_id: '',
    // deduction_id: '',
    name: '',
    // description: '',
    // relief_type: '',
    amount_type: '',
    amount: '',
    rate: '',
    system_reserved: false,
})

const clearForm = () => {
    form.value = {
        id: '',
        // earning_id: '',
        // deduction_id: '',
        name: '',
        // description: '',
        // relief_type: '',
        amount_type: '',
        amount: '',
        rate: '',
        system_reserved: false,
    }
}

const amountTypes = [
    {
        id: 'fixed_amount',
        name: 'Fixed Amount'
    },
    {
        id: 'percentage',
        name: 'Percentage'
    },
]

// const reliefTypes = [
//     {
//         id: 'deduction',
//         name: 'Deduction'
//     },
//     {
//         id: 'earning',
//         name: 'Earning'
//     },
// ]

const amountType = (amountType) => {
    return amountTypes.find(type => type.id == amountType)?.name
}

const action = ref('Add')

const showAddModal = () => {
    clearForm()
    
    action.value = 'Add'

    $('#reliefs-modal').modal('show')
}

const showEditModal = (relief) => {
    form.value = {
        id: relief.id,
        // earning_id: relief.earning_id,
        // deduction_id: relief.deduction_id,
        name: relief.name,
        // description: relief.description,
        // relief_type: relief.relief_type,
        amount_type: relief.amount_type,
        amount: relief.amount,
        rate: relief.rate,
        system_reserved: relief.system_reserved,
    }
    
    action.value = 'Edit'

    $('#reliefs-modal').modal('show')
}

const loader = ref(true)
const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.name) {
        formUtil.errorMessage('Enter name')
        return
    }

    if (!form.value.amount_type) {
        formUtil.errorMessage('Select amount type')
        return
    }

    if (form.value.amount_type == 'fixed_amount' && (form.value.amount === undefined || form.value.amount === null || form.value.amount === '' || form.value.amount < 0)) {
        formUtil.errorMessage('Enter amount')
        return
    }

    if (form.value.amount_type == 'percentage' && (form.value.rate === undefined || form.value.rate === null || form.value.rate === '' || form.value.rate < 0)) {
        formUtil.errorMessage('Enter percentage')
        return
    }

    let uri = ''

    if (action.value == 'Add') {
        uri = `${baseUrl}/reliefs`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/reliefs/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            fetchReliefs()
            
            formUtil.successMessage(response.data.message)

            $('#reliefs-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const reliefs = ref([])
const fetchReliefs = async () => {
    try {
        let response = await apiClient.get(`${baseUrl}/reliefs`)

        reliefs.value = response.data

        loader.value = false
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

onMounted(() => {
    fetchReliefs()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>