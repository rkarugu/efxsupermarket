<template>
    <!-- && (user.id == device.latest_device_log?.issued_to) && (device.latest_device_log?.is_received ==0) -->
    <div style="text-align: right">
        <button 
            class="btn btn-primary" 
            title="Allocate Sim Card"
            @click="showAddSimModal" 
            v-if="(permissions.includes('device-center___add-sim') || userRole == '1') && (!device?.sim_card)" 
        >
            <i class="fa fa-plus"></i> Sim Card
        </button>
        <button 
            class="btn btn-primary" 
            title="Remove Sim Card"
            @click="showRemoveSimModal" 
            v-if="(permissions.includes('device-center___remove-sim') || userRole == '1') && (device?.sim_card)" 
        >
            <i class="fa fa-minus"></i> Sim Card
        </button>
    </div>

    <hr>

    <div class="table-responsive" v-if="!loader">
        <table class="table table-bordered table-hover" id="simDatatable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Date</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(sim, index) in simData" :key="sim.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ sim.createdOn }}</td>
                    <td>{{ sim.createdBy }}</td>
                </tr>
            </tbody>
        </table>

    </div>
    <Loader v-else />

    <Modal 
        id="add-sim-modal" 
        :modalTitle="`Allocate Sim Card`" 
        :buttonText="`Allocate Sim Card`"
        :processing
        @submit-clicked="handleSubmit"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <label for="sim_card">Sim Card <span style="color: red">*</span></label>
                    <Select2Select 
                        :options="simsData" 
                        optionValue="id" 
                        optionLabel="phone_number" 
                        placeholder="Select Sim Card..." 
                        v-model="form.sim_card" 
                    />
                </div>
            </div>
        </template>
    </Modal>

    <Modal 
        id="remove-sim-modal" 
        :modalTitle="`Remove Sim Card`" 
        :buttonText="`Remove Sim Card`"
        :processing
        @submit-clicked="handleRemoveSubmit"
    >
        <template #modal-body>
            <div class="row">
                <div class="form-group col-md-12">
                    <p>Are you Sure you want to Remove Sim Card?</p>
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
const simsData = ref([]);

const { apiClient } = useApi()

const form = ref({
    device_id: deviceId,
    sim_card: ''
})
const clearForm = () => {
    form.value = {
        device_id: deviceId,
        sim_card: ''
    }
}

const showAddSimModal = () => {
    clearForm()

    fetchSimCards()

    $('#add-sim-modal').modal('show')
}

const formRemove = ref({
    device_id: deviceId,
    sim_card: device?.sim_card?.id
})
const clearFormRemove = () => {
    form.value = {
        device_id: deviceId,
        sim_card: device?.sim_card?.id
    }
}

const showRemoveSimModal = () => {
    clearFormRemove()

    $('#remove-sim-modal').modal('show')
}


const handleSubmit = () => {
    if (!form.value.sim_card) {
        formUtil.errorMessage('Select Sim Card')
        return
    }

    apiClient.post(`device-center/allocate-device-simcard`, form.value)
        .then(response => {
            loader.value = true
            
            formUtil.successMessage(response.data.message)
            $('#add-sim-modal').modal('hide')
            clearForm()
            
            processing.value = false
            fetchDeviceSim(deviceId)
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const handleRemoveSubmit = () => {

    apiClient.post(`device-center/remove-device-simcard`, formRemove.value)
        .then(response => {
            loader.value = true
            
            formUtil.successMessage(response.data.message)
            $('#remove-sim-modal').modal('hide')
            clearFormRemove()
            
            processing.value = false
            fetchDeviceSim(deviceId)
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}


const simData = ref([])
const fetchSimCards = async (deviceId) => {
    loader .value = true
    try {
        
        let response = await apiClient.get(`device-center/get-device-sims`)
        
        simsData.value = response.data.data

        loader.value = false

    } catch (error) {
        formUtil.errorMessage(error.response?.data?.message || "An error occurred")
        loader.value = false
    }
}

const fetchDeviceSim = async (deviceId) => {
    loader .value = true
    try {
        
        let response = await apiClient.get(`device-center/get-device-sim/${deviceId}`)
        
        simData.value = response.data.data

        loader.value = false

        $(document).ready(function() {
            $('#simDatatable').DataTable({
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

watch(() => simData, () => {
    loader.value = false
})


onMounted(() => {    
    fetchDeviceSim(deviceId)
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }
</style>