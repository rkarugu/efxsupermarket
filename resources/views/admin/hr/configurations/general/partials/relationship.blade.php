<div style="padding:10px" id="relationship-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-relationship___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Relationship
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Relationship</th>
                    <th>Description</th>
                    <th style="width: 150px">System Reserved</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(relationship, index) in relationships" :key="relationship.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ relationship.name }}</td>
                    <td>@{{ relationship.description }}</td>
                    <td style="text-align: center" >
                        <span class="badge bg-green" v-if="relationship.system_reserved">Yes</span>
                        <span class="badge bg-yellow" v-else>No</span>
                    </td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div" v-if="!relationship.system_reserved || user.role_id == 1">
                            <a href="#" @click.prevent="showEditModal(relationship)" v-if="(permissions.includes('hr-and-payroll-configurations-relationship___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteRelationship(relationship.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-relationship___delete') || user.role_id == '1') && (!relationship.employee_emergency_contacts_count && !relationship.employee_beneficiaries_count)"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="relationship-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Relationship</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="relationship-name">Relationship</label>
                            <input type="text" class="form-control" id="relationship-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="relationship-description">Description</label>
                            <textarea class="form-control" id="relationship-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                        <div class="form-group" v-if="user.role_id == 1">
                            <input type="checkbox" id="system-reserved" v-model="form.system_reserved">
                            <label for="system-reserved" style="margin-left: 5px">System Reserved</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Relationship</button>
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
                    return permissions.includes('hr-and-payroll-configurations-relationship___edit') || 
                        permissions.includes('hr-and-payroll-configurations-relationship___delete') ||
                        user.role_id == '1'

                })
                
                const relationships = ref([])
                const fetchRelationships = () => {
                    axios.get(`${baseUrl}/relationships`)
                        .then(response => relationships.value = response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.error))
                }

                const action = ref('Add')

                const form = ref({
                    name: '',
                    description: '',
                    system_reserved: false
                })

                const clearForm = () => {
                    form.value.relationship = ''
                    form.value.description = ''
                    form.value.system_reserved = false
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#relationship-modal').modal('show')
                }

                const editId = ref(null)
                const showEditModal = (relationship) => {
                    editId.value = relationship.id
                    
                    form.value.name = relationship.name
                    form.value.description = relationship.description
                    form.value.system_reserved = relationship.system_reserved

                    action.value = 'Edit'

                    $('#relationship-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/relationships`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/relationships/${editId.value}`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchRelationships()
                            
                            formUtil.successMessage(response.data.message)

                            $('#relationship-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteRelationship = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this relationship?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/relationships/${id}`)
                                    .then(response => {
                                        fetchRelationships()

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
                    fetchRelationships()
                })

                return {
                    user,
                    permissions,
                    relationships,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteRelationship,
                    canViewActions
                }
            }
        }).mount('#relationship-app')
    </script>
@endpush