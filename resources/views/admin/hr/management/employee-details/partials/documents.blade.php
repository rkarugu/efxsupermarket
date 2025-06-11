<div style="padding:10px" id="documents-app">
    <div v-cloak>
        <div style="text-align: right">
            <button class="btn btn-primary" @click="showAddModal">
                <i class="fa fa-plus"></i>
                Add Document
            </button>
        </div>

        <hr>
        
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Document Type</th>
                    <th>Document</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(employeeDocument, index) in employeeDocuments" :key="employeeDocument.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ employeeDocument.document_type.name }}</td>
                    <td>
                        <a :href="`/storage/${employeeDocument.file_path}`" target="_blank">
                            Open
                            <i class="fa fa-external-link" style="font-size: 12px"></i>
                        </a>
                    </td>
                    <td style="text-align: center">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(employeeDocument)">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="confirmDelete(employeeDocument.id)">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="employee-document-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Document </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="document-type">Document Type</label>
                            <select class="form-control" id="document-type" v-model="form.document_type_id" :onchange="documentTypeChanged">
                                <option :value="documentType.id" v-for="documentType in documentTypes" :key="documentType.id">@{{ documentType.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="document">
                                Document
                                <span v-if="action == 'Edit'">
                                    <a :href="`/storage/${form.file_path}`" target="_blank">
                                        Current File
                                        <i class="fa fa-external-link" style="font-size: 12px"></i>
                                    </a>
                                </span>
                            </label>
                            <input type="file" class="form-control" id="document">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Document</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="employee-document-confirm-modal" tabindex="-1" role="dialog">
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
                    document_type_id: '',
                    file_path: '',
                })

                const clearForm = () => {
                    form.value.document_type_id = ''
                    form.value.file_path = ''
                }

                const documentTypeChanged = (event) => {
                    form.value.document_type_id = $(event.target).val()
                }

                const action = ref('Add')
                
                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#employee-document-modal').modal('show')
                }
                
                const editId = ref(null)
                const showEditModal = (employeeDocument) => {
                    action.value = 'Edit'

                    editId.value = employeeDocument.id
                    
                    form.value.document_type_id = employeeDocument.document_type_id
                    form.value.file_path = employeeDocument.file_path

                    $('#document-type').val(form.value.document_type_id).trigger('change')

                    $('#employee-document-modal').modal('show')
                }
                
                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.document_type_id) {
                        formUtil.errorMessage('Enter organization name')
                        return
                    }

                    let documentFile = document.getElementById('document')

                    if (!documentFile.files.length) {
                        formUtil.errorMessage('Choose a file')
                        return
                    }

                    processing.value = true

                    const formData = new FormData()

                    formData.append('employee_id', form.value.employee_id)
                    formData.append('document_type_id', form.value.document_type_id)
                    formData.append('file_path', documentFile.files[0])

                    submitAxios(formData)
                }

                const submitAxios = (formData) => {
                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/employee-documents-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/employee-documents-edit/${editId.value}`
                    }

                    axios.post(uri, formData)
                        .then(response => {
                            fetchEmployeeDocuments()
                            
                            formUtil.successMessage(response.data.message)

                            processing.value = false

                            $('#employee-document-modal').modal('hide')
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
                    modalTitle.value = 'Delete Document?'
                    modalMessage.value = 'Are you sure you want to delete this document?'
                    modalAction.value = 'Delete'
                    modalActionRef.value = deleteDocument

                    deleteId.value = id
                    
                    $('#employee-document-confirm-modal').modal('show')
                }

                const deleteDocument = () => {
                    if (processing.value) {
                        return
                    }
                    
                    processing.value = true

                    axios.delete(`${baseUrl}/employee-documents-delete/${deleteId.value}`)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            fetchEmployeeDocuments()

                            processing.value = false

                            $('#employee-document-confirm-modal').modal('hide')
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

                const employeeDocuments = ref([])
                const fetchEmployeeDocuments = () => {
                    fetchData(`${baseUrl}/employee-documents-list/${employee.id}`, employeeDocuments)
                }
                
                const documentTypes = ref([])
                onMounted(() => {
                    fetchEmployeeDocuments()

                    fetchData('hr/configurations/document-types', documentTypes)

                    $('#documents-app select').select2({
                        placeholder: 'Select...'
                    })
                })
                
                return {
                    processing,
                    form, 
                    submitForm,
                    documentTypeChanged,
                    employeeDocuments,
                    documentTypes,
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
        }).mount('#documents-app')
    </script>
@endpush