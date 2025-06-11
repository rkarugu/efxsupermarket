<div style="padding:10px" id="job-level-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-info pull-right" 
                style="margin-left: 10px"
                @click="showUploadModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-job-level___bulk-upload') || user.role_id == '1')"
            >
                <i class="fa fa-upload"></i>
                Bulk Upload
            </button>
            
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-job-level___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Job Level
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Job Level</th>
                    <th style="width: 30%">Job Group</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(jobLevel, index) in jobLevels" :key="jobLevel.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ jobLevel.name }}</td>
                    <td>@{{ jobLevel.job_group.name }}</td>
                    <td>@{{ jobLevel.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(jobLevel)" v-if="(permissions.includes('hr-and-payroll-configurations-job-level___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteJobLevel(jobLevel.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-job-level___delete') || user.role_id == '1') && (!jobLevel.job_grades_count && !jobLevel.job_titles_count)"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="job-level-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Job Level</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="job-group-select">Job Group</label>
                            <select class="form-control" id="job-group-select" v-model="form.job_group_id" :onchange="jobGroupChanged">
                                <option :value="jobGroup.id" v-for="(jobGroup, index) in jobGroups" :key="jobGroup.id">@{{ jobGroup.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="job-level-name">Job Level</label>
                            <input type="text" class="form-control" id="job-level-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="job-level-description">Description</label>
                            <textarea class="form-control" id="job-level-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Job Level</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="job-level-upload-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Bulk Upload Job Levels</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="job-level-upload-file" accept=".xlsx" :onchange="documentChanged">
                            <span style="font-size: 12px; font-style: italic">No file selected</span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('hr.configurations.job-levels-bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
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
                    return permissions.includes('hr-and-payroll-configurations-job-level___edit') || 
                        permissions.includes('hr-and-payroll-configurations-job-level___delete') ||
                        user.role_id == '1'

                })

                const showUploadModal = () => {
                    $('#job-level-upload-file').val(null)
                    
                    $('#job-level-upload-modal').modal('show')
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
                    const file = document.getElementById('job-level-upload-file')

                    if (!file.files.length) {
                        formUtil.errorMessage('Select a file to upload')
                        return
                    }

                    processing.value = true

                    const formData = new FormData()

                    formData.append('uploaded_file', file.files[0])

                    axios.post('/api/hr/configurations/job-levels-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            // fetchJobLevels()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'job_levels_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Job Levels uploaded successfully')
                            }
                            
                            // $('#job-level-upload-modal').modal('hide')

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
                
                const jobLevels = ref([])
                const fetchJobLevels = () => {
                    axios.get(`${baseUrl}/job-levels-list`)
                        .then(response => jobLevels.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    job_group_id: '',
                    name: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.job_group_id = ''
                    form.value.name = ''
                    form.value.description = ''

                    $('#job-group-select').val(null).trigger('change')
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#job-level-modal').modal('show')
                }

                const showEditModal = (jobLevel) => {
                    form.value.id = jobLevel.id
                    form.value.job_group_id = jobLevel.job_group_id
                    form.value.name = jobLevel.name
                    form.value.description = jobLevel.description

                    $('#job-group-select').val(jobLevel.job_group_id).trigger('change')

                    action.value = 'Edit'

                    $('#job-level-modal').modal('show')
                }

                const jobGroupChanged = (event) => {
                    form.value.job_group_id = $(event.target).val()
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/job-levels-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/job-levels-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            // fetchJobLevels()
                            
                            formUtil.successMessage(response.data.message)

                            // $('#job-level-modal').modal('hide')
                            
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

                const deleteJobLevel = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this job level?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/job-levels-delete/${id}`)
                                    .then(response => {
                                        fetchJobLevels()

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

                const jobGroups = ref([])
                onMounted(() => {
                    fetchJobLevels()

                    // Fetch job groups
                    axios.get(`${baseUrl}/job-groups-list`)
                        .then(response => jobGroups.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))

                        $('#job-level-app select').select2({
                            placeholder: 'Select...',
                        });
                })

                return {
                    user,
                    permissions,
                    jobLevels,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteJobLevel,
                    canViewActions,
                    jobGroups,
                    jobGroupChanged,
                    showUploadModal,
                    documentChanged,
                    submitBulkUpload
                }
            }
        }).mount('#job-level-app')
    </script>
@endpush