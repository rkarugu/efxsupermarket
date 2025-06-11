@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
    $pendingRoute = '"' . route('return-to-supplier.from-store.pending') . '"';
    $approvedRoute = '"' . route('return-to-supplier.from-store.approved') . '"';
@endphp

<script>
    window.user = {!! $user !!}
    window.return = {!! $return !!}
    window.pendingRoute = {!! $pendingRoute !!}
    window.approvedRoute = {!! $approvedRoute !!}
</script>

@section('content')
    <div id="app" v-cloak>
        <section class="content" id="return-from-store">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Approve Return From Store </h3>
                </div>
                <div class="box-body">
                    <form action="" method="post" v-cloak>
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Requester</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" :value="storeReturn.user.name" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Request Date</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" readonly value="{{ $return->created_at->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Note</label>
                                        <div class="col-sm-7">
                                            <textarea class="form-control" rows="3" v-model="form.note"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="storeReturn.user.user_restaurent.name" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="storeReturn.location.location_name" readonly>
                                        </div>
                                    </div>
                                </div>
        
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Bin Location</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="storeReturn.uom.title" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="storeReturn.supplier.name" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        
        <section class="content">
            <div class="box box-primary">
                <div class="box-header with-border no-padding-h-b">
                    <div class="col-md-12 no-padding-h ">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="box-title">Return From Store Line</h3>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>QOH</th>
                                    <th>Quantity</th>
                                    <th>Weight (Kg)</th>
                                    <th>Cost (KES)</th>
                                    <th>Exclusive</th>
                                    <th>Vat</th>
                                    <th>Total Cost (KES)</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(lineItem, index) in form.lineItems" :key="index">
                                    <td style="width: 300px">
                                        @{{ form.lineItems[index].code }}
                                    </td>
                                    <td>@{{ form.lineItems[index].description }}</td>
                                    <td>@{{ form.lineItems[index].qoh }}</td>
                                    <td style="width: 100px">
                                        <input 
                                            class="form-control" 
                                            v-model="form.lineItems[index].quantity" 
                                            v-show="form.lineItems[index].code"
                                            @keyUp="calculateWeightAndCost(index)"
                                        >
                                    </td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].weight) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].cost) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].exclusive) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].vat) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].total_cost) }}</span></td>
                                    <td class="text-right">
                                        @if (isset($user->permissions['return-to-supplier-from-store___approve']) || $user->role_id == '1')
                                            <button type="button" class="btn btn-danger btn-sm" @click="removeItem(index)">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" style="text-align:right">
                                        Total Weight (Kg)
                                    </th>
                                    <td>@{{ numberWithCommas(totalWeight) }}</td>
                                    <th colspan="4" style="text-align:right">
                                        Total Exclusive (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalExclusive) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Total VAT (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalVat) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="9" style="text-align:right">
                                        Total Cost (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalCost) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12" v-if="form.lineItems.length">
                        <div>
                            @if (isset($user->permissions['return-to-supplier-from-store___approve']) || $user->role_id == '1')
                                <button type="button" class="btn btn-primary btn-sm" :disabled="processing" @click="approveRequest">
                                    @{{ processing ? 'Processing...' : 'Approve' }}
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#reject-modal" style="margin-left: 10px" :disabled="processing">
                                    @{{ processing ? 'Processing...' : 'Reject' }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <div class="modal fade" id="reject-modal" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title">Reject Return</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="">Are you sure you want to reject this return?</label>
                        </div>
                        <div class="form-group">
                            <label for="reason">Reason <span style="color: red">*</span></label>
                            <textarea class="form-control" id="reason" rows="3" placeholder="Enter reason" v-model="rejectReason"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary pull-right" style="margin-left: 10px" :disabled="processing" @click="rejectRequest">Reject</button>
                        <button type="button" class="btn btn-default pull-right" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
        "imports": {
            "vue": "{{ config('app.env') == 'local' ? asset('js/vue.esm-browser.min.js') : asset('js/vue.esm-browser.prod.min.js') }}"
        }
        }
    </script>

    <script type="module">
        import {createApp, onMounted, ref, watch, computed } from 'vue';

        createApp({
            setup() {
                const storeReturn = ref(window.return)
                
                const formUtil = new Form()
                
                const form = ref({
                    user_id: '',
                    note: storeReturn.value.note,

                    lineItems: [],

                    deletedItems: []
                })

                storeReturn.value.store_return_items.forEach(item => {
                    let quantity = item.quantity
                    let standardCost = parseFloat(item.cost)
                    let vatRate = parseFloat(item.inventory_item.tax_manager.tax_value)
                    let totalCost = quantity * standardCost
                    let vat = (vatRate * totalCost) / (100 + vatRate)

                    form.value.lineItems.push({
                        id: item.id,
                        code: item.inventory_item.stock_id_code,
                        description: item.inventory_item.description,
                        qoh: item.inventory_item.stock_moves_sum_qauntity,
                        quantity: quantity,
                        weight: item.weight,
                        cost: standardCost,
                        exclusive: (totalCost - vat).toFixed(2),
                        vat: vat.toFixed(2),
                        cost: standardCost,
                        total_cost: item.total_cost,
                        inventory_item: item.inventory_item
                    })
                })

                const calculateWeightAndCost = (index) => {
                    let item = form.value.lineItems[index].inventory_item

                    let quantity = form.value.lineItems[index].quantity
                    let standardCost = parseFloat(item.standard_cost)
                    let vatRate = parseFloat(item.tax_manager.tax_value)
                    let totalCost = quantity * standardCost
                    let vat = (vatRate * totalCost) / (100 + vatRate)

                    form.value.lineItems[index].weight = (quantity * parseInt(item.net_weight)).toFixed(2)
                    form.value.lineItems[index].exclusive = (totalCost - vat).toFixed(2)
                    form.value.lineItems[index].vat = vat.toFixed(2)
                    form.value.lineItems[index].total_cost = (quantity * parseFloat(item.standard_cost)).toFixed(2)
                }

                const totalWeight = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.weight), 0).toFixed(2)
                })

                const totalExclusive = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.exclusive), 0).toFixed(2)
                })

                const totalVat = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.vat), 0).toFixed(2)
                })

                const totalCost = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.total_cost), 0).toFixed(2)
                })

                const removeItem = (index) => {
                    form.value.deletedItems.push(form.value.lineItems[index].id)
                    form.value.lineItems.splice(index, 1)
                }

                const processing = ref(false)
                const approveRequest = () => {

                    if (!form.value.note) {
                        formUtil.errorMessage('Enter note')
                        return
                    }

                    let error = false

                    form.value.lineItems.forEach(lineItem => {
                        if (!lineItem.quantity || lineItem.quantity == '0') {
                            formUtil.errorMessage(`Enter quantity for ${lineItem.code}`)
                            return
                        }

                        if (!/^[0-9]+$/.test(lineItem.quantity) || parseInt(lineItem.quantity) < 0) {
                            formUtil.errorMessage(`Enter valid quantity for ${lineItem.code}`)
                            error = true
                            return
                        }

                        if (lineItem.quantity > lineItem.qoh) {
                            formUtil.errorMessage(`Item ${lineItem.code} return quantity exceeds QOH`)
                            error = true
                            return
                        }
                    })

                    if (error) {
                        return
                    }

                    Swal.fire({
                            title: 'Approve this request?',
                            showCancelButton: true,
                            confirmButtonText: `Approve`,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                processing.value = true
            
                                axios.post(`/api/approve-return-from-store/${storeReturn.value.id}`, form.value)
                                    .then(response => {
                                        formUtil.successMessage('Return approved successfully!')
                                        
                                        processing.value = false
            
                                        window.location = window.approvedRoute
                                    })
                                    .catch(error => {
                                        // formUtil.errorMessage(error.response.data.error)
                                        if (error?.response?.data?.error) {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                html: error.response.data.error,
                                            });
                                        }else{
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error!',
                                                html: 'Something went wrong.',
                                            });
                                        }
                                        processing.value = false
                                    })
                            }
                        }) 
                }

                const rejectReason = ref('')
                
                const rejectRequest = () => {
                    if (!rejectReason.value) {
                        formUtil.errorMessage('Enter reason')
                        return
                    }

                    processing.value = true

                    axios.post(`/api/reject-return-from-store/${storeReturn.value.id}`, { reject_reason: rejectReason.value })
                        .then(response => {
                            formUtil.successMessage('Return rejected successfully!')

                            processing.value = false

                            window.location = window.pendingRoute

                        })
                        .catch(error => {
                            // formUtil.errorMessage(error.response.data.error)
                            if (error?.response?.data?.error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: error.response.data.error,
                                });
                            }else{
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    html: 'Something went wrong.',
                                });
                            }
                            processing.value = false
                        }) 
                }

                const numberWithCommas = (value) => {
                    if (value) {
                        let parts = value.toString().split(".");
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return parts.join(".");
                    } else {
                        return "0";
                    }
                }

                onMounted(() => {
                    form.value.user_id = window.user.id
                })
                
                return {
                    storeReturn,
                    form,
                    calculateWeightAndCost,
                    removeItem,
                    totalWeight,
                    totalCost,
                    numberWithCommas,
                    processing,
                    approveRequest,
                    rejectRequest,
                    totalExclusive,
                    totalVat,
                    rejectReason
                }
            }
        }).mount('#app')
    </script>
@endsection

