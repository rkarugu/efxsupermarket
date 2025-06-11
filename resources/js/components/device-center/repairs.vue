<template>
    <hr>

    <div class="table-responsive" v-if="!loader">
        <table class="table table-bordered table-hover" id="repairsDatatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Repaired Date</th>
                    <th>Device</th>
                    <th>Repair Cost</th>
                    <th>Charged To</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(repair, index) in repairData" :key="repair.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ repair.repair_date }}</td>
                    <td>{{ repair.repairedDate }}</td>
                    <td>{{ repair.device.device_no }}</td>
                    <td>{{ repair.repairCost }}</td>
                    <td>{{ repair.charged_user }}</td>
                    <td>{{ repair.status }}</td>
                    <td>
                        <a 
                        title="Repair Complete"
                        @click="showCompleteModal(repair)" 
                        v-if="(repair.status=='Repair')" 
                        >
                        <i class="fa-solid fa-thumbs-up"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <Loader v-else />

    <Modal 
        id="complete-modal" 
        :modalTitle="`Completed Device Repair`" 
        :buttonText="`Complete Device Repair`"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                
                <div class="form-group col-md-12">
                    <label for="comment">Comment <small>(Optional)</small></label>
                    <textarea class="form-control" id="comment" rows="5" placeholder="Comment" v-model="form.comment"></textarea>
                </div>
            </div>
        </template>
    </Modal>

</template>

<script setup>
import { onMounted, ref, watch } from 'vue'
import Loader from "@/components/ui/Loader.vue"
import Select2Select from '@/components/ui/form/Select2Select.vue'
import formUtil from '@/composables/useForm.js'
import { useApi } from '@/composables/useApi.js'
import Modal from "@/components/ui/Modal.vue"

const { user, device, userRole, userPermissions } = defineProps({
    user: {
        type: String,
        required: true
    },
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
    device: {
        type: Object,
        required: true
    }
})
const deviceId = device.id;
const userInfo = user
const permissions = Object.keys(JSON.parse(userPermissions))

const action = ref('Add')

const loader = ref(true)
const processing = ref(false)

const { apiClient } = useApi()

const form = ref({
    device_id: deviceId,
    repair_id: null,
    comment: '',
})
const clearForm = () => {
    form.value = {
        device_id: deviceId,
        repair_id: null,
    }
}

const showCompleteModal = (repair) => {
    clearForm()
    form.value.repair_id = repair.id;
    $('#complete-modal').modal('show')
}


const handleSubmit = () => {

    apiClient.post(`device-center/repair-complete`, form.value)
        .then(response => {
            loader.value = true
            
            formUtil.successMessage(response.data.message)
            $('#complete-modal').modal('hide')
            clearForm()
            
            processing.value = false
            fetchDeviceRepair(deviceId)
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const handleSubmitGive = () => {
    if (!formGive.value.otp) {
        formUtil.errorMessage('Provide OTP Value')
        return
    }

    apiClient.post(`device-center/verify-device-allocate`, formGive.value)
        .then(response => {
            loader.value = true
            
            formUtil.successMessage(response.data.message)
            $('#device-give-modal').modal('hide')
            clearGiveForm()
            
            processing.value = false
            fetchDeviceRepair(deviceId)
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const handleSubmitReturn = () => {

    apiClient.post(`device-center/initiate-device-return/${device.id}`, )
        .then(response => {
            loader.value = true
            
            formUtil.successMessage(response.data.message)
            $('#device-return-modal').modal('hide')
            clearReturnForm()
            
            processing.value = false
            fetchDeviceRepair(deviceId)
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}





const repairData = ref([])
const fetchDeviceRepair = async (deviceId) => {
    loader .value = true
    try {
        
        let response = await apiClient.get(`device-center/get-device-repair/${deviceId}`)
        
        repairData.value = response.data.data

        loader.value = false

        $(document).ready(function() {
            $('#repairsDatatable').DataTable({
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
                'scrollX': true,
                // 'scrollY': '100vh',
               
            });
            
        })
    } catch (error) {
        formUtil.errorMessage(error.response?.data?.message || "An error occurred")
        loader.value = false
    }
}


const usersData = ref([])
const fetchDeviceUsers = async (deviceId) => {
    loader .value = true
    try {
        
        let response = await apiClient.get(`device-center/get-device-users/${deviceId}`)
        
        usersData.value = response.data.data
        loader.value = false
    } catch (error) {
        formUtil.errorMessage(error.response?.data?.message || "An error occurred")
        loader.value = false
    }
}

watch(() => repairData, () => {
    loader.value = false
    
})

onMounted(() => {
    fetchDeviceRepair(deviceId)
    fetchDeviceUsers(deviceId)
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>