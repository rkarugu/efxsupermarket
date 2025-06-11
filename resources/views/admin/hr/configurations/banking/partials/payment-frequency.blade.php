<div style="padding:10px" id="payment-frequency-app">
    <div v-cloak>
        <div style="text-align: right">
            <button 
                class="btn btn-primary" 
                @click="showAddModal" 
                v-if="(permissions.includes('hr-and-payroll-configurations-general___create') || user.role_id == '1')"
            >
                Add Payment Frequency
            </button>
        </div>

        <hr>

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 30%">Frequency</th>
                    <th>Description</th>
                    <th style="width: 10%" v-if="canViewActions">Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(paymentFrequency, index) in paymentFrequencies" :key="paymentFrequency.id">
                    <td>@{{ ++index }}</td>
                    <td>@{{ paymentFrequency.frequency }}</td>
                    <td>@{{ paymentFrequency.description }}</td>
                    <td style="text-align: center" v-if="canViewActions">
                        <div class="action-button-div">
                            <a href="#" @click.prevent="showEditModal(paymentFrequency)" v-if="(permissions.includes('hr-and-payroll-configurations-general___edit') || user.role_id == '1')">
                                <i class="fa fa-pencil fa-lg text-primary" title="Edit"></i>
                            </a>

                            <a href="#" @click.prevent="deletePaymentFrequency(paymentFrequency.id)" v-if="(permissions.includes('hr-and-payroll-configurations-general___delete') || user.role_id == '1')">
                                <i class="fa fa-trash fa-lg text-danger" title="Delete"></i>
                            </a>                                            
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="modal fade" id="payment-frequency-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">@{{ action }} Payment Frequency</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="payment-frequency-frequency">Payment Frequency</label>
                            <input type="text" class="form-control" id="payment-frequency-frequency" placeholder="Enter frequency" v-model="form.frequency">
                        </div>
                        <div class="form-group">
                            <label for="payment-frequency-description">Description</label>
                            <textarea class="form-control" id="payment-frequency-description" rows="3" placeholder="Enter description" v-model="form.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled=processing @click="submitForm">@{{ action }} Payment Frequency</button>
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
                    return permissions.includes('hr-and-payroll-configurations-general___edit') || 
                        permissions.includes('hr-and-payroll-configurations-general___delete') ||
                        user.role_id == '1'

                })
                
                const paymentFrequencies = ref([])
                const fetchPaymentFrequencies = async () => {
                    try {
                        let response = await axios.get(`${baseUrl}/payment-frequency-list`)

                        paymentFrequencies.value = response.data
                    } catch (error) {
                        formUtil.errorMessage(error.response.data.error)
                    }
                }

                const action = ref('Add')

                const form = ref({
                    id: '',
                    frequency: '',
                    description: ''
                })

                const clearForm = () => {
                    form.value.frequency = ''
                    form.value.description = ''
                }

                const showAddModal = () => {
                    action.value = 'Add'

                    clearForm()

                    $('#payment-frequency-modal').modal('show')
                }

                const showEditModal = (paymentFrequency) => {
                    form.value.id = paymentFrequency.id
                    form.value.frequency = paymentFrequency.frequency
                    form.value.description = paymentFrequency.description

                    action.value = 'Edit'

                    $('#payment-frequency-modal').modal('show')
                }

                const processing = ref(false)

                const submitForm = () => {
                    if (!form.value.frequency) {
                        formUtil.errorMessage('Enter frequency')
                        return
                    }

                    let uri = ''

                    if (action.value == 'Add') {
                        uri = `${baseUrl}/payment-frequency-create`
                    } else if (action.value == 'Edit') {
                        uri = `${baseUrl}/payment-frequency-edit`
                    }

                    axios.post(uri, form.value)
                        .then(response => {
                            fetchPaymentFrequencies()
                            
                            formUtil.successMessage(response.data.message)

                            $('#payment-frequency-modal').modal('hide')
                            
                            clearForm()
                            
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false
                        })
                }

                const deletePaymentFrequency = (id) => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Delete this payment frequency?',
                            showCancelButton: true,
                            confirmButtonText: `Delete`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.delete(`${baseUrl}/payment-frequency-delete/${id}`)
                                    .then(response => {
                                        fetchPaymentFrequencies()

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
                    fetchPaymentFrequencies()
                })

                return {
                    user,
                    permissions,
                    paymentFrequencies,
                    action,
                    processing,
                    form, 
                    submitForm,
                    showAddModal,
                    showEditModal,
                    deletePaymentFrequency,
                    canViewActions
                }
            }
        }).mount('#payment-frequency-app')
    </script>
@endpush