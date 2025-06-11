<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-nssf___create') || userRole == '1')"
        >
            <i class="fa fa-plus"></i>
            Add NSSF
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
                <tr v-for="(nssf, index) in nssfs" :key="nssf.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ numberWithCommas(nssf.from) }}</td>
                    <td>{{ numberWithCommas(nssf.to) }}</td>
                    <td>{{ nssf.rate }}</td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(nssf)" v-if="canEditNssf">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                        <a href="javascript:void(0)" style="margin-left: 10px;" @click="deleteNssf(nssf.id)" v-if="canDeleteNssf">
                            <i class="fas fa-trash text-danger" title="Delete"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="nssf-modal" 
        :modalTitle="`${action} NSSF`" 
        :buttonText="`${action} NSSF`"
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
        id="nssf-confirm-modal"
        modalTitle="Delete NSSF"
        buttonText="Delete"
        :processing
        @submit-clicked="handleDelete"
    >
        <template #modal-body>
            Are you sure you want to delete this NSSF?
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

const canEditNssf = computed(() => permissions.includes('hr-and-payroll-configurations-nssf___edit') || userRole == 1)
const canDeleteNssf = computed(() => permissions.includes('hr-and-payroll-configurations-nssf___delete') || userRole == 1)

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

    $('#nssf-modal').modal('show')
}

const showEditModal = (nssf) => {
    action.value = 'Edit'

    form.value = {
        id: nssf.id,
        from: nssf.from,
        to: nssf.to,
        rate: nssf.rate,
    }

    $('#nssf-modal').modal('show')        
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
        uri = `${baseUrl}/nssfs`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/nssfs/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            fetchNssfs()
            
            formUtil.successMessage(response.data.message)

            $('#nssf-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

let deleteId = null
const deleteNssf = (id) => {
    deleteId = id

    $('#nssf-confirm-modal').modal('show')
}

const handleDelete = () => {
    processing.value = true
    
    apiClient.delete(`${baseUrl}/nssfs/${deleteId}`)
        .then(response => {
            loader.value = true

            fetchNssfs()
            
            formUtil.successMessage(response.data.message)
            
            $('#nssf-confirm-modal').modal('hide')

            processing.value = false
            deleteId = null
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        }) 
}

const nssfs = ref([])
const fetchNssfs = () => {
    apiClient.get(`${baseUrl}/nssfs`)
       .then(response => {
            nssfs.value = response.data
            
            loader.value = false
        })
       .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            loader.value = false
        })
}

onMounted(() => {
    fetchNssfs()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>