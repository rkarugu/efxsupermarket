<div style="padding:10px" id="marital-status-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-marital-status___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Marital Status
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Marital Status</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(maritalStatus, index) in maritalStatuses" :key="maritalStatus.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ maritalStatus.name }}</td>
                    <td>@{{ maritalStatus.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(maritalStatus)" v-if="(permissions.includes('hr-and-payroll-configurations-marital-status___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteMaritalStatus(maritalStatus.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-marital-status___delete') || user.role_id == '1') && !maritalStatus.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="marital-status-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Marital Status</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="marital-status-name">Marital Status</label>
                            <input type="text" class="form-control" id="marital-status-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="marital-status-description">Description</label>
                            <textarea class="form-control" id="marital-status-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Marital Status</button>
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
                    return permissions.includes('hr-and-payroll-configurations-marital-status___edit') || 
                        permissions.includes('hr-and-payroll-configurations-marital-status___delete') ||
                        user.role_id == '1'

                })
                
                const maritalStatuses = ref([])
                const fetchMaritalStatuses = () => {
                    axios.get(`${baseUrl}/marital-statuses-list`)
                        .then(response => maritalStatuses.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    name: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.name = ''
                    form.value.description = ''
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#marital-status-modal').modal('show')
                }

                const showEditModal = (maritalStatus) => {
                    form.value.id = maritalStatus.id
                    form.value.name = maritalStatus.name
                    form.value.description = maritalStatus.description

                    action.value = 'Edit'

                    $('#marital-status-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/marital-statuses-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/marital-statuses-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchMaritalStatuses()
                            
                            formUtil.successMessage(response.data.message)

                            $('#marital-status-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteMaritalStatus = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this marital status?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/marital-statuses-delete/${id}`)
                                    .then(response => {
                                        fetchMaritalStatuses()

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
                    fetchMaritalStatuses()
                })

                return {
                    user,
                    permissions,
                    maritalStatuses,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteMaritalStatus,
                    canViewActions
                }
            }
        }).mount('#marital-status-app')
    </script>
@endpush