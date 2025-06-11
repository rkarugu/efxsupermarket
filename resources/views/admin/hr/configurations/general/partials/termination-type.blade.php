<div style="padding:10px" id="termination-type-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-termination-type___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Termination Type
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Termination Type</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(terminationType, index) in terminationTypes" :key="terminationType.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ terminationType.name }}</td>
                    <td>@{{ terminationType.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(terminationType)" v-if="(permissions.includes('hr-and-payroll-configurations-termination-type___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="deleteTerminationType(terminationType.id)" v-if="(permissions.includes('hr-and-payroll-configurations-termination-type___delete') || user.role_id == '1')">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="termination-type-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Termination Type</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="termination-type-name">Termination Type</label>
                            <input type="text" class="form-control" id="termination-type-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="termination-type-description">Description</label>
                            <textarea class="form-control" id="termination-type-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Termination Type</button>
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
                    return permissions.includes('hr-and-payroll-configurations-termination-type___edit') || 
                        permissions.includes('hr-and-payroll-configurations-termination-type___delete') ||
                        user.role_id == '1'

                })
                
                const terminationTypes = ref([])
                const fetchTerminationTypes = () => {
                    axios.get(`${baseUrl}/termination-types-list`)
                        .then(response => terminationTypes.value = response.data)
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

                    $('#termination-type-modal').modal('show')
                }

                const showEditModal = (terminationType) => {
                    form.value.id = terminationType.id
                    form.value.name = terminationType.name
                    form.value.description = terminationType.description

                    action.value = 'Edit'

                    $('#termination-type-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/termination-types-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/termination-types-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchTerminationTypes()
                            
                            formUtil.successMessage(response.data.message)

                            $('#termination-type-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteTerminationType = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this termination type?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/termination-types-delete/${id}`)
                                    .then(response => {
                                        fetchTerminationTypes()

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
                    fetchTerminationTypes()
                })

                return {
                    user,
                    permissions,
                    terminationTypes,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteTerminationType,
                    canViewActions
                }
            }
        }).mount('#termination-type-app')
    </script>
@endpush