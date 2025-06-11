@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
    $pendingRoute = '"' . route('return-to-supplier.from-grn.pending') . '"';
    $approvedRoute = '"' . route('return-to-supplier.from-grn.approved') . '"';
@endphp

<script>
    window.user = {!! $user !!}
    window.returns = {!! $returns !!}
    window.branches = {!! json_encode(getBranchesDropdown()) !!}
    window.locations = {!! json_encode(getStoreLocationDropdownByBranch($user->restaurant_id)) !!}
    window.bins = {!! json_encode(getUnitOfMeasureList()) !!}
    window.suppliers = {!! json_encode(getSupplierDropdown()) !!}
    window.pendingRoute = {!! $pendingRoute !!}
    window.approvedRoute = {!! $approvedRoute !!}
</script>

<style>
table .select2-selection__arrow b{
    display:none !important;
}
</style>

@section('content')
    <div id="app" v-cloak>
        <section class="content" id="return-from-store">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Approve Return From GRN </h3>
                </div>
                <div class="box-body">
                    <form action="" method="post" v-cloak>
                        @csrf
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Employee name</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" value="{{ $user->name }}" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Return Date</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" readonly value="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">GRN</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" :value="grnNo" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Purchase Order No.</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" :value="purchaseOrderNo" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Supplier Name</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" :value="supplierName" readonly>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Branch</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="branchName" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control" :value="locationName" readonly>
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
                            <h3 class="box-title">Return From GRN Line</h3>
                        </div>
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Received</th>
                                    <th>Already Returned</th>
                                    <th>QOH</th>
                                    <th>Return Quantity</th>
                                    <th>Weight (Kg)</th>
                                    <th>Cost (KES)</th>
                                    <th>Exclusive</th>
                                    <th>Vat</th>
                                    <th>Total Cost (KES)</th>
                                    <th>Reason</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(lineItem, index) in form.lineItems" :key="index">
                                    <td>@{{ form.lineItems[index].item_code }}</td>
                                    <td>@{{ form.lineItems[index].item_description }}</td>
                                    <td>@{{ form.lineItems[index].received }}</td>
                                    <td>@{{ form.lineItems[index].returned }}</td>
                                    <td>@{{ form.lineItems[index].qoh }}</td>
                                    <td style="width: 100px">
                                        <input 
                                            class="form-control" 
                                            v-model="form.lineItems[index].quantity" 
                                            @keyUp="calculateWeightAndCost(index)"
                                        >
                                    </td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].weight) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].cost) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].exclusive) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].vat) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].total_cost) }}</span></td>
                                    <td>
                                        <input type="text" class="form-control" v-model="form.lineItems[index].reason">
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="6" style="text-align:right">
                                        Total Weight (Kg)
                                    </th>
                                    <td>@{{ numberWithCommas(totalWeight) }}</td>
                                    <th colspan="4" style="text-align:right">
                                        Total Exclusive (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalExclusive) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total VAT (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalVat) }}</td>
                                </tr>
                                <tr>
                                    <th colspan="11" style="text-align:right">
                                        Total Cost (KES)
                                    </th>
                                    <td>@{{ numberWithCommas(totalCost) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="col-md-12">
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" :disabled="processing" @click="approveRequest">Approve</button>
                            <button type="button" class="btn btn-secondary btn-sm" style="margin-left: 10px" :disabled="processing" @click="rejectRequest">Reject</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('uniquepagescript')
    <script src="{{ asset('js/sweetalert.js') }}"></script>
    <script src="{{ asset('js/form.js') }}"></script>
    <script src="{{ asset('assets/admin/bower_components/select2/dist/js/select2.full.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            $('select').select2({
                placeholder: 'Select...',
            });
        });
    </script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
            "imports": {
                "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.js"
            }
        }
    </script>

    <script type="module">
        import {createApp, onMounted, ref, watch, computed } from 'vue';

        createApp({
            setup() {
                const returns = window.returns
                
                const formUtil = new Form()
                
                const form = ref({
                    user_id: '',

                    lineItems: []
                })

                const branchName = returns[0].grn.purchase_order.get_branch.name
                const locationName = returns[0].grn.purchase_order.store_location.location_name
                const grnNo = returns[0].grn.grn_number
                const purchaseOrderNo = returns[0].grn.purchase_order.purchase_no
                const supplierName = returns[0].supplier.name

                const numberWithCommas = (value) => {
                    if (value) {
                        let parts = value.toString().split(".");
                        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                        return parts.join(".");
                    } else {
                        return "0";
                    }
                }
                
                returns.forEach(returnItem => {
                    let quantity = returnItem.returned_quantity
                    let sellingPrice = parseFloat(returnItem.grn.invoice_info.order_price)
                    let vatRate = parseFloat(returnItem.grn.invoice_info.vat_rate)
                    let totalCost = quantity * sellingPrice
                    let vat = (vatRate * totalCost) / (100 + vatRate)

                    form.value.lineItems.push({
                        id: returnItem.id,
                        supplier_id: returnItem.grn.wa_supplier_id,
                        item_code: returnItem.grn.item_code,
                        item_description: returnItem.grn.item_description,
                        received: returnItem.grn.qty_received,
                        returned: returnItem.grn.returned_grns_sum_returned_quantity ?? 0,
                        qoh: returnItem.grn.purchase_order_item.inventory_item.stock_moves_sum_qauntity ?? 0,
                        quantity: returnItem.returned_quantity,
                        weight: returnItem.returned_quantity * returnItem.grn.purchase_order_item.inventory_item.net_weight,
                        cost: sellingPrice.toFixed(2),
                        exclusive: (totalCost - vat).toFixed(2),
                        vat: vat.toFixed(2),
                        total_cost: totalCost.toFixed(2),
                        reason: returnItem.reason
                    })
                })

                const calculateWeightAndCost = (index) => {
                    let item = returns.find(item => item.id == form.value.lineItems[index].id)
                    
                    let quantity = form.value.lineItems[index].quantity
                    let sellingPrice = parseFloat(item.grn.invoice_info.order_price)
                    let vatRate = parseFloat(item.grn.invoice_info.vat_rate)
                    let totalCost = quantity * sellingPrice
                    let vat = parseFloat(vatRate) / 100 * totalCost

                    form.value.lineItems[index].weight = (quantity * parseFloat(item.grn.purchase_order_item.inventory_item.net_weight)).toFixed(2)
                    form.value.lineItems[index].cost = item.grn.invoice_info.order_price
                    form.value.lineItems[index].exclusive = (totalCost - vat).toFixed(2)
                    form.value.lineItems[index].vat = vat.toFixed(2)
                    form.value.lineItems[index].total_cost = (quantity * sellingPrice).toFixed(2)
                }

                const totalWeight = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.weight), 0).toFixed(2)
                })

                const totalExclusive = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem
                        .exclusive), 0).toFixed(2)
                })

                const totalVat = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem
                        .vat), 0).toFixed(2)
                })

                const totalCost = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.total_cost), 0).toFixed(2)
                })

                const processing = ref(false)
                const approveRequest = () => {

                    let error = false
                    form.value.lineItems.forEach(lineItem => {
                        if (!/^[0-9]+$/.test(lineItem.quantity)) {
                            formUtil.errorMessage(`Enter valid quantity for ${lineItem.item_code}`)
                            error = true
                            return
                        }

                        if (parseInt(lineItem.quantity) < 1) {
                            formUtil.errorMessage(`Return quantity for ${lineItem.item_code} must be greater than 1`)
                            error = true
                            return
                        }

                        if (parseInt(lineItem.quantity) + lineItem.returned > lineItem.received) {
                            formUtil.errorMessage(`Item ${lineItem.item_code} exceeds its allowed return quantity`)
                            error = true
                            return
                        }

                        if (lineItem.quantity > lineItem.qoh) {
                            formUtil.errorMessage(`Item ${lineItem.item_code} return quantity exceeds QOH`)
                            error = true
                            return
                        }

                        if (parseInt(lineItem.quantity) > 0 && !lineItem.reason) {
                            formUtil.errorMessage(`Enter reason for ${lineItem.item_code}`)
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
            
                                axios.post(`/api/approve-return-from-grn`, form.value)
                                    .then(response => {
                                        formUtil.successMessage('Return approved successfully!')
                                        
                                        processing.value = false
            
                                        window.location = window.approvedRoute
                                    })
                                    .catch(error => {
                                        formUtil.errorMessage(error.response.data.error)
                                        processing.value = false
                                    })
                            }
                        })                        
                }

                const rejectRequest = () => {
                    Swal.fire({
                        title: 'Reject this request?',
                        showCancelButton: true,
                        confirmButtonText: `Reject`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post(`/api/reject-return-from-grn`, form.value)
                                .then(response => {
                                    formUtil.successMessage('Return rejected successfully!')

                                    processing.value = false

                                    window.location = window.pendingRoute

                                })
                                .catch(error => {
                                    formUtil.errorMessage(error.response.data.error)
                                    processing.value = false
                                }) 
                        }
                    }) 
                }

                const user = ref({})

                onMounted(() => {
                    user.value = window.user

                    form.value.user_id = user.value.id
                })
                
                return {
                    form,
                    user,
                    calculateWeightAndCost,
                    totalWeight,
                    totalCost,
                    numberWithCommas,
                    processing,
                    purchaseOrderNo,
                    supplierName,
                    returns,
                    grnNo,
                    approveRequest,
                    rejectRequest,
                    branchName,
                    locationName,
                    totalExclusive,
                    totalVat
                }
            }
        }).mount('#app')
    </script>
@endsection

