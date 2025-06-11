<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-deduction___create') || userRole == '1')"
        >
            <i class="fa fa-plus"></i>
            Add Deduction
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-show="!loader">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Code</th>
                    <th>Amount Type</th>
                    <!-- <th>Amount</th> -->
                    <th>Rate (%)</th>
                    <!-- <th>Has Brackets?</th>
                    <th>Is Statutory?</th> -->
                    <th>Is Recurring?</th>
                    <th>Is Reliefable?</th>
                    <th>System Reserved?</th>
                    <th class="noneedtoshort">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(deduction, index) in deductions" :key="deduction.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ deduction.name }}</td>
                    <td>{{ deduction.code }}</td>
                    <td>{{ amountType(deduction.amount_type) }}</td>
                    <!-- <td class="text-right">{{ numberWithCommas(deduction.amount) ?? 'N/A'}}</td> -->
                    <td class="text-right">{{ deduction.rate ?? 'N/A'}}</td>
                    <!-- <td style="text-align: center" >
                        <span class="badge bg-green" v-if="deduction.has_brackets">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="deduction.is_statutory">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td> -->
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="deduction.is_recurring">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="deduction.is_reliefable">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="deduction.system_reserved">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(deduction)" v-if="canEditDeduction(deduction)">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                        <!-- <a href="javascript:void(0)" style="margin-left: 10px;" @click="showBracketsModal(deduction)" v-if="deduction.has_brackets">
                            <i class="fas fa-list" title="Brackets"></i>
                        </a> -->
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="deductions-modal" 
        :modalTitle="`${action} Deduction`" 
        :buttonText="`${action} Deduction`"
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
                <div class="form-group col-md-6" v-if="form.amount_type == 'percentage'">
                    <label for="percentage">Percentage <span style="color: red">*</span></label>
                    <input type="number" step=".01" min="0" class="form-control" id="percentage" placeholder="Percentage" v-model="form.rate">
                </div>
                <!-- <template v-if="!form.has_brackets">
                    <div class="form-group col-md-6" v-if="form.amount_type == 'fixed_amount' || form.amount_type == ''">
                        <label for="amount">Amount <span style="color: red">*</span></label>
                        <input type="number" step=".01" min="0" class="form-control" id="amount" placeholder="Amount" v-model="form.amount">
                    </div>
                </template>
                <div class="col-md-12">
                    <div class="checkbox">
                        <label style="font-weight: bold;">
                            <input type="checkbox" v-model="form.has_brackets"> Has Brackets?
                        </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                        <label style="font-weight: bold;">
                            <input type="checkbox" v-model="form.is_statutory"> Is Statutory?
                        </label>
                    </div>
                </div> -->
                <div class="col-md-12">
                    <div class="checkbox">
                        <label style="font-weight: bold;">
                            <input type="checkbox" v-model="form.is_recurring"> Is Recurring?
                        </label>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                        <label style="font-weight: bold;">
                            <input type="checkbox" v-model="form.is_reliefable"> Is Reliefable?
                        </label>
                    </div>
                </div>
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

    <!-- <DeductionBrackets :deduction="selectedDeduction" @save-brackets="saveBrackets" /> -->
</template>

<script setup>
import { computed, onMounted, ref, watch } from 'vue'
import Loader from "@/components/ui/Loader.vue"
import Modal from "@/components/ui/Modal.vue"
import Select2Select from '@/components/ui/form/Select2Select.vue'
// import DeductionBrackets from '@/components/payroll-configuration/DeductionBrackets.vue'
import { numberWithCommas } from '@/utils.js'
import formUtil from '@/composables/useForm.js'
import { useApi } from '@/composables/useApi.js'

const { deductions, userRole, userPermissions } = defineProps({
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
})

const emit = defineEmits(['refreshDeductions'])

const permissions = Object.keys(JSON.parse(userPermissions))

const { apiClient } = useApi()

const baseUrl = 'hr/configurations'

const canEditDeduction = (deduction) => {
    return (permissions.includes('hr-and-payroll-configurations-deduction___edit') || userRole == 1) && (deduction.system_reserved ? userRole == 1 : true)
}

const form = ref({
    id: '',
    name: '',
    // description: '',
    amount_type: '',
    // amount: '',
    rate: '',
    // has_brackets: false,
    // is_statutory: false,
    is_recurring: false,
    is_reliefable: false,
    system_reserved: false,
})

const clearForm = () => {
    form.value = {
        id: '',
        name: '',
        // description: '',
        amount_type: '',
        // amount: '',
        rate: '',
        // has_brackets: false,
        // is_statutory: false,
        is_recurring: false,
        is_reliefable: false,
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

const amountType = (amountType) => {
    return amountTypes.find(type => type.id == amountType)?.name
}

watch([() => form.value.amount_type, () => form.value.has_brackets], () => {
    // form.value.amount = ''
    form.value.rate = ''
})

const action = ref('Add')

const showAddModal = () => {
    clearForm()
    
    action.value = 'Add'

    $('#deductions-modal').modal('show')
}

const showEditModal = (deduction) => {
    action.value = 'Edit'
    
    form.value = {
        id: deduction.id,
        name: deduction.name,
        // description: deduction.description,
        amount_type: deduction.amount_type,
        // amount: deduction.amount,
        rate: deduction.rate,
        // has_brackets: deduction.has_brackets,
        // is_statutory: deduction.is_statutory,
        is_recurring: deduction.is_recurring,
        is_reliefable: deduction.is_reliefable,
        system_reserved: deduction.system_reserved,
    }

    setTimeout(() => {
        // form.value.amount = deduction.amount ?? '',
        form.value.rate = deduction.rate ?? '',
            
        $('#deductions-modal').modal('show')
    }, 100);
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

    if (form.value.amount_type == 'percentage' && !form.value.rate) {
        formUtil.errorMessage('Enter percentage')
        return
    }
    
    // if (!form.value.has_brackets) {
    //     if (form.value.amount_type == 'fixed_amount' && !form.value.amount) {
    //         formUtil.errorMessage('Enter amount')
    //         return
    //     }
    
    // }

    let uri = ''

    if (action.value == 'Add') {
        uri = `${baseUrl}/deductions`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/deductions/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            emit('refreshDeductions')
            
            formUtil.successMessage(response.data.message)

            $('#deductions-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

watch(() => deductions, () => {
    loader.value = false
})

// const selectedDeduction = ref({})
// const showBracketsModal = (deduction) => {
//     if (deduction.has_brackets) {
//         selectedDeduction.value = deduction
        
//         $('#brackets-modal').modal('show')
//     }
// }

// const saveBrackets = () => {
//     loader.value = true

//     emit('refreshDeductions')
// }

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>