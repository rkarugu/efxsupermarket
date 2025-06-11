<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-earning___create') || userRole == '1')"
        >
            <i class="fa fa-plus"></i>
            Add Earning
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
                    <th>Type</th>
                    <th>Amount Type</th>
                    <!-- <th>Amount</th> -->
                    <th>Rate (%)</th>
                    <th>Ratio</th>
                    <th>Is Taxable?</th>
                    <!-- <th>Tax Rate</th> -->
                    <th>Is Recurring?</th>
                    <th>Is Reliefable?</th>
                    <th>System Reserved?</th>
                    <th class="noneedtoshort">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(earning, index) in earnings" :key="earning.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ earning.name }}</td>
                    <td>{{ earning.code }}</td>
                    <td>{{ earningType(earning.type) }}</td>
                    <td>{{ amountType(earning.amount_type) }}</td>
                    <!-- <td class="text-right">{{ numberWithCommas(earning.amount) ?? 'N/A'}}</td> -->
                    <td class="text-right">{{ earning.rate ?? 'N/A'}}</td>
                    <td class="text-right">{{ earning.ratio ?? 'N/A'}}</td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="earning.is_taxable">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <!-- <td class="text-right">{{ earning.tax_rate ?? 'N/A'}}</td> -->
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="earning.is_recurring">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="earning.is_reliefable">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="earning.system_reserved">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(earning)" v-if="canEditEarning(earning)">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="earnings-modal" 
        :modalTitle="`${action} Earning`" 
        :buttonText="`${action} Earning`"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="name">Name <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="name" placeholder="Name" v-model="form.name">
                </div>
                <div class="form-group col-md-12">
                    <label for="earning-type">Earning Type <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="types" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select earning type..." 
                        :select2Options="{minimumResultsForSearch: Infinity}" 
                        v-model="form.type" 
                    />
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
                <!-- <div class="form-group col-md-6" v-if="form.amount_type == 'fixed_amount' || form.amount_type == ''">
                    <label for="amount">Amount <span style="color: red">*</span></label>
                    <input type="number" step=".01" min="0" class="form-control" id="amount" placeholder="Amount" v-model="form.amount">
                </div> -->
                <div class="form-group col-md-6" v-if="form.amount_type == 'percentage'">
                    <label for="percentage">Percentage <span style="color: red">*</span></label>
                    <input type="number" step=".01" min="0" class="form-control" id="percentage" placeholder="Percentage" v-model="form.rate">
                </div>
                <div class="form-group col-md-6" v-if="form.amount_type == 'ratio'">
                    <label for="ratio">Ratio <span style="color: red">*</span></label>
                    <input type="number" step=".01" min="0" class="form-control" id="ratio" placeholder="Ratio" v-model="form.ratio">
                </div>
                <div class="col-md-12">
                    <div class="checkbox">
                        <label style="font-weight: bold;">
                            <input type="checkbox" v-model="form.is_taxable"> Is Taxable?
                        </label>
                    </div>
                </div>
                <!-- <div class="form-group col-md-6" v-if="form.is_taxable">
                    <input type="number" step=".01" min="0" class="form-control" placeholder="Tax Rate" v-model="form.tax_rate">
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
</template>

<script setup>
import { computed, ref, watch } from 'vue'
import Loader from "@/components/ui/Loader.vue"
import Modal from "@/components/ui/Modal.vue"
import Select2Select from '@/components/ui/form/Select2Select.vue'
import { numberWithCommas } from '@/utils.js'
import formUtil from '@/composables/useForm.js'
import { useApi } from '@/composables/useApi.js'

const { earnings, userRole, userPermissions } = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
    earnings: {
        type: Array,
        required: true
    }
})

const emit = defineEmits(['earningsFormSubmitted'])

const permissions = Object.keys(JSON.parse(userPermissions))

const { apiClient } = useApi()

const baseUrl = 'hr/configurations'

const canEditEarning = (earning) => {
    return (permissions.includes('hr-and-payroll-configurations-earning___edit') || userRole == 1) && (earning.system_reserved ? userRole == 1 : true)
}

const form = ref({
    id: '',
    name: '',
    // description: '',
    type: '',
    amount_type: '',
    // amount: '',
    rate: '',
    ratio: '',
    is_taxable: false,
    // tax_rate: '',
    is_recurring: false,
    is_reliefable: false,
    system_reserved: false,
})

const clearForm = () => {
    form.value = {
        id: '',
        name: '',
        // description: '',
        type: '',
        amount_type: '',
        // amount: '',
        rate: '',
        ratio: '',
        is_taxable: false,
        // tax_rate: '',
        is_recurring: false,
        is_reliefable: false,
        system_reserved: false,
    }
}

const types = [
    {
        id: 'cash',
        name: 'Cash'
    },
    {
        id: 'non_cash',
        name: 'Non-cash'
    },
]

const amountTypes = [
    {
        id: 'fixed_amount',
        name: 'Fixed Amount'
    },
    {
        id: 'percentage',
        name: 'Percentage'
    },
    {
        id: 'ratio',
        name: 'Ratio'
    },
]

const earningType = (earningType) => {
    return types.find(type => type.id == earningType)?.name
}

const amountType = (amountType) => {
    return amountTypes.find(type => type.id == amountType)?.name
}

watch(() => form.value.amount_type, () => {
    // form.value.amount = ''
    form.value.rate = ''
    form.value.ratio = ''
})

const action = ref('Add')

const showAddModal = () => {
    clearForm()
    
    action.value = 'Add'

    $('#earnings-modal').modal('show')
}

const showEditModal = (earning) => {
    action.value = 'Edit'
    
    form.value = {
        id: earning.id,
        name: earning.name,
        // description: earning.description,
        type: earning.type,
        amount_type: earning.amount_type,
        is_taxable: earning.is_taxable,
        // tax_rate: earning.tax_rate ?? '',
        is_recurring: earning.is_recurring,
        is_reliefable: earning.is_reliefable,
        system_reserved: earning.system_reserved,
    }
    
    setTimeout(() => {
        // form.value.amount = earning.amount ?? '',
        form.value.rate = earning.rate ?? '',
        form.value.ratio = earning.ratio ?? '',
            
        $('#earnings-modal').modal('show')        
    }, 100);
}

const loader = ref(true)
const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.type) {
        formUtil.errorMessage('Select earning type')
        return
    }
    
    if (!form.value.name) {
        formUtil.errorMessage('Enter name')
        return
    }

    if (!form.value.amount_type) {
        formUtil.errorMessage('Select amount type')
        return
    }

    // if (form.value.amount_type == 'fixed_amount' && !form.value.amount) {
    //     formUtil.errorMessage('Enter amount')
    //     return
    // }

    if (form.value.amount_type == 'percentage' && !form.value.rate) {
        formUtil.errorMessage('Enter percentage')
        return
    }

    if (form.value.amount_type == 'ratio' && !form.value.ratio) {
        formUtil.errorMessage('Enter ratio')
        return
    }

    // if (form.value.is_taxable && !form.value.tax_rate) {
    //     formUtil.errorMessage('Enter tax rate')
    //     return
    // }

    let uri = ''

    if (action.value == 'Add') {
        uri = `${baseUrl}/earnings`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/earnings/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            emit('earningsFormSubmitted')
            
            formUtil.successMessage(response.data.message)

            $('#earnings-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

watch(() => earnings, () => {
    loader.value = false
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>