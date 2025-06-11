<div style="padding:10px" id="payment-mode-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-payment-modes___create') || user.role_id == '1')"
            >
                <i class="fa fa-plus"></i>
                Add Payment Mode
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Payment Mode</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(paymentMode, index) in paymentModes" :key="paymentMode.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ paymentMode.name }}</td>
                    <td>@{{ paymentMode.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(paymentMode)" v-if="(permissions.includes('hr-and-payroll-configurations-payment-modes___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a 
                                href="#" 
                                @click.prevent="deletePaymentMode(paymentMode.id)" 
                                v-if="(permissions.includes('hr-and-payroll-configurations-payment-modes___delete') || user.role_id == '1') && !paymentMode.employees_count">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="payment-mode-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Payment Mode</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="payment-mode-name">Payment Mode</label>
                            <input type="text" class="form-control" id="payment-mode-name" placeholder="Enter mode" v-model="form.name">
                        </div>
                        <div class="form-group">
                            <label for="payment-mode-description">Description</label>
                            <textarea class="form-control" id="payment-mode-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Payment Mode</button>
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
                    return permissions.includes('hr-and-payroll-configurations-payment-modes___edit') || 
                        permissions.includes('hr-and-payroll-configurations-payment-modes___delete') ||
                        user.role_id == '1'

                })
                
                const paymentModes = ref([])
                const fetchPaymentModes = () => {
                    axios.get(`${baseUrl}/payment-mode-list`)
                        .then(response => paymentModes.value = response.data)
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

                    $('#payment-mode-modal').modal('show')
                }

                const showEditModal = (paymentMode) => {
                    form.value.id = paymentMode.id
                    form.value.name = paymentMode.name
                    form.value.description = paymentMode.description

                    action.value = 'Edit'

                    $('#payment-mode-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.name) {
                        formUtil.errorMessage('Enter mode')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/payment-mode-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/payment-mode-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchPaymentModes()
                            
                            formUtil.successMessage(response.data.message)

                            $('#payment-mode-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deletePaymentMode = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this payment mode?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/payment-mode-delete/${id}`)
                                    .then(response => {
                                        fetchPaymentModes()

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
                    fetchPaymentModes()
                })

                return {
                    user,
                    permissions,
                    paymentModes,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deletePaymentMode,
                    canViewActions
                }
            }
        }).mount('#payment-mode-app')
    </script>
@endpush