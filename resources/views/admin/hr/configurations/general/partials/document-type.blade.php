<div style="padding:10px" id="document-type-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-document-type___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Document Type
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Document Type</th>
                    <th>Description</th>
                    <th style="width: 220px">Required During Onboarding</th>
                    <th style="width: 150px" v-if="user.role_id == 1">System Reserved</th>
                    <th style="width: 100px" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(documentType, index) in documentTypes" :key="documentType.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ documentType.name }}</td>
                    <td>@{{ documentType.description }}</td>
                    <td style="text-align: center">
                        <span class="badge bg-green" v-if="documentType.required_during_onboarding">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" v-if="user.role_id == 1">
                        <span class="badge bg-green" v-if="documentType.system_reserved">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div" v-if="!documentType.system_reserved || user.role_id == 1">
                            <a href="#" @click.prevent="showEditModal(documentType)" v-if="(permissions.includes('hr-and-payroll-configurations-document-type___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteDocumentType(documentType.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-document-type___delete') || user.role_id == '1') && !documentType.employee_documents_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                        <div v-else>
                            <i class="fa fa-lock" title="Delete"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="document-type-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Document Type</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="document-type-name">Document Type</label>
                            <input type="text" class="form-control" id="document-type-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="document-type-description">Description</label>
                            <textarea class="form-control" id="document-type-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                        <div class="form-group">
                            <input type="checkbox" id="required" v-model="form.required_during_onboarding">
                            <label for="required" style="margin-left: 5px">Required during onboarding</label>
                        </div>
                        <div class="form-group" v-if="user.role_id == 1">
                            <input type="checkbox" id="system-reserved" v-model="form.system_reserved">
                            <label for="system-reserved" style="margin-left: 5px">System Reserved</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Document Type</button>
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
                    return permissions.includes('hr-and-payroll-configurations-document-type___edit') || 
                        permissions.includes('hr-and-payroll-configurations-document-type___delete') ||
                        user.role_id == '1'

                })
                
                const documentTypes = ref([])
                const fetchDocumentTypes = () => {
                    axios.get(`${baseUrl}/document-types`)
                        .then(response => documentTypes.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    name: '',
                    description: '',
                    required_during_onboarding: false,
                    system_reserved: false,
                })

                const clearForm = () => {
                    form.value.name = ''
                    form.value.description = ''
                    form.value.required_during_onboarding = false
                    form.value.system_reserved = false
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#document-type-modal').modal('show')
                }

                const editId = ref(null)
                const showEditModal = (documentType) => {
                    editId.value = documentType.id
                    
                    form.value.name = documentType.name
                    form.value.description = documentType.description
                    form.value.required_during_onboarding = documentType.required_during_onboarding
                    form.value.system_reserved = documentType.system_reserved

                    action.value = 'Edit'

                    $('#document-type-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/document-types`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/document-types/${editId.value}`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchDocumentTypes()
                            
                            formUtil.successMessage(response.data.message)

                            $('#document-type-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteDocumentType = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this document type?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/document-types/${id}`)
                                    .then(response => {
                                        fetchDocumentTypes()

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
                    fetchDocumentTypes()
                })

                return {
                    user,
                    permissions,
                    documentTypes,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteDocumentType,
                    canViewActions
                }
            }
        }).mount('#document-type-app')
    </script>
@endpush