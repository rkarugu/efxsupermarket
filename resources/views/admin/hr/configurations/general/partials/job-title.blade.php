<div style="padding:10px" id="job-title-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-info pull-right" 
                style="margin-left: 10px"
                @click="showUploadModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-job-grade___bulk-upload') || user.role_id == '1')"
            >
                <i class="fa fa-upload"></i>
                Bulk Upload
            </button>
            
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-job-title___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Job Title
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Job Title</th>
                    <th>Job Level</th>
                    <th>Job Group</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(jobTitle, index) in jobTitles" :key="jobTitle.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ jobTitle.name }}</td>
                    <td>@{{ jobTitle.job_level.name }}</td>
                    <td>@{{ jobTitle.job_level.job_group.name }}</td>
                    <td>@{{ jobTitle.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(jobTitle)" v-if="(permissions.includes('hr-and-payroll-configurations-job-title___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteJobTitle(jobTitle.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-job-title___delete') || user.role_id == '1') && !jobTitle.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="job-title-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Job Title</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="job-level-select-2">Job Level</label>
                            <select class="form-control" id="job-level-select-2" v-model="form.job_level_id" :onchange="jobLevelChanged">
                                <option :value="jobLevel.id" v-for="(jobLevel, index) in jobLevels" :key="jobLevel.id">@{{ `${jobLevel.job_group.name} (${jobLevel.name})` }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="job-title-name">Job Title</label>
                            <input type="text" class="form-control" id="job-title-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="job-title-description">Description</label>
                            <textarea class="form-control" id="job-title-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Job Title</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="job-title-upload-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Bulk Upload Job Titles</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="job-title-upload-file" accept=".xlsx" :onchange="documentChanged">
                            <span style="font-size: 12px; font-style: italic">No file selected</span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('hr.configurations.job-titles-bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
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
        import {createApp, onMounted, ref, computed, watch} from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const permissions = Object.keys(user.permissions)

                const formUtil = new Form()
                const baseUrl = '/api/hr/configurations'

                const canViewActions = computed(() => {
                    return permissions.includes('hr-and-payroll-configurations-job-title___edit') || 
                        permissions.includes('hr-and-payroll-configurations-job-title___delete') ||
                        user.role_id == '1'

                })

                const showUploadModal = () => {
                    $('#job-title-upload-file').val(null)
                    
                    $('#job-title-upload-modal').modal('show')
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
                    const file = document.getElementById('job-title-upload-file')

                    if (!file.files.length) {
                        formUtil.errorMessage('Select a file to upload')
                        return
                    }

                    processing.value = true

                    const formData = new FormData()

                    formData.append('uploaded_file', file.files[0])

                    axios.post('/api/hr/configurations/job-titles-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            fetchJobTitles()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'job_titles_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Job Titles uploaded successfully')
                            }
                            
                            $('#job-title-upload-modal').modal('hide')

                            file.value = ''
                            $(file).val('').trigger('change')

                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                    
                }
                
                const jobTitles = ref([])
                const fetchJobTitles = () => {
                    axios.get(`${baseUrl}/job-titles-list`)
                        .then(response => jobTitles.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    job_level_id: '',
                    name: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.job_level_id = ''
                    form.value.name = ''
                    form.value.description = ''

                    $('#job-level-select-2').val('').trigger('change')
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#job-title-modal').modal('show')
                }

                const showEditModal = (jobTitle) => {
                    form.value.id = jobTitle.id
                    form.value.job_level_id = jobTitle.job_level_id
                    form.value.name = jobTitle.name
                    form.value.description = jobTitle.description

                    $('#job-level-select-2').val(jobTitle.job_level_id).trigger('change')

                    action.value = 'Edit'

                    $('#job-title-modal').modal('show')
                }

                const jobLevelChanged = (event) => {
                    form.value.job_level_id = $(event.target).val()
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.job_level_id) {
                        formUtil.errorMessage('Select job level')
                        return
                    }

                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/job-titles-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/job-titles-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchJobTitles()
                            
                            formUtil.successMessage(response.data.message)

                            $('#job-title-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteJobTitle = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this job title?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/job-titles-delete/${id}`)
                                    .then(response => {
                                        fetchJobTitles()

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

                const jobLevels = ref([])
                onMounted(() => {
                    fetchJobTitles()

                    // Fetch job levels
                    axios.get(`${baseUrl}/job-levels-list`)
                        .then(response => jobLevels.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))

                    $('#job-title-app select').select2({
                        placeholder: 'Select...',
                    });
                })

                return {
                    user,
                    permissions,
                    jobTitles,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteJobTitle,
                    canViewActions,
                    jobLevels,
                    jobLevelChanged,
                    showUploadModal,
                    documentChanged,
                    submitBulkUpload
                }
            }
        }).mount('#job-title-app')
    </script>
@endpush