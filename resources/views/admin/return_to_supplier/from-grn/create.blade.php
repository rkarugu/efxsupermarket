@extends('layouts.admin.admin')

@php
    $user = getLoggeduserProfile();
@endphp

<script>
    window.user = {!! $user !!}
    window.branches = {!! json_encode(getBranchesDropdown()) !!}
    window.locations = {!! json_encode(getStoreLocationDropdownByBranch($user->restaurant_id)) !!}
    window.suppliers = {!! json_encode(getSupplierDropdown()) !!}
    window.bins = {!! json_encode($bins) !!}
</script>

<style>
    table .select2-selection__arrow b {
        display: none !important;
    }
</style>

@section('content')
    <div id="app" v-cloak>
        <section class="content" id="return-from-store">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Create Return From GRN </h3>
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
                                        <div class="col-sm-7" @click="grnSelectClicked">
                                            <select class="form-control" id="grn-select" :disabled="!form.location_id"
                                                v-model="form.grn_id" :onchange="grnChanged">
                                                <option :value="grn.id" v-for="grn in grns" :key="grn.id">
                                                    @{{ grn.grn_number }}
                                                </option>
                                            </select>
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
                                            <select class="form-control" :disabled="user.role_id != 1"
                                                v-model="form.branch_id" :onchange="branchChanged">
                                                <option :value="index" :selected="index == user.restaurant_id"
                                                    v-for="(branch, index) in branches" :key="index">
                                                    @{{ branch }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Store Location</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="location-select" :disabled="!Object.keys(locations).length" v-model="form.location_id"
                                                :onchange="locationChanged">
                                                <option :value="index"
                                                    :selected="index == user.wa_location_and_store_id"
                                                    v-for="(location, index) in locations" :key="index">
                                                    @{{ location }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row" style="margin-bottom: 20px">
                                    <div class="form-group">
                                        <label for="inputEmail3" class="col-sm-5 control-label">Bin Location</label>
                                        <div class="col-sm-6">
                                            <select class="form-control" id="uom-select" :disabled="!Object.keys(bins).length || !canChangeBin" v-model="form.bin_id" :onchange="uomChanged">
                                                <option :value="index" :selected="index == user.wa_unit_of_measures_id" v-for="(bin, index) in bins" :key="index">
                                                    @{{ bin }}
                                                </option>
                                            </select>
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
            <template>
                <div class="alert alert-warning alert-dismissible" v-if="form.lineItems.length">
                    Items with 0 return quantities will be ignored

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                </div>

                <div class="alert alert-warning alert-dismissible"
                    v-if="form.grn_id && !fetchingLineItems && !form.lineItems.length">
                    GRN has no inventory items

                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                </div>
            </template>
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
                                    <th>VAT</th>
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
                                        <input class="form-control" v-model="form.lineItems[index].quantity"
                                            @keyUp="calculateWeightAndCost(index)">
                                    </td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].weight) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].cost) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].exclusive) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].vat) }}</span></td>
                                    <td><span v-show="form.lineItems[index].quantity">@{{ numberWithCommas(form.lineItems[index].total_cost) }}</span></td>
                                    <td>
                                        <input type="text" class="form-control"
                                            v-model="form.lineItems[index].reason">
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
                            <button type="button" class="btn btn-primary btn-sm"
                                :disabled="processing || !form.lineItems.length" @click="submitForm">Process</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('uniquepagestyle')
    <link href="{{ asset('assets/admin/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" />
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

            $('table select').select2({
                placeholder: '',
            });
        });
    </script>

    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script type="importmap">
        {
        "imports": {
            "vue": "https://unpkg.com/vue@3/dist/vue.esm-browser.prod.js"
        }
        }
    </script>

    <script type="module">
        import {
            createApp,
            onMounted,
            ref,
            watch,
            computed
        } from 'vue';

        createApp({
            setup() {
                const user = ref(window.user)
                const branches = ref(window.branches)
                const locations = ref(window.locations)
                const bins = ref(window.bins)

                const permissions = Object.keys(user.value.permissions)

                const canChangeBin = computed(() => {
                    return permissions.includes('return-to-supplier-from-grn___change-bin') || [1, 154].includes(user.value.role_id)
                })

                const grns = ref([])
                const grnLineItems = ref([])

                const formUtil = new Form()

                const form = ref({
                    user_id: user.value.id,
                    branch_id: user.value.restaurant_id ?? '',
                    location_id: user.value.wa_location_and_store_id ?? '',
                    bin_id: user.value.wa_unit_of_measures_id ?? '',
                    grn_id: '',

                    lineItems: []
                })

                const branchChanged = (event) => {
                    form.value.branch_id = $(event.target).val();

                    form.value.location_id = ''
                    $('#location-select').val(null).trigger('change')
                    
                    locations.value = []
                    grns.value = []
                    grnLineItems.value = []
                    form.value.grn_id = ''
                    form.value.lineItems = []

                    axios.get(`/api/location-and-stores/${form.value.branch_id}`)
                        .then(response => {
                            locations.value = response.data
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
                        })
                }

                const locationChanged = (event) => {
                    form.value.location_id = $(event.target).val();

                    form.value.bin_id = ''
                    $('#bin-select').val(null).trigger('change')

                    bins.value = []
                    grns.value = []
                    grnLineItems.value = []
                    form.value.lineItems = []

                    if (form.value.location_id) {
                        axios.get(`/api/location-store-uom/${form.value.location_id}`)
                            .then(response => {
                                bins.value = response.data
                            })
                            .catch(error => {
                                formUtil.errorMessage(error.response.data.error)
                            })
                    }
                }

                const uomChanged = (event) => {
                    form.value.bin_id = $(event.target).val();

                    grns.value = []
                    grnLineItems.value = []
                    form.value.lineItems = []

                    fetchGrns()
                }

                const grnSelectClicked = () => {
                    if (!form.value.location_id) {
                        formUtil.errorMessage('Select store location and bin to get grns')
                    }
                }

                const findGrn = () => {
                    return grns.value.find(grn => grn.id == form.value.grn_id)
                }

                const purchaseOrderNo = computed(() => {
                    return findGrn()?.purchase_no
                })

                const supplierName = computed(() => {
                    return findGrn()?.supplier_name
                })

                const fetchingLineItems = ref(false)
                const grnChanged = (event) => {
                    form.value.grn_id = $(event.target).val()

                    form.value.lineItems = []

                    if (grns.value.find(grn => grn.id == form.value.grn_id).supplier_invoice_id) {
                        return
                    }
                    
                    fetchingLineItems.value = true

                    axios.get(`/api/grn-line-items/`, {
                        params: {
                            grn_number: findGrn().grn_number,
                            uom_id: form.value.bin_id
                        }
                    })
                        .then(response => {
                            grnLineItems.value = response.data

                            grnLineItems.value.forEach(lineItem => form.value.lineItems.push({
                                id: lineItem.id,
                                grn_number: lineItem.grn_number,
                                supplier_id: lineItem.wa_supplier_id,
                                item_code: lineItem.item_code,
                                item_description: lineItem.item_description,
                                received: lineItem.qty_received,
                                returned: lineItem.returned_grns_sum_returned_quantity ?? 0,
                                // qoh: lineItem.purchase_order_item.inventory_item
                                //     .stock_moves_sum_qauntity ?? 0,
                                qoh: lineItem.inventory_item
                                    .stock_moves_sum_qauntity ?? 0,
                                quantity: 0,
                                weight: 0,
                                cost: 0,
                                exclusive: 0,
                                vat: 0,
                                total_cost: 0,
                                reason: ''
                            }))

                            fetchingLineItems.value = false
                        })
                        .catch(error => {
                            console.log(error);
                            formUtil.errorMessage(error.response.data.error)
                            fetchingLineItems.value = false
                        })
                }

                const calculateWeightAndCost = (index) => {
                    let item = grnLineItems.value.find(item => item.id == form.value.lineItems[index].id)
                    
                    let quantity = form.value.lineItems[index].quantity
                    let sellingPrice = parseFloat(item.invoice_info.order_price)
                    let vatRate = parseFloat(item.invoice_info.vat_rate)
                    let totalCost = quantity * sellingPrice
                    let vat = (vatRate * totalCost) / (100 + vatRate)

                    // form.value.lineItems[index].weight = (quantity * parseFloat(item.purchase_order_item.inventory_item.net_weight)).toFixed(2)
                    form.value.lineItems[index].weight = (quantity * parseFloat(item.inventory_item.net_weight)).toFixed(2)
                    form.value.lineItems[index].cost = item.invoice_info.order_price
                    form.value.lineItems[index].exclusive = (totalCost - vat).toFixed(2)
                    form.value.lineItems[index].vat = vat.toFixed(2)
                    form.value.lineItems[index].total_cost = (quantity * sellingPrice).toFixed(2)
                }

                const totalWeight = computed(() => {
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem.weight),
                        0).toFixed(2)
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
                    return form.value.lineItems.reduce((acc, lineItem) => acc + parseFloat(lineItem
                        .total_cost), 0).toFixed(2)
                })

                const processing = ref(false)
                const submitForm = () => {

                    if (!form.value.location_id) {
                        formUtil.errorMessage('Select a store location')
                        return
                    }

                    if (!form.value.lineItems.some(lineItem => lineItem.quantity > 0)) {
                        formUtil.errorMessage('At least one item needs a return quantity')
                        return
                    }

                    let error = false
                    form.value.lineItems.forEach(lineItem => {
                        if (!/^[0-9]+$/.test(lineItem.quantity) || parseInt(lineItem.quantity) < 0) {
                            formUtil.errorMessage(`Enter valid quantity for ${lineItem.item_code}`)
                            error = true
                            return
                        }

                        if (parseInt(lineItem.quantity) + lineItem.returned > lineItem.received) {
                            formUtil.errorMessage(
                                `Item ${lineItem.item_code} exceeds its allowed return quantity`)
                            error = true
                            return
                        }

                        if (lineItem.quantity > lineItem.qoh) {
                            formUtil.errorMessage(
                                `Item ${lineItem.item_code} return quantity exceeds QOH`)
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

                    processing.value = true

                    axios.post('/api/process-return-from-grn', form.value)
                        .then(response => {

                            formUtil.successMessage('Return processed successfully!')

                            form.value.grn_id = ''
                            $('#grn-select').val(null).trigger('change')
                            form.value.lineItems = []

                            processing.value = false
                        })
                        .catch(error => {
                            formUtil.errorMessage(error.response.data.error)
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

                // Fetch grns
                const fetchGrns = () => {
                    axios.get('/api/grns-list', {
                            params: {
                                uom_id: form.value.bin_id
                            }
                        })
                        .then(response => {
                            grns.value = response.data
                        })
                        .catch(error => {
                            formUtil.error(error.response.data.error)
                        })
                }

                onMounted(() => {
                    if (form.value.location_id && form.value.bin_id) {
                        fetchGrns()
                    }
                })

                return {
                    form,
                    user,
                    branches,
                    locations,
                    bins,
                    calculateWeightAndCost,
                    totalWeight,
                    totalCost,
                    submitForm,
                    numberWithCommas,
                    processing,
                    grns,
                    purchaseOrderNo,
                    supplierName,
                    grnChanged,
                    fetchingLineItems,
                    branchChanged,
                    locationChanged,
                    uomChanged,
                    grnSelectClicked,
                    totalExclusive,
                    totalVat,
                    permissions,
                    canChangeBin
                }
            }
        }).mount('#app')
    </script>
@endsection
