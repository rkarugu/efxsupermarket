<template>
    <Card cardTitle="Casuals">

        <template #header-action>
            <div>
                <button
                    class="btn btn-info"
                    style="margin-right: 10px"
                    data-toggle="modal"
                    data-target="#bulk-upload-modal"
                    v-if="(permissions.includes('hr-management-casuals___bulk-upload') || userRole == '1')"
                >
                    <i class="fa fa-upload"></i>
                    Bulk Upload
                </button>
                
                <a href="/admin/hr/management/casuals/create" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i>
                    Add New Casual
                </a>
            </div>
        </template>

        <div class="row filters">
            <div class="col-md-2 form-group">
                <label for="">Branch</label>
                <Select2Select 
                    :options="branches" 
                    optionValue="id" 
                    optionLabel="name" 
                    placeholder="Select Branch..." 
                    v-model="branchId" 
                />
            </div>
            
            <div class="form-group">
                <button type="button" class="btn btn-success" :disabled="processing" @click="handleFilter">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </div>
            
            <div class="form-group" v-if="false">
                <button type="button" class="btn btn-secondary" style="margin-left: 10px;" :disabled="processing" @click="clearFilter" v-if="filter">
                    <i class="fas fa-filter-circle-xmark"></i>
                    Clear Filter
                </button>
            </div>
        </div>

        <hr style="margin-top: 0;">

        <div class="table-responsive" v-if="!loader">
            <table class="table table-bordered table-hover nowrap" id="create_datatable_10">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Branch</th>
                        <th>Phone No.</th>
                        <th>ID No.</th>
                        <th>Gender</th>
                        <th>Date of Birth</th>
                        <th>Status</th>
                        <th class="noneedtoshort">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(casual, index) in casuals" :key="casual.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ casual.full_name }}</td>
                        <td>{{ casual.branch.name }}</td>
                        <td>{{ casual.phone_no }}</td>
                        <td>{{ casual.id_no }}</td>
                        <td>{{ casual.gender.name }}</td>
                        <td>{{ casual.date_of_birth }}</td>
                        <td style="text-align: center" >
                            <span class="badge bg-green" v-if="casual.active">Active</span>
                            <span class="badge bg-yellow" v-else>Inactive</span>
                        </td>
                        <td style="text-align: center; color: #337ab7;">
                            <template v-if="canEdit">
                                <a :href="`/admin/hr/management/casuals/${casual.id}/edit`" class="btn-icon">
                                    <i class="fas fa-edit" title="Edit"></i>
                                </a>
                                <a href="javascript:void(0)" @click="deactivate(casual)" class="btn-icon" v-if="casual.active">
                                    <i class="fas fa-user-slash" title="Set As Inactive"></i>
                                </a>
                                <a href="javascript:void(0)" @click="activate(casual)" class="btn-icon" v-else>
                                    <i class="fas fa-user-check" title="Set As Active"></i>
                                </a>
                            </template>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Loader v-else />
    </Card>

    <Modal
        id="deactivation-modal"
        modalTitle="Deactivate Casual"
        buttonText="Deactivate"
        :processing
        @submit-clicked="handleDeactivation"
    >
        <template #modal-body>
            <p>Are you sure you want to deactivate this casual?</p>

            <div class="form-group">
                <label for="reason">Reason <span style="color: red">*</span></label>
                <input type="text" class="form-control" id="reason" placeholder="Enter reason" v-model="form.reason">
            </div>

            <div class="form-group">
                <label for="narration">Narration</label>
                <textarea class="form-control" id="narration" rows="5" placeholder="Enter narration" v-model="form.narration"></textarea>
            </div>
        </template>
    </Modal>

    <Modal
        id="activation-modal"
        modalTitle="Activate Casual"
        buttonText="Activate"
        :processing
        @submit-clicked="handleActivation"
    >
        <template #modal-body>
            Are you sure you want to activate this casual?
        </template>
    </Modal>

    <div class="modal fade" id="bulk-upload-modal" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Bulk Upload Casuals</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input type="file" class="form-control" id="upload-file" accept=".xlsx" :onchange="documentChanged">
                        <span style="font-size: 12px; font-style: italic">No file selected</span>
                    </div>

                </div>
                <div class="modal-footer">
                    <a :href="bulkUploadTemplate" class="btn btn-warning pull-left" download>Download Template</a>
                    <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled="processing" @click="submitBulkUpload">Upload</button>
                    <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import Card from "@/components/ui/Card.vue"
