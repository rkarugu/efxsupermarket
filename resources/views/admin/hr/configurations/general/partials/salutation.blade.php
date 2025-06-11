<div style="padding:10px" id="salutation-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-salutation___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Salutation
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Salutation</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(salutation, index) in salutations" :key="salutation.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ salutation.name }}</td>
                    <td>@{{ salutation.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(salutation)" v-if="(permissions.includes('hr-and-payroll-configurations-salutation___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteSalutation(salutation.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-salutation___delete') || user.role_id == '1') && !salutation.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="salutation-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Salutation</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="salutation-name">Salutation</label>
                            <input type="text" class="form-control" id="salutation-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="salutation-description">Description</label>
                            <textarea class="form-control" id="salutation-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Salutation</button>
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
                    return permissions.includes('hr-and-payroll-configurations-salutation___edit') || 
                        permissions.includes('hr-and-payroll-configurations-salutation___delete') ||
                        user.role_id == '1'

                })
                
                const salutations = ref([])
                const fetchsalutations = () => {
                    axios.get(`${baseUrl}/salutations-list`)
                        .then(response => salutations.value = response.data)
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

                    $('#salutation-modal').modal('show')
                }

                const showEditModal = (salutation) => {
                    form.value.id = salutation.id
                    form.value.name = salutation.name
                    form.value.description = salutation.description

                    action.value = 'Edit'

                    $('#salutation-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/salutations-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/salutations-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchsalutations()
                            
                            formUtil.successMessage(response.data.message)

                            $('#salutation-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteSalutation = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this salutation?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/salutations-delete/${id}`)
                                    .then(response => {
                                        fetchsalutations()

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
                    fetchsalutations()
                })

                return {
                    user,
                    permissions,
                    salutations,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteSalutation,
                    canViewActions
                }
            }
        }).mount('#salutation-app')
    </script>
@endpush