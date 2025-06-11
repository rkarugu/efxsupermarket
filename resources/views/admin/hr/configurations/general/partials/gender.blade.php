<div style="padding:10px" id="gender-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-gender___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Gender
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Name</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(gender, index) in genders" :key="gender.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ gender.name }}</td>
                    <td>@{{ gender.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(gender)" v-if="(permissions.includes('hr-and-payroll-configurations-gender___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deleteGender(gender.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-gender___delete') || user.role_id == '1') && !gender.employees_count"
                            >
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="gender-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Gender</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="gender-name">Name</label>
                            <input type="text" class="form-control" id="gender-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="gender-description">Description</label>
                            <textarea class="form-control" id="gender-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Gender</button>
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
                    return permissions.includes('hr-and-payroll-configurations-gender___edit') || 
                        permissions.includes('hr-and-payroll-configurations-gender___delete') ||
                        user.role_id == '1'

                })
                
                const genders = ref([])
                const fetchGenders = () => {
                    axios.get(`${baseUrl}/gender-list`)
                        .then(response => genders.value = response.data)
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

                    $('#gender-modal').modal('show')
                }

                const showEditModal = (gender) => {
                    form.value.id = gender.id
                    form.value.name = gender.name
                    form.value.description = gender.description

                    action.value = 'Edit'

                    $('#gender-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/gender-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/gender-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchGenders()
                            
                            formUtil.successMessage(response.data.message)

                            $('#gender-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteGender = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this gender?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/gender-delete/${id}`)
                                    .then(response => {
                                        fetchGenders()

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
                    fetchGenders()
                })

                return {
                    user,
                    permissions,
                    genders,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteGender,
                    canViewActions
                }
            }
        }).mount('#gender-app')
    </script>
@endpush