import Modal from "@/components/ui/Modal.vue"
import Loader from "@/components/ui/Loader.vue"
import Select2Select from "@/components/ui/form/Select2Select.vue"
import { useApi } from '@/composables/useApi.js'
import formUtil from '@/composables/useForm.js'

const { apiClient } = useApi()

const props = defineProps({
    userRole: {
        type: String,
        required: true
    },
    userPermissions: {
        type: String,
        required: true
    },
    branches: {
        type: String,
        required: true
    },
    bulkUploadTemplate: {
        type: String,
        required: true
    },
})

const branches = JSON.parse(props.branches)
branches.unshift({
    id: 0,
    name: 'ALL'
})
const permissions = Object.keys(JSON.parse(props.userPermissions))

const branchId = ref(0)
const employmentTypeId = ref('')

const canEdit = computed(() => permissions.includes('hr-management-casuals___edit') || props.userRole == 1)

const processing = ref(false)

const form = ref({
    id: '',
    reason: '',
    narration: '',
})

const activate = (casual) => {
    form.value.id = casual.id
    
    $('#activation-modal').modal('show')
}

const handleActivation = () => {
    processing.value = true

    apiClient.post(`hr/management/casuals-activate/${form.value.id}`)
        .then(response => {
            formUtil.successMessage(response.data.message)

            $('#activation-modal').modal('hide')

            fetchCasuals()

            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const deactivate = (casual) => {
    form.value.id = casual.id
    
    $('#deactivation-modal').modal('show')
}

const handleDeactivation = () => {
    if (!form.value.reason) {
        formUtil.errorMessage('Enter reason')
        return
    }
    
    processing.value = true

    apiClient.post(`hr/management/casuals-deactivate/${form.value.id}`, form.value)
        .then(response => {
            formUtil.successMessage(response.data.message)

            $('#deactivation-modal').modal('hide')

            form.value = {
                reason: '',
                narration: '',
            }

            fetchCasuals()

            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
}

const filter = ref(false)
const handleFilter = async () => {
    processing.value = true
    
    await fetchCasuals(branchId.value)
    
    processing.value = false
    filter.value = true
}

const clearFilter = async () => {
    branchId.value = 0
    processing.value = true

    await fetchCasuals()

    processing.value = false
    filter.value = false
}

const loader = ref(true)

const casuals = ref({})
const fetchCasuals = async (branchId = '') => {
    loader.value = true
    
    try {
        let response = await apiClient.get('hr/management/casuals', {
            params: {
                'branch_id': branchId == '0' ? '' : branchId,
            }
        })
        
        casuals.value = response.data

        loader.value = false

        $(document).ready(function() {
            $('#create_datatable_10').DataTable({
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
        formUtil.errorMessage(error.response.data.message)
    }
}

const documentChanged = (event) => {
    let fileInput = event.target

    if (fileInput.files.length) {
        fileInput.nextElementSibling.innerText = fileInput.files[0].name
    } else {
        fileInput.nextElementSibling.innerText = 'No file selected'
    }
}

const submitBulkUpload = () => {
    const file = document.getElementById('upload-file')

    if (!file.files.length) {
        formUtil.errorMessage('Select a file to upload')
        return
    }

    processing.value = true

    const formData = new FormData()

    formData.append('uploaded_file', file.files[0])

    apiClient.post('hr/management/casuals-bulk-upload', formData, {
        responseType: 'blob'
    })
        .then(response => {
            fetchCasuals(branchId.value)

            if (response.status == 201) {
                
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;

                link.setAttribute('download', 'casuals_bulk_upload_errors.xlsx');
                
                document.body.appendChild(link);
                link.click();

                document.body.removeChild(link);
                
                formUtil.warningMessage("Some errors were encountered during upload.")
            } else {
                formUtil.successMessage('Casuals uploaded successfully')
            }
            
            $('#bulk-upload-modal').modal('hide')

            file.value = ''
            $(file).val('').trigger('change')

            processing.value = false
        })
        .catch(error => {
            formUtil.errorMessage(error.response.data.message)
            processing.value = false
        })
    
}

onMounted(() => {
    fetchCasuals()
})

</script>

<style scoped>
    .text-right {
        text-align: right;
    }

    .modal table thead th {
        text-transform: uppercase;
    }

    .modal table tbody td {
        padding: 5px
    }

    .filters {
        display: flex;
        align-items: flex-end;
    }
</style>
