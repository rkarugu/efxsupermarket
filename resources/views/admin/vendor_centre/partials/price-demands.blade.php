@php
    $user = getLoggeduserProfile();
@endphp

<div style="padding: 10px;" id="price-demands-app">
    <div class="d-flex justify-content-between">
        <ul class="nav nav-tabs" style="margin-bottom: 10px">
            <li class="active" @click="currentTab = 'pending'"><a href="#pending-price-demands" data-toggle="tab">Pending</a></li>
            <li @click="currentTab = 'completed'"><a href="#completed-price-demands" data-toggle="tab">Completed</a></li>
        </ul>

        <div class="d-flex align-items-center">
            <div class="checkbox" style="margin-right: 10px" v-if="currentTab == 'pending' && demands.length">
                <label>
                    <input type="checkbox" v-model="enableMerge">
                    Merge Demands
                </label>
            </div>
            <button class="btn btn-primary" @click="mergeDemands" v-if="enableMerge && demandsToMerge.length">Merge Demands</button>
        </div>
    </div>

    <table class="table table-bordered" v-cloak>
        <thead>
            <tr>
                <th style="width: 3%;"> #</th>
                <th>Date</th>
                <th>Branch</th>
                <th>Demand No.</th>
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
                <td>@{{ demand.user.name }}</td>
                <td>@{{ demand.demand_items_count }}</td>
                <td>@{{ numberWithCommas(demand.demand_amount) }}</td>
                <td>@{{ numberWithCommas(demand.vat_amount) }}</td>
                <td>@{{ numberWithCommas(demand.edited_demand_amount) }}</td>
                <td>
                    <span v-if="demand.approved">Yes</span>
                    <span v-else>No</span>
                </td>
                <td>@{{ demand.credit_note_no }}</td>
                <td>
                    <div class="action-button-div" v-if="!enableMerge">
                        <a :href="`/admin/n-item-demands/details/${demand.id}`" target="_blank"><i
                                class="fas fa-eye text-primary fa-lg" style="color: #337ab7;"
                                title="View"></i></a>

                        {{-- <a :href="`/admin/n-item-demands/download/${demand.id}`" target="_blank" v-if="demand.approved"> --}}
                            <a :href="`/admin/n-item-demands/download/${demand.id}`" target="_blank">
                            <i class="fa fa-file-pdf fa-lg" style="color: #337ab7;" title="Print"></i>
                        </a>      

                        <a 
                            href="#" 
                            target="_blank"
                            data-toggle="modal" 
                            data-target="#price-edit-modal" 
                            @click.prevent="editDemand(demand)"
                            v-if="(permissions.includes('item-demands___edit') || user.role_id == '1') && !demand.approved"
                        >
                            <i class="fa fa-pencil  fa-lg" style="color: #337abedit7;" title="Edit"></i>
                        </a>

                        <a 
                            :href="`/admin/n-item-demands/convert/${demand.id}`"
                            target="_blank"
                            v-if="demand.approved && (permissions.includes('item-demands___convert') || user.role_id == '1') && !demand.processed"
                        >
                            <i class="fa fa-arrow-right fa-lg" style="color: #337ab7;" title="Convert"></i>
                        </a>

                        <a 
                            href="#"
                            target="_blank"
                            @click.prevent="approveDemand(demand.id)"
                            v-if="!demand.approved && (permissions.includes('item-demands___approve') || user.role_id == '1') && !demandEdited(demand)"
                        >
                            <i class="fa fa-check-circle fa-lg" style="color: #337ab7;" title="Approve"></i>
                        </a>                                            

                        <a 
                            href="#"
                            target="_blank"
                            @click.prevent="approveDemand(demand.id)"
                            v-if="!demand.approved && (permissions.includes('item-demands___approve-edited') || user.role_id == '1') && demandEdited(demand)"
                        >
                            <i class="fa fa-check-circle fa-lg" style="color: #337ab7;" title="Approve"></i>
                        </a>                                            
                    </div>
                    <div class="checkbox" style="margin-block: 0" v-else>
                        <label>
                            <input type="checkbox" :Value="demand.id" v-model="demandsToMerge">
                        </label>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <div class="modal fade" id="price-edit-modal">
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

    <script type="module">
        import {createApp, computed, onMounted, ref, watch } from 'vue';

        createApp({
            setup() {
                const user = {!! $user !!}
                const supplier_id = {!! $supplier->id !!}
                const formUtil = new Form()

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

                    axios.post(`/api/edit-price-demand/${editForm.value.id}`, editForm.value)
                        .then(response => {
                            formUtil.successMessage(response.data.message)

                            fetchSupplierPriceDemands()

                            $('#price-edit-modal').modal('hide')
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
            
                                axios.post(`/api/edit-and-approve-price-demand/${editForm.value.id}`, {
                                    ...editForm.value,
                                    user_id: user.id
                                })
                                    .then(response => {
                                        formUtil.successMessage(response.data.message)
            
                                        fetchSupplierPriceDemands()

                                        $('#price-edit-modal').modal('hide')
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
            
                                axios.post(`/api/approve-price-demand/${id}`, {
                                    user_id: user.id
                                })
                                    .then(response => {
                                        formUtil.successMessage(response.data.message)
            
                                        fetchSupplierPriceDemands()

                                        $('#price-edit-modal').modal('hide')
                                        processing.value = false
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.message)
                                        processing.value = false
            
                                    })
                            }
                        })             
                }

                const enableMerge = ref(false)

                const demandsToMerge = ref([])

                const mergeDemands = () => {
                    Swal.fire({
                            title: 'Merge these demands?',
                            showCancelButton: true,
                            confirmButtonText: `Merge`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.post(`/api/merge-price-demands`, {
                                    user_id: user.id,
                                    demands: demandsToMerge.value
                                })
                                    .then(response => {
                                        formUtil.successMessage(response.data.message)
            
                                        fetchSupplierPriceDemands()

                                        processing.value = false
                                        enableMerge.value = false
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.message)
                                        processing.value = false
            
                                    })
                            }
                        })         
                }
                
                const currentTab = ref('pending')
                
                const allDemands = ref([])
                
                const demands = computed(() => {
                    return allDemands.value.filter(demand => {
                        if (currentTab.value == 'pending') {
                            return !demand.approved || !demand.processed
                        } else if (currentTab.value == 'completed') {
                            return demand.processed
                        }
                    })
                })

                const fetchSupplierPriceDemands = () => {
                    axios.get(`/api/supplier-price-demands/${supplier_id}`)
                        .then(response => {
                            allDemands.value = response.data
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })
                }
                
                onMounted(() => {
                    fetchSupplierPriceDemands()
                })

                return {
                    demands,
                    allDemands,
                    numberWithCommas,
                    dayjs,
                    currentTab,
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
                    enableMerge,
                    mergeDemands,
                    demandsToMerge
                }
            }
        }).mount('#price-demands-app')
    </script>
@endpush

