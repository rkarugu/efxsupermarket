<div style="padding:10px" id="bank-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-info pull-right" 
                style="margin-left: 10px"
                @click="showUploadModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-bank___bulk-upload') || user.role_id == '1')"
            >
                <i class="fa fa-upload"></i>
                Bulk Upload
            </button>
            
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-bank___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Bank
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Bank</th>
                    <th>Code</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(bank, index) in banks" :key="bank.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ bank.name }}</td>
                    <td>@{{ bank.code }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(bank)" v-if="(permissions.includes('hr-and-payroll-configurations-bank___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="deleteBank(bank.id)" v-if="canBeDeleted(bank)">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="bank-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Bank</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="bank-name">Name</label>
                            <input type="text" class="form-control" id="bank-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="bank-code">Code</label>
                            <input type="text" class="form-control" id="bank-code" placeholder="Enter code" v-model="form.code">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Bank</button>
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
                        <h4 class="modal-title">Bulk Upload Banks and Branches</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="file" class="form-control" id="upload-file" accept=".xlsx" :onchange="documentChanged">
                            <span style="font-size: 12px; font-style: italic">No file selected</span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('hr.configurations.banks-bulk-upload-template') }}" class="btn btn-warning pull-left" download>Download Template</a>
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

                const deletePermission = () => {
                    return permissions.includes('hr-and-payroll-configurations-bank___delete')
                }
                
                const canViewActions = computed(() => {
                    return permissions.includes('hr-and-payroll-configurations-bank___edit') || 
                        deletePermission() ||
                        user.role_id == '1'

                })

                const canBeDeleted = (bank) => {
                    return (deletePermission() || user.role_id == '1') &&
                        (!bank.branches_count && !bank.bank_accounts_count)
                }

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

                    axios.post('/api/hr/configurations/banks-bulk-upload', formData, {
                        responseType: 'blob'
                    })
                        .then(response => {
                            // fetchBanks()

                            if (response.status == 201) {
                                
                                const url = window.URL.createObjectURL(new Blob([response.data]));
                                const link = document.createElement('a');
                                link.href = url;

                                link.setAttribute('download', 'banks_bulk_upload_errors.xlsx');
                                
                                document.body.appendChild(link);
                                link.click();

                                document.body.removeChild(link);
                                
                                formUtil.warningMessage("Some errors were encountered during upload.")
                            } else {
                                formUtil.successMessage('Banks uploaded successfully')
                            }
                            
                            // $('#upload-modal').modal('hide')

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
                
                const banks = ref([])
                const fetchBanks = () => {
                    axios.get(`${baseUrl}/bank-list`)
                        .then(response => banks.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    name: '',
                    code: '',
                })

                const clearForm = () => {
                    form.value.name = ''
                    form.value.code = ''
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#bank-modal').modal('show')
                }

                const showEditModal = (bank) => {
                    form.value.id = bank.id
                    form.value.name = bank.name
                    form.value.code = bank.code

                    action.value = 'Edit'

                    $('#bank-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    if (!form.value.code) {
                        formUtil.errorMessage('Enter code')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/bank-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/bank-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            // fetchBanks()
                            
                            formUtil.successMessage(response.data.message)

                            setTimeout(() => {
                                window.location.reload()
                            }, 1000);

                            // $('#bank-modal').modal('hide')
                            
                            // clearForm()
                            
                            // processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteBank = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this bank?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/bank-delete/${id}`)
                                    .then(response => {
                                        fetchBanks()

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
                    fetchBanks()
                })

                return {
                    user,
                    permissions,
                    banks,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteBank,
                    canBeDeleted,
                    canViewActions,
                    showUploadModal,
                    documentChanged,
                    submitBulkUpload
                }
            }
        }).mount('#bank-app')
    </script>
@endpush