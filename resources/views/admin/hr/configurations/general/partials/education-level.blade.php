<div style="padding:10px" id="education-level-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-education-level___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Education Level
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Education Level</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(educationLevel, index) in educationLevels" :key="educationLevel.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ educationLevel.name }}</td>
                    <td>@{{ educationLevel.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(educationLevel)" v-if="(permissions.includes('hr-and-payroll-configurations-education-level___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteEducationLevel(educationLevel.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-education-level___delete') || user.role_id == '1') && !educationLevel.employees_count">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="education-level-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Education Level</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="education-level-name">Education Level</label>
                            <input type="text" class="form-control" id="education-level-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="education-level-description">Description</label>
                            <textarea class="form-control" id="education-level-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Education Level</button>
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
                    return permissions.includes('hr-and-payroll-configurations-education-level___edit') || 
                        permissions.includes('hr-and-payroll-configurations-education-level___delete') ||
                        user.role_id == '1'

                })
                
                const educationLevels = ref([])
                const fetchEducationLevel = async () => {
                    axios.get(`${baseUrl}/education-levels-list`)
                        .then(response => educationLevels.value = response.data)
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

                    $('#education-level-modal').modal('show')
                }

                const showEditModal = (educationLevel) => {
                    form.value.id = educationLevel.id
                    form.value.name = educationLevel.name
                    form.value.description = educationLevel.description

                    action.value = 'Edit'

                    $('#education-level-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/education-levels-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/education-levels-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchEducationLevel()
                            
                            formUtil.successMessage(response.data.message)

                            $('#education-level-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteEducationLevel = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this education level?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/education-levels-delete/${id}`)
                                    .then(response => {
                                        fetchEducationLevel()

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
                    fetchEducationLevel()
                })

                return {
                    user,
                    permissions,
                    educationLevels,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteEducationLevel,
                    canViewActions
                }
            }
        }).mount('#education-level-app')
    </script>
@endpush