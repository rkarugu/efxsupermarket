@php
    $user = getLoggeduserProfile();
@endphp

<div style="padding: 10px;" id="return-demands-app">
    <ul class="nav nav-tabs" style="margin-bottom: 10px">
        <li class="active" @click="currentTab = 'pending'"><a href="#pending-return-demands" data-toggle="tab">Pending</a></li>
        <li @click="currentTab = 'completed'"><a href="#completed-return-demands" data-toggle="tab">Completed</a></li>
    </ul>

    <table class="table table-bordered" v-cloak>
        <thead>
            <tr>
                <th style="width: 3%;"> #</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Demand No.</th>
                <th>Return Type</th>
                <th>Document No.</th>
                <th>Created By</th>
                <th>Items</th>
                <th>Total Amount</th>
                <th>Vat Amount</th>
                <th>New Amount</th>
                <th>Approved</th>
                <th>Credit Note</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr v-for="(demand, index) in demands" :key="demand.id">
                <th style="width: 3%;" scope="row">
                    @{{ index + 1 }}
                </th>
                <td>@{{ dayjs(demand.created_at).format('YYYY-MM-DD HH:mm:ss') }}</td>
                <td>@{{ demand.user.user_restaurent.name }}</td>
                <td>@{{ demand.demand_no }}</td>
                <td>@{{ _.startCase(demand.return_type) }}</td>
                <td>@{{ demand.return_document_no }}</td>
                <td>@{{ demand.user.name }}</td>
                <td>@{{ demand.return_demand_items_count }}</td>
                <td>KES @{{ numberWithCommas(demand.demand_amount.toFixed(2)) }}</td>
                <td>@{{ numberWithCommas(demand.vat_amount) }}</td>
                <td>@{{ numberWithCommas(demand.edited_demand_amount) }}</td>
                <td>
                    <span v-if="demand.approved">Yes</span>
                    <span v-else>No</span>
                </td>
                <td>@{{ demand.credit_note_no }}</td>
                <td>
                    <div class="action-button-div">
                        <a :href="`/admin/return-demands/details/${demand.id}`" target="_blank"><i
                                class="fas fa-eye text-primary fa-lg" style="color: #337ab7;"
                                title="View"></i></a>

                        <a :href="`/admin/return-demands/print/${demand.id}`" target="_blank" v-if="demand.approved">
                            <i class="fa fa-file-pdf fa-lg" style="color: #337ab7;" title="Print"></i>
                        </a>      

                        <a 
                            href="#" 
                            data-toggle="modal" 
                            data-target="#return-edit-modal" 
                            @click.prevent="editDemand(demand)"
                            v-if="(permissions.includes('return-demands___edit') || user.role_id == '1') && !demand.approved"
                        >
                            <i class="fa fa-pencil  fa-lg" style="color: #337abedit7;" title="Edit"></i>
                        </a>

                        <a 
                            :href="`/admin/return-demands/convert/${demand.id}`"
                            target="_blank"
                            v-if="demand.approved && (permissions.includes('return-demands___convert') || user.role_id == '1') && !demand.processed"
                        >
                            <i class="fa fa-arrow-right fa-lg" style="color: #337ab7;" title="Convert"></i>
                        </a>

                        <a 
                            href="#"
                            @click.prevent="approveDemand(demand.id)"
                            v-if="!demand.approved && (permissions.includes('return-demands___approve') || user.role_id == '1') && !demandEdited(demand)"
                        >
                            <i class="fa fa-check-circle fa-lg" style="color: #337ab7;" title="Approve"></i>
                        </a>                                            

                        <a 
                            href="#"
                            @click.prevent="approveDemand(demand.id)"
                            v-if="!demand.approved && (permissions.includes('return-demands___approve-edited') || user.role_id == '1') && demandEdited(demand)"
                        >
                            <i class="fa fa-check-circle fa-lg" style="color: #337ab7;" title="Approve"></i>
                        </a>                                            
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="modal fade" id="return-edit-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">Edit Demand</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="demand-no">Demand No.</label>
                        <input type="text" class="form-control" id="demand-no" v-model="editForm.demand_no" disabled>
                        <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                    </div>
                    <div class="form-group">
                        <label for="demand-amount">Demand Amount</label>
                        <input type="text" class="form-control" id="demand-amount" v-model="editForm.demand_amount" disabled>
                        <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                    </div>
                    <div class="form-group">
                        <label for="vat-amount">Vat Amount</label>
                        <input type="text" class="form-control" id="vat-amount" v-model="editForm.vat_amount">
                        <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                    </div>
                    <div class="form-group">
                        <label for="new-demand-amount">New Demand Amount</label>
                        <input type="text" class="form-control" id="new-demand-amount" v-model="editForm.edited_demand_amount">
                        <div data-lastpass-icon-root="" style="position: relative !important; height: 0px !important; width: 0px !important; float: left !important;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
                    <button 
                        type="button" 
                        class="btn btn-primary" 
                        :disabled="processing" 
                        @click="editAndApproveDemand" 
                        v-if="canEditAndApprove && permissions.includes('return-demands___approve-edited')"
                    >
                        Edit & Approve Demand
                    </button>
                    <button type="button" class="btn btn-primary" :disabled="processing" @click="editDemandSubmit">Edit Demand</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>

    <script type="module">
        import {createApp, computed, onMounted, ref, watch } from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const supplier_id = {!! $supplier->id !!}
                const formUtil = new Form()

                const currentTab = ref('pending')
                
                const allDemands = ref([])
                
                const demands = computed(() => {
                    return allDemands.value.filter(demand => {
                        if (currentTab.value == 'pending') {
                            return !demand.approved || !demand.processed 
                        } else if (currentTab.value == 'completed') {
                            return demand.approved
                        }
                    })
                })

                const permissions = Object.keys(user.permissions)

                const demandEdited = (demand) => {
                    return demand.demand_amount != demand.edited_demand_amount
                }

                const editForm = ref({
                    id: '',
                    demand_no: '',
                    demand_amount: '',
                    vat_amount: '',
                    edited_demand_amount: '',
                })
                
                const editDemand = (demand) => {
                    editForm.value.id = demand.id
                    editForm.value.demand_no = demand.demand_no
                    editForm.value.demand_amount = numberWithCommas(demand.demand_amount)
                    editForm.value.vat_amount = numberWithCommas(demand.vat_amount)
                    editForm.value.edited_demand_amount = numberWithCommas(demand.edited_demand_amount)
                }

                watch(() => editForm.value.vat_amount, (value) => {
                    editForm.value.vat_amount = numberWithCommas(value.replace(/,/g, ''))
                })

                watch(() => editForm.value.edited_demand_amount, (value) => {
                    editForm.value.edited_demand_amount = numberWithCommas(value.replace(/,/g, ''))
                })

                const processing = ref(false)
                const editDemandSubmit = () => {
                    processing.value = true

                    editForm.value.vat_amount = editForm.value.vat_amount.replace(/,/g, '')
                    editForm.value.edited_demand_amount = editForm.value.edited_demand_amount.replace(/,/g, '')

                    axios.post(`/api/edit-return-demand/${editForm.value.id}`, editForm.value)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            fetchSupplierReturnDemands()

                            $('#return-edit-modal').modal('hide')
                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.message)
                            processing.value = false

                        })
                }

                const canEditAndApprove = computed(() => {
                    let editedDemandAmount = parseFloat(editForm.value.edited_demand_amount.replace(/,/g, ''))
                    let demandAmount = parseFloat(editForm.value.demand_amount.replace(/,/g, ''))
                    return Math.abs(editedDemandAmount - demandAmount) <= 1000
                })

                const editAndApproveDemand = () => {
                    if (processing.value) {
                        return
                    }
                    
                    Swal.fire({
                            title: 'Edit and approve this demand?',
                            showCancelButton: true,
                            confirmButtonText: `Edit & Approve`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true

                                editForm.value.vat_amount = editForm.value.vat_amount.replace(/,/g, '')
                                editForm.value.edited_demand_amount = editForm.value.edited_demand_amount.replace(/,/g, '')
            
                                axios.post(`/api/edit-and-approve-return-demand/${editForm.value.id}`, {
                                    ...editForm.value,
                                    user_id: user.id
                                })
                                    .then(response => {
                                        formUtil.successMessage(response.data.message)
            
                                        fetchSupplierReturnDemands()

                                        $('#return-edit-modal').modal('hide')
                                        processing.value = false
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.message)
                                        processing.value = false
            
                                    })
                            }
                        })       
                }

                const approveDemand = (id) => {
                    if (processing.value) {
                        return
                    }

                    Swal.fire({
                            title: 'Approve this demand?',
                            showCancelButton: true,
                            confirmButtonText: `Approve`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.post(`/api/approve-return-demand/${id}`, {
                                    user_id: user.id
                                })
                                    .then(response => {
                                        formUtil.successMessage(response.data.message)
            
                                        fetchSupplierReturnDemands()
                                        processing.value = false
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.message)
                                        processing.value = false
            
                                    })
                            }
                        })             
                }

                const fetchSupplierReturnDemands = () => {
                    axios.get(`/api/supplier-return-demands/${supplier_id}`)
                        .then(response => {
                            allDemands.value = response.data
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })

                }

                onMounted(() => {
                    fetchSupplierReturnDemands()
                })

                return {
                    demands,
                    allDemands,
                    numberWithCommas,
                    dayjs,
                    currentTab,
                    user,
                    demands,
                    user,
                    approveDemand,
                    editDemand,
                    editDemandSubmit,
                    editForm,
                    processing,
                    permissions,
                    demandEdited,
                    canEditAndApprove,
                    editAndApproveDemand,
                }
            }
        }).mount('#return-demands-app')
    </script>
@endpush

