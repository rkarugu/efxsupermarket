<div style="padding:10px" id="educational-information-app">
    <div v-cloak>
        <div style="text-align: right">
            <button class="btn btn-primary" @click="showAddModal">
                <i class="fa fa-plus"></i>
                Add Education History
            </button>
        </div>

        <hr>
        
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Institution Name</th>
                    <th>Enrollment Date</th>
                    <th>Completion Date</th>
                    <th>Award</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(educationHistory, index) in educationHistories" :key="educationHistory.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ educationHistory.institution_name }}</td>
                    <td>@{{ educationHistory.enrollment_date }}</td>
                    <td>@{{ educationHistory.completion_date }}</td>
                    <td>@{{ educationHistory.award }}</td>
                    <td style="text-align: center">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(educationHistory)">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="confirmDelete(educationHistory.id)">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="education-history-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Education History </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="institution-name">Institution Name</label>
                            <input type="text" class="form-control" id="institution-name" placeholder="Enter institution name" v-model="form.institution_name">
                        </div>
                        <div class="form-group">
                            <label for="enrollment-date">Enrollment Date</label>
                            <input type="date" class="form-control" id="enrollment-date" placeholder="Enter enrollment date" v-model="form.enrollment_date">
                        </div>
                        <div class="form-group">
                            <label for="completion-date">Completion Date</label>
                            <input type="date" class="form-control" id="completion-date" placeholder="Enter completion date" v-model="form.completion_date">
                        </div>
                        <div class="form-group">
                            <label for="award">Award</label>
                            <input type="text" class="form-control" id="award" placeholder="Enter award" v-model="form.award">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Education History</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="education-history-confirm-modal" tabindex="-1" role="dialog">
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
                    institution_name: '',
                    enrollment_date: '',
                    completion_date: '',
                    award: '',
                })

                const clearForm = () => {
                    form.value.institution_name = ''
                    form.value.enrollment_date = ''
                    form.value.completion_date = ''
                    form.value.award = ''
                }

                const action = ref('Add')
                
                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#education-history-modal').modal('show')
                }
                
                const editId = ref(null)
                const showEditModal = (educationHistory) => {
                    action.value = 'Edit'

                    editId.value = educationHistory.id
                    
                    form.value.institution_name = educationHistory.institution_name
                    form.value.enrollment_date = educationHistory.enrollment_date
                    form.value.completion_date = educationHistory.completion_date
                    form.value.award = educationHistory.award

                    $('#education-history-modal').modal('show')
                }
                
                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.institution_name) {
                        formUtil.errorMessage('Enter institution name')
                        return
                    }

                    if (!form.value.enrollment_date) {
                        formUtil.errorMessage('Enter enrollment date')
                        return
                    }

                    if (!form.value.completion_date) {
                        formUtil.errorMessage('Enter completion date')
                        return
                    }

                    if (!form.value.award) {
                        formUtil.errorMessage('Enter award')
                        return
                    }
                    
                    processing.value = true

                    submitAxios()
                }

                const submitAxios = () => {
                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/employee-education-histories-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/employee-education-histories-edit/${editId.value}`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchEducationHistories()
                            
                            formUtil.successMessage(response.data.message)

                            processing.value = false

                            $('#education-history-modal').modal('hide')
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
                    modalTitle.value = 'Delete Education History?'
                    modalMessage.value = 'Are you sure you want to delete this education history?'
                    modalAction.value = 'Delete'
                    modalActionRef.value = deleteEducationHistory

                    deleteId.value = id
                    
                    $('#education-history-confirm-modal').modal('show')
                }

                const deleteEducationHistory = () => {
                    if (processing.value) {
                        return
                    }
                    
                    processing.value = true

                    axios.delete(`${baseUrl}/employee-education-histories-delete/${deleteId.value}`)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            fetchEducationHistories()

                            processing.value = false

                            $('#education-history-confirm-modal').modal('hide')
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

                const educationHistories = ref([])
                const fetchEducationHistories = () => {
                    fetchData(`${baseUrl}/employee-education-histories-list/${employee.id}`, educationHistories)
                }
                
                onMounted(() => {
                    fetchEducationHistories()
                })
                
                return {
                    processing,
                    form, 
                    submitForm,
                    educationHistories,
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
        }).mount('#educational-information-app')
    </script>
@endpush