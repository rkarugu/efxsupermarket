<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-paye___create') || userRole == '1')"
        >
            <i class="fa fa-plus"></i>
            Add PAYE
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-show="!loader">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th style="width: 30%;">From</th>
                    <th style="width: 30%;">To</th>
                    <th style="width: 30%;">Rate (%)</th>
                    <th class="noneedtoshort" style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(paye, index) in payes" :key="paye.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ numberWithCommas(paye.from) }}</td>
                    <td>{{ numberWithCommas(paye.to) }}</td>
                    <td>{{ paye.rate }}</td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(paye)" v-if="canEditPaye">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                        <a href="javascript:void(0)" style="margin-left: 10px;" @click="deletePaye(paye.id)" v-if="canDeletePaye">
                            <i class="fas fa-trash text-danger" title="Delete"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="paye-modal" 
        :modalTitle="`${action} PAYE`" 
        :buttonText="`${action} PAYE`"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="from">From <span style="color: red">*</span></label>
                    <AmountInput id="from" placeholder="From" v-model="form.from" />
                </div>
                <div class="form-group col-md-12">
                    <label for="to">To <span style="color: red">*</span></label>
                    <AmountInput id="to" placeholder="To" v-model="form.to" />
                </div>
                <div class="form-group col-md-12">
                    <label for="rate">Rate <span style="color: red">*</span></label>
                    <input type="number" class="form-control" id="rate" placeholder="Rate" v-model="form.rate">
                </div>
            </div>
        </template>
    </Modal>

    <Modal
        id="confirm-modal"
        modalTitle="Delete PAYE"
        buttonText="Delete"
        :processing
        @submit-clicked="handleDelete"
    >
        <template #modal-body>
            Are you sure you want to delete this PAYE?
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

const { userRole, userPermissions } = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    }
})

const permissions = Object.keys(JSON.parse(userPermissions))

const { apiClient } = useApi()

const baseUrl = 'hr/configurations'

const canEditPaye = computed(() => permissions.includes('hr-and-payroll-configurations-paye___edit') || userRole == 1)
const canDeletePaye = computed(() => permissions.includes('hr-and-payroll-configurations-paye___delete') || userRole == 1)

const form = ref({
    id: '',
    from: '1000',
    to: '',
    rate: '',
})

const clearForm = () => {
    form.value = {
        id: '',
        from: '',
        to: '',
        rate: '',
    }
}

const action = ref('Add')

const showAddModal = () => {
    clearForm()
    
    action.value = 'Add'

    $('#paye-modal').modal('show')
}

const showEditModal = (paye) => {
    action.value = 'Edit'

    form.value = {
        id: paye.id,
        from: paye.from,
        to: paye.to,
        rate: paye.rate,
    }

    $('#paye-modal').modal('show')        
}

const loader = ref(true)
const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.from) {
        formUtil.errorMessage('Enter from amount')
        return
    }

    if (!form.value.to) {
        formUtil.errorMessage('Enter to amount')
        return
    }

    if (!form.value.rate) {
        formUtil.errorMessage('Enter rate')
        return
    }

    let uri = ''

    if (action.value == 'Add') {
        uri = `${baseUrl}/payes`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/payes/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            fetchPayes()
            
            formUtil.successMessage(response.data.message)

            $('#paye-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

let deleteId = null
const deletePaye = (id) => {
    deleteId = id

    $('#confirm-modal').modal('show')
}

const handleDelete = () => {
    processing.value = true
    
    apiClient.delete(`${baseUrl}/payes/${deleteId}`)
        .then(response => {
            loader.value = true

            fetchPayes()
            
            formUtil.successMessage(response.data.message)
            
            $('#confirm-modal').modal('hide')

            processing.value = false
            deleteId = null
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        }) 
}

const payes = ref([])
const fetchPayes = () => {
    apiClient.get(`${baseUrl}/payes`)
       .then(response => {
            payes.value = response.data
            
            loader.value = false
        })
       .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            loader.value = false
        })
}

onMounted(() => {
    fetchPayes()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>