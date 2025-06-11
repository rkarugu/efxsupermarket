<template>

    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            title="Allocate Device"
            @click="showAddModal" 
            v-if="(permissions.includes('device-center___allocate') || userRole == '1') && (!device.latest_device_log || user.id == device.latest_device_log?.issued_to.id) && (!device.latest_repair || device.latest_repair?.status=='Completed')" 
        >
            <i class="fa fa-plus"></i> Allocate
        </button>
        <button 
                        class="btn btn-primary"
                        title="Initiate Return Device" 
                        @click="showReturnModal" 
                        v-if="(permissions.includes('device-center___initiate-return') || userRole == '1') && (device.latest_device_log?.is_received ==1)  && (device.latest_device_log?.issued_to.id != user.id)" 
                    >
                    <i class="fa-solid fa-right-left"></i> Initiate Return
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-if="!loader">
        <table class="table table-bordered table-hover" id="historyDatatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Issued To</th>
                    <th>Issued By</th>
                    <th>Status</th>
                    <th>Type</th>
                    <th>Received</th>
                    <th>Comment</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(history, index) in historyData" :key="history.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ history.date_issued }}</td>
                    <td>{{ history.issued_to.name }}</td>
                    <td>{{ history.issued_by.name }}</td>
                    <td>{{ history.status }}</td>
                    <td>{{ history.issue_type }}</td>
                    <td>{{ history.is_received?'True':'False' }}</td>
                    <td>{{ history.issued_by_comment }}</td>
                    <td>
                        <a 
                        title="Receive Device"
                        @click="showGiveModal(history)" 
                        v-if="(history.is_received==0)" 
                        >
                            <i class="fa-solid fa-hand-holding-hand"></i>
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
    <Loader v-else />

    <Modal 
        id="device-modal" 
        :modalTitle="`Transfer Device`" 
        :buttonText="`Transfer Device`"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                
                <div class="form-group col-md-12">
                    <label for="issued_to">Issue To <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="usersData" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select Issue To..." 
                        v-model="form.issued_to" 
                    />
                </div>
                <div class="form-group col-md-12">
                    <label for="issue-type">Issue Type <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="types" 
                        optionValue="id" 
                        optionLabel="name" 
                        placeholder="Select Issue type..."  
                        v-model="form.type" 
                    />
                </div>
                <div class="form-group col-md-12">
                    <label for="comment">Comment <small>(Optional)</small></label>
                    <textarea class="form-control" id="comment" rows="5" placeholder="Comment" v-model="form.comment"></textarea>
                </div>
                
            </div>
        </template>
    </Modal>

    <Modal 
        id="device-give-modal" 
        :modalTitle="`Give Device`" 
        :buttonText="`Verify Give Device`"
        :processing
        @submit-clicked="handleSubmitGive"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="issued_to">Issue To <span style="color: red">*</span></label>
                    <input class="form-control" type="text" v-model="formGive.issued_to" disabled>
                </div>
                <div class="form-group col-md-12">
                    <label for="issue-type">Otp <span style="color: red">*</span></label>
                    <input class="form-control" type="text" v-model="formGive.otp">
                    <!-- <small>{{ formGive.verify_otp }}</small> -->
                </div>
                <div class="form-group col-md-12" v-if="historyData[0]?.issued_by_comment === null">
                    <label for="comment">Comment <small>(Optional)</small></label>
                    <textarea class="form-control" id="comment" rows="5" placeholder="Comment" v-model="formGive.comment"></textarea>
                </div>
            </div>
        </template>
    </Modal>

    <Modal 
        id="device-return-modal" 
        :modalTitle="`Return Device`" 
        :buttonText="`Return Device`"
        :processing
        @submit-clicked="handleSubmitReturn"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <p>Are you Sure you want to Initiate Return of <b>Device No.{{ device.device_no }}</b> from <b>{{ device.latest_device_log?.issued_to.name }}</b>?</p>
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
    issued_to: '',
    type: '',
})
const clearForm = () => {
    form.value = {
        device_id: deviceId,
        issued_to: '',
        type: '',
    }
}

const formGive = ref({
    device_id: deviceId,
    logId: '',
    issued_to: '',
    otp: '',
    verify_otp: '',
    comment: ''
})

const clearGiveForm = () => {
    formGive.value = {
        device_id: deviceId,
        logId: '',
        issued_to: '',
        otp: '',
        verify_otp: '',
        comment: ''
    }
}

const formReturn = ref({
    device_id: deviceId,
    logId: '',
});

const clearReturnForm = () => {
    form.value = {
        device_id: deviceId,
        logId: '',
    }
}


const types = [
    {
        id: 'Permanent',
        name: 'Permanent'
    },
    {
        id: 'Temporary',
        name: 'Temporary'
    },
]

const showAddModal = () => {
    clearForm()

    $('#device-modal').modal('show')
}

const showGiveModal = (log) => {
    clearGiveForm()
    formGive.value.logId = log.id;
    formGive.value.issued_to = log.issued_to.name;
    formGive.value.verify_otp = log.verify_otp;
    $('#device-give-modal').modal('show')
}

const showReturnModal = () => {
    clearReturnForm()
    $('#device-return-modal').modal('show')
}

const handleSubmit = () => {
    if (!form.value.type) {
        formUtil.errorMessage('Select Issue type')
        return
    }
    
    if (!form.value.issued_to) {
        formUtil.errorMessage('Select Issued To')
        return
    }  

    apiClient.post(`device-center/allocate-device`, form.value)
        .then(response => {
            loader.value = true
            
            formUtil.successMessage(response.data.message)
            $('#device-modal').modal('hide')
            clearForm()
            
            processing.value = false
            fetchDeviceHistory(deviceId)
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
            fetchDeviceHistory(deviceId)
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
            fetchDeviceHistory(deviceId)
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}





const historyData = ref([])
const fetchDeviceHistory = async (deviceId) => {
    loader .value = true
    try {
        
        let response = await apiClient.get(`device-center/get-device-history/${deviceId}`)
        
        historyData.value = response.data.data

        loader.value = false

        $(document).ready(function() {
            $('#historyDatatable').DataTable({
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

watch(() => historyData, () => {
    loader.value = false
})

onMounted(() => {
    fetchDeviceHistory(deviceId)
    fetchDeviceUsers(deviceId)
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>