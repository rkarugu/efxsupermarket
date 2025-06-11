<div style="padding:10px" id="emergency-contacts-app">
    <div v-cloak>
        <div style="text-align: right">
            <button class="btn btn-primary" @click="showAddModal">
                <i class="fa fa-plus"></i>
                Add Emergency Contact
            </button>
        </div>

        <hr>
        
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Phone No.</th>
                    <th>Email</th>
                    <th>Relationship</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(emergencyContact, index) in emergencyContacts" :key="emergencyContact.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ emergencyContact.full_name }}</td>
                    <td>@{{ emergencyContact.phone_no }}</td>
                    <td>@{{ emergencyContact.email }}</td>
                    <td>@{{ selectedRelationship(emergencyContact.relationship.id) != 'Other' ? emergencyContact.relationship?.name : emergencyContact.custom_relationship }}</td>
                    <td style="text-align: center">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(emergencyContact)">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="confirmDelete(emergencyContact.id)">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="emergency-contact-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Emergency Contact </h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="relationship">Relationship</label>
                            <select class="form-control" id="relationship-select" v-model="form.relationship_id" :onchange="relationshipChanged">
                                <option :value="relationship.id" v-for="relationship in relationships" :key="relationship.id">@{{ relationship.name }}</option>
                            </select>
                        </div>
                        <div class="form-group" v-if="selectedRelationship(form.relationship_id) == 'Other'">
                            <label for="custom-relationship">Relationship Name</label>
                            <input type="text" class="form-control" id="custom-relationship" placeholder="Enter relationship" v-model="form.custom_relationship">
                        </div>
                        <div class="form-group">
                            <label for="full-name">Full Name</label>
                            <input type="text" class="form-control" id="full-name" placeholder="Enter full name" v-model="form.full_name">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" placeholder="Enter email" v-model="form.email">
                        </div>
                        <div class="form-group">
                            <label for="phone-no">Phone No.</label>
                            <input type="text" class="form-control" id="phone-no" placeholder="Enter phone no." v-model="form.phone_no">
                        </div>
                        <div class="form-group">
                            <label for="id-no">ID No.</label>
                            <input type="text" class="form-control" id="id-no" placeholder="Enter ID no." v-model="form.id_no">
                        </div>
                        <div class="form-group">
                            <label for="place-of-work">Place of Work</label>
                            <input type="text" class="form-control" id="place-of-work" placeholder="Enter place of work" v-model="form.place_of_work">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Emergency Contact</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="confirm-modal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title"> @{{ modalTitle }} </h3>

                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">
                        @{{ modalMessage }}
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                            <button type="button" class="btn btn-primary" :disabled="processing" @click="modalActionRef">@{{ modalAction }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script type="module">
        import { createApp, ref, onMounted } from 'vue';

        createApp({
            setup() {
                const employee = {!! $employee !!}

                const formUtil = new Form()
                const baseUrl = 'hr/management'

                const form = ref({
                    employee_id: employee.id,
                    relationship_id: '',
                    custom_relationship: '',
                    full_name: '',
                    email: '',
                    phone_no: '',
                    place_of_work: '',
                    id_no: '',
                })

                const clearForm = () => {
                    form.value.relationship_id = ''
                    form.value.custom_relationship = ''
                    form.value.full_name = ''
                    form.value.email = ''
                    form.value.phone_no = ''
                    form.value.place_of_work = ''
                    form.value.id_no = ''

                    $('#relationship-select').val('').trigger('change')
                }

                const relationshipChanged = (event) => {
                    form.value.relationship_id = $(event.target).val()

                    form.value.custom_relationship = ''
                }

                const action = ref('Add')
                
                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#emergency-contact-modal').modal('show')
                }
                
                const editId = ref(null)
                const showEditModal = (emergencyContact) => {
                    action.value = 'Edit'

                    editId.value = emergencyContact.id
                    
                    form.value.relationship_id = emergencyContact.relationship_id
                    $('#relationship-select').val(form.value.relationship_id).trigger('change')

                    form.value.custom_relationship = emergencyContact.custom_relationship
                    form.value.full_name = emergencyContact.full_name
                    form.value.email = emergencyContact.email
                    form.value.phone_no = emergencyContact.phone_no
                    form.value.place_of_work = emergencyContact.place_of_work
                    form.value.id_no = emergencyContact.id_no

                    
                    $('#emergency-contact-modal').modal('show')
                }
                
                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.relationship_id) {
                        formUtil.errorMessage('Select relationship')
                        return
                    }

                    if (selectedRelationship(form.value.relationship_id) == 'Other' && !form.value.custom_relationship) {
                        formUtil.errorMessage('Enter relationship name')
                        return
                    }

                    if (!form.value.full_name) {
                        formUtil.errorMessage('Enter full name')
                        return
                    }

                    if (!form.value.email) {
                        formUtil.errorMessage('Enter email')
                        return
                    }

                    if (!form.value.phone_no) {
                        formUtil.errorMessage('Enter phone no.')
                        return
                    }

                    if (!form.value.id_no) {
                        formUtil.errorMessage('Enter ID no.')
                        return
                    }

                    if (!form.value.place_of_work) {
                        formUtil.errorMessage('Enter place of work')
                        return
                    }
                    
                    processing.value = true

                    submitAxios()
                }

                const submitAxios = () => {
                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/employee-emergency-contacts-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/employee-emergency-contacts-edit/${editId.value}`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchEmergencyContacts()
                            
                            formUtil.successMessage(response.data.message)

                            processing.value = false

                            $('#emergency-contact-modal').modal('hide')
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const modalTitle = ref('')
                const modalMessage = ref('')
                const modalAction = ref('')
                const modalActionRef = ref(null)
                
                const deleteId = ref(null)
                const confirmDelete = (id) => {
                    modalTitle.value = 'Delete Emergency Contact?'
                    modalMessage.value = 'Are you sure you want to delete this emergency contact?'
                    modalAction.value = 'Delete'
                    modalActionRef.value = deleteEmergencyContact

                    deleteId.value = id
                    
                    $('#confirm-modal').modal('show')
                }

                const deleteEmergencyContact = () => {
                    if (processing.value) {
                        return
                    }
                    
                    processing.value = true

                    axios.delete(`${baseUrl}/employee-emergency-contacts-delete/${deleteId.value}`)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            fetchEmergencyContacts()

                            processing.value = false

                            $('#confirm-modal').modal('hide')
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                            processing.value = false
                        })
                }

                const fetchData = (uri, referenceVariable) => {
                    axios.get(uri)
                        .then(response => referenceVariable.value = response.data.data ?? response.data)
                        .catch(error => formUtil.errorMessage(error.response.data.message))
                }

                const emergencyContacts = ref([])
                const fetchEmergencyContacts = () => {
                    fetchData(`${baseUrl}/employee-emergency-contacts-list/${employee.id}`, emergencyContacts)
                }

                
                const relationships = ref([])
                onMounted(() => {
                    fetchEmergencyContacts()

                    fetchData('hr/configurations/relationships', relationships)

                    $('#emergency-contacts-app select').select2({
                        placeholder: 'Select...'
                    })
                })

                const selectedRelationship = (id) => {
                    return relationships.value.find(relationship => relationship.id == id)?.name ?? ''
                }
                
                return {
                    processing,
                    form, 
                    submitForm,
                    emergencyContacts,
                    relationships,
                    action,
                    showAddModal,
                    showEditModal,
                    relationshipChanged,
                    confirmDelete,
                    modalTitle,
                    modalMessage,
                    modalAction,
                    modalActionRef,
                    selectedRelationship
                }
            }
        }).mount('#emergency-contacts-app')
    </script>
@endpush