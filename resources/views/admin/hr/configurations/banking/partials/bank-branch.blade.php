<div style="padding:10px" id="bank-branch-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-bank-branch___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Bank Branch
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th>Branch</th>
                    <th>Code</th>
                    <th>Bank</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(branch, index) in bankBranches" :key="branch.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ branch.name }}</td>
                    <td>@{{ branch.branch_code }}</td>
                    <td>@{{ branch.bank.name }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(branch)" v-if="(permissions.includes('hr-and-payroll-configurations-bank-branch___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="deleteBankBranch(branch.id)" v-if="canBeDeleted(branch)">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="bank-branch-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Bank Branch</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="bank-branch-bank">Bank</label>
                            <select class="form-control" id="bank-select" v-model="form.bank_id" :onchange="bankChanged">
                                <option :value="bank.id" v-for="(bank, index) in banks" :key="bank.id">@{{ bank.name }}</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bank-branch-name">Bank Branch</label>
                            <input type="text" class="form-control" id="bank-branch-name" placeholder="Enter branch" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="bank-branch-code">Code</label>
                            <input type="text" class="form-control" id="bank-branch-code" placeholder="Enter code" v-model="form.branch_code">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Bank Branch</button>
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
                    return permissions.includes('hr-and-payroll-configurations-bank-branch___delete')
                }
                
                const canViewActions = computed(() => {
                    return permissions.includes('hr-and-payroll-configurations-bank-branch___edit') || 
                        deletePermission() ||
                        user.role_id == '1'

                })

                const canBeDeleted = (branch) => {
                    return (deletePermission() || user.role_id == '1') && !branch.bank_accounts_count
                }
                
                const bankBranches = ref([])
                const fetchBankBranches = () => {
                    axios.get(`${baseUrl}/bank-branch-list`)
                        .then(response => bankBranches.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    bank_id: '',
                    name: '',
                    branch_code: '',
                })

                const clearForm = () => {
                    form.value.bank_id = ''
                    form.value.name = ''
                    form.value.branch_code = ''

                    $('#bank-select').val(null).trigger('change')
                }

                const bankChanged = (event) => {
                    form.value.bank_id = $(event.target).val()
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#bank-branch-modal').modal('show')
                }

                const showEditModal = (bankBranch) => {
                    form.value.id = bankBranch.id
                    form.value.bank_id = bankBranch.bank_id
                    form.value.name = bankBranch.name
                    form.value.branch_code = bankBranch.branch_code

                    action.value = 'Edit'

                    $('#bank-select').val(bankBranch.bank_id).trigger('change')

                    $('#bank-branch-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.bank_id) {
                        formUtil.errorMessage('Select bank')
                        return
                    }

                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    if (!form.value.branch_code) {
                        formUtil.errorMessage('Enter code')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/bank-branch-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/bank-branch-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchBankBranches()
                            
                            formUtil.successMessage(response.data.message)

                            $('#bank-branch-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteBankBranch = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this branch?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/bank-branch-delete/${id}`)
                                    .then(response => {
                                        fetchBankBranches()

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

                const banks = ref([])
                onMounted(() => {
                    // Fetch banks list
                    axios.get(`${baseUrl}/bank-list`)
                        .then(response => banks.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.message))
                    
                        fetchBankBranches()

                        $('#bank-branch-app select').select2({
                            placeholder: 'Select...',
                        });
                })

                return {
                    user,
                    permissions,
                    bankBranches,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteBankBranch,
                    canViewActions,
                    banks,
                    bankChanged,
                    canBeDeleted
                }
            }
        }).mount('#bank-branch-app')
    </script>
@endpush