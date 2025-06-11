<div style="padding:10px" id="job-group-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-info pull-right" 
                style="margin-left: 10px"
                @click="showUploadModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-job-group___bulk-upload') || user.role_id == '1')"
            >
                <i class="fa fa-upload"></i>
                Bulk Upload
            </button>
            
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-job-group___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Job Group
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Job Group</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(jobGroup, index) in jobGroups" :key="jobGroup.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ jobGroup.name }}</td>
                    <td>@{{ jobGroup.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(jobGroup)" v-if="(permissions.includes('hr-and-payroll-configurations-job-group___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteJobGroup(jobGroup.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-job-group___delete') || user.role_id == '1') && !jobGroup.job_levels_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="job-group-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Job Group</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="job-group-name">Job Group</label>
                            <input type="text" class="form-control" id="job-group-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="job-group-description">Description</label>
                            <textarea class="form-control" id="job-group-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Job Group</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="job-group-upload-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Bulk Upload Job Groups</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="job-group-upload-file" accept=".xlsx" :onchange="documentChanged">
                            <span style="font-size: 12px; font-style: italic">No file selected</span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('hr.configurations.job-groups-bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled="processing" @click="submitBulkUpload">Upload</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import {createApp, onMounted, ref, computed} from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const permissions = Object.keys(user.permissions)

                const formUtil = new Form()
                const baseUrl = '/api/hr/configurations'

                const canViewActions = computed(() => {
                    return permissions.includes('hr-and-payroll-configurations-job-group___edit') || 
                        permissions.includes('hr-and-payroll-configurations-job-group___delete') ||
                        user.role_id == '1'

                })

                const showUploadModal = () => {
                    $('#job-group-upload-file').val(null)
                    
                    $('#job-group-upload-modal').modal('show')
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
                    const file = document.getElementById('job-group-upload-file')

                    if (!file.files.length) {
                        formUtil.errorMessage('Select a file to upload')
                        return
                    }

                    processing.value = true

                    const formData = new FormData()

                    formData.append('uploaded_file', file.files[0])

                    axios.post('/api/hr/configurations/job-groups-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            // fetchJobGroups()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'job_groups_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Job Groups uploaded successfully')
                            }
                            
                            // $('#job-group-upload-modal').modal('hide')

                            // file.value = ''
                            // $(file).val('').trigger('change')

                            // processing.value = false

                            setTimeout(() => {
                                window.location.reload()
                            }, 3000)
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                    
                }
                
                const jobGroups = ref([])
                const fetchJobGroups = () => {
                    axios.get(`${baseUrl}/job-groups-list`)
                        .then(response => jobGroups.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    name: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.name = ''
                    form.value.description = ''
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#job-group-modal').modal('show')
                }

                const showEditModal = (jobGroup) => {
                    form.value.id = jobGroup.id
                    form.value.name = jobGroup.name
                    form.value.description = jobGroup.description

                    action.value = 'Edit'

                    $('#job-group-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/job-groups-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/job-groups-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            // fetchJobGroups()
                            
                            formUtil.successMessage(response.data.message)

                            // $('#job-group-modal').modal('hide')
                            
                            // clearForm()
                            
                            // processing.value = false

                            setTimeout(() => {
                                window.location.reload()
                            }, 1000);
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteJobGroup = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this job group?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/job-groups-delete/${id}`)
                                    .then(response => {
                                        fetchJobGroups()

                                        formUtil.successMessage(response.data.message)

                                        processing.value = false
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.message)
                                        processing.value = false
                                    })
                            }
                        }) 
                }

                onMounted(() => {
                    fetchJobGroups()
                })

                return {
                    user,
                    permissions,
                    jobGroups,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteJobGroup,
                    canViewActions,
                    showUploadModal,
                    documentChanged,
                    submitBulkUpload
                }
            }
        }).mount('#job-group-app')
    </script>
@endpush