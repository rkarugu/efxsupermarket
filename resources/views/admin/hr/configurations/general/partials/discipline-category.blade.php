<div style="padding:10px" id="discipline-category-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-discipline-category___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Discipline Category
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Discipline Category</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(disciplineCategory, index) in disciplineCategories" :key="disciplineCategory.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ disciplineCategory.name }}</td>
                    <td>@{{ disciplineCategory.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(disciplineCategory)" v-if="(permissions.includes('hr-and-payroll-configurations-discipline-category___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="deleteDisciplineCategory(disciplineCategory.id)" v-if="(permissions.includes('hr-and-payroll-configurations-discipline-category___delete') || user.role_id == '1')">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="discipline-category-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Discipline Category</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="discipline-category-name">Discipline Category</label>
                            <input type="text" class="form-control" id="discipline-category-name" placeholder="Enter name" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="discipline-category-description">Description</label>
                            <textarea class="form-control" id="discipline-category-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Discipline Category</button>
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
                    return permissions.includes('hr-and-payroll-configurations-discipline-category___edit') || 
                        permissions.includes('hr-and-payroll-configurations-discipline-category___delete') ||
                        user.role_id == '1'

                })
                
                const disciplineCategories = ref([])
                const fetchDisciplineCategories = () => {
                    axios.get(`${baseUrl}/discipline-categories-list`)
                        .then(response => disciplineCategories.value = response.data)
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

                    $('#discipline-category-modal').modal('show')
                }

                const showEditModal = (disciplineCategory) => {
                    form.value.id = disciplineCategory.id
                    form.value.name = disciplineCategory.name
                    form.value.description = disciplineCategory.description

                    action.value = 'Edit'

                    $('#discipline-category-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter discipline category name')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/discipline-categories-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/discipline-categories-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchDisciplineCategories()
                            
                            formUtil.successMessage(response.data.message)

                            $('#discipline-category-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deleteDisciplineCategory = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this discipline category?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/discipline-categories-delete/${id}`)
                                    .then(response => {
                                        fetchDisciplineCategories()

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
                    fetchDisciplineCategories()
                })

                return {
                    user,
                    permissions,
                    disciplineCategories,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deleteDisciplineCategory,
                    canViewActions
                }
            }
        }).mount('#discipline-category-app')
    </script>
@endpush