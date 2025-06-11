<div style="padding:10px" id="professional-information-app">
    <div v-cloak>
        <div style="text-align: right">
            <button class="btn btn-primary" @click="showAddModal">
                <i class="fa fa-plus"></i>
                Add Professional History
            </button>
        </div>

        <hr>
        
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Organization Name</th>
                    <th>Job Title</th>
                    <th>Job Level</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(professionalHistory, index) in professionalHistories" :key="professionalHistory.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ professionalHistory.organization_name }}</td>
                    <td>@{{ professionalHistory.job_title }}</td>
                    <td>@{{ professionalHistory.job_level }}</td>
                    <td>@{{ professionalHistory.start_date }}</td>
                    <td>@{{ professionalHistory.end_date }}</td>
                    <td style="text-align: center">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(professionalHistory)">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="confirmDelete(professionalHistory.id)">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="professional-history-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Professional History </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="organization-name">Organization Name</label>
                            <input type="text" class="form-control" id="organization-name" placeholder="Enter organization name" v-model="form.organization_name">
                        </div>
                        <div class="form-group">
                            <label for="job-title">Job Title</label>
                            <input type="text" class="form-control" id="job-title" placeholder="Enter job title" v-model="form.job_title">
                        </div>
                        <div class="form-group">
                            <label for="job-level">Job Level</label>
                            <input type="text" class="form-control" id="job-level" placeholder="Enter job level" v-model="form.job_level">
                        </div>
                        <div class="form-group">
                            <label for="start-date">Start Date</label>
                            <input type="date" class="form-control" id="start-date" placeholder="Enter start date" v-model="form.start_date">
                        </div>
                        <div class="form-group">
                            <label for="end-date">End Date</label>
                            <input type="date" class="form-control" id="end-date" placeholder="Enter end date" v-model="form.end_date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Professional History</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="professional-history-confirm-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> @{{ modalTitle }} </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        @{{ modalMessage }}
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" :disabled="processing" @click="modalActionRef">@{{ modalAction }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import { createApp, ref, onMounted } from 'vue';

        createApp({
            setup() {
                const employee = {!! $employee !!}

                const formUtil = new Form()
                const baseUrl = 'hr/management'

                const form = ref({
                    employee_id: employee.id,
                    organization_name: '',
                    job_title: '',
                    job_level: '',
                    start_date: '',
                    end_date: '',
                })

                const clearForm = () => {
                    form.value.organization_name = ''
                    form.value.job_title = ''
                    form.value.job_level = ''
                    form.value.start_date = ''
                    form.value.end_date = ''
                }

                const action = ref('Add')
                
                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#professional-history-modal').modal('show')
                }
                
                const editId = ref(null)
                const showEditModal = (professionalHistory) => {
                    action.value = 'Edit'

                    editId.value = professionalHistory.id
                    
                    form.value.organization_name = professionalHistory.organization_name
                    form.value.job_title = professionalHistory.job_title
                    form.value.job_level = professionalHistory.job_level
                    form.value.start_date = professionalHistory.start_date
                    form.value.end_date = professionalHistory.end_date

                    $('#professional-history-modal').modal('show')
                }
                
                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.organization_name) {
                        formUtil.errorMessage('Enter organization name')
                        return
                    }

                    if (!form.value.job_title) {
                        formUtil.errorMessage('Enter job title')
                        return
                    }

                    if (!form.value.job_level) {
                        formUtil.errorMessage('Enter job level')
                        return
                    }

                    if (!form.value.start_date) {
                        formUtil.errorMessage('Enter start date')
                        return
                    }

                    if (!form.value.end_date) {
                        formUtil.errorMessage('Enter end date')
                        return
                    }
                    
                    processing.value = true

                    submitAxios()
                }

                const submitAxios = () => {
                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/employee-professional-histories-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/employee-professional-histories-edit/${editId.value}`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchProfessionalHistories()
                            
                            formUtil.successMessage(response.data.message)

                            processing.value = false

                            $('#professional-history-modal').modal('hide')
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const modalTitle = ref('')
                const modalMessage = ref('')
                const modalAction = ref('')
                const modalActionRef = ref(null)
                
                const deleteId = ref(null)
                const confirmDelete = (id) => {
                    modalTitle.value = 'Delete Professional History?'
                    modalMessage.value = 'Are you sure you want to delete this professional history?'
                    modalAction.value = 'Delete'
                    modalActionRef.value = deleteProfessionalHistory

                    deleteId.value = id
                    
                    $('#professional-history-confirm-modal').modal('show')
                }

                const deleteProfessionalHistory = () => {
                    if (processing.value) {
                        return
                    }
                    
                    processing.value = true

                    axios.delete(`${baseUrl}/employee-professional-histories-delete/${deleteId.value}`)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            fetchProfessionalHistories()

                            processing.value = false

                            $('#professional-history-confirm-modal').modal('hide')
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            processing.value = false
                        })
                }

                const fetchData = (uri, referenceVariable) => {
                    axios.get(uri)
                        .then(response => referenceVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.message))
                }

                const professionalHistories = ref([])
                const fetchProfessionalHistories = () => {
                    fetchData(`${baseUrl}/employee-professional-histories-list/${employee.id}`, professionalHistories)
                }
                
                onMounted(() => {
                    fetchProfessionalHistories()
                })
                
                return {
                    processing,
                    form, 
                    submitForm,
                    professionalHistories,
                    action,
                    showAddModal,
                    showEditModal,
                    confirmDelete,
                    modalTitle,
                    modalMessage,
                    modalAction,
                    modalActionRef
                }
            }
        }).mount('#professional-information-app')
    </script>
@endpush