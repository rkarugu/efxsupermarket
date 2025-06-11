<div style="padding:10px" id="job-grade-app">
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
                v-if="(permissions.includes('hr-and-payroll-configurations-job-grade___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Job Grade
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Job Grade</th>
                    <th>Job Level</th>
                    <th>Job Group</th>
                    <th>Min Salary</th>
                    <th>Max Salary</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(jobGrade, index) in jobGrades" :key="jobGrade.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ jobGrade.name }}</td>
                    <td>@{{ jobGrade.job_level.name }}</td>
                    <td>@{{ jobGrade.job_level.job_group.name }}</td>
                    <td>@{{ numberWithCommas(jobGrade.min_salary) }}</td>
                    <td>@{{ numberWithCommas(jobGrade.max_salary) }}</td>
                    <td>@{{ jobGrade.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(jobGrade)" v-if="(permissions.includes('hr-and-payroll-configurations-job-grade___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteJobGrade(jobGrade.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-job-grade___delete') || user.role_id == '1') && !jobGrade.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="job-grade-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Job Grade</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="job-level-select">Job Level</label>
                            <select class="form-control" id="job-level-select" v-model="form.job_level_id" :onchange="jobLevelChanged">
                                <option :value="jobLevel.id" v-for="(jobLevel, index) in jobLevels" :key="jobLevel.id">@{{ `${jobLevel.job_group.name} (${jobLevel.name})` }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="job-grade-name">Job Grade</label>
                            <input type="text" class="form-control" id="job-grade-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="job-grade-min-salary">Minimum Salary</label>
                            <input type="text" class="form-control" id="job-grade-min-salary" placeholder="Enter minimum salary" v-model="form.min_salary">
                        </div>
                        <div class="form-group">
                            <label for="job-grade-max-salary">Maximum Salary</label>
                            <input type="text" class="form-control" id="job-grade-max-salary" placeholder="Enter maximum salary" v-model="form.max_salary">
                        </div>
                        <div class="form-group">
                            <label for="job-grade-description">Description</label>
                            <textarea class="form-control" id="job-grade-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Job Grade</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="job-grade-upload-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Bulk Upload Job Grades</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="job-grade-upload-file" accept=".xlsx" :onchange="documentChanged">
                            <span style="font-size: 12px; font-style: italic">No file selected</span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('hr.configurations.job-grades-bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
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
                    return permissions.includes('hr-and-payroll-configurations-job-grade___edit') || 
                        permissions.includes('hr-and-payroll-configurations-job-grade___delete') ||
                        user.role_id == '1'

                })

                const showUploadModal = () => {
                    $('#job-grade-upload-file').val(null)
                    
                    $('#job-grade-upload-modal').modal('show')
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
                    const file = document.getElementById('job-grade-upload-file')

                    if (!file.files.length) {
                        formUtil.errorMessage('Select a file to upload')
                        return
                    }

                    processing.value = true

                    const formData = new FormData()

                    formData.append('uploaded_file', file.files[0])

                    axios.post('/api/hr/configurations/job-grades-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            fetchJobGrades()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'job_grades_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Job Grades uploaded successfully')
                            }
                            
                            $('#job-grade-upload-modal').modal('hide')

                            file.value = ''
                            $(file).val('').trigger('change')

                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                    
                }
                
                const jobGrades = ref([])
                const fetchJobGrades = () => {
                    axios.get(`${baseUrl}/job-grades-list`)
                        .then(response => jobGrades.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    job_level_id: '',
                    name: '',
                    min_salary: '',
                    max_salary: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.job_level_id = ''
                    form.value.name = ''
                    form.value.min_salary = ''
                    form.value.max_salary = ''
                    form.value.description = ''

                    $('#job-level-select').val(null).trigger('change')
                }

                watch(() => form.value.min_salary, (value) => {
                    form.value.min_salary = numberWithCommas(value.replace(/,/g, ''))
                })

                watch(() => form.value.max_salary, (value) => {
                    form.value.max_salary = numberWithCommas(value.replace(/,/g, ''))
                })

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#job-grade-modal').modal('show')
                }

                const showEditModal = (jobGrade) => {
                    form.value.id = jobGrade.id
                    form.value.job_level_id = jobGrade.job_level_id
                    form.value.name = jobGrade.name
                    form.value.min_salary = numberWithCommas(jobGrade.min_salary)
                    form.value.max_salary = numberWithCommas(jobGrade.max_salary)
                    form.value.description = jobGrade.description

                    $('#job-level-select').val(jobGrade.job_level_id).trigger('change')

                    action.value = 'Edit'

                    $('#job-grade-modal').modal('show')
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

                    if (!form.value.min_salary) {
                        formUtil.errorMessage('Enter minimum salary')
                        return
                    }

                    if (!form.value.max_salary) {
                        formUtil.errorMessage('Enter maximum salary')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/job-grades-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/job-grades-edit`
                    }

                    axios.post(uri, {
                        ...form.value,
                        min_salary: parseInt(form.value.min_salary.replace(/,/g, '')),
                        max_salary: parseInt(form.value.max_salary.replace(/,/g, ''))
                    })
                        .then(response => {
                            fetchJobGrades()
                            
                            formUtil.successMessage(response.data.message)

                            $('#job-grade-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteJobGrade = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this job grade?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/job-grades-delete/${id}`)
                                    .then(response => {
                                        fetchJobGrades()

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
                    fetchJobGrades()

                    // Fetch job levels
                    axios.get(`${baseUrl}/job-levels-list`)
                        .then(response => jobLevels.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))

                    $('#job-grade-app select').select2({
                        placeholder: 'Select...',
                    });
                })

                return {
                    user,
                    permissions,
                    jobGrades,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteJobGrade,
                    canViewActions,
                    numberWithCommas,
                    jobLevels,
                    jobLevelChanged,
                    showUploadModal,
                    documentChanged,
                    submitBulkUpload
                }
            }
        }).mount('#job-grade-app')
    </script>
@endpush