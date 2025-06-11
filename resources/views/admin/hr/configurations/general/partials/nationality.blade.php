<div style="padding:10px" id="nationality-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-info pull-right" 
                style="margin-left: 10px"
                @click="showUploadModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-nationality___bulk-upload') || user.role_id == '1')"
            >
                <i class="fa fa-upload"></i>
                Bulk Upload
            </button>
            
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-nationality___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Nationality
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Nationality</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(nationality, index) in nationalities" :key="nationality.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ nationality.name }}</td>
                    <td>@{{ nationality.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(nationality)" v-if="(permissions.includes('hr-and-payroll-configurations-nationality___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteNationality(nationality.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-nationality___delete') || user.role_id == '1') && !nationality.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="nationality-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Nationality</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nationality-name">Nationality</label>
                            <input type="text" class="form-control" id="nationality-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="nationality-description">Description</label>
                            <textarea class="form-control" id="nationality-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Nationality</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="upload-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Bulk Upload Nationalities</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="upload-file" accept=".xlsx" :onchange="documentChanged">
                            <span style="font-size: 12px; font-style: italic">No file selected</span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('hr.configurations.nationalities-bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
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
                    return permissions.includes('hr-and-payroll-configurations-nationality___edit') || 
                        permissions.includes('hr-and-payroll-configurations-nationality___delete') ||
                        user.role_id == '1'

                })

                const showUploadModal = () => {
                    $('#upload-file').val(null)
                    
                    $('#upload-modal').modal('show')
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

                    axios.post('/api/hr/configurations/nationalities-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            fetchNationalities()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'nationalities_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Nationality uploaded successfully')
                            }
                            
                            $('#upload-modal').modal('hide')

                            file.value = ''
                            $(file).val('').trigger('change')

                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                    
                }
                
                const nationalities = ref([])
                const fetchNationalities = () => {
                    axios.get(`${baseUrl}/nationalities-list`)
                        .then(response => nationalities.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    name: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.nationality = ''
                    form.value.description = ''
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#nationality-modal').modal('show')
                }

                const showEditModal = (nationality) => {
                    form.value.id = nationality.id
                    form.value.name = nationality.name
                    form.value.description = nationality.description

                    action.value = 'Edit'

                    $('#nationality-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/nationalities-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/nationalities-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchNationalities()
                            
                            formUtil.successMessage(response.data.message)

                            $('#nationality-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteNationality = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this nationality?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/nationalities-delete/${id}`)
                                    .then(response => {
                                        fetchNationalities()

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
                    fetchNationalities()
                })

                return {
                    user,
                    permissions,
                    nationalities,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteNationality,
                    canViewActions,
                    showUploadModal,
                    documentChanged,
                    submitBulkUpload
                }
            }
        }).mount('#nationality-app')
    </script>
@endpush