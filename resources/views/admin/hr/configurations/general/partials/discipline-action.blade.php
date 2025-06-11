<div style="padding:10px" id="discipline-action-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-discipline-action___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Discipline Action
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Discipline Action</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(disciplineAction, index) in disciplineActions" :key="disciplineAction.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ disciplineAction.name }}</td>
                    <td>@{{ disciplineAction.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(disciplineAction)" v-if="(permissions.includes('hr-and-payroll-configurations-discipline-action___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="deleteDisciplineAction(disciplineAction.id)" v-if="(permissions.includes('hr-and-payroll-configurations-discipline-action___delete') || user.role_id == '1')">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="discipline-action-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Discipline Action</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="discipline-action-name">Discipline Action</label>
                            <input type="text" class="form-control" id="discipline-action-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="discipline-action-description">Description</label>
                            <textarea class="form-control" id="discipline-action-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Discipline Action</button>
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
                    return permissions.includes('hr-and-payroll-configurations-discipline-action___edit') || 
                        permissions.includes('hr-and-payroll-configurations-discipline-action___delete') ||
                        user.role_id == '1'

                })
                
                const disciplineActions = ref([])
                const fetchDisciplineActions = () => {
                    axios.get(`${baseUrl}/discipline-actions-list`)
                        .then(response => disciplineActions.value = response.data)
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

                    $('#discipline-action-modal').modal('show')
                }

                const showEditModal = (disciplineAction) => {
                    form.value.id = disciplineAction.id
                    form.value.name = disciplineAction.name
                    form.value.description = disciplineAction.description

                    action.value = 'Edit'

                    $('#discipline-action-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/discipline-actions-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/discipline-actions-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchDisciplineActions()
                            
                            formUtil.successMessage(response.data.message)

                            $('#discipline-action-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteDisciplineAction = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this discipline action?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/discipline-actions-delete/${id}`)
                                    .then(response => {
                                        fetchDisciplineActions()

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
                    fetchDisciplineActions()
                })

                return {
                    user,
                    permissions,
                    disciplineActions,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteDisciplineAction,
                    canViewActions
                }
            }
        }).mount('#discipline-action-app')
    </script>
@endpush