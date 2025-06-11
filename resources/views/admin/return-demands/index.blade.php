@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
@endphp

@section('content')
    <section class="content" id="app">
        <div class="session-message-container">
            @include('message')
        </div>
        
        <div class="box box-primary" v-cloak>
            <div class="box-header with-border">
                <div class="box-header-flex">
                    <h3 class="box-title"> Return Demands </h3>
                </div>
            </div>

            <div class="box-body">
                <form>
                    <div class="form-group col-sm-4">
                        <label for="supplier">Supplier</label>

                        <select name="supplier" class="form-control" id=supplier-id>
                            <option value="" selected disabled></option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" @if (request()->supplier == $supplier->id) selected @endif>
                                    {{ $supplier->name }} </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="from">From</label>
                        <input type="date" class="form-control" name="from" id="from"
                            value="{{ request()->from }}">
                    </div>
                    <div class="form-group col-sm-2">
                        <label for="to">To</label>
                        <input type="date" class="form-control" name="to" id="to"
                            value="{{ request()->to }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 25px;">Filter</button>
                    <a href="{{ route('demands.item-demands.new') }}" class="btn btn-primary"
                        style="margin-top:25px;">Clear</a>

                </form>

                <div style="clear:both;">
                    <hr>
                </div>


                <table class="table table-bordered" id="create_datatable_25">
                    <thead>
                        <tr>
                            <th style="width: 3%;"> #</th>
                            <th>Date</th>
                            <th>Branch</th>
                            <th>Demand No.</th>
                            <th>Return Type</th>
                            <th>Document No.</th>
                            <th>Created By</th>
                            <th>Supplier</th>
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
                        <tr v-for="(demand, index) in demands" :key="demands.id">
                            <th style="width: 3%;" scope="row">@{{ ++index }}</th>
                            <td>@{{  dayjs(demand.created_at).format('YYYY-MM-DD HH:mm:ss') }}</td>
                            <td>@{{ demand.user.user_restaurent.name }}</td>
                            <td>@{{ demand.demand_no }}</td>
                            <td>@{{ _.startCase(demand.return_type) }}</td>
                            <td>@{{ demand.return_document_no }}</td>
                            <td>@{{ demand.user.name ?? '-' }}</td>
                            <td>@{{ demand.supplier.name ?? '-' }}</td>
                            <td>@{{ demand.return_demand_items.length }}</td>
                            <td>@{{ numberWithCommas(demand.demand_amount) }}</td>
                            <td>@{{ numberWithCommas(demand.vat_amount) }}</td>
                            <td>@{{ numberWithCommas(demand.edited_demand_amount) }}</td>
                            <td>
                                <span v-if="demand.approved">Yes</span>
                                <span v-else>No</span>
                            </td>
                            <td>@{{ demand.credit_note_no }}</td>
                            <td>
                                <div class="action-button-div">
                                    <a :href="`/admin/return-demands/details/${demand.id}`"><i
                                            class="fas fa-eye text-primary fa-lg" style="color: #337ab7;"
                                            title="View"></i></a>

                                    <a :href="`/admin/return-demands/print/${demand.id}`" target="_blank" v-if="demand.approved">
                                        <i class="fa fa-file-pdf fa-lg" style="color: #337ab7;" title="Print"></i>
                                    </a>      
                                    <a 
                                        href="#" 
                                        data-toggle="modal" 
                                        data-target="#edit-modal" 
                                        @click.prevent="editDemand(demand)"
                                        v-if="(permissions.includes('return-demands___edit') || user.role_id == '1') && !demand.approved"
                                    >
                                        <i class="fa fa-pencil  fa-lg" style="color: #337abedit7;" title="Edit"></i>
                                    </a>

                                    <a 
                                        :href="`/admin/return-demands/convert/${demand.id}`"
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
            </div>

            <div class="modal fade" id="edit-modal">
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
    </section>

@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('js/utils.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $("body").addClass('sidebar-collapse');
            
            $("#supplier-id").select2({
                placeholder: 'Select supplier',
                allowClear: true
            });

            $("#item-id").select2({
                placeholder: 'Select item',
                allowClear: true
            });
        });
    </script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.21/lodash.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script type="module">
        import { createApp, ref, watch, computed } from 'vue';

        createApp({
            setup() {
                const formUtil = new Form()
                const user = {!! $user !!}
                const demands = {!! $demands !!}

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

                            window.location.reload()
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
            
                                        window.location.reload()
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
            
                                        window.location.reload()
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.message)
                                        processing.value = false
            
                                    })
                            }
                        })             
                }

                return {
                    demands,
                    user,
                    approveDemand,
                    numberWithCommas,
                    editDemand,
                    editDemandSubmit,
                    editForm,
                    processing,
                    dayjs,
                    permissions,
                    demandEdited,
                    canEditAndApprove,
                    editAndApproveDemand,
                }
            }
        }).mount('#app')
    </script>
@endsection
