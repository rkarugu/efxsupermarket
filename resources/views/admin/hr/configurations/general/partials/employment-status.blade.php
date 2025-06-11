<div style="padding:10px" id="employment-status-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-employment-status___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Employment Status
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Status</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(employmentStatus, index) in employmentStatuses" :key="employmentStatus.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ employmentStatus.name }}</td>
                    <td>@{{ employmentStatus.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(employmentStatus)" v-if="(permissions.includes('hr-and-payroll-configurations-employment-status___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteEmploymentStatus(employmentStatus.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-employment-status___delete') || user.role_id == '1') && !employmentStatus.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="employment-status-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Employment Status</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="employment-status-name">Status</label>
                            <input type="text" class="form-control" id="employment-status-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="employment-status-description">Description</label>
                            <textarea class="form-control" id="employment-status-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Employment Status</button>
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
                    return permissions.includes('hr-and-payroll-configurations-employment-status___edit') || 
                        permissions.includes('hr-and-payroll-configurations-employment-status___delete') ||
                        user.role_id == '1'

                })
                
                const employmentStatuses = ref([])
                const fetchEmploymentStatuses = () => {
                    axios.get(`${baseUrl}/employment-statuses-list`)
                        .then(response => employmentStatuses.value = response.data)
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

                    $('#employment-status-modal').modal('show')
                }

                const showEditModal = (employmentStatus) => {
                    form.value.id = employmentStatus.id
                    form.value.name = employmentStatus.name
                    form.value.description = employmentStatus.description

                    action.value = 'Edit'

                    $('#employment-status-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/employment-statuses-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/employment-statuses-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchEmploymentStatuses()
                            
                            formUtil.successMessage(response.data.message)

                            $('#employment-status-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteEmploymentStatus = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this employment status?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/employment-statuses-delete/${id}`)
                                    .then(response => {
                                        fetchEmploymentStatuses()

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
                    fetchEmploymentStatuses()
                })

                return {
                    user,
                    permissions,
                    employmentStatuses,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteEmploymentStatus,
                    canViewActions
                }
            }
        }).mount('#employment-status-app')
    </script>
@endpush