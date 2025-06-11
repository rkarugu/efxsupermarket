<template>
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            @click="showAddModal" 
            v-if="(permissions.includes('hr-and-payroll-configurations-setting___create') || userRole == '1')"
        >
            <i class="fa fa-plus"></i>
            Add Setting
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-show="!loader">
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Name</th>
                    <th>Value</th>
                    <th class="noneedtoshort" style="width: 10%;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(setting, index) in settings" :key="setting.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ setting.name }}</td>
                    <td>{{ setting.value }}</td>
                    <td style="text-align: center; color: #337ab7;">
                        <a href="javascript:void(0)" @click="showEditModal(setting)" v-if="canEdit">
                            <i class="fas fa-edit" title="Edit"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <Loader v-show="loader" />

    <Modal 
        id="settings-modal" 
        :modalTitle="`${action} Setting`" 
        :buttonText="`${action} Setting`"
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
                    <label for="value">Value <span style="color: red">*</span></label>
                    <input type="text" class="form-control" id="value" placeholder="Value" v-model="form.value">
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
})

const permissions = Object.keys(JSON.parse(props.userPermissions))

const canEdit = computed(() => permissions.includes('hr-and-payroll-configurations-setting___edit') || props.userRole == '1')

const { apiClient } = useApi()

const baseUrl = 'hr/configurations'

const form = ref({
    id: '',
    name: '',
    value: '',
})

const clearForm = () => {
    form.value = {
        id: '',
        name: '',
        value: '',
    }
}

const action = ref('Add')

const showAddModal = () => {
    clearForm()
    
    action.value = 'Add'

    $('#settings-modal').modal('show')
}

const showEditModal = (setting) => {
    form.value = {
        id: setting.id,
        name: setting.name,
        value: setting.value,
    }
    
    action.value = 'Edit'

    $('#settings-modal').modal('show')
}

const loader = ref(true)
const processing = ref(false)

const handleSubmit = () => {
    if (!form.value.name) {
        formUtil.errorMessage('Enter name')
        return
    }

    if (!form.value.value) {
        formUtil.errorMessage('Enter value')
        return
    }

    let uri = ''

    if (action.value == 'Add') {
        uri = `${baseUrl}/payroll-settings`
    } else if (action.value == 'Edit') {
        uri = `${baseUrl}/payroll-settings/${form.value.id}`
    }

    apiClient.post(uri, form.value)
        .then(response => {
            loader.value = true
            
            fetchSettings()
            
            formUtil.successMessage(response.data.message)

            $('#settings-modal').modal('hide')
            
            clearForm()
            
            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const settings = ref([])
const fetchSettings = async () => {
    try {
        let response = await apiClient.get(`${baseUrl}/payroll-settings`)

        settings.value = response.data

        loader.value = false
    } catch (error) {
        formUtil.errorMessage(error.response.data.message)
    }
}

onMounted(() => {
    fetchSettings()
})

</script>
