<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-shif___create') || userRole == '1') && !shifs.length"
        >
            <i class="fa fa-plus"></i>
            Add SHIF
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-show="!loader">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Name</th>
                    <th>Rate (%)</th>
                    <th class="noneedtoshort" style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(shif, index) in shifs" :key="shif.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ shif.name }}</td>
                    <td>{{ shif.rate }}</td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(shif)" v-if="canEditShif">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="shif-modal" 
        :modalTitle="`${action} SHIF`" 
        :buttonText="`${action} SHIF`"
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
                    <label for="rate">Rate <span style="color: red">*</span></label>
                    <input type="number" step=".01" min="0" class="form-control" id="rate" placeholder="Rate" v-model="form.rate">
                </div>
            </div>
        </template>
    </Modal>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Loader from "@/components/ui/Loader.vue"
import Modal from "@/components/ui/Modal.vue"
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
})

const permissions = Object.keys(JSON.parse(props.userPermissions))

const canEditShif = computed(() => permissions.includes('hr-and-payroll-configurations-shif___edit') || props.userRole == '1')

const { apiClient } = useApi()

const baseUrl = 'hr/configurations'

const form = ref({
    id: '',
    name: '',
    rate: '',
})

const clearForm = () => {
    form.value = {
        id: '',
        name: '',
        rate: '',
    }
}

const action = ref('Add')

const showAddModal = () => {
    clearForm()
    
    action.value = 'Add'

    $('#shif-modal').modal('show')
}

const showEditModal = (shif) => {
    form.value = {
        id: shif.id,
        name: shif.name,
        rate: shif.rate,
    }
    
    action.value = 'Edit'

    $('#shif-modal').modal('show')
}

const loader = ref(true)
const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.name) {
        formUtil.errorMessage('Enter name')
        return
    }

    if (!form.value.rate) {
        formUtil.errorMessage('Enter rate')
        return
    }

    let uri = ''

    if (action.value == 'Add') {
        uri = `${baseUrl}/shif`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/shif/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            fetchShif()
            
            formUtil.successMessage(response.data.message)

            $('#shif-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const shifs = ref([])
const fetchShif = async () => {
    try {
        let response = await apiClient.get(`${baseUrl}/shif`)

        shifs.value = response.data

        loader.value = false
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

onMounted(() => {
    fetchShif()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